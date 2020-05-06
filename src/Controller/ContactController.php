<?php
/**
 * Created by PhpStorm.
 * User: Adrien
 * Date: 15/04/2020
 */

namespace App\Controller;

use App\Model\EventManager;
use App\Model\ActivityManager;
use App\Model\PartnerManager;

class ContactController extends AbstractController
{
    /**
     * Display home page
     *
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */

    public function index()
    {
        /* add data required for the view to the tab here */
        return $this->twig->render('Contact/index.html.twig');
    }
}
