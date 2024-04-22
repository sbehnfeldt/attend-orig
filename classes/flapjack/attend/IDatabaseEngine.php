<?php
namespace flapjack\attend;


use flapjack\attend\database\Classroom;
use flapjack\attend\database\Schedule;
use flapjack\attend\database\Student;
use Propel\Runtime\Collection\Collection;

interface IDatabaseEngine
{
    public function connect(array $config);

    public function getClassroomById(int $id) : ?Classroom;

    public function getClassrooms() : Collection;

    public function postClassroom(array $body) : Classroom;

    public function putClassroomById(int $id, array $body) : ?Classroom;

    public function deleteClassroomById(int $id) : ?int;

    public function getStudentById(int $id) : ?Student;

    public function getStudents() : Collection;

    public function postStudent(array $body) : Student;

    public function putStudentById(int $id, array $body) : ?Student;

    public function deleteStudentById(int $id) : ?int;

    public function getScheduleById(int $id) : ?Schedule;

    public function getSchedules() : Collection;

    public function postSchedule(array $body) : Schedule;

    public function putScheduleById(int $id, array $body) : ?Schedule;

    public function deleteScheduleById(int $id) : ?int;
}