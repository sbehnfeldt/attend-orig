<?php
namespace Attend;


interface IDatabaseEngine
{
    public function connect(array $config);

    public function getClassroomById(int $id) : array;

    public function getClassrooms() : array;

    public function postClassroom(array $body) : array;

    public function putClassroomById(int $id, array $body) : array;

    public function deleteClassroomById(int $id) : int;

    public function getStudentById(int $id) : array;

    public function getStudents() : array;

    public function postStudent(array $body) : array;

    public function putStudentById(int $id, array $body) : array;

    public function deleteStudentById(int $id) : int;

    public function getScheduleById(int $id) : array;

    public function getSchedules() : array;

    public function postSchedule(array $body) : array;

    public function putScheduleById(int $id, array $body) : array;

    public function deleteScheduleById(int $id) : int;
}