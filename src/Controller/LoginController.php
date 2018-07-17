<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;


use App\Entity\Admin;
use App\Forms\AdminType;

use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends Controller
{
    /**
     * @Route("/login", name="login")
     * @Method({"POST"})
     */
    public function loginAction(Request $request, AuthenticationUtils $authenticationUtils, SerializerInterface $serializer)
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        return new JsonResponse($this->get("serializer")->normalize(['error' => $error->getMessage()]), 200);
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout()
    {

    }
}
