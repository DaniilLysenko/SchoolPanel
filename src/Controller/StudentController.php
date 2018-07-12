<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use App\Entity\Student;

use App\Forms\StudentType;

class StudentController extends Controller
{
    /**
     * @Route("/school/{page}", name="studentList")
     */
    public function index($page = 1)
    {
    	$repository = $this->getDoctrine()->getRepository(Student::class);
        $form = $this->createForm(StudentType::class);

        return $this->render('student/index.html.twig', [
            'students' => $repository->findBy(array(), array('id' => 'DESC')),
            'addForm' => $form->createView()
        ]);
    }
}
