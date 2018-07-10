<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;

class AdminController extends Controller
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
    			return new JsonResponse(["error" => "data is incorrect"]);
    		}
    	}
    	return new JsonResponse(["error" => "data is missing"]);
    }
}
