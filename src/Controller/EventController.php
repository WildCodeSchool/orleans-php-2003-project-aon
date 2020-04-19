<?php
/**
 * Created by PhpStorm.
 * User: Emmanuelle
 * Date: 15/04/2020
 */

namespace App\Controller;

use App\Model\EventManager;

class EventController extends AbstractController
{

    /**
     * Display activity page
     *
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function add()
    {
        $errorsAndData=$this->checkPostData();

        return $this->twig->render('Event/_eventDetails.html.twig', ['errors'=>$errorsAndData[0],
                                                                            'data'=>$errorsAndData[1]]);
    }

    /**
     * This method analyse the $_POST to check that every needed data needed for inserting or adding event is here
     * it check there are not empty, have the right size and the right type
     * @return array : array[] contains the data to be inserted in a clean form, array[1] contains error messages
     */
    private function checkPostData() : array
    {
        //errors array
        $errors=[
            'title' => '',
            'description' => '',
            'picture' => '',
            'date' => '',
            'location' => ''];

        //data array
        $data=array();

        $errorsAndData=['errors' => $errors, 'data' => $data];

        $checked=$this->checkTextFromPost('title', "le titre", 50);
        $errors=array_merge($errors, $checked[0]);
        $data=array_merge($data, $checked[1]);

        $checked=$this->checkTextFromPost('description', "la description", 250);
        $errors=array_merge($errors, $checked[0]);
        $data=array_merge($data, $checked[1]);

        $checked=$this->checkTextFromPost('picture', "l'illustration", 250);
        $errors=array_merge($errors, $checked[0]);
        $data=array_merge($data, $checked[1]);

        //check date
        if (empty($_POST['date'])) {
            $errors['date'] .= "Vous devez indiquer la date de l'évenement";
        } elseif (!preg_match("/([12]\d{3}-(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01]))/", trim($_POST['date']))) {
            $errors['date'] .= "La date doit avoir le format aaaa-mm-jj";
        } else {
            $data['date']=trim($_POST['date']);
        }

        $checked=$this->checkTextFromPost('location', "l'endroit", 50);
        $errors=array_merge($errors, $checked[0]);
        $data=array_merge($data, $checked[1]);

        return $errorsAndData;
    }

    public function checkTextFromPost($fieldName, $userFieldName, $maxLength) : array
    {
        $data=array();
        $errors=array();
        $errors[$fieldName]='';

        if (empty($_POST[$fieldName])) {
            $errors[$fieldName] .= "Vous devez indiquer le nom de $userFieldName";
        } elseif (strlen(trim($_POST[$fieldName]))>$maxLength) {
            $errors[$fieldName] .= "Le nom de $userFieldName ne doit pas dépasser $maxLength caractères";
        } else {
            $data[$fieldName]=trim($_POST['location']);
        }

        return ['errors' => $errors, 'data'=>$data];
    }
}
