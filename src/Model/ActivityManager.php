<?php
/**
 * Created by PhpStorm.
 * User: Adrien
 * Date: 15/04/2020
 */

namespace App\Model;

/**
 *
 */
class ActivityManager extends AbstractManager
{
    /**
     *
     */
    const TABLE = 'activity';

    /**
     *  Initializes this class.
     */
    public function __construct()
    {
        parent::__construct(self::TABLE);
    }


    /**
     * Get all row from database.
     *
     * @return array
     */
    public function selectActivitiesToBeDisplayed(): array
    {
        return $this->pdo->query('SELECT * FROM ' . $this->table . ' WHERE to_be_displayed=1')->fetchAll();
    }

    public function selectAllActivitiesForAdmin(): array
    {
        return $this->pdo->query('SELECT ac.name, a.age, l.day, l.time FROM lesson as l 
        INNER JOIN activity as ac ON ac.id=l.activity_id JOIN age as a ON a.id=l.age_id')->fetchAll();
    }


    /**
     * @param array $event
     * @return bool
     */
    public function insert(array $event):bool
    {
        $statement = $this->pdo->prepare("INSERT INTO lesson 
            (`name`, `age`, `day`, `time`) 
            VALUES 
            (:name, :age, :day, :time)");

        $statement->bindValue('name', $event['name'], \PDO::PARAM_STR);
        $statement->bindValue('age', $event['age'], \PDO::PARAM_STR);
        $statement->bindValue('day', $event['day'], \PDO::PARAM_STR);
        $statement->bindValue('time', $event['time'], \PDO::PARAM_STR);

        return $statement->execute();
    }
}
