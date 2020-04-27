<?php
/**
 * Created by PhpStorm.
 * User: Adrien
 * Date: 27/04/2020
 */

namespace App\Model;

/**
 *
 */
class LessonManager extends AbstractManager
{
    /**
     *
     */
    const TABLE = 'lesson';

    /**
     *  Initializes this class.
     */
    public function __construct()
    {
        parent::__construct(self::TABLE);
    }

    public function selectEverthingForOneById(int $activityId)
    {
        // prepared request
        $statement = $this->pdo->prepare("SELECT * FROM $this->table RIGHT JOIN activity
            ON lesson.activity_id=activity.id WHERE activity.id=:id ");

        $statement->bindValue('id', $activityId, \PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }
}
