<?php

namespace Attend\RepoEngine;


class ClassroomsRepository extends Repository
{
    public static function getTableName()
    {
        return 'classrooms';
    }

    public static function getColumnNames()
    {
        return [
            'id'         => [
                'insert' => false,
                'update' => false,
                'select' => true
            ],
            'label'      => [
                'insert' => true,
                'update' => true,
                'select' => true
            ],
            'ordering'   => [
                'insert' => true,
                'update' => true,
                'select' => true
            ],
            'created_at' => [
                'insert' => false,
                'update' => false,
                'select' => true
            ],
            'updated_at' => [
                'insert' => false,
                'update' => false,
                'select' => true
            ]
        ];
    }

    public function insert($inserts)
    {
        if (array_key_exists('ordering', $inserts) && ('' == $inserts[ 'ordering' ])) {
            $inserts[ 'ordering' ] = null;

        } else if (isset($inserts[ 'ordering' ])) {
            $sql = 'UPDATE classrooms SET ordering = ordering + 1 where ordering >= ?';
            $sth = $this->getPdo()->prepare($sql);
            $sth->execute([$inserts[ 'ordering' ]]);
        }

        return parent::insert($inserts);
    }


}
