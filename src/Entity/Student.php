<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\StudentRepository")
 */
class Student
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\Length(min = 5,max = 100, minMessage="Name should be more than {{ limit }} characters")
     * @Assert\NotBlank(message="Name can not be blank")
     */
    private $name;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Length(min = 1, max = 2)
     * @Assert\Type(
     *     type="integer",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    private $age;

    /**
     * @ORM\Column(type="string", length=10)
     * @Assert\Choice({"man", "woman"}, message="Choice valid sex type")
     * @Assert\NotBlank(message="Sex type can not be blank")
     */
    private $sex;

    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\Regex("/[0-9]/", message="Your phone can contain only numbers")
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=200, options={"default" = "/web/img/avatars/def.jpg"}))
     */
    private $avatar = "/web/img/avatars/def.jpg";

    /**
     * @ORM\ManyToMany(targetEntity="Teacher", inversedBy="teacherStudents")
     */
    private $studentTeachers;

    public function __construct()
    {
        $this->studentTeachers = new ArrayCollection();
    }

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

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(int $age): self
    {
        $this->age = $age;

        return $this;
    }

    public function getSex(): ?string
    {
        return $this->sex;
    }

    public function setSex(string $sex): self
    {
        $this->sex = $sex;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(string $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function addStudentTeacher(Teacher $teacher)
    {
        if ($this->studentTeachers->contains($teacher)) {
            return;
        }
        $this->studentTeachers[] = $teacher;
    }

    public function removeStudentTeacher(Teacher $teacher)
    {
        if ($this->studentTeachers->contains($teacher)) {
            $this->studentTeachers->removeElement($teacher);
        }
    }

    /**
     * @return ArrayCollection|Teacher[]
     */
    public function getStudentTeacher()
    {
        return $this->studentTeachers;
    }
}
