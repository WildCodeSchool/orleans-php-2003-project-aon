<?php

namespace App\Controller;

use App\Model\LessonManager;

class AdminLessonController extends AbstractController
{
    public function createLesson(string $message = "")
    {
        $message = urldecode($message);
        return $this->twig->render('Admin/addLesson.html.twig', ['message' => $message]);
    }
}
