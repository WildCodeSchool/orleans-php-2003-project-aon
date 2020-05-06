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
    public function index($message = "")
    {
        $message = urldecode($message);

        return $this->twig->render(
            'Contact/index.html.twig',
            ['subjects'=> $this->getSubjectsList(),
             'message'=>$message]
        );
    }

    public function sendMessage(): string
    {
        $toBeReturned = "";
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errorsAndData = $this->checkContactPostData();

            if (count($errorsAndData['data']) == count($errorsAndData['errors'])) {
                $data=$errorsAndData['data'];
                mail("doc-albert@laposte.net", $data['subject'], $data['messageContent'], 'From: '.$data['email']);
                header("location:/contact/index/Votre message a été envoyé");
            } else {
                $toBeReturned = $this->twig->render(
                    'Contact/index.html.twig',
                    ['message' => "Votre message n'a pas été envoyé",
                    'subjects'=> $this->getSubjectsList(),
                    'errors' => $errorsAndData['errors'],
                    'contact' => $errorsAndData['data']]
                );
            }
        }
        return $toBeReturned;
    }

    private function getSubjectsList() : array
    {
        $activityManager= new ActivityManager();
        $activitiesList=$activityManager->selectAll();
        $subjects=array();
        foreach ($activitiesList as $activity) {
            $subjects[]=$activity['name'];
        }

        $subjects[]='Stages';
        $subjects[]='Autres';

        return $subjects;
    }

    private function checkContactPostData() : array
    {
        //errors array
        $errors=[
            'name' => '',
            'email' => '',
            'subject' => '',
            'messageContent' => '',
            ];

        //data array
        $data=array();

        $maxLength=50;
        if (empty($_POST['name'])) {
            $errors['name'] = "Vous devez indiquer votre nom";
        } elseif (strlen(trim($_POST['name']))>$maxLength) {
            $errors['name'] = "Votre nom ne doit pas dépasser $maxLength caractères";
        } else {
            $data['name'] =trim($_POST['name']);
        }

        $maxLength=50;
        if (empty($_POST['email'])) {
            $errors['email'] = "Vous devez indiquer votre email";
        } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "Vous devez entrer un email valide";
        } else {
            $data['email'] =trim($_POST['email']);
        }

        $maxLength=50;
        if (empty($_POST['subject'])) {
            $errors['subject'] = "Vous devez indiquer le sujet de votre message";
        } elseif (strlen(trim($_POST['email']))>$maxLength) {
            $errors['subject'] = "Votre email ne doit pas dépasser $maxLength caractères";
        } else {
            $data['subject'] =trim($_POST['subject']);
        }

        $maxLength=5000;
        if (empty($_POST['messageContent'])) {
            $errors['messageContent'] = "Vous devez indiquer un message";
        } elseif (strlen(trim($_POST['messageContent']))>$maxLength) {
            $errors['messageContent'] = "Votre message ne doit pas dépasser $maxLength caractères";
        } else {
            $data['messageContent'] =trim($_POST['messageContent']);
        }

        return ['errors' => $errors, 'data' => $data];
    }
}
