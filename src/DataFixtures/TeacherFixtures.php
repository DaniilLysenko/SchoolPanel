<?php

namespace App\DataFixtures;

use App\Entity\Teacher;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class TeacherFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
 		for ($i = 1; $i <= 10; $i++) {
            $teacher = new Teacher();
            $teacher->setName('Test Teacher');
            $teacher->setCourse("Some course");
            $manager->persist($teacher);
        }

        $manager->flush();
    }
}
