<?php

namespace Attend\RepoEngine;


class SchedulesRepository extends Repository
{
    static public function getTableName()
    {
        return 'schedules';
    }

    static public function getColumnNames()
    {
        return [
            'id'         => [
                'insert' => false,
                'update' => false,
                'select' => true
            ],
            'student_id' => [
                'insert' => true,
                'update' => true,
                'select' => true
            ],
            'schedule'   => [
                'insert' => true,
                'update' => true,
                'select' => true
            ],
            'start_date' => [
                'insert' => true,
                'update' => true,
                'select' => true
            ],
            'entered_at' => [
                'insert' => false,
                'update' => false,
                'select' => true
            ]
        ];
    }

    static protected function preProcessInserts($inserts)
    {
        if (empty($inserts[ 'start_date' ])) {
            $inserts[ 'start_date' ] = date('Y-m-d');
        }

        return parent::preProcessInserts($inserts);
    }

    static protected function postProcessInserts(&$cols, &$vals)
    {
        $cols[] = 'entered_at';
        $vals[] = time();

        return parent::postProcessInserts($cols, $vals);
    }
}
