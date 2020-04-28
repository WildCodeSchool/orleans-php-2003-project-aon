<?php
/**
 * Created by PhpStorm.
 * User: Emmanuelle
 * Date: 15/04/2020
 */

namespace App\Controller;

use App\Model\ActivityManager;
use App\Model\LessonManager;

class ActivityController extends AbstractController
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
        //activity
        $activityManager=new ActivityManager();
        $activities=$activityManager->selectAll();
        $maxLength=50;

        $activitiesLength=count($activities);
        for ($i=0; $i<$activitiesLength; $i++) {
            if (strlen($activities[$i]['description'])>$maxLength) {
                $activities[$i]['shortDescription']=substr($activities[$i]['description'], 0, $maxLength).'...';
            }
        }

        return $this->twig->render('Activity/index.html.twig', ['activities'=>$activities]);
    }

    public function showActivity(int $id, int $ageClassId = -1)
    {
        $activityManager=new ActivityManager();
        $activity=$activityManager->selectOneById($id);

        $lessonManager=new LessonManager();
        $ageClasses=$lessonManager->selectAgeClassesForOneById($id);
        $ageClass=null;
        foreach ($ageClasses as &$ageClass) {
            if ($ageClass['id']==$ageClassId) {
                $ageClass['action']='active';
            }
        }

        $lessonsByAgeClass=array();
        if ($ageClassId>=0) {
            $lessonsByAgeClass[]=$lessonManager->selectEverthingForOneById($id, $ageClassId);
        } else {
            if (!empty($ageClasses)) {
                $lessonsByAgeClass[]=$lessonManager->selectEverthingForOneById($id, $ageClasses[0]['id']);
            }
        }

        return $this->twig->render('Activity/showActivity.html.twig', ['activity'=>$activity,
            'lessonsByAgeClass' => $lessonsByAgeClass,
            'ageClasses' => $ageClasses]);
    }
}
