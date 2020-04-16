<?php

namespace App\Controller;

use App\Model\EventManager;
use App\Model\ActivityManager;
use App\Model\PartnerManager;

class HomeController extends AbstractController
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
        //event section
        $eventManager=new EventManager();
        $event=$eventManager->selectNextEvent();

        //activity section
        $activityManager=new ActivityManager();
        $activities=$activityManager->selectActivitiesToBeDisplayed();
        $maxLength=50;

        $activitiesLength=count($activities);
        for ($i=0; $i<$activitiesLength; $i++) {
            if (strlen($activities[$i]['description'])>$maxLength) {
                $activities[$i]['shortDescription']=substr($activities[$i]['description'], 0, $maxLength).'...';
            }
        }

        //partner
        $partnerManager = new PartnerManager();
        $partners = $partnerManager->selectAll();
      
        /* add data required for the view to the tab here */
        return $this->twig->render('Home/index.html.twig', ['event'=>$event, 'activities'=>$activities, 'partners'=>$partners]);
    }
}
