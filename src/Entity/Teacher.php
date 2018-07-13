<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TeacherRepository")
 */
class Teacher
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=150)
     * @Assert\Length(min = 5, max = 100, minMessage="Name should be more than {{ limit }} characters")
     * @Assert\NotBlank(message="Name can not be blank")
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=150)
     * @Assert\Length(min = 5,max = 100, minMessage="Corse should be more than {{ limit }} characters")
     * @Assert\NotBlank(message="Course can not be blank")
     */
    private $course;

    public function getId()
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCourse(): ?string
    {
        return $this->course;
    }

    public function setCourse(string $course): self
    {
        $this->course = $course;

        return $this;
    }
}
