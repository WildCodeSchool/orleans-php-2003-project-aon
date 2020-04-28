<?php

namespace App\Controller;

use App\Model\ActivityManager;
use App\Model\PoolManager;

class AdminActivityController extends AbstractController
{

    public function createActivity(string $message = "")
    {
        $message = urldecode($message);
        return $this->twig->render('Admin/addActivity.html.twig', ['message' => $message]);
    }

    public function addActivity()
    {   $poolManager = new PoolManager();

        $toBeReturned="";
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errorsAndData=$this->checkEventPostData();
            $data=$errorsAndData['data'];
            $errors=$errorsAndData['errors'];

            if (count($data)==5 && empty($data['id'])) {
                $activityManager=new ActivityManager();
                $activityManager->insert($data);
                header("location:/admin/index");
        } else {
            $toBeReturned = $this->twig->render('Admin/addActivity.html.twig', ['errors'=>$errors,
                'data'=>$data,
                'message'=>"L'activité n'a pas pu être créée."]);
            }
        }
        return $toBeReturned;
    }

    /**
     * This method analyse the $_POST to check that every needed data needed for inserting or adding event is here
     * it check there are not empty, have the right size and the right type
     * @return array : array[] contains the data to be inserted in a clean form, array[1] contains error messages
     */
    private function checkEventPostData() : array
    {
        //errors array
        $errors=[
            'activity-name'=> '',
            'picture' => '',
            'age' => '',
            'price' => '',
            'day' => '',
            'time' => '',
            'pool' => '',
            'id' => '',
            'description' => '',
            'display' => '',
            ];



        //data array
        $data=array();

        $checked=$this->checkTextFromPost('title', "le titre", 50);
        $errors=array_merge($errors, $checked['errors']);
        $data=array_merge($data, $checked['data']);

        $checked=$this->checkTextFromPost('description', "la description", 250);
        $errors=array_merge($errors, $checked['errors']);
        $data=array_merge($data, $checked['data']);

        $checked=$this->checkTextFromPost('picture', "la photo", 250);
        $errors=array_merge($errors, $checked['errors']);
        $data=array_merge($data, $checked['data']);

        //check date
        if (empty($_POST['date'])) {
            $errors['date'] .= "Vous devez indiquer la date de l'évenement";
        } elseif (!preg_match("/([12]\d{3}-(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01]))/", trim($_POST['date']))) {
            $errors['date'] .= "La date doit avoir le format aaaa-mm-jj";
        } else {
            $data['date']=trim($_POST['date']);
        }

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

        $checked=$this->checkTextFromPost('location', "l'endroit", 50);
        $errors=array_merge($errors, $checked['errors']);
        $data=array_merge($data, $checked['data']);

        return ['errors' => $errors, 'data' => $data];
    }

    /**
     * Check if the provided fieldName exist in $_POST as String and match with the maximul length
     * @param string $postFieldName
     * @param string $userFieldName
     * @param int $maxLength
     * @return array array['erros'] contains the errors list, array['data'] contained date clean for use in database
     */
    public function checkTextFromPost(string $postFieldName, string $userFieldName, int $maxLength) : array
    {
        $data=array();
        $errors=array();
        $errors[$postFieldName]='';

        if (empty($_POST[$postFieldName])) {
            $errors[$postFieldName] .= "Vous devez indiquer $userFieldName de l'évenement";
        } elseif (strlen(trim($_POST[$postFieldName]))>$maxLength) {
            $errors[$postFieldName] .= "Le nom de $userFieldName ne doit pas dépasser $maxLength caractères";
        } else {
            $data[$postFieldName]=trim($_POST[$postFieldName]);
        }

        return ['errors' => $errors, 'data'=>$data];
    }
}
