<?php

namespace App\Controller;

use App\Model\ActivityManager;
use App\Model\EventManager;
use \FilesystemIterator;

class AdminEventController extends AbstractController
{
    private function getAvailablePictures() : array
    {
        $availablePictures=array();
        $path="assets/images/";
        $iterator = new FilesystemIterator($path);
        foreach ($iterator as $fileInfo) {
            $availablePictures[] = $fileInfo->getFilename();
        }

        return $availablePictures;
    }

    /**
     * Handle item deletion
     *
     * @param int $id
     */
    public function delete(int $id): void
    {
        $eventManager = new EventManager();
        $eventManager->delete($id);
        header('Location:/Admin/index');
    }


    public function createEvent(string $message = "")
    {
        $message = urldecode($message);
        return $this->twig->render('Admin/addEvent.html.twig', ['message' => $message]);
    }

    /**
     * Display event informations specified by $id
     *
     * @param int $id
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function showEvent(int $id, string $message = "")
    {
        $message = urldecode($message);
        $eventManager = new EventManager();
        $event = $eventManager->selectOneById($id);

        $availablePictures=$this->getAvailablePictures();

        return $this->twig->render('Admin/showEvent.html.twig', [
            'data' => $event,
            'message' => $message,
            'availablePictures' => $availablePictures ]);
    }
    /**
     * Display event edition page specified by $id
     *
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */

    public function addEvent()
    {
        $toBeReturned="";
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errorsAndData=$this->checkEventPostData();
            $data=$errorsAndData['data'];
            $errors=$errorsAndData['errors'];

            $fileNameAndError=$this->upload();
            if ($fileNameAndError['fileName']!="") {
                $data['picture']=$fileNameAndError['fileName'];
                $errors['picture']=$fileNameAndError['error'];
            }

            if (count($data)==5 && empty($data['id'])) {
                $eventManager=new EventManager();
                $eventManager->insert($data);
                header("location:/admin/index");
            } else {
                $availablePictures=$this->getAvailablePictures();

                $toBeReturned = $this->twig->render('Admin/addEvent.html.twig', ['errors'=>$errors,
                    'data'=>$data,
                    'availablePictures' => $availablePictures,
                    'message'=>"L'évènement n'a pas pu être créé."]);
            }
        }
        return $toBeReturned;
    }

    private function upload() : array
    {
        $maxFileSize=1048576;
        $acceptedTypes=["image/jpeg", "image/svg+xml", "image/jpg", "image/gif", "image/png"];
        $processedFileName="";
        $errorMessage="";

        if (!empty($_FILES['picture'])) {
            $processedFileName=$_FILES['picture']['name'];
            $fileTmpName=$_FILES['picture']['tmp_name'];
            $fileType=$_FILES['picture']['type'];
            $fileSize=$_FILES['picture']['size'];
            $fileError=$_FILES['picture']['error'];

            if (0==$fileError) {
                if ($fileSize>$maxFileSize) {
                    $errorMessage="Le fichier $processedFileName dépasse la taille maximale de $maxFileSize";
                } elseif (!in_array($fileType, $acceptedTypes)) {
                    $errorMessage="Le type du fichier $fileType n'est pas 
                    dans la liste :".implode(",", $acceptedTypes) ;
                } else {
                    $extension = pathinfo($processedFileName, PATHINFO_EXTENSION);
                    $processedFileName = uniqid() . '.' .$extension;
                    move_uploaded_file($fileTmpName, "assets/images/".$processedFileName);
                }
            }
        }

        return ['fileName' => $processedFileName, 'error' => $errorMessage];
    }


    /**
     * Display event edition page specified by $id
     *
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function editEvent(): string
    {
        $toBeReturned = "";
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errorsAndData = $this->checkEventPostData();

            $fileNameAndError=$this->upload();
            if ($fileNameAndError['fileName']!="") {
                $errorsAndData['data']['picture']=$fileNameAndError['fileName'];
                $errorsAndData['errors']['picture']=$fileNameAndError['error'];
            }

            if (count($errorsAndData['data']) == 6) {
                $eventManager = new EventManager();
                $eventManager->updateEvent($errorsAndData['data']);
                header("location:/adminEvent/showEvent/" . $errorsAndData['data']['id'] . "/L'évènement a bien été modifié");
            } else {
                $availablePictures=$this->getAvailablePictures();

                $toBeReturned = $this->twig->render(
                    'Admin/showEvent.html.twig',
                    ['errors' => $errorsAndData['errors'],
                        'availablePictures' => $availablePictures,
                        'data' => $errorsAndData['data']]
                );
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
            'title' => '',
            'description' => '',
            'picture' => '',
            'date' => '',
            'location' => '',
            'id' => ''];

        //data array
        $data=array();

        $this->checkTextFromPost('title', "le titre", 50, $errors, $data);

        $this->checkTextFromPost('description', "la description", 10000, $errors, $data);

        $this->checkTextFromPost('picture', "la photo", 250, $errors, $data);

        //check date
        if (empty($_POST['date'])) {
            $errors['date'] .= "Vous devez indiquer la date de l'évènement";
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

        $this->checkTextFromPost('location', "l'endroit", 50, $errors, $data);

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
            $error = "Vous devez indiquer $userFieldName de l'évènement";
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