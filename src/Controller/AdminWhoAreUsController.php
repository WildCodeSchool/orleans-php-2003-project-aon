<?php


namespace App\Controller;

use App\Model\WhoAreUsManager;


class AdminWhoAreUsController extends AbstractController
{

    /**
     * Display item edition page specified by $id
     *
     * @param int $id
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function edit(int $id): string
    {
        $whoAreUsManager = new WhoAreUsManager();
        $whoAreUs = $whoAreUsManager->selectOneById($id);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $whoAreUs['description'] = $_POST['description'];
            $whoAreUs['picture'] = $_POST['picture'];

            $whoAreUsManager->update($whoAreUs);
        }

        return $this->twig->render('WhoAreUs/edit.html.twig', ['whoAreUs' => $whoAreUs]);
    }


    public function showWhoAreUs(int $id)
    {
        $WhoAreUsManager = new WhoAreUsManager();
        $whoAreUs = $WhoAreUsManager->selectOneById($id);

        $availablePictures=$this->getAvailablePictures();

        return $this->twig->render(
            'Admin/_WhoAreUs.html.twig',
            ['data' => $whoAreUs,
                'availablePictures' => $availablePictures
            ]
        );
    }

    public function editWhoAreUs(): string
    {
        $toBeReturned = "";
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errorsAndData = $this->checkActivityPostData();

            $fileNameAndError=$this->upload();
            if ($fileNameAndError['fileName']!="") {
                $errorsAndData['data']['picture']=$fileNameAndError['fileName'];
                $errorsAndData['errors']['picture']=$fileNameAndError['error'];
            }

            if (count($errorsAndData['data']) == 2) {
                $WhoAreUsManager = new WhoAreUsManager();
                $WhoAreUsManager->updateActivity($errorsAndData['data']);
                header("location:/AdminWhoAreUs/edit/1" .
                    "/Qui sommes nous a bien été modifiée");
            } else {
                $availablePictures=$this->getAvailablePictures();

                $toBeReturned = $this->twig->render(
                    'Admin/edit.html.twig',
                    ['errors' => $errorsAndData['errors'],
                        'availablePictures' => $availablePictures,
                        'data' => $errorsAndData['data']]
                );
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
                    move_uploaded_file($fileTmpName, "assets/activityImages/".$processedFileName);
                }
            }
        }

        return ['fileName' => $processedFileName, 'error' => $errorMessage];
    }

    private function checkActivityPostData() : array
    {
        //errors array
        $errors=[
            'description' => '',
            'picture' => '',
            'id' => ''];

        //data array
        $data=array();

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
}
