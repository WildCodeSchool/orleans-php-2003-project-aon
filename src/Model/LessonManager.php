<?php

namespace App\Model;

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

    /**
     * Get all row from database.
     *
     * @return array
     */
    public function selectAll(): array
    {
        return $this->pdo->query('SELECT * FROM ' . $this->table)->fetchAll();
    }

    /**
     * @param array $lesson
     * @return bool
     */
    public function insert(array $lesson):bool
    {
        $statement = $this->pdo->prepare("INSERT INTO " . self::TABLE .
            " (`time`, `day`, `price`) 
            VALUES 
            (:time, :day, :price)");

        $statement->bindValue('time', $lesson['time'], \PDO::PARAM_STR);
        $statement->bindValue('day', $lesson['day'], \PDO::PARAM_STR);
        $statement->bindValue('price', $lesson['price'], \PDO::PARAM_STR);

        return $statement->execute();
    }
}
