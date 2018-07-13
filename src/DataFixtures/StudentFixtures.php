<?php

namespace App\DataFixtures;

use App\Entity\Student;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class StudentFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
 		for ($i = 1; $i <= 20; $i++) {
            $student = new Student();
            $student->setName('Test User');
            $student->setSex(mt_rand(0, 1));
            $student->setAge(mt_rand(6, 17));
            $student->setPhone('phone');
            $student->setAvatar('/web/img/avatars/def.jpg');
            $manager->persist($student);
        }

        $manager->flush();
    }
}
