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

        /**
         * Display lesson creation page
         *
         * @return string
         * @throws \Twig\Error\LoaderError
         * @throws \Twig\Error\RuntimeError
         * @throws \Twig\Error\SyntaxError
         */
   // public function addLesson()
    //{

       // if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // $lessonManager = new LessonManager();
            // $lesson = [
            //    'time' => $_POST['time'],
            //    'day' => $_POST['day'],
            //    'price' => $_POST['price'],

           // $id = $lessonManager->insert($lesson);
           // header('Location:/Admin/index');
        //}
      //  return $this->twig->render('Admin/addLesson.html.twig');
    //}
}
