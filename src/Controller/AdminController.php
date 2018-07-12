<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Mcfedr\JsonFormBundle\Controller\JsonController;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\Student;
use App\Forms\StudentType;

class AdminController extends JsonController
{
    /**
     * @Route("/", name="login")
     */
    public function index()
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController'
        ]);
    }

    /**
     * @Route("/login")
     * @Method({"POST"})
     */

    public function login()
    {
    	if (isset($_POST['login']) && isset($_POST['password'])) {
    		if ($_POST['login'] === 'admin' && $_POST['password'] === 'admin') {
    			return new JsonResponse(["status" => 200]);
    		} else {
    			return new JsonResponse(["error" => "Data is incorrect"]);
    		}
    	}
    	return new JsonResponse(["error" => "Data is missing"]);
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

    // Rerender form (another variant to code above)

    // public function addAction(Request $request)
    // {
    //     $student = new Student();
    //     $form = $this->createForm(StudentType::class, $student);
    //     $form->handleRequest($request);
    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $this->getDoctrine()->getManager()->persist($student);
    //         $this->getDoctrine()->getManager()->flush();
    //         return new JsonResponse(array('message' => 'Success!'), 200);
    //     } 
    //     $response = new JsonResponse(array(
    //         'message' => 'Error',
    //         'form' => $this->renderView('layouts/form.html.twig',array('addForm' =>$form->createView()))), 400
    //     );
    //     return $response;
    // }

    /**
     * @Route("/remove", name="remove")
     * @Method({"POST"})
     */
    public function remove()
    {
        if (isset($_POST['id'])) {
            if (!preg_match('/[0-9]/',$_POST['id'])) {
                return new JsonResponse(["errors" => array("Id is invalid")]);
            }
            $student = $this->getDoctrine()->getRepository(Student::class)->find($_POST['id']);
            $student->delete();
            $this->getDoctrine()->getManager()->flush();
            return new JsonResponse(["success" => "OK"]);
        }
        return new JsonResponse(["errors" => array("Data is missing")]);
    }
}
