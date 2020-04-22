<?php
/**
 * Created by PhpStorm.
 * User: Emmanuelle
 * Date: 15/04/2020
 */

namespace App\Controller;

use App\Model\EventManager;

class AdminController extends AbstractController
{

    public function index()
    {
        return $this->twig->render('Admin/_eventDetails.html.twig');
    }

    /**
     * Display activity page
     *
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function addEvent()
    {
        $errorsAndData=$this->checkEventPostData();
        $toBeReturned="";
        if (count($errorsAndData['data'])==5) {
            $eventManager=new EventManager();
            $eventManager->insert($errorsAndData['data']);
            header("location:/admin/index");
        } else {
            $toBeReturned = $this->twig->render('Admin/_eventDetails.html.twig', ['errors'=>$errorsAndData['errors'],
                'data'=>$errorsAndData['data']]);
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
            'title' => '',
            'description' => '',
            'picture' => '',
            'date' => '',
            'location' => ''];

        //data array
        $data=array();

        $checked=$this->checkTextFromPost('title', "du titre", 50);
        $errors=array_merge($errors, $checked['errors']);
        $data=array_merge($data, $checked['data']);

        $checked=$this->checkTextFromPost('description', "de la description", 250);
        $errors=array_merge($errors, $checked['errors']);
        $data=array_merge($data, $checked['data']);

        $checked=$this->checkTextFromPost('picture', "de l'illustration", 250);
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

        $checked=$this->checkTextFromPost('location', "de l'endroit", 50);
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
            $errors[$postFieldName] .= "Vous devez indiquer le nom $userFieldName";
        } elseif (strlen(trim($_POST[$postFieldName]))>$maxLength) {
            $errors[$postFieldName] .= "Le nom de $userFieldName ne doit pas dépasser $maxLength caractères";
        } else {
            $data[$postFieldName]=trim($_POST[$postFieldName]);
        }

        return ['errors' => $errors, 'data'=>$data];
    }
}
