<?php

namespace App\Controller;

use App\Model\ActivityManager;
use App\Model\EventManager;
use \FilesystemIterator;

class AdminActivityController extends AbstractController
{

    public function deleteActivity(int $id): void
    {
        $activityManager = new ActivityManager();
        $activityManager->delete($id);
        header('Location:/admin/index');
    }

    public function addActivity(string $message = "")
    {
        $message = urldecode($message);
        $toBeReturned="";

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errorsAndData=$this->checkActivityPostData();
            $data=$errorsAndData['data'];
            $errors=$errorsAndData['errors'];
            unset($errors['id']);

            $fileNameAndError=$this->upload();

            if (!empty($fileNameAndError['fileName'])) {
                $data['picture']=$fileNameAndError['fileName'];
                unset($errors['picture']);
            }

            if (!empty($fileNameAndError['error'])) {
                $errors['picture']=$fileNameAndError['error'];
            }

            if (empty($errors)) {
                    $activityManager=new ActivityManager();
                    $activityManager->insert($data);
                    header("location:/admin/index");
            } else {
                $toBeReturned = $this->twig->render('Admin/addActivity.html.twig', ['errors'=>$errors,
                    'data'=>$data,
                    'message'=>"L'activité n'a pas pu être créé."]);
            }
        } else {
            $toBeReturned = $this->twig->render('Admin/addActivity.html.twig', ['message' => $message]);
        }
        return $toBeReturned;
    }

    public function editActivity(int $id = 0, string $message = ""): string
    {
        $message = urldecode($message);

        $toBeReturned="";

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errorsAndData = $this->checkActivityPostData();
            $data=$errorsAndData['data'];
            $errors=$errorsAndData['errors'];

            $fileNameAndError=$this->upload();
            if (!empty($fileNameAndError['fileName'])) {
                $data['picture']=$fileNameAndError['fileName'];
                unset($errors['picture']);
            }

            if (!empty($fileNameAndError['error'])) {
                $errors['picture']=$fileNameAndError['error'];
            }

            if (empty($errors)) {
                $activityManager = new ActivityManager();
                $activityManager->updateActivity($data);
                header("location:/adminActivity/editActivity/" .
                    $data['id'] .
                    "/L'activité a bien été modifiée");
            } else {
                $toBeReturned = $this->twig->render(
                    'Admin/editActivity.html.twig',
                    ['errors' => $errors,
                     'message' => $message,
                     'data' => $data]
                );
            }
        } else {
            $activityManager = new ActivityManager();
            $activity = $activityManager->selectOneById($id);

            $toBeReturned = $this->twig->render(
                'Admin/editActivity.html.twig',
                ['data' => $activity,
                 'message' => $message]
            );
        }
        return $toBeReturned;
    }

    /**
     * try to upload a file
     * @return array[fileName=>uploadedFileNameOnServerIfExist, error=>errorDescriptionIfAny]
     */
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
                    move_uploaded_file($fileTmpName, "assets/activityImages/".$processedFileName);
                }
            }
        }

        return ['fileName' => $processedFileName, 'error' => $errorMessage];
    }

    /**
     * check that every needed data is in the $_POST and without issue
     * @return array[data[dataName=>dataValue],errors[dataName=>errorValue]
     */
    private function checkActivityPostData() : array
    {
        //data array
        $data=array();

        //errors array
        $errors=array();

        //check name
        $this->checkTextFromPost('name', "le nom", 50, $errors, $data);

        //check description
        $this->checkTextFromPost('description', "la description", 10000, $errors, $data);

        //check picture
        $this->checkTextFromPost('picture', "la photo", 250, $errors, $data);

        //check id
        if (empty($_POST['id'])) {
            $errors['id'] = "ID ERROR";
        } elseif (!is_numeric(trim($_POST['id']))) {
            $errors['id'] = "Format id incorrect";
        } elseif (intval(trim($_POST['id']))<1) {
            $errors['id'] = "Id is negative";
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

        if (empty($_POST[$postFieldName])) {
            $error = "Vous devez indiquer $userFieldName de l'activité";
        } elseif (strlen(trim($_POST[$postFieldName]))>$maxLength) {
            $error = "Le nom de $userFieldName ne doit pas dépasser $maxLength caractères";
        } else {
            $datum =trim($_POST[$postFieldName]);
        }

        if (!empty($error)) {
            $errors[$postFieldName]=$error;
        }
        if (!empty($datum)) {
            $data[$postFieldName] = $datum;
        }

        return ['errors' => $errors, 'data'=>$data];
    }
}
