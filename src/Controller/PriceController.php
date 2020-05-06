<?php
/**
 * Created by PhpStorm.
 * User: Emmanuelle
 * Date: 15/04/2020
 */

namespace App\Controller;

use App\Model\LessonManager;

class PriceController extends AbstractController
{
    public function index()
    {
        $lessonManager = new LessonManager();
        $prices= $lessonManager->selectAllPrices();

        return $this->twig->render('Price/index.html.twig', ['prices' => $prices]);
    }
}