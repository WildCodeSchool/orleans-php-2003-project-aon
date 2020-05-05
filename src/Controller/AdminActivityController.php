<?php

namespace App\Controller;

use App\Model\ActivityManager;
use App\Model\EventManager;
use \FilesystemIterator;

class AdminActivityController extends AbstractController
{

    private function getAvailablePictures() : array
    {
        $availablePictures=array();
        $path="assets/activityImages/";
        $iterator = new FilesystemIterator($path);
        foreach ($iterator as $fileinfo) {
            $availablePictures[] = $fileinfo->getFilename();
        }

        return $availablePictures;
    }

    public function showActivity(int $id, string $message = "")
    {
        $message = urldecode($message);
        $activityManager = new ActivityManager();
        $activity = $activityManager->selectOneById($id);

        $availablePictures=$this->getAvailablePictures();

        return $this->twig->render(
            'Admin/showActivity.html.twig',
            ['data' => $activity,
                'message' => $message,
                'availablePictures' => $availablePictures
            ]
        );
    }

    public function editActivity(): string
    {
        $toBeReturned = "";
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errorsAndData = $this->checkActivityPostData();

            if (count($errorsAndData['data']) == 5) {
                $activityManager = new ActivityManager();
                $activityManager->updateActivity($errorsAndData['data']);
                header("location:/adminActivity/showActivity/" .
                    $errorsAndData['data']['id'] .
                    "/L'activité a bien été modifiée");
            } else {
                $availablePictures=$this->getAvailablePictures();

                $toBeReturned = $this->twig->render(
                    'Admin/showActivity.html.twig',
                    ['errors' => $errorsAndData['errors'],
                        'availablePictures' => $availablePictures,
                        'data' => $errorsAndData['data']]
                );
            }
        }
        return $toBeReturned;
    }

    private function checkActivityPostData() : array
    {
        //errors array
        $errors=[
            'name' => '',
            'description' => '',
            'picture' => '',
            'id' => ''];

        //data array
        $data=array();

        $this->checkTextFromPost('name', "le nom", 50, $errors, $data);

        $this->checkTextFromPost('description', "la description", 10000, $errors, $data);

        $this->checkTextFromPost('picture', "la photo", 250, $errors, $data);

        //check id
        if (empty($_POST['id'])) {
            $errors['id'] .= "ID ERROR";
        } elseif (!is_numeric(trim($_POST['id']))) {
            $errors['id'] .= "Format id incorrect";
        } elseif (intval(trim($_POST['id']))<1) {
            $errors['id'] .= "Id is negative";
        } else {
            $data['id']=intval(trim($_POST['id']));
        }

        //check toBeDisplayed
        if (empty($_POST['toBeDisplayed'])) {
            $data['to_be_displayed']=0;
        } else {
            $data['to_be_displayed']=1;
        }

        return ['errors' => $errors, 'data' => $data];
    }

    /**
     * Check if the provided fieldName exist in $_POST as String and match with the maximul length
     * @param string $postFieldName
     * @param string $userFieldName
     * @param int $maxLength
     * @return array array['erros'] contains the errors list, array['data'] contained date clean for use in database
     */
    public function checkTextFromPost(
        string $postFieldName,
        string $userFieldName,
        int $maxLength,
        &$errors,
        &$data
    ) : array {
        $datum="";
        $error="";

        if (empty($_POST[$postFieldName])) {
            $error = "Vous devez indiquer $userFieldName de l'évenement";
        } elseif (strlen(trim($_POST[$postFieldName]))>$maxLength) {
            $error = "Le nom de $userFieldName ne doit pas dépasser $maxLength caractères";
        } else {
            $datum =trim($_POST[$postFieldName]);
        }

        $errors[$postFieldName]=$error;
        if ($datum!="") {
            $data[$postFieldName] = $datum;
        }

        return ['error' => $error, 'data'=>$data];
    }
}
