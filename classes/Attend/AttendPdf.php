<?php
namespace Attend;


class AttendPdf extends \FPDF
{

    /** @var  PDO $pdo */
    protected $pdo;

    /** @var  IDatabaseEngine */
    protected $engine;

    protected $classes;
    protected $students;
    protected $schedules;
    protected $dark;
    protected static $decoder = [
        [0x0001, 0x0020, 0x0400],
        [0x0002, 0x0040, 0x0800],
        [0x0004, 0x0080, 0x1000],
        [0x0008, 0x0100, 0x2000],
        [0x0010, 0x0200, 0x4000]
    ];


    /** @var  array */
    protected static $dayAbbrevs = ['mon', 'tue', 'wed', 'thu', 'fri'];

    /** @var array */
    protected $theClassroom;

    /** @var  array */
    protected $colWidths;

    /** @var  int */
    protected $rowHeight;

    /** @var  int */
    protected $headerHeight;

    /** @var  \DateTime */
    protected $weekOf;

    public function __construct($orientation = 'P', $unit = 'mm', $size = 'A4')
    {
        $this->theClassroom = null;
        $this->colWidths    = [];
        $this->headerHeight = 5;
        $this->rowHeight    = 10;
        $this->weekOf       = new \DateTime();
        $this->dark         = [false, false, false, false, false];
        parent::__construct($orientation, $unit, $size);
    }

    /**
     * @return IDatabaseEngine
     */
    public function getEngine() : IDatabaseEngine
    {
        return $this->engine;
    }

    /**
     * @param IDatabaseEngine $engine
     */
    public function setEngine(IDatabaseEngine $engine)
    {
        $this->engine = $engine;
    }


    /**
     * @return array
     */
    public function getTheClassroom()
    {
        return $this->theClassroom;
    }

    /**
     * @param array $theClassroom
     */
    public function setTheClassroom($theClassroom)
    {
        $this->theClassroom = $theClassroom;
    }

    /**
     * @return PDO
     */
    public function getPdo()
    {
        return $this->pdo;
    }

    /**
     * @param \PDO $pdo
     */
    public function setPdo($pdo)
    {
        $this->pdo = $pdo;
    }


    /**
     * @return array
     */
    public function getColWidths()
    {
        return $this->colWidths;
    }

    /**
     * @param array $colWidths
     */
    public function setColWidths($colWidths)
    {
        $this->colWidths = $colWidths;
    }

    /**
     * @return int
     */
    public function getRowHeight()
    {
        return $this->rowHeight;
    }

    /**
     * @param int $rowHeight
     */
    public function setRowHeight($rowHeight)
    {
        $this->rowHeight = $rowHeight;
    }

    /**
     * @return int
     */
    public function getHeaderHeight()
    {
        return $this->headerHeight;
    }

    /**
     * @param int $headerHeight
     */
    public function setHeaderHeight($headerHeight)
    {
        $this->headerHeight = $headerHeight;
    }

    /**
     * @return \DateTime
     */
    public function getWeekOf()
    {
        return $this->weekOf;
    }

    /**
     * @param string $weekOf
     */
    public function setWeekOf($weekOf)
    {
        $this->weekOf = new \DateTime($weekOf);
    }

    public function setDark($i)
    {
        $this->dark[ $i ] = true;
    }

    public function clearDark($i)
    {
        $this->dark[ $i ] = false;
    }

    public function getDark($i)
    {
        return $this->dark[ $i ];
    }

    /**
     * @return array
     */
    public static function getDayAbbrevs()
    {
        return self::$dayAbbrevs;
    }


    /**
     * @param $student
     *
     * From all of a student's schedules, build a composite schedule effective
     * the week of $this->startDate
     */
    public function getCompositeSchedule($student, $startDate)
    {

        // Find which of the student's schedules is in effect on $startDate.
//        $schedules = $this->getApi()->fetchSchedules($student['id']);
        $url       = 'http://' . $_SERVER[ 'HTTP_HOST' ] . '/attend-api/schedules?filters=student_id::' . $student[ 'id' ];
        $schedules = file_get_contents($url);
        $schedules = json_decode($schedules, true);
        $schedules = $schedules[ 'data' ];

        $sched = null;
        $index = 0;
        while ($index < count($schedules)) {
            if ($schedules[ $index ][ 'start_date' ] > $startDate) {
                break;
            }
            $sched = $schedules[ $index ];
            $index++;
        }

        // $sched is now the schedule in effect on $startDate.  (If $sched is null, this means that the student
        // starts at some point in the future of $startDate.  This is needed so that users may enroll students
        // in advance.
        $composite = null;
        $cur       = new \DateTime($this->getWeekOf()->format('Y-m-d'));
        foreach ($this->getDayAbbrevs() as $i => $day) {
            if (null == $sched) {
                $composite[ $day ] = null;
            } else if ($cur->format('w') - $i >= 2) {
                // If some clown passes in a $startDate in the middle of the week, the effective
                // schedule for all days prior to the start date should be null
                $composite[ $day ] = null;
            } else {
                $composite[ $day ] = $sched[ $day ];
            }

            // Prepare for the next day: see if the student's next schedule goes into effect
            $cur->add(new \DateInterval('P1D'));
            if ($index < count($schedules)) {
                if ($cur >= $schedules[ $index ][ 'startDate' ]) {
                    $sched = $schedules[ $index ];
                    $index++;
                }
            }

        }


        return $composite;
    }

    protected function prepare()
    {
        $this->classes = [];
        $classes       = $this->getEngine()->getClassrooms();
        for ($i = 0; $i < count($classes); $i++) {
            $class                           = $classes[ $i ];
            $class[ 'students' ]             = [];
            $this->classes[ $class[ 'Id' ] ] = $class;
        }

        $this->students = [];
        $students       = $this->getEngine()->getStudents();
        for ($i = 0; $i < count($students); $i++) {
            $student                            = $students[ $i ];
            $student[ 'schedules' ]             = [];
            $this->students[ $student[ 'Id' ] ] = $student;

            $classroomId                                   = $student[ 'ClassroomId' ];
            $this->classes[ $classroomId ][ 'students' ][] = $student[ 'Id' ];
        }


        $this->schedules = [];
        $schedules       = $this->getEngine()->getSchedules();
        for ($i = 0; $i < count($schedules); $i++) {
            $schedule                             = $schedules[ $i ];
            $this->schedules[ $schedule[ 'Id' ] ] = $schedule;

            $studentId                                     = $schedule[ 'StudentId' ];
            $this->students[ $studentId ][ 'schedules' ][] = $schedule[ 'Id' ];
        }

        return;
    }
}
