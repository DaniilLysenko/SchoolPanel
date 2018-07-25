<?php

namespace App\DataFixtures;

use App\Entity\Student;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class StudentFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $names = ['Den', 'Andrii', 'Ivan', 'Vasyl', 'Olesya', 'Vika', 'Vlad', 'Ihor', 'Stark', 'Eduard'];
        for ($i = 1; $i <= 20; $i++) {
            $student = new Student();
            $student->setName($names[mt_rand(0, 5)].' User');
            $student->setSex(mt_rand(0, 1));
            $student->setAge(mt_rand(6, 17));
            $student->setPhone('phone');
            $student->setAvatar('/web/img/avatars/def.jpg');
            $student->setVersion(1.0);
            $manager->persist($student);
        }

        $manager->flush();
    }
}
