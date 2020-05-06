<?php

namespace App\Controller;

use App\Model\StageManager;

class StageController extends AbstractController
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
        //Stage
        $stageManager = new StageManager();
        $stages = $stageManager->selectAll();

        return $this->twig->render('Stage/index.html.twig', ['stages' => $stages]);
    }
}