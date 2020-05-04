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

    public function selectEverthingForOneById(int $activityId, int $ageClassId = -1)
    {
        $ageQuery="";
        if ($ageClassId >= 0) {
            $ageQuery=" and age.id=$ageClassId";
        }

        // prepared request
        $statement = $this->pdo->prepare("SELECT
            lesson.price,
            lesson.day,
            lesson.time,
            pool.name AS pool_name,
            age.age,
            age.id AS age_id
            FROM $this->table
            RIGHT JOIN activity ON lesson.activity_id=activity.id 
            JOIN pool ON lesson.pool_id=pool.id 
            JOIN age ON lesson.age_id=age.id 
            WHERE activity.id=:id 
            ".$ageQuery);

        $statement->bindValue('id', $activityId, \PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }

    public function selectAgeClassesForOneById(int $activityId)
    {
        // prepared request
        $statement = $this->pdo->prepare("SELECT DISTINCT
            age.age,
            age.id
            FROM $this->table
            JOIN age ON lesson.age_id=age.id 
            WHERE activity_id=:id 
            ");//

        $statement->bindValue('id', $activityId, \PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }

    public function selectAllLessonsForAdmin(): array
    {
        $query = ('SELECT ac.name, a.age, p.pool_name, l.day, l.time, l.price FROM lesson as l 
                    INNER JOIN activity as ac ON ac.id=l.activity_id 
                    JOIN age as a ON a.id=l.age_id JOIN pool as p ON p.id=l.pool_id');

        return $this->pdo->query($query)->fetchAll();
    }
}
