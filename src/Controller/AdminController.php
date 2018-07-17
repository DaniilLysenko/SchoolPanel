<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Mcfedr\JsonFormBundle\Controller\JsonController;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\Student;
use App\Entity\Admin;
use App\Forms\StudentType;
use App\Forms\EditType;
use App\Forms\AdminType;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AdminController extends JsonController
{
    /**
     * @Route("/", name="index")
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
        $this->getDoctrine()->getManager()->persist($student);
        $this->getDoctrine()->getManager()->flush();

        return new JsonResponse($this->get("serializer")->normalize([
            'student' => $student
        ]), 200);
    }

    /**
     * @Route("/remove", name="remove")
     * @Method({"POST"})
     */
    public function remove(Request $request)
    {
        if (!is_null($request->get('id'))) {
            $student = $this->getDoctrine()->getRepository(Student::class)->find($request->get('id'));
            if ($student) {
                $this->getDoctrine()->getManager()->remove($student);
                $this->getDoctrine()->getManager()->flush();
                return new JsonResponse(["success" => "OK"]);
            }
            return new JsonResponse(["errors" => ["Student not found"]], 400);
        }
        return new JsonResponse(["errors" => ["Data is missing"]], 400);
    }

    /**
     * @Route("/edit", name="edit"),
     * @Method({"POST"})
     */
    public function edit(Request $request)
    {
        $student = new Student();
        $form = $this->createForm(EditType::class, $student);
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
