<?php
/**
 * Created by PhpStorm.
 * User: Emmanuelle
 * Date: 15/04/2020
 */

namespace App\Controller;

use App\Model\ActivityManager;

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

    public function showActivity(int $id)
    {
        $activityManager=new ActivityManager();
        $activity=$activityManager->selectOneById($id);
        return $this->twig->render('Activity/showActivity.html.twig', ['activity' => $activity]);
    }
}
