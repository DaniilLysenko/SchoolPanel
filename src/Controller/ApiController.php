<?php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Mcfedr\JsonFormBundle\Controller\JsonController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
// Entities
use App\Entity\Student;
// Forms
use App\Forms\StudentType;

class ApiController extends JsonController
{
    /**
     * @Route("/api/add", name="addStudent")
     * @Method({"POST"})
     */
    public function addAction(Request $request)
    {
        $student = new Student();
        $form = $this->createForm(StudentType::class, $student);
        $this->handleJsonForm($form, $request);
        $student->setAvatar("/web/img/avatars/def.jpg");
        $this->getDoctrine()->getManager()->persist($student);
        $this->getDoctrine()->getManager()->flush();
        return new JsonResponse($this->get("serializer")->normalize([
            'student' => $student
        ]), 200);
    }

    /**
     * @Route("/api/delete/{id}", name="deleteStudent")
     * @Method({"GET"})
     */
    public function deleteAction($id)
    {
        $student = $this->getDoctrine()->getRepository(Student::class)->find($id);
        if ($student) {
            $this->getDoctrine()->getManager()->remove($student);
            $this->getDoctrine()->getManager()->flush();
            return new JsonResponse(['success' => "Student deleted succefully"], 200);
        }
        return new JsonResponse(['errors' => ['Student not found']], 400);
    }

    /**
     * @Route("/api/edit/{id}", name="editStudent")
     * @Method({"POST"})
     */
    public function editAction(Request $request, $id)
    {
        $student = $this->getDoctrine()->getRepository(Student::class)->find($id);
        if ($student) {
            $form = $this->createForm(StudentType::class, $student);
            $this->handleJsonForm($form, $request);
            $this->getDoctrine()->getManager()->persist($student);
            $this->getDoctrine()->getManager()->flush();
            return new JsonResponse($this->get("serializer")->normalize([
                'student' => $student
            ]), 200);
        }
        return new JsonResponse(['errors' => ['Student not found']], 400);
    }

    /**
     * @Route("/api/student/{id}", name="student")
     * @Method({"GET"})
     */
    public function singleStudentAction($id)
    {
        $student = $this->getDoctrine()->getRepository(Student::class)->find($id);
        if (!$student) {
            return new JsonResponse(['errors' => ['Student not found']], 400);
        }
        return new JsonResponse($this->get("serializer")->normalize([
            'student' => $student
        ]), 200);
    }

    /**
     * @Route("/api/teachers/{id}", name="studentTeachers")
     * @Method({"POST"})
     */
    public function studentTeachersAction($id)
    {
        $student = $this->getDoctrine()->getRepository(Student::class)->find($id);
        if ($student) {
            $teachers = $student->getStudentTeacher();
            return new JsonResponse($this->get("serializer")->normalize(['teachers' => $teachers]), 200);
        }
        return new JsonResponse(['errors' => ['Student not found']], 400);
    }
}
