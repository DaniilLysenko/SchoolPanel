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
    	$rep = $this->getDoctrine()->getRepository(Student::class);
        $addForm = $this->createForm(StudentType::class);
        $editForm = $this->createForm(EditType::class);

        $students = $rep->findBy([], ['id' => 'DESC']);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate($students,$page,3);

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
        $result = [];
        $tid = $request->get('tid');
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

    /**
     * @Route("/removeTeacher/{sid}/{tid}", name="removeTeacher")
     * @Method({"POST"})
     */
    public function removeTeacher($sid, $tid)
    {
        $em = $this->getDoctrine()->getManager();
        $student = $em->getRepository(Student::class)->find($sid);
        if (!$student) {
            return new JsonResponse(['errors' => "Student not found"], 400);
        }
        $teacher = $em->getRepository(Teacher::class)->find($tid);
        if (!$teacher) {
            return new JsonResponse(['errors' => "Teacher not found"], 400);
        }        

        $student->removeStudentTeacher($teacher);
        
        $em->persist($student);
        $em->flush();
        return new JsonResponse(['success' => "Teacher removed successfuly"], 200);
    }

    /**
     * @Route("/search/{query}", name="searchStudent")
     */
    public function search($query)
    {
        $rep = $this->getDoctrine()->getManager()->getRepository(Student::class);
        $students = $rep->studentSearch($query);
        return new JsonResponse($this->get("serializer")->normalize(['students' => $students]), 200);
    }
}
