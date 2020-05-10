<?php

namespace App\Controller;

use App\Model\LessonManager;
use App\Model\ActivityManager;
use App\Model\EventManager;
use App\Model\MessageManager;
use \FilesystemIterator;

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
        $activityManager = new ActivityManager();
        $activities = $activityManager->getActivityList();

        $adminEvent = new EventManager();
        $event = $adminEvent->selectAll();

        $lessonManager = new LessonManager();
        $lessons = $lessonManager->selectAllLessonsForAdmin();

        $messageManager = new MessageManager();
        $messages = $messageManager->selectAll();

        return $this->twig->render(
            'Admin/index.html.twig',
            ['event' => $event,
                'lessons' => $lessons,
                'messagesBox' => $messages,
                'activities' => $activities]
        );
    }
}
