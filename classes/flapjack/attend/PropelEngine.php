<?php
namespace flapjack\attend;


use flapjack\attend\database\Classroom;
use flapjack\attend\database\ClassroomQuery;
use flapjack\attend\database\Schedule;
use flapjack\attend\database\ScheduleQuery;
use flapjack\attend\database\Student;
use flapjack\attend\database\StudentQuery;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Connection\ConnectionManagerSingle;
use Propel\Runtime\Exception\PropelException;
use Propel\Runtime\Propel;


class PropelEngine implements IDatabaseEngine
{
    public function __construct()
    {
    }

    public function connect(array $config): void
    {
        $host     = $config['host'];
        $dbname   = $config['dbname'];
        $user     = $config['uname'];
        $password = $config['pword'];

        $serviceContainer = Propel::getServiceContainer();
        $serviceContainer->checkVersion(2);
        $serviceContainer->setAdapterClass('attend', 'mysql');
        $manager = new ConnectionManagerSingle('attend');
        $manager->setConfiguration(array(
            'classname'   => 'Propel\\Runtime\\Connection\\ConnectionWrapper',
            'dsn'         => "mysql:host=$host;dbname=$dbname",
            'user'        => $user,
            'password'    => $password,
            'attributes'  =>
                array(
                    'ATTR_EMULATE_PREPARES' => false,
                    'ATTR_TIMEOUT'          => 30,
                ),
            'model_paths' =>
                array(
                    0 => 'src',
                    1 => 'vendor',
                ),
        ));
        $serviceContainer->setConnectionManager($manager);
        $serviceContainer->setDefaultDatasource('attend');

        $serviceContainer->initDatabaseMapFromDumps(array(
            'attend' =>
                array(
                    'tablesByName'    =>
                        array(
                            'classrooms' => '\\flapjack\\attend\\database\\Map\\ClassroomTableMap',
                            'schedules'  => '\\flapjack\\attend\\database\\Map\\ScheduleTableMap',
                            'students'   => '\\flapjack\\attend\\database\\Map\\StudentTableMap',
                        ),
                    'tablesByPhpName' =>
                        array(
                            '\\Classroom' => '\\flapjack\\attend\\database\\Map\\ClassroomTableMap',
                            '\\Schedule'  => '\\flapjack\\attend\\database\\Map\\ScheduleTableMap',
                            '\\Student'   => '\\flapjack\\attend\\database\\Map\\StudentTableMap',
                        ),
                ),
        ));
    }


    /**
     * @param  int  $id
     *
     * @return Classroom|null
     *
     * Retrieve a single classroom record by its ID
     */
    public function getClassroomById(int $id): ?Classroom
    {
        return ClassroomQuery::create()->findPk($id);
    }


    /**
     * @return Collection
     *
     * Fetch all the classroom records
     */
    public function getClassrooms(): Collection
    {
        return ClassroomQuery::create()->find();
    }


    /**
     * @param  array  $body
     *
     * @return Classroom
     * @throws PropelException
     *
     * Insert a new classroom record
     */
    public function postClassroom(array $body): Classroom
    {
        $classroom = new Classroom();
        $classroom->setLabel($body['Label']);

        if ($body['Ordering']) {
            $classroom->setOrdering($body['Ordering']);

            // If an "Ordering" value is specified, bump up by one the ordering value for any current classrooms
            // whose current "Ordering" value is the same or higher than that of the new classroom
            $classrooms = ClassroomQuery::create()
                                        ->filterByOrdering(['min' => $body['Ordering']])
                                        ->orderBy('Ordering', Criteria::DESC)
                                        ->find();

            /** @var Classroom $c */
            foreach ($classrooms as $c) {
                $c->setOrdering($c->getOrdering() + 1);
                $c->save();
            }
        } else {
            // If no "Ordering" value is specified in the POST request, set the "Ordering" value to one higher than
            // the current highest "Ordering" value
            $maxValue = ClassroomQuery::create()
                                      ->withColumn(
                                          'MAX(Ordering)',
                                          'max_ordering'
                                      )
                                      ->select(['max_ordering'])
                                      ->orderBy('max_ordering', Criteria::DESC)
                                      ->findOne();

            $classroom->setOrdering($maxValue + 1);
        }

        $classroom->setCreatedAt(time());
        $classroom->save();

        return $classroom;
    }


