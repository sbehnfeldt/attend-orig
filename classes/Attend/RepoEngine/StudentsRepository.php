<?php

namespace Attend\RepoEngine;


class StudentsRepository extends Repository
{
    static public function getTableName()
    {
        return 'students';
    }

    static public function getColumnNames()
    {
        return [
            'id'           => [
                'insert' => false,
                'update' => false,
                'select' => true,
            ],
            'family_name'  => [
                'insert' => true,
                'update' => true,
                'select' => true,
            ],
            'first_name'   => [
                'insert' => true,
                'update' => true,
                'select' => true,
            ],
            'enrolled'     => [
                'insert' => true,
                'update' => true,
                'select' => true,
            ],
            'classroom_id' => [
                'insert' => true,
                'update' => true,
                'select' => true,
            ]
        ];
    }

    static protected function preProcessInserts($inserts)
    {
        if (array_key_exists('classroom_id', $inserts)) {
            $temp = json_decode($inserts[ 'classroom_id' ], true);
            if ($temp) {
                $inserts[ 'classroom_id' ] = $temp[ 'data' ];
            }
        }

        return parent::preProcessInserts($inserts);
    }


    static protected function preProcessUpdates($updates)
    {
        // classroom_id may be null
        if (array_key_exists('classroom_id', $updates)) {
            $temp = json_decode($updates[ 'classroom_id' ], true);
            if ($temp) {
                $updates[ 'classroom_id' ] = $temp[ 'data' ];
            }
        }

        return parent::preProcessUpdates($updates);
    }

    protected function parseCriteria($filters)
    {
        $criteria = [];
        $vals     = [];

        $filters = explode('|', $filters);
        foreach ($filters as $filter) {
            $f          = explode('::', $filter);
            $criteria[] = "${f[0]} = ?";
            $vals[]     = $f[ 1 ];
        }

        return [$criteria, $vals];
    }


}
