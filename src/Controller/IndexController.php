<?php

namespace App\Controller;

use App\Tools\RssParsers\TheRegisterParser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /**
     * @Route("/index", name="index")
     * @param TheRegisterParser $theRegisterParser
     * @return RedirectResponse|Response
     */
    public function index(TheRegisterParser $theRegisterParser)
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login_get');
        }

        $theRegisterParser->parse();

        return $this->render('index/index.html.twig');
    }
}
