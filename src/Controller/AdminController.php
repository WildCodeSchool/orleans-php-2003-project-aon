<?php


namespace App\Controller;
use App\Model\EventManager;


class AdminController extends AbstractController
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
        $adminEvent = new EventManager();
        $event = $adminEvent->selectAll();
        return $this->twig->render('Admin/index.html.twig', ['event' => $event]);
    }
}