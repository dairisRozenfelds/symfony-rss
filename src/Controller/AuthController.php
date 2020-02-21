<?php

namespace App\Controller;

use App\Tools\Validator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AuthController extends AbstractController
{
    const CSRF_TOKEN_NAME = 'login';

    /**
     * @var Validator
     */
    private $validator;

    /**
     * AuthController constructor.
     * @param Validator $validator
     */
    public function __construct(Validator $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @Route("/login", name="app_login_get", methods={"GET"})
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
         if ($this->getUser()) {
             return $this->redirectToRoute('index');
         }

        return $this->render('security/login.html.twig', [
            'submitRoute' => $this->generateUrl('app_login_post'),
            'submitRedirectRoute' => $this->generateUrl('index'),
            'registerRoute' => $this->generateUrl('app_register_get'),
            'tokenName' => self::CSRF_TOKEN_NAME
        ]);
    }

    /**
     * @Route("/login", name="app_login_post", methods={"POST"})
     * @return void
     */
    public function loginAjax(): void
    {
        return;
    }

    /**
     * @Route("/logout", name="app_logout", methods={"GET"})
     * @return void
     */
    public function logout(): void
    {
        return;
    }
}
