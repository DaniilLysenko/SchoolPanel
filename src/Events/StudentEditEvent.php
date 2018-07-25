<?php

namespace App\Events;

use Symfony\Component\EventDispatcher\Event;
use App\Entity\Student;

class StudentEditEvent extends Event
{
    const NAME = 'student.edit';

    private $student;

    public function __construct(Student $student)
    {
        $this->student = $student;
    }

    public function getStudent()
    {
        return $this->student;
    }
}