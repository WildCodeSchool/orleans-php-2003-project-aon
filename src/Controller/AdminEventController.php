<?php

namespace App\Controller;

use App\Model\ActivityManager;
use App\Model\EventManager;
use \FilesystemIterator;

class AdminEventController extends AbstractController
{
    /**
     * Handle item deletion
     */
    public function delete(): void
    {
        if (!empty($_POST['id'])) {
            $id = $_POST['id'];
            $eventManager = new EventManager();
            $eventManager->delete($id);
            header('Location:/Admin/index');
        }
    }


    public function createEvent(string $message = "")
    {
        $message = urldecode($message);
        return $this->twig->render('Admin/addEvent.html.twig', ['message' => $message]);
    }

    /**
     * Display event edition page specified by $id
     *
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */

    public function addEvent(string $message = "")
    {
        $message = urldecode($message);
        $toBeReturned = "";

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errorsAndData = $this->checkEventPostData();
            $data = $errorsAndData['data'];
            $errors = $errorsAndData['errors'];
            unset($errors['id']);

            $fileNameAndError = $this->upload();

            if (!empty($fileNameAndError['fileName'])) {
                $data['picture']=$fileNameAndError['fileName'];
                unset($errors['picture']);
            }

            if (!empty($fileNameAndError['error'])) {
                $errors['picture']=$fileNameAndError['error'];
            }

            if (empty($errors)) {
                $eventManager=new EventManager();
                $eventManager->insert($data);
                header("location:/admin/index");
            } else {
                $toBeReturned = $this->twig->render('Admin/addEvent.html.twig', ['errors' => $errors,
                    'data' => $data,
                    'message' => "L'évènement n'a pas pu être créé."]);
            }
        } else {
            $toBeReturned = $this->twig->render('Admin/addEvent.html.twig', ['message' => $message]);
        }
        return $toBeReturned;
    }

    private function upload(): array
    {
        $maxFileSize = 1048576;
        $acceptedTypes = ["image/jpeg", "image/svg+xml", "image/jpg", "image/gif", "image/png"];
        $processedFileName = "";
        $errorMessage = "";

        if (!empty($_FILES['picture'])) {
            $processedFileName = $_FILES['picture']['name'];
            $fileTmpName = $_FILES['picture']['tmp_name'];
            $fileType = $_FILES['picture']['type'];
            $fileSize = $_FILES['picture']['size'];
            $fileError = $_FILES['picture']['error'];

            if (0 == $fileError) {
                if ($fileSize > $maxFileSize) {
                    $errorMessage = "Le fichier $processedFileName dépasse la taille maximale de $maxFileSize";
                } elseif (!in_array($fileType, $acceptedTypes)) {
                    $errorMessage = "Le type du fichier $fileType n'est pas 
                    dans la liste :" . implode(",", $acceptedTypes);
                } else {
                    $extension = pathinfo($processedFileName, PATHINFO_EXTENSION);
                    $processedFileName = uniqid() . '.' . $extension;
                    move_uploaded_file($fileTmpName, "assets/eventImages/" . $processedFileName);
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
    public function editEvent(int $id = -1, string $message = ""): string
    {
        $message = urldecode($message);

        $toBeReturned = "";

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errorsAndData = $this->checkEventPostData();
            $data=$errorsAndData['data'];
            $errors=$errorsAndData['errors'];

            $fileNameAndError = $this->upload();
            if (!empty($fileNameAndError['fileName'])) {
                $data['picture']=$fileNameAndError['fileName'];
                unset($errors['picture']);
            }

            if (!empty($fileNameAndError['error'])) {
                $errors['picture']=$fileNameAndError['error'];
            }

            if (empty($errors)) {
                $eventManager = new EventManager();
                $eventManager->updateEvent($data);
                header("location:/adminEvent/editEvent/" . $data['id'] .
                    "/L'évènement a bien été modifié");
            } else {
                $toBeReturned = $this->twig->render(
                    'Admin/showEvent.html.twig',
                    ['errors' => $errors,
                        'data' => $data,
                        'message' => $message,]
                );
            }
        } else {
            if ($id > 0) {
                $eventManager = new EventManager();
                $event = $eventManager->selectOneById($id);

                $toBeReturned = $this->twig->render(
                    'Admin/showEvent.html.twig',
                    [   'data' => $event,
                        'message' => $message,]
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
    private function checkEventPostData(): array
    {
        //errors array
        $errors = [];

        //data array
        $data = [];

        $this->checkTextFromPost('title', "le titre", 50, $errors, $data);

        $this->checkTextFromPost('description', "la description", 10000, $errors, $data);

        $this->checkTextFromPost('picture', "la photo", 250, $errors, $data);

        //check date
        if (empty($_POST['date'])) {
            $errors['date'] = "Vous devez indiquer la date de l'évènement";
        } elseif (!preg_match("/([12]\d{3}-(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01]))/", trim($_POST['date']))) {
            $errors['date'] = "La date doit avoir le format aaaa-mm-jj";
        } else {
            $data['date'] = trim($_POST['date']);
        }

        //check id
        if (empty($_POST['id'])) {
            $errors['id'] = "ID ERROR";
        } elseif (!is_numeric(trim($_POST['id']))) {
            $errors['id'] = "Format id incorrect";
        } elseif (intval(trim($_POST['id'])) < 1) {
            $errors['id'] = "Id is negative";
        } else {
            $data['id'] = intval(trim($_POST['id']));
        }

        //check url
        if ((!empty($_POST['link']))) {
            if (filter_var(trim($_POST['link']), FILTER_VALIDATE_URL)) {
                $data['link'] = trim($_POST['link'] ?? '');
            } else {
                $errors['link'] = "Le lien doit avoir le format suivant : www.my-event.com";
            }
        } else {
            $data['link'] = "";
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

        if (empty($_POST[$postFieldName])) {
            $error = "Vous devez indiquer $userFieldName de l'activité";
        } elseif (strlen(trim($_POST[$postFieldName]))>$maxLength) {
            $error = "Le nom de $userFieldName doit être compris entre 1 et $maxLength caractères";
        } elseif (strlen(trim($_POST[$postFieldName])) <1) {
             $error = "Le nom de $userFieldName doit être compris entre 1 et $maxLength caractères";
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
