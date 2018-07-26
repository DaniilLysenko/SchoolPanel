<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Mcfedr\JsonFormBundle\Controller\JsonController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use App\Entity\Student;
use App\Entity\Teacher;
use App\Models\SearchModel;

use App\Forms\StudentType;
use App\Forms\SearchStudentType;
use App\Forms\UploadImageType;

class StudentController extends JsonController
{
    /**
     * @Route("/school/{page}", name="studentList")
     */
    public function studentListAction(Request $request, $page = 1)
    {
        $sort = 's.id';
        $direction = 'desc';
        if ($request->query->get('sort') !== null) {
            $sort = $request->query->get('sort');
        }
        if ($request->query->get('direction') !== null) {
            $direction = $request->query->get('direction');
        }

        $rep = $this->getDoctrine()->getManager()->getRepository(Student::class);
        $students = $rep->studentOrder($sort, $direction, $page, 3);
        return new JsonResponse($this->get("serializer")->normalize(['students' => $students, 'url' => '/school/', 'page' => $page]), 200);
    }

    /**
     * @Route("/", name="index")
     */
    public function index($page = 1)
    {
        $rep = $this->getDoctrine()->getRepository(Student::class);
        $addForm = $this->createForm(StudentType::class);
        $editForm = $this->createForm(UploadImageType::class);
        $searchForm = $this->createForm(SearchStudentType::class);
        $students = $rep->studentFind();

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate($students, $page, 3);

        $pagination->setUsedRoute('studentList');

        $count = $rep->countAllStudents();

        return $this->render('student/index.html.twig', [
            'pagination' => $pagination,
            'addForm' => $addForm->createView(),
            'editForm' => $editForm->createView(),
            'searchForm' => $searchForm->createView(),
            'count' => ceil($count / 3),
            'page' => $page
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
     * @Route("/addTeacher", name="addTeacher")
     * @Method({"POST"})
     */
    public function addTeacherAction(Request $request)
    {
        $result = [];
        $tid = $request->get('tid');
        $sid = $request->get('sid');
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
     * @Method({"GET"})
     */
    public function removeTeacherAction($sid, $tid)
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
        return new JsonResponse(['success' => "Teacher removed successfully"], 200);
    }

    /**
     * @Route("/search", name="searchStudent")
     * @Method({"POST"})
     */
    public function searchAction(Request $request, $page = 1)
    {
        $search = new SearchModel();
        $form = $this->createForm(SearchStudentType::class, $search);
        $this->handleJsonForm($form, $request);
        $rep = $this->getDoctrine()->getManager()->getRepository(Student::class);

        if (trim($form->get('name')->getData()) == "") {
            $students = $rep->studentFind();
        } else {
            $students = $rep->studentSearch($search);
        }

        $count = count($students);

        return new JsonResponse($this->get("serializer")->normalize(['students' => $students, 'url' => '/search/'.
            $form->get('name')->getData().'/', 'page' => $page, 'count' => ceil($count/3)]), 200);
    }

    /**
     * @Route("/search/{query}/{page}", name="search")
     * @Method({"GET"})
     */
    public function searchPag(Request $request, $query, $page = 1)
    {
        $rep = $this->getDoctrine()->getManager()->getRepository(Student::class);

        $search = new SearchModel();
        $search->setName($query);

        $sort = 's.id';
        $direction = 'desc';
        if ($request->query->get('sort') !== null) {
            $sort = $request->query->get('sort');
        }
        if ($request->query->get('direction') !== null) {
            $direction = $request->query->get('direction');
        }

        $students = $rep->studentOrderSearch($search, $sort, $direction, $page, 3);

        $count = count($students);

        return new JsonResponse($this->get("serializer")->normalize(['students' => $students, 'url' => '/search/'.
            $query.'/', 'page' => $page, 'count' => ceil($count/3)]), 200);
    }
}
