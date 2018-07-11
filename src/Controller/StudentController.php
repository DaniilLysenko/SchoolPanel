<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use App\Entity\Student;

class StudentController extends Controller
{
    /**
     * @Route("/school/{page}", name="studentList")
     */
    public function index($page = 1)
    {
    	$repository = $this->getDoctrine()->getRepository(Student::class);
        $pages = $repository->getCountPages() / 5;
        $offset = $page > 1 ? (($page - 1) * 5) : 0;
        return $this->render('student/index.html.twig', [
            'students' => $repository->findBy(array(), array('id' => 'DESC'), 5, $offset),
            'pages' => $pages,
            'page' => $page
        ]);
    }
}
