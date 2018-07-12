<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use App\Entity\Student;

use App\Forms\StudentType;
use App\Forms\EditType;

class StudentController extends Controller
{
    /**
     * @Route("/school/{page}", name="studentList")
     */
    public function index($page = 1)
    {
    	$repository = $this->getDoctrine()->getRepository(Student::class);
        $addForm = $this->createForm(StudentType::class);
        $editForm = $this->createForm(EditType::class);

        return $this->render('student/index.html.twig', [
            'students' => $repository->findBy(array(), array('id' => 'DESC')),
            'addForm' => $addForm->createView(),
            'editForm' => $editForm->createView()
        ]);
    }
}
