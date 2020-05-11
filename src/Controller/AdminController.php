<?php

namespace App\Controller;

use App\Model\LessonManager;
use App\Model\ActivityManager;
use App\Model\EventManager;
use App\Model\WhoAreUsManager;
use App\Model\MessageManager;
use \FilesystemIterator;

class AdminController extends AbstractController
{


    /**
     * Display activity page
     *
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function index()
    {
        $activityManager = new ActivityManager();
        $activities = $activityManager->getActivityList();

        $adminEvent = new EventManager();
        $event = $adminEvent->selectAll();

        $lessonManager = new LessonManager();
        $lessons = $lessonManager->selectAllLessonsForAdmin();

        $whoAreUsManager = new WhoAreUsManager();
        $whoAreUs = $whoAreUsManager->selectAll();
        $messageManager = new MessageManager();
        $messages = $messageManager->selectAll();

        return $this->twig->render(
            'Admin/index.html.twig',
            ['event' => $event,
                'lessons' => $lessons,
                'activities' => $activities,
                'whoAreUs' => $whoAreUs,
                'messagesBox' => $messages,]
        );
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
        return $this->twig->render('Admin/showEvent.html.twig', ['data' => $event, 'message' => $message]);
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

            if (count($data)==5 && empty($data['id'])) {
                $eventManager=new EventManager();
                $eventManager->insert($data);
                header("location:/admin/index");
            } else {
                $toBeReturned = $this->twig->render('Admin/addEvent.html.twig', ['errors'=>$errors,
                    'data'=>$data,
                    'message'=>"L'évènement n'a pas pu être créé."]);
            }
        }
        return $toBeReturned;
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

            if (count($errorsAndData['data']) == 6) {
                $eventManager = new EventManager();
                $eventManager->updateEvent($errorsAndData['data']);
                header("location:/admin/showEvent/" . $errorsAndData['data']['id'] . "/L'évènement a bien été modifié");
            } else {
                $toBeReturned = $this->twig->render(
                    'Admin/showEvent.html.twig',
                    ['errors' => $errorsAndData['errors'],
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
