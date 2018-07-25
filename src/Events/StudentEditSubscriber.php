<?php
namespace App\Events;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class StudentEditSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
       return [StudentEditEvent::NAME => 'onStudentEdit'];
    }

    public function onStudentEdit(StudentEditEvent $event)
    {
        $student = $event->getStudent();
        $student->setVersion($student->getVersion() + 0.1);
    }
}