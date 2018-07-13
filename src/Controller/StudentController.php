<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use App\Entity\Student;
use App\Entity\Teacher;

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

        $students = $repository->findBy([], ['id' => 'DESC']);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $students,
            $page,
            3
        );

        return $this->render('student/index.html.twig', [
            'pagination' => $pagination,
            'addForm' => $addForm->createView(),
            'editForm' => $editForm->createView()
        ]);
    }

    /**
     * @Route("/single/{id}", name="singleStudent")
     */
    public function single($id)
    {
        sleep(1);
        $st = $this->getDoctrine()->getRepository(Student::class)->find($id);
        return new JsonResponse($this->get("serializer")->normalize([
            'student' => $st
        ]), 200);
    }

    /**
     * @Route("/allTeachers/{id}", name="teachers")
     */
    public function teachers($id)
    {
        $student = $this->getDoctrine()->getRepository(Student::class)->find($id);
        $stTeachers = $student->getStudentTeacher();
        $allTeachers = $this->getDoctrine()->getRepository(Teacher::class)->findAll();
        return new JsonResponse($this->get("serializer")->normalize(['allTeachers' => $allTeachers, 'teachers' => $stTeachers]), 200);
    }

    /**
     * @Route("/addTeacher/{sid}", name="addTeacher")
     * @Method({"POST"})
     */
    public function addTeacher(Request $request, $sid)
    {
        $tid = $request->get('tid');
        $result = [];
        $student = $this->getDoctrine()->getRepository(Student::class)->find($sid);
        for ($i = 0; $i < count($tid); $i++) {
            $teacher = $this->getDoctrine()->getRepository(Teacher::class)->find($tid[$i]);
            $result[] = $teacher;
            $student->addStudentTeacher($teacher);    
        }
        
        $this->getDoctrine()->getManager()->persist($student);
        $this->getDoctrine()->getManager()->flush();
        return new JsonResponse($this->get("serializer")->normalize(['teacher' => $result]), 200);
    }
}
