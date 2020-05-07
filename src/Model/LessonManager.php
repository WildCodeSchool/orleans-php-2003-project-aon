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

    public function selectEverythingForOneById(int $activityId, int $ageClassId = -1)
    {
        $ageQuery = "";
        if ($ageClassId >= 0) {
            $ageQuery = " and age.id=$ageClassId";
        }

        // prepared request
        $statement = $this->pdo->prepare("SELECT
            lesson.price,
            lesson.day,
            lesson.time,
            pool.pool_name AS pool_name,
            age.age,
            age.id AS age_id
            FROM $this->table
            RIGHT JOIN activity ON lesson.activity_id=activity.id 
            JOIN pool ON lesson.pool_id=pool.id 
            JOIN age ON lesson.age_id=age.id 
            WHERE activity.id=:id 
            " . $ageQuery);

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

    /**
     * @param array $lesson
     * @return bool
     */
    public function insert(array $lesson): bool
    {
        $query = 'INSERT INTO ' . self::TABLE . '(`activity_id`, `age_id`, `pool_id`,`day`, `time`,`price`) 
                    VALUES (:activity, :age, :pool, :day, :time, :price)';
        $statement = $this->pdo->prepare($query);
        $statement->bindValue('activity', $lesson['activity'], \PDO::PARAM_STR);
        $statement->bindValue('age', $lesson['age'], \PDO::PARAM_STR);
        $statement->bindValue('pool', $lesson['pool'], \PDO::PARAM_STR);
        $statement->bindValue('day', $lesson['day'], \PDO::PARAM_STR);
        $statement->bindValue('time', $lesson['time'], \PDO::PARAM_STR);
        $statement->bindValue('price', $lesson['price']);

        return $statement->execute();
    }

    public function selectAllLessonsForAdmin(): array
    {
        $query = ('SELECT l.id, ac.name, a.age, p.pool_name, l.day, l.time, l.price FROM lesson as l 
                    INNER JOIN activity as ac ON ac.id=l.activity_id 
                    JOIN age as a ON a.id=l.age_id JOIN pool as p ON p.id=l.pool_id');

        return $this->pdo->query($query)->fetchAll();
    }

    public function editLesson(array $lesson):bool
    {
        $query =  "UPDATE " . self::TABLE . " SET `activity_id` = :activity, 
                   `age_id` = :age, `pool_id` = :pool, `day` = :day, `time` = :time, `price`= :price WHERE id=:id";
  
        $statement = $this->pdo->prepare($query);
        $statement->bindValue('activity', $lesson['activity'], \PDO::PARAM_STR);
        $statement->bindValue('age', $lesson['age'], \PDO::PARAM_STR);
        $statement->bindValue('pool', $lesson['pool'], \PDO::PARAM_STR);
        $statement->bindValue('day', $lesson['day'], \PDO::PARAM_STR);
        $statement->bindValue('time', $lesson['time'], \PDO::PARAM_STR);
        $statement->bindValue('price', $lesson['price']);
        $statement->bindValue('id', $lesson['id']);

        return $statement->execute();
    }
  
    public function delete(int $id): void
    {
        // prepared request
        $statement = $this->pdo->prepare("DELETE FROM " . self::TABLE . " WHERE id=:id");
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->execute();
    }

    public function selectAllPrices(): array
    {
        $query = ('SELECT l.price, a.age, ac.name FROM lesson AS l JOIN age AS a ON a.id=l.age_id 
                    JOIN activity AS ac ON ac.id=l.activity_id ORDER BY ac.name ASC, a.age ASC');

        return $this->pdo->query($query)->fetchAll();
    }
  
    public function selectOneById(int $id)
    {
        $statement = $this->pdo->prepare("SELECT l.id, ac.name, a.age, p.pool_name, l.day, l.time, l.price 
                                                    FROM lesson as l JOIN activity as ac ON ac.id=l.activity_id
                                                    JOIN age as a ON a.id=l.age_id JOIN pool as p ON p.id=l.pool_id
                                                    WHERE l.id=:id");
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetch();
    }
}
