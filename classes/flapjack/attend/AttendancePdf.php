<?php
namespace flapjack\attend;

class AttendancePdf extends AttendPdf
{


    public function __construct()
    {
        parent::__construct();
    }

    public function Header()
    {
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 10, 'Attendance', 0, 0, 'C');

        $this->SetFont('Arial', '', 12);
        $this->ln();
        $this->Cell(0, 10, $this->getTheClassroom()[ 'Label' ], 0, 0, 'L');
        $this->Cell(0, 10, 'Week of ' . $this->getWeekOf()->format('M j, Y'), 0, 1, 'R');

        // Draw the table header
        $this->SetFont('Arial', '', 10);
        $this->SetFillColor(200);

        $i = 0;
        $d = new \DateTime($this->getWeekOf()->format('Y-m-d'));
        $this->Cell($this->colWidths[ $i++ ], $this->getHeaderHeight(), '', 'LTR', 0, 'C', true);
        $this->Cell($this->colWidths[ $i++ ], $this->getHeaderHeight(), $d->format('D'), 'LTR', 0, 'C', true);
        $this->Cell($this->colWidths[ $i++ ], $this->getHeaderHeight(), $d->add(new \DateInterval('P1D'))->format('D'),
            'LTR', 0, 'C', true);
        $this->Cell($this->colWidths[ $i++ ], $this->getHeaderHeight(), $d->add(new \DateInterval('P1D'))->format('D'),
            'LTR', 0, 'C', true);
        $this->Cell($this->colWidths[ $i++ ], $this->getHeaderHeight(), $d->add(new \DateInterval('P1D'))->format('D'),
            'LTR', 0, 'C', true);
        $this->Cell($this->colWidths[ $i++ ], $this->getHeaderHeight(), $d->add(new \DateInterval('P1D'))->format('D'),
            'LTR', 0, 'C', true);
        $this->Cell($this->colWidths[ $i++ ], $this->getHeaderHeight(), '', 'LTR', 0, 'C', true);
        $this->ln();

