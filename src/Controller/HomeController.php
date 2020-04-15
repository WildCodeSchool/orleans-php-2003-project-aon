<?php
/**
 * Created by PhpStorm.
 * User: aurelwcs
 * Date: 08/04/19
 * Time: 18:40
 */

namespace App\Controller;

use App\Model\ActivityManager;

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
        //activity
        $activityManager=new ActivityManager();
        $activities=$activityManager->selectActivitiesToBeDisplayed();
        $maxLength=50;

        $activitiesLength=count($activities);
        for ($i=0;$i<$activitiesLength;$i++) {
            if(strlen($activities[$i]['description'])>$maxLength){
                $activities[$i]['shortDescription']=substr ( $activities[$i]['description'] , 0, $maxLength).'...';
            }
        }
        
        return $this->twig->render('Home/index.html.twig', ['activities'=>$activities]);
    }
}
