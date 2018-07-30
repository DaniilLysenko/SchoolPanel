<?php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Mcfedr\JsonFormBundle\Controller\JsonController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
// Entities
use App\Entity\Student;
use App\Entity\Teacher;
// Forms
use App\Forms\StudentType;
use App\Forms\SearchStudentType;
// Models
use App\Models\SearchModel;

class ApiController extends JsonController
{
    private $sort = 's.id';
    private $direction = 'desc';
    /**
     * @Route("/api/add", name="addStudent", methods={"POST"})
     */
    public function addAction(Request $request)
    {
        $student = new Student();
        $form = $this->createForm(StudentType::class, $student);
        $this->handleJsonForm($form, $request);
        $this->getDoctrine()->getManager()->persist($student);
        $this->getDoctrine()->getManager()->flush();
        return new JsonResponse($this->get("serializer")->normalize([
            'student' => $student
        ]), 200);
    }

    /**
     * @Route("/api/delete/{id}", name="deleteStudent", methods={"GET"})
     */
    public function deleteAction($id)
    {
        $student = $this->getDoctrine()->getRepository(Student::class)->find($id);
        if ($student) {
            $this->getDoctrine()->getManager()->remove($student);
            $this->getDoctrine()->getManager()->flush();
            return new JsonResponse(['success' => "Student deleted successfully"], 200);
        }
        return new JsonResponse(['errors' => ['Student not found']], 400);
    }

    /**
     * @Route("/api/edit/{id}", name="editStudent", methods={"POST"})
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
     * @Route("/api/student/{id}", name="student", methods={"GET"})
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
     * @Route("/api/studentTeachers/{id}", name="studentTeachers", methods={"POST"})
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
     * @Route("/api/teachers", name="allTeachers", methods={"GET"})
     */
    public function teachersAction()
    {
        $teachers = $this->getDoctrine()->getRepository(Teacher::class)->findAll();
        return new JsonResponse($this->get("serializer")->normalize(['teachers' => $teachers]), 200);
    }

    /**
     * @Route("/api/teachers/add", name="addStudentTeacher", methods={"POST"})
     */
    public function addStudentTeacherAction(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $request->request->replace(is_array($data) ? $data : array());

        $teachers = $data['teachers'];
        $student_id = $data['student_id'];

        $result = [];

        $student = $this->getDoctrine()->getRepository(Student::class)->find($student_id);

        if ($student) {
            foreach ($teachers as $teacher) {
                $teacher = $this->getDoctrine()->getRepository(Teacher::class)->find($teacher);
                if ($teacher) {
                    $student->addStudentTeacher($teacher);
                    $result[] = $teacher;
                } else {
                    return new JsonResponse(['errors' => ['Teacher not found']], 400);
                }
            }
            $this->getDoctrine()->getManager()->persist($student);
            $this->getDoctrine()->getManager()->flush();
            return new JsonResponse($this->get("serializer")->normalize(['teacher' => $result]), 200);
        }
        return new JsonResponse(['errors' => ['Student not found']], 400);
    }

    /**
     * @Route("/api/teachers/remove/{sid}/{tid}", name="removeStudentTeacher", methods={"GET"})
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
        return new JsonResponse(['success' => "Teacher removed successfully"], 200);
    }

    /**
     * @Route("/api/school/{page}", methods={"GET"})
     */
    public function schoolPaginateAction(Request $request, $page = 1)
    {
        if ($request->query->get('sort') !== null) {
            $this->sort = $request->query->get('sort');
        }
        if ($request->query->get('direction') !== null) {
            $this->direction = $request->query->get('direction');
        }

        $rep = $this->getDoctrine()->getManager()->getRepository(Student::class);
        $students = $rep->studentOrder($this->sort, $this->direction, $page, 3);

        return new JsonResponse($this->get("serializer")->normalize(['students' => $students]), 200);
    }

    /**
     * @Route("/api/search", name="studentApiSearch", methods={"POST"})
     */
    public function searchAction(Request $request)
    {
        $search = new SearchModel();
        $form = $this->createForm(SearchStudentType::class, $search);
        $this->handleJsonForm($form, $request);
        $rep = $this->getDoctrine()->getManager()->getRepository(Student::class);

        $students = $rep->studentSearch($search)->getQuery()->getResult();

        return new JsonResponse($this->get("serializer")->normalize(['students' => $students]), 200);
    }

    /**
     * @Route("/api/search/{query}/{page}", methods={"GET"})
     */
    public function searchPaginateAction(Request $request, $query, $page = 1)
    {
        if ($request->query->get('sort') !== null) {
            $this->sort = $request->query->get('sort');
        }
        if ($request->query->get('direction') !== null) {
            $this->direction = $request->query->get('direction');
        }

        $rep = $this->getDoctrine()->getManager()->getRepository(Student::class);
        $students = $rep->studentOrderSearch($query, $this->sort, $this->direction, $page, 3);

        return new JsonResponse($this->get("serializer")->normalize(['students' => $students]), 200);
    }
}
