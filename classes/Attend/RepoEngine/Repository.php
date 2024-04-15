<?php

namespace Attend\RepoEngine;

use Exception;
use PDO;


abstract class Repository implements iRepository
{
    /** @var  PDO $pdo */
    private $pdo;


    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @param PDO $pdo
     */
    public function setPdo(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @return PDO
     */
    public function getPdo()
    {
        return $this->pdo;
    }

    public function getPrimaryKey()
    {
        return 'id';
    }

    public function getColumns($op)
    {
        $columns = [];
        foreach (static::getColumnNames() as $k => $v) {
            if ($v[ $op ]) {
                $columns[] = $k;
            }
        }

        return $columns;
    }

    protected function parseCriteria($filters)
    {
        return [[], []];
    }

    public function select($params = [])
    {
        $values = [];

        list($criteria, $vals) = empty($params[ 'filters' ]) ? [[], []] : static::parseCriteria($params[ 'filters' ]);
        $criteria = implode(' AND ', $criteria);
        $values   = array_merge($values, $vals);

        $sql = sprintf("SELECT %s FROM %s %s",
            implode(', ', $this->getColumns('select')),
            $this->getTableName(),
            $criteria ? "WHERE $criteria" : '');

        $sth     = $this->pdo->prepare($sql);
        $b       = $sth->execute($values);
        $results = $sth->fetchAll();

        return $results;
    }

    public function selectOne($id)
    {
        $sql     = sprintf("SELECT %s FROM %s where %s = ?",
            implode(', ', $this->getColumns('select')),
            $this->getTableName(),
            $this->getPrimaryKey());
        $sth     = $this->pdo->prepare($sql);
        $b       = $sth->execute([$id]);
        $results = $sth->fetchAll();

        return $results;
    }

    /****************************************************************************************************
     * Insert
     ****************************************************************************************************/
    public function insert($inserts)
    {
        list($cols, $vals) = static::preProcessInserts($inserts);
        static::postProcessInserts($cols, $vals);


        $sql = sprintf("INSERT INTO %s (%s) VALUES(%s)",
            $this->getTableName(),
            implode(', ', $cols),
            implode(', ', array_fill(0, count($cols), '?'))
        );
        $sth = $this->pdo->prepare($sql);
        $b   = $sth->execute($vals);

        $id = $this->pdo->lastInsertId();

        return $id;
    }

    static protected function preProcessInserts($inserts)
    {
        $allCols = static::getColumnNames();
        $cols    = [];
        $vals    = [];

        foreach ($inserts as $k => $v) {
            if ( ! isset($allCols[ $k ])) {
                throw new Exception(sprintf('Unknown column name "%s"'), $k);
            }
            if ( ! isset($allCols[ $k ][ 'insert' ])) {
                throw new Exception(sprintf('Insert rule not defined for column "%s"'), $k);
            }
            if (is_callable($allCols[ $k ][ 'insert' ])) {
                if ( ! $allCols[ $k ][ 'insert' ]()) {
                    throw new Exception(sprintf('Cannot write to column "%s"', $k));
                }
            } else if ( ! $allCols[ $k ][ 'insert' ]) {
                throw new Exception(sprintf('Cannot write to column "%s"', $k));
            }
            $cols[] = $k;
            $vals[] = $v;
        }

        return [$cols, $vals];
    }

    static protected function postProcessInserts(&$cols, &$vals)
    {
        return;
    }


    /****************************************************************************************************
     * Updates
     ****************************************************************************************************/
    public function updateOne($id, $updates)
    {
        list($cols, $vals) = static::preProcessUpdates($updates);
        $vals[] = $id;
        for ($i = 0; $i < count($cols); $i++) {
            $cols[ $i ] .= '= ?';
        }
        $cols = implode(', ', $cols);
        $sql  = sprintf("UPDATE %s SET %s WHERE id=?", $this->getTableName(), $cols);
        $sth  = $this->pdo->prepare($sql);
        $b    = $sth->execute($vals);

        return;
    }


// Break the update data associative arrays into 2 parallel indexed arrays
    static protected function preProcessUpdates($updates)
    {
        $cols = $vals = [];
        foreach ($updates as $k => $v) {
            $cols[] = $k;
            $vals[] = $v;
        }

        return [$cols, $vals];
    }


    /****************************************************************************************************
     * Deletes
     ****************************************************************************************************/
    public function deleteOne($id)
    {
        $sql = sprintf("DELETE FROM %s WHERE id=?", $this->getTableName());
        $sth = $this->pdo->prepare($sql);
        $b   = $sth->execute([$id]);

        return;
    }
}
