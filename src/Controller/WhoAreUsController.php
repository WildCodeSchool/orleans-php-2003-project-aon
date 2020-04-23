<?php

namespace App\Controller;

use App\Model\WhoAreUsManager;

class WhoAreUsController extends AbstractController
{
    /**
     * Display activity page
     *
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function index()
    {
            //whoAreUs
            $whoAreUsManager=new WhoAreUsManager();
            $whoAreUs = $whoAreUsManager->selectAll();

            return $this->twig->render('WhoAreUs/index.html.twig', ['whoAreUs' => $whoAreUs]);
    }
}
