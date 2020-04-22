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
    /**
     * Display event informations specified by $id
     *
     * @param int $id
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function showEvent(int $id)
    {
        $eventManager = new EventManager();
        $event = $eventManager->selectOneById($id);
        return $this->twig->render('Admin/showEvent.html.twig', ['event' => $event]);
    }


    /**
     * Display event edition page specified by $id
     *
     * @param int $id
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function editEvent(int $id): string
    {
        $eventManager = new EventManager();
        $event = $eventManager->selectOneById($id);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $event['title'] = $_POST['title'];
            $eventManager->updateEvent($event);
        }
        return $this->twig->render('Admin/showEvent/html/twig', ['event' => $event]);
    }
}