        $i = 0;
        $d = new \DateTime($this->getWeekOf()->format('Y-m-d'));
        $this->Cell($this->colWidths[ $i++ ], $this->getHeaderHeight(), 'Student', 'LBR', 0, 'C', true);
        $this->Cell($this->colWidths[ $i++ ], $this->getHeaderHeight(), $d->format("M j"), 'LBR', 0, 'C', true);
        $this->Cell($this->colWidths[ $i++ ], $this->getHeaderHeight(),
            $d->add(new \DateInterval('P1D'))->format('M j'),
            'LBR', 0, 'C', true);
        $this->Cell($this->colWidths[ $i++ ], $this->getHeaderHeight(),
            $d->add(new \DateInterval('P1D'))->format('M j'),
            'LBR', 0, 'C', true);
        $this->Cell($this->colWidths[ $i++ ], $this->getHeaderHeight(),
            $d->add(new \DateInterval('P1D'))->format('M j'),
            'LBR', 0, 'C', true);
        $this->Cell($this->colWidths[ $i++ ], $this->getHeaderHeight(),
            $d->add(new \DateInterval('P1D'))->format('M j'),
            'LBR', 0, 'C', true);
        $this->Cell($this->colWidths[ $i++ ], $this->getHeaderHeight(), 'Notes', 'LBR', 0, 'C', true);
        $this->ln();
    }

    public function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 10);
        $this->Cell(0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }


    public function Output($dest = '', $name = '', $isUTF8 = false)
    {
        $this->prepare();


        $this->AliasNbPages();
        $this->colWidths = [50, 15, 15, 15, 15, 15, 50];
        $this->SetFillColor(200);
        foreach ($this->classes as &$class) {
            usort($class[ 'students' ], function ($id1, $id2) {
                if ($this->students[ $id1 ][ 'FamilyName' ] > $this->students[ $id2 ][ 'FamilyName' ]) {
                    return 1;
                }
                if ($this->students[ $id1 ][ 'FamilyName' ] < $this->students[ $id2 ][ 'FamilyName' ]) {
                    return -1;
                }
                if ($this->students[ $id1 ][ 'FirstName' ] > $this->students[ $id2 ][ 'FirstName' ]) {
                    return 1;
                }
                if ($this->students[ $id1 ][ 'FirstName' ] < $this->students[ $id2 ][ 'FirstName' ]) {
                    return -1;
                }

                return 0;
            });
            $this->outputClassroom($class);
        }

        parent::Output($dest, $name, $isUTF8);
    }

    private function outputClassroom($classroom)
    {
        $this->setTheClassroom($classroom);
        $this->AddPage('P');
        $totals = [0, 0, 0, 0, 0];
        foreach ($classroom[ 'students' ] as $studentId) {
            $subtotals = $this->outputStudent($studentId);

            if ($subtotals) {
                for ($i = 0; $i < count($subtotals); $i++) {
                    if ($subtotals[ $i ]) {
                        $totals[ $i ]++;
                    }
                }
            }
        }

        // Output a few blank rows, for drop-ins
        for ($i = 0; $i < 3; $i++) {
            for ($j = 0; $j < 7; $j++) {
                $this->Cell($this->colWidths[ $j ], $this->rowHeight, '', 1, 0);
            }
            $this->ln();
        }
        $this->outputStudentCount($totals);
    }

    private function outputStudent($studentId)
    {
        $attendance = [false, false, false, false, false];
        $student    = $this->students[ $studentId ];
        if ('1' != $student[ 'Enrolled' ]) {
            return 0;
        }
        usort($student[ 'schedules' ], function ($id1, $id2) {
            $date1 = \DateTime::createFromFormat('Y-m-d', $this->schedules[ $id1 ][ 'StartDate' ]);
            $date2 = \DateTime::createFromFormat('Y-m-d', $this->schedules[ $id2 ][ 'StartDate' ]);
            if ($date1 < $date2) {
                return -1;
            }
            if ($date1 > $date2) {
                return 1;
            }

            return 0;
        });

        $this->Cell($this->colWidths[ 0 ], $this->rowHeight,
            $student[ 'FamilyName' ] . ', ' . $student[ 'FirstName' ], 1, 0);
        $thisWeek = clone $this->getWeekOf();
        $today    = new \DateTime($this->schedules[ $student[ 'schedules' ][ 0 ] ][ 'StartDate' ]);
        $notes    = [
            'HD'  => 0,
            'HDL' => 0,
            'FD'  => 0
        ];
        $j        = 0;
        for ($i = 0; $i < 5; $i++) {
            while (($j + 1) < count($student[ 'schedules' ])) {
                $next = new \DateTime($this->schedules[ $student[ 'schedules' ][ $j + 1 ] ][ 'StartDate' ]);
                if ($next > $thisWeek) {
                    break;
                }
                $today = $next;
                $j++;
            }
            $s    = [
                'am'    => false,
                'pm'    => false,
                'lunch' => false
            ];
            $temp = $this->schedules[ $student[ 'schedules' ][ $j ] ][ 'Schedule' ];
            if (0 != ($temp & self::$decoder[ $i ][ 0 ])) {
                $s[ 'am' ]        = true;
                $attendance[ $i ] = true;
            }
            if (0 != ($temp & self::$decoder[ $i ][ 1 ])) {
                $s[ 'lunch' ]     = true;
                $attendance[ $i ] = true;
            }
            if (0 != ($temp & self::$decoder[ $i ][ 2 ])) {
                $s[ 'pm' ]        = true;
                $attendance[ $i ] = true;
            }
            if ($s[ 'am' ] && $s[ 'pm' ]) {
                $notes[ 'FD' ]++;
            } else if ($s[ 'am' ]) {
                if ($s[ 'lunch' ]) {
                    $notes[ 'HDL' ]++;
                } else {
                    $notes[ 'HD' ]++;
                }
            } else if ($s[ 'pm' ]) {
                if ($s[ 'lunch' ]) {
                    $notes[ 'HDL' ]++;
                } else {
                    $notes[ 'HD' ]++;
                }
            }
            $shade = ! ($s[ 'am' ] || $s[ 'lunch' ] || $s[ 'pm' ]);
            $this->Cell($this->colWidths[ $i + 1 ], $this->rowHeight, '', 1, 0, 'C', $shade);
            $thisWeek->modify('+1 day');
            $today->modify('+1 day');
        }

        foreach (['HD', 'HDL', 'FD'] as $k) {
            if ($notes[ $k ] == 0) {
                unset ($notes[ $k ]);
            } else {
                $notes[ $k ] = $notes[ $k ] . $k;
            }
        }
        $this->Cell($this->colWidths[ 6 ], $this->rowHeight, implode(',', $notes), 1);
        $this->ln();

        return $attendance;
    }

    private function outputStudentCount($counts)
    {
        $this->Cell($this->colWidths[ 0 ], $this->rowHeight, 'Totals:', 1, 0);
        for ($i = 0; $i < count($counts); $i++) {
            $this->Cell($this->colWidths[ $i + 1 ], $this->rowHeight, $counts[ $i ], 1, 0);
        }
        $this->Cell($this->colWidths[ 6 ], $this->rowHeight, '', 1, 0);
        $this->ln();
    }
}
