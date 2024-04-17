<?php
namespace flapjack\attend;

class SigninPdf extends AttendPdf
{

    public function __construct()
    {
        parent::__construct();
        $this->setRowHeight(15);
    }

    public function Header()
    {
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 10, 'Sign-In', 0, 0, 'C');

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
        $this->ln();

        $i = 0;
        $d = new \DateTime($this->getWeekOf()->format('Y-m-d'));
        $this->Cell($this->colWidths[ $i++ ], $this->getHeaderHeight(), '', 'LR', 0, 'C', true);
        $this->Cell($this->colWidths[ $i++ ], $this->getHeaderHeight(), $d->format('M j'), 'LBR', 0, 'C', true);
        $this->Cell($this->colWidths[ $i++ ], $this->getHeaderHeight(),
            $d->add(new \DateInterval('P1D'))->format('M d'),
            'LBR', 0, 'C', true);
        $this->Cell($this->colWidths[ $i++ ], $this->getHeaderHeight(),
            $d->add(new \DateInterval('P1D'))->format('M d'),
            'LBR', 0, 'C', true);
        $this->Cell($this->colWidths[ $i++ ], $this->getHeaderHeight(),
            $d->add(new \DateInterval('P1D'))->format('M d'),
            'LBR', 0, 'C', true);
        $this->Cell($this->colWidths[ $i++ ], $this->getHeaderHeight(),
            $d->add(new \DateInterval('P1D'))->format('M d'),
            'LBR', 0, 'C', true);
        $this->ln();

        $i = 0;
        $this->Cell($this->colWidths[ $i++ ], $this->getHeaderHeight(), '', 'LR', 0, 'C', true);
        foreach (self::getDayAbbrevs() as $day) {
            $this->Cell($this->colWidths[ $i ] / 2, $this->getHeaderHeight(), "In", 'LBR', 0, 'C', true);
            $this->Cell($this->colWidths[ $i++ ] / 2, $this->getHeaderHeight(), "", 'LR', 0, 'C', true);
        }
        $this->ln();


        $i = 0;
        $this->Cell($this->colWidths[ $i++ ], $this->getHeaderHeight(), 'Student', 'LBR', 0, 'C', true);
        foreach (self::getDayAbbrevs() as $day) {
            $this->Cell($this->colWidths[ $i ] / 2, $this->getHeaderHeight(), "Out", 'LBR', 0, 'C', true);
            $this->Cell($this->colWidths[ $i++ ] / 2, $this->getHeaderHeight(), "Initial", 'LBR', 0, 'C', true);
        }
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
        $this->SetFont('Arial', '', 10);
        $this->AliasNbPages();
        $this->colWidths = [55, 45, 45, 45, 45, 45];
        $this->SetFillColor(200);

        foreach ($this->classes as $class) {
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
        parent::Output();
    }

    private function outputClassroom($class)
    {
        $this->setTheClassroom($class);
        $this->AddPage('L');
        foreach ($class[ 'students' ] as $studentId) {
            $this->outputStudent($studentId);
        }

        // Output a few blank rows, for drop-ins
        for ($j = 0; $j < 3; $j++) {
            $this->Cell($this->colWidths[ 0 ], $this->rowHeight, '', 1, 0);
            for ($i = 0; $i < 5; $i++) {
                $x = $this->GetX();
                $y = $this->GetY();
                $this->SetDrawColor(175, 175, 175);
                $this->Line($x, $y + ($this->getRowHeight() / 2), $x + $this->colWidths[ $i + 1 ],
                    $y + ($this->getRowHeight() / 2));
                $this->SetDrawColor(0, 0, 0);
                $this->Cell($this->colWidths[ $i + 1 ] / 2, $this->getRowHeight(), "", 1, 0, 'C', false);
                $this->Cell($this->colWidths[ $i + 1 ] / 2, $this->getRowHeight(), "", 1, 0, 'C', false);
            }
            $this->ln();
        }
    }

    private function outputStudent($studentId)
    {
        $student = $this->students[ $studentId ];
        if ('1' != $student[ 'Enrolled' ]) {
            return;
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

        $this->Cell($this->colWidths[ 0 ], $this->getRowHeight(),
            $student[ 'FirstName' ] . ' ' . $student[ 'FamilyName' ], 1, 0);
        for ($i = 0; $i < 5; $i++) {
            $x = $this->GetX();
            $y = $this->GetY();
            $this->SetDrawColor(175, 175, 175);
            $this->Line($x, $y + ($this->getRowHeight() / 2), $x + $this->colWidths[ $i + 1 ],
                $y + ($this->getRowHeight() / 2));
            $this->SetDrawColor(0, 0, 0);
            $this->Cell($this->colWidths[ $i + 1 ] / 2, $this->getRowHeight(), "", 1, 0, 'C', $this->getDark($i));
            $this->Cell($this->colWidths[ $i + 1 ] / 2, $this->getRowHeight(), "", 1, 0, 'C', $this->getDark($i));
        }
        $this->ln();
    }
}
