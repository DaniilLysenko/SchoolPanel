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
use App\Entity\Teacher;
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
     * @Route("/api/studentTeachers/{id}", name="studentTeachers")
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

    /**
     * @Route("/api/teachers", name="allTeachers")
     * @Method({"GET"})
     */
    public function teachersAction()
    {
        $teachers = $this->getDoctrine()->getRepository(Teacher::class)->findAll();
        return new JsonResponse($this->get("serializer")->normalize(['teachers' => $teachers]), 200);
    }

    /**
     * @Route("/api/teachers/add", name="addStudentTeacher")
     * @Method({"POST"})
     */
    public function addStudentTeacherAction(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $request->request->replace(is_array($data) ? $data : array());

        $teachers = $data['teachers'];
        $student_id = $data['student_id'];

        $student = $this->getDoctrine()->getRepository(Student::class)->find($student_id);

        if ($student) {
            foreach ($teachers as $teacher) {
                $teacher = $this->getDoctrine()->getRepository(Teacher::class)->find($teacher);
                if ($teacher) {
                    $student->addStudentTeacher($teacher);
                } else {
                    return new JsonResponse(['errors' => ['Teacher not found']], 400);
                }
            }
            $this->getDoctrine()->getManager()->persist($student);
            $this->getDoctrine()->getManager()->flush();
            return new JsonResponse(['success' => ['Teachers added succefully']], 200);
        }
        return new JsonResponse(['errors' => ['Student not found']], 400);
    }

    /**
     * @Route("/api/teachers/remove/{sid}/{tid}", name="removeStudentTeacher")
     * @Method({"GET"})
     */
    public function removeStudentTeacherAction($tid, $sid)
    {
        $em = $this->getDoctrine()->getManager();
        $student = $em->getRepository(Student::class)->find($sid);
        if (!$student) {
            return new JsonResponse(['errors' => 'Student not found'], 400);
        }
        $teacher = $em->getRepository(Teacher::class)->find($tid);
        if (!$teacher) {
            return new JsonResponse(['errors' => 'Teacher not found'], 400);
        }        

        $student->removeStudentTeacher($teacher);
        
        $em->persist($student);
        $em->flush();
        return new JsonResponse(['success' => "Teacher removed successfuly"], 200);
    }
}
