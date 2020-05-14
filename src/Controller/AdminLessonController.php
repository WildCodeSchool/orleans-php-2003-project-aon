<?php

namespace App\Controller;

use App\Model\AbstractManager;
use App\Model\ActivityManager;
use App\Model\AgeManager;
use App\Model\LessonManager;
use App\Model\PoolManager;

class AdminLessonController extends AbstractController
{

    public function createLesson(string $message = "")
    {
        $message = urldecode($message);
        return $this->twig->render('Admin/addLesson.html.twig', ['message' => $message]);
    }

    public function addLesson()
    {
        $activityManager = new ActivityManager();
        $ageManager = new AgeManager();
        $poolManager = new PoolManager();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $lesson = array_map('trim', $_POST);

            if (!empty($_POST['create_pool']) && !empty($_POST['new_pool'])) {
                $_POST['pool'] = $poolManager->insert(['new_pool' => $_POST['new_pool']]);
                $lesson['pool_name'] = $_POST['new_pool'];
            } else {
                $errors = $this->validation($lesson);

                if (empty($errors)) {
                    $lessonManager = new LessonManager();
                    $lessonManager->insert($lesson);
                    header("location:/admin/index");
                }
            }
        }

        $activities = $activityManager->selectAll();
        $ages = $ageManager->selectAll();
        $pools = $poolManager->selectAll();

        return $this->twig->render('Admin/addLesson.html.twig', [
            'errors' => $errors ?? [],
            'lesson' => $lesson ?? [],
            'activities' => $activities ?? [],
            'ages' => $ages ?? [],
            'pools' => $pools ?? [],
        ]);
    }


    private function idExist(int $id, AbstractManager $manager, string $name, string $key): array
    {
        $ids = $manager->selectAll();
        $idList = array_column($ids, 'id');
        $errors = [];
        if (!in_array($id, $idList)) {
            $errors[$key] = "Cette $name n'existe pas";
            return $errors;
        }
        return [];
    }

    private function validation(array $lesson): array
    {
        $activityManager = new ActivityManager();
        $ageManager = new AgeManager();
        $poolManager = new PoolManager();
        $errors = [];

        $errors = array_merge($errors, $this->idExist($_POST['age'], $ageManager, 'classe d\'âge', 'age'));

        $errors = array_merge($errors, $this->idExist($_POST['activity'], $activityManager, 'activité', 'activity'));
        $errors = array_merge($errors, $this->idExist($_POST['pool'], $poolManager, 'piscine', 'pool'));

        if (empty($lesson['day'])) {
            $errors['day'] = 'Le jour doit être indiqué';
        } elseif (!in_array($lesson['day'], ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'])) {
            $errors['day'] = 'Le jour doit être dans la liste';
        }
        if (empty($lesson['time'])) {
            $errors['time'] = 'L\'heure doit être indiquée';
        } elseif (!preg_match(
            '^([0-1]?[0-9]|2[0-3])h[0-5][0-9]-([0-1]?[0-9]|2[0-3])h[0-5][0-9]^',
            $lesson['time']
        )) {
            $errors['time'] = 'Format de l\'heure 20h00-21h30';
        }
        if (empty($lesson['price'])) {
            $errors['price'] = 'Le prix doit être indiqué';
        } elseif (!is_numeric($lesson['price'])) {
            $errors['price'] = 'Le prix doit être indiqué en chiffres';
        } elseif ($lesson['price'] < 0) {
            $errors['price'] = 'Le prix doit être supérieur à 0';
        }

        return $errors ?? [];
    }

    /**
     * Handle item deletion
     */
    public function delete(): void
    {
        if (!empty($_POST['id'])) {
            $id = $_POST['id'];
            $lessonManager = new LessonManager();
            $lessonManager->delete($id);
            header('Location:/Admin/index');
        }
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

    public function editLesson(int $id = null)
    {

        $lessonManager = new LessonManager();
        $poolManager = new PoolManager();
        $ageManager = new AgeManager();
        $activityManager = new ActivityManager();
        $lesson = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $lesson = array_map('trim', $_POST);

            if (!empty($_POST['create_pool']) && !empty($_POST['new_pool'])) {
                $_POST['pool'] = $poolManager->insert(['new_pool' => $_POST['new_pool']]);
                $lesson['pool_name'] = $_POST['new_pool'];
            } else {
                $errors = $this->validation($lesson);

                if (empty($errors)) {
                    $lessonManager->editLesson($lesson);
                    header("location:/admin/index");
                }
            }
        }

        $activities = $activityManager->selectAll();
        $ages = $ageManager->selectAll();
        $pools = $poolManager->selectAll();
        if (!empty($id)) {
            $lesson = $lessonManager->selectOneById($id);
        }

        return $this->twig->render('Admin/editLesson.html.twig', [
            'errors' => $errors ?? [],
            'lesson' => $lesson ?? [],
            'activities' => $activities ?? [],
            'ages' => $ages ?? [],
            'pools' => $pools ?? [],
        ]);
    }
}
