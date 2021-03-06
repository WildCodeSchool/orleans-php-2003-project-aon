<?php


namespace App\Controller;

use App\Model\WhoAreUsManager;

class AdminWhoAreUsController extends AbstractController
{

    public function editWhoAreUs(): string
    {
        $toBeReturned = "";
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errorsAndData = $this->checkWhoAreUsPostData();
            $fileNameAndError=$this->upload();

            if ($fileNameAndError['fileName']!="") {
                $errorsAndData['data']['picture']=$fileNameAndError['fileName'];
                $errorsAndData['errors']['picture']=$fileNameAndError['error'];
            }
            if (count($errorsAndData['data']) == count($errorsAndData['errors'])) {
                $whoAreUsManager = new WhoAreUsManager();
                $whoAreUsManager->update($errorsAndData['data']);
                header("location:/AdminWhoAreUs/editWhoAreUs" .
                    "/Qui sommes nous a bien été modifiée");
            } else {
                $toBeReturned = $this->twig->render(
                    'WhoAreUs/formWhoAreUs.html.twig',
                    ['errors' => $errorsAndData['errors'],
                        'whoAreUs' => $errorsAndData['data'],
                        'message' => 'Les modifications n\'ont pas été enregistrées'
                    ]
                );
            }
        }

        $whoAreUsManager = new WhoAreUsManager();
        $whoAreUs = $whoAreUsManager->selectOneById(1);
        $toBeReturned = $this->twig->render('WhoAreUs/formWhoAreUs.html.twig', ['whoAreUs' => $whoAreUs]);



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
                    move_uploaded_file($fileTmpName, "assets/activityImages/".$processedFileName);
                }
            }
        }
        return ['fileName' => $processedFileName, 'error' => $errorMessage];
    }
    private function checkWhoAreUsPostData() : array
    {
        //errors array
        $errors=[
            'description' => '',
            'picture' => ''];
        //data array
        $data=array();
              $this->checkTextFromPost('description', "la description", 10000, $errors, $data);
              $this->checkTextFromPost('picture', "la photo", 250, $errors, $data);

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
            $error = "Vous devez indiquer $userFieldName";
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