    /**
     * @param  int  $id
     * @param  array  $body
     *
     * @return ?Classroom
     * @throws PropelException
     *
     * Update a single classroom record, identified by its ID
     */
    public function putClassroomById(int $id, array $body): ?Classroom
    {
        if ( null !== ($classroom = ClassroomQuery::create()->findPk($id))) {
            if ($body['Ordering'] > $classroom->getOrdering()) {
                // New "Ordering" is higher than before; re-order existing classrooms down
                $classrooms = ClassroomQuery::create()
                                            ->filterByOrdering(
                                                ['min' => $classroom->getOrdering() + 1, 'max' => $body['Ordering']]
                                            )
                                            ->orderBy('Ordering', Criteria::ASC)
                                            ->find();

                /** @var Classroom $c */
                foreach ($classrooms as $c) {
                    $c->setOrdering($c->getOrdering() - 1);
                    $c->save();
                }
            } elseif ($body['Ordering'] < $classroom->getOrdering()) {
                // New "Ordering" is lower than before; re-order existing classrooms up
                $classrooms = ClassroomQuery::create()
                                            ->filterByOrdering(
                                                ['min' => $body['Ordering'], 'max' => $classroom->getOrdering()]
                                            )
                                            ->orderBy('Ordering', Criteria::DESC)
                                            ->find();

                /** @var Classroom $c */
                foreach ($classrooms as $c) {
                    $c->setOrdering($c->getOrdering() + 1);
                    $c->save();
                }
            }

            $classroom->setLabel($body['Label']);
            $classroom->setOrdering($body['Ordering']);
            $classroom->setUpdatedAt(time());
            $classroom->save();
        }
        return $classroom;
    }


    /**
     * @param  int  $id
     *
     * @return ?int
     * @throws PropelException
     *
     * Delete a single classroom record, identified by its ID
     */
    public function deleteClassroomById(int $id): ?int
    {
        if ( null === ( $resource = ClassroomQuery::create()->findPk($id))) {
            return null;
        }

        // Adjust the "Ordering" value by -1 for all classroom records
        // whose current "Ordering" value exceeds that of the classroom being deleted
        $classrooms = ClassroomQuery::create()
                                    ->filterByOrdering(['min' => $resource->getOrdering() + 1])
                                    ->orderBy('Ordering', Criteria::DESC)
                                    ->find();
        foreach ($classrooms as $classroom) {
            $classroom->setOrdering($classroom->getOrdering() - 1);
            $classroom->save();
        }

        $resource->delete();
        return $id;
    }


    /**
     * @param  int  $id
     *
     * @return array|null
     *
     * Retrieve a single student record by its ID
     */
    public function getStudentById(int $id): ?Student
    {
        return StudentQuery::create()->findPk($id);
    }


    /**
     * @return Collection
     *
     * Fetch all the student records
     */
    public function getStudents(): Collection
    {
        return StudentQuery::create()->find();
    }


    /**
     * @param  array  $body
     *
     * @return Student
     * @throws PropelException Insert a new student record
     */
    public function postStudent(array $body): Student
    {
        $student = new Student();
        $student->setFamilyName($body['FamilyName']);
        $student->setFirstName($body['FirstName']);
        $student->setEnrolled($body['Enrolled']);
        $student->setClassroomId($body['ClassroomId']);
        $student->save();

        return $student;
    }


    /**
     * @param  int  $id
     * @param  array  $body
     *
     * @return Student|null
     * @throws PropelException
     */
    public function putStudentById(int $id, array $body): ?Student
    {
        if ( null !== ($student = StudentQuery::create()->findPk($id))) {
            $student->setFamilyName($body['FamilyName']);
            $student->setFirstName($body['FirstName']);
            $student->setEnrolled($body['Enrolled']);
            $student->setClassroomId($body['ClassroomId']);
            $student->save();
        }
        return $student;
    }


    /**
     * @param  int  $id
     *
     * @return int|null
     * @throws PropelException
     */
    public function deleteStudentById(int $id): ?int
    {
        if ( null === ($student = StudentQuery::create()->findPk($id))) {
            return null;
        }

        $student->delete();
        return $id;
    }

    /**
     * @param  int  $id
     *
     * @return Schedule|null
     */
    public function getScheduleById(int $id): ?Schedule
    {
        return ScheduleQuery::create()->findPk($id);
    }

    /**
     * @return Collection
     */
    public function getSchedules(): Collection
    {
        return ScheduleQuery::create()->find();
    }

    /**
     * @param  array  $body
     *
     * @return Schedule
     * @throws PropelException
     */
    public function postSchedule(array $body): Schedule
    {
        $schedule = new Schedule();
        $schedule->setStartDate($body['StartDate']);
        $schedule->setSchedule($body['Schedule']);
        $schedule->setStudentId($body['StudentId']);
        $schedule->save();

        return $schedule;
    }

    /**
     * @param  int  $id
     * @param  array  $body
     *
     * @return Schedule|null
     * @throws PropelException
     */
    public function putScheduleById(int $id, array $body): ?Schedule
    {
        if ( null !== ($schedule = ScheduleQuery::create()->findPk($id))) {
            $schedule->setStartDate($body['StartDate']);
            $schedule->setSchedule($body['Schedule']);
            $schedule->setStudentId($body['StudentId']);
            $schedule->save();
        }

        return $schedule;
    }


    /**
     * @param  int  $id
     *
     * @return int|null
     * @throws PropelException
     */
    public function deleteScheduleById(int $id): ?int
    {
        if ( null === ($schedule = ScheduleQuery::create()->findPk($id))) {
            return null;
        }
        $schedule->delete();
        return $id;
    }
}
