<?php

namespace Attend\Database;

use Attend\Database\Base\Student as BaseStudent;

/**
 * Skeleton subclass for representing a row from the 'students' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class Student extends BaseStudent
{
    static private $decoder = [
        [0x0001, 0x0020, 0x0400],
        [0x0002, 0x0040, 0x0800],
        [0x0004, 0x0080, 0x1000],
        [0x0008, 0x0100, 0x2000],
        [0x0010, 0x0200, 0x4000]
    ];

    private $notes;

    public function __construct()
    {
        parent::__construct();
        $this->notes = [
            'FD'  => 0,
            'HD'  => 0,
            'HDL' => 0
        ];
    }


    public function writeSchedule(\DateTime $weekOf, int $dayOf) : string
    {
        $decoder = Student::$decoder[ $dayOf ];
        $code    = $this->getSchedules()[ $this->getSchedules()->count() - 1 ]->getSchedule();

        if (($code & $decoder[ 0 ]) && ($code & $decoder[ 2 ])) {
            $x = 'FD';
        } else if ($code & $decoder[ 0 ]) {
            if ($code & $decoder[ 1 ]) {
                $x = 'HDL';
            } else {
                $x = 'HD';
            }

        } else if ($code & $decoder[ 2 ]) {
            if ($code & $decoder[ 1 ]) {
                $x = 'HDL';
            } else {
                $x = 'HD';
            }
        } else {
            return '';
        }
        $this->notes[ $x ]++;

        return $x;
    }


    public function writeSummary()
    {
        $summary = [];
        if ($this->notes[ 'FD' ]) {
            $summary[] = $this->notes[ 'FD' ] . 'FD';
        }
        if ($this->notes[ 'HD' ]) {
            $summary[] = $this->notes[ 'HD' ] . 'HD';
        }
        if ($this->notes[ 'HDL' ]) {
            $summary[] = $this->notes[ 'HDL' ] . 'HDL';
        }

        return implode(',', $summary);
    }
}
