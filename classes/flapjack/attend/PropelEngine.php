<?php
namespace flapjack\attend;


use flapjack\attend\database\Classroom;
use flapjack\attend\database\ClassroomQuery;
use flapjack\attend\database\Schedule;
use flapjack\attend\database\ScheduleQuery;
use flapjack\attend\database\Student;
use flapjack\attend\database\StudentQuery;
use Propel\Runtime\Connection\ConnectionManagerSingle;
use Propel\Runtime\Propel;


class PropelEngine implements IDatabaseEngine
{
    public function __construct()
    {
    }

    public function connect(array $config): void
    {
        $host     = $config[ 'host' ];
        $dbname   = $config[ 'dbname' ];
        $user     = $config[ 'uname' ];
        $password = $config[ 'pword' ];

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

        $serviceContainer->initDatabaseMapFromDumps(array (
            'attend' =>
                array (
                    'tablesByName' =>
                        array (
                            'classrooms' => '\\flapjack\\attend\\database\\Map\\ClassroomTableMap',
                            'schedules' => '\\flapjack\\attend\\database\\Map\\ScheduleTableMap',
                            'students' => '\\flapjack\\attend\\database\\Map\\StudentTableMap',
                        ),
                    'tablesByPhpName' =>
                        array (
                            '\\Classroom' => '\\flapjack\\attend\\database\\Map\\ClassroomTableMap',
                            '\\Schedule' => '\\flapjack\\attend\\database\\Map\\ScheduleTableMap',
                            '\\Student' => '\\flapjack\\attend\\database\\Map\\StudentTableMap',
                        ),
                ),
        ));
    }


    public function getClassroomById(int $id) : array
    {
        $query    = new ClassroomQuery();
        $resource = $query->findPk($id);
        if (null === $resource) {
            return [];
        }

        return $resource->toArray();
    }


    public function getClassrooms() : array
    {
        $query    = new ClassroomQuery();
        $resource = $query->find();

        return $resource->toArray();
    }

    public function postClassroom(array $body) : array
    {
        $resource = new Classroom();
        $resource->setLabel($body[ 'Label' ]);
        $resource->setOrdering($body[ 'Ordering' ]);
        $resource->save();

        return $resource->toArray();
    }

    public function putClassroomById(int $id, array $body) : array
    {
        $query    = new ClassroomQuery();
        $resource = $query->findPk($id);
        if (null === $resource) {
            return [];
        }

        $resource->setLabel($body[ 'Label' ]);
        $resource->setOrdering($body[ 'Ordering' ]);
        $resource->save();

        return $resource->toArray();
    }

    public function deleteClassroomById(int $id) : int
    {
        $query    = new ClassroomQuery();
        $resource = $query->findPk($id);
        if (null === $resource) {
            return 0;
        }
        $resource->delete();

        return $id;
    }

    public function getStudentById(int $id): ?array
    {
        $query    = new StudentQuery();
        $resource = $query->findPk($id);

        return $resource?->toArray();
    }

    public function getStudents() : array
    {
        $query    = new StudentQuery();
        $resource = $query->find();

        return $resource->toArray();
    }


    public function postStudent(array $body) : array
    {
        $resource = new Student();
        $resource->setFamilyName($body[ 'FamilyName' ]);
        $resource->setFirstName($body[ 'FirstName' ]);
        $resource->setEnrolled($body[ 'Enrolled' ]);
        $resource->setClassroomId($body[ 'ClassroomId' ]);
        $resource->save();

        return $resource->toArray();
    }


    public function putStudentById(int $id, array $body) : ?array
    {
        $query    = new StudentQuery();
        $resource = $query->findPk($id);
        if (null === $resource) {
            return null;
        }

        $resource->setFamilyName($body[ 'FamilyName' ]);
        $resource->setFirstName($body[ 'FirstName' ]);
        $resource->setEnrolled($body[ 'Enrolled' ]);
        $resource->setClassroomId($body[ 'ClassroomId' ]);
        $resource->save();

        return $resource->toArray();
    }

    public function deleteStudentById(int $id) : ?int
    {
        $query    = new StudentQuery();
        $resource = $query->findPk($id);
        if (null === $resource) {
            return null;
        }
        $resource->delete();

        return $id;
    }

    public function getScheduleById(int $id) : ?array
    {
        $query    = new ScheduleQuery();
        $resource = $query->findPk($id);

        return $resource?->toArray();
    }

    public function getSchedules() : array
    {
        $query    = new ScheduleQuery();
        $resource = $query->find();

        return $resource->toArray();
    }

    public function postSchedule(array $body) : array
    {
        $resource = new Schedule();
        $resource->setStartDate($body[ 'StartDate' ]);
        $resource->setSchedule($body[ 'Schedule' ]);
        $resource->setStudentId($body[ 'StudentId' ]);
        $resource->save();

        return $resource->toArray();
    }

    public function putScheduleById(int $id, array $body) : ?array
    {
        $query    = new ScheduleQuery();
        $resource = $query->findPk($id);
        if (null === $resource) {
            return null;
        }

        $resource->setStartDate($body[ 'StartDate' ]);
        $resource->setSchedule($body[ 'Schedule' ]);
        $resource->setStudentId($body[ 'StudentId' ]);
        $resource->save();

        return $resource->toArray();
    }


    public function deleteScheduleById(int $id) : ?int
    {
        $query    = new ScheduleQuery();
        $resource = $query->findPk($id);
        if (null === $resource) {
            return null;
        }
        $resource->delete();

        return $id;
    }
}
