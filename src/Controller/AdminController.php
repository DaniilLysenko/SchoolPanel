<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Mcfedr\JsonFormBundle\Controller\JsonController;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\Student;
use App\Forms\StudentType;
use App\Forms\UploadImageType;

class AdminController extends JsonController
{
    /**
     * @Route("/login", name="login")
     * @Method({"GET"})
     */
    public function index()
    {
        return $this->render('admin/index.html.twig');
    }

    /**
     * @Route("/add", name="add")
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
     * @Route("/remove/{id}", name="remove")
     * @Method({"GET"})
     */
    public function removeAction($id)
    {
        $student = $this->getDoctrine()->getRepository(Student::class)->find($id);
        if ($student) {
            $this->getDoctrine()->getManager()->remove($student);
            $this->getDoctrine()->getManager()->flush();
            return new JsonResponse(["success" => "OK"]);
        }
        return new JsonResponse(["errors" => ["Student not found"]], 400);
    }

    /**
     * @Route("/edit", name="edit"),
     * @Method({"POST"})
     */
    public function editAction(Request $request)
    {
        $student = new Student();
        $form = $this->createForm(UploadImageType::class, $student);
        $form->handleRequest($request);
        $mime = ['jpeg', 'png'];
        if ($form->isSubmitted()) {
            $file = $form->get('avatar')->getData();
            if (in_array($file->guessExtension(), $mime)) {
                $fileName = $form->get('id')->getData().'.'.$file->guessExtension();

                $file->move($this->getParameter('avatars_directory'), $fileName);
                $student = $this->getDoctrine()->getRepository(Student::class)->find($form->get('id')->getData());
                $student->setAvatar('/web/img/avatars/'.$fileName);
                

                $this->getDoctrine()->getManager()->persist($student);
                $this->getDoctrine()->getManager()->flush();

                return new JsonResponse(['success' => true], 200);
            }
            return new JsonResponse(['errors' => 'Invalid image'], 400);
        }
        return new JsonResponse(['errors' => 'Submit error'], 400);
    }
}
