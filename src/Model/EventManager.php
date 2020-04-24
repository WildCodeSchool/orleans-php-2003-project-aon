<?php
/**
 * Created by PhpStorm.
 * User: Emmanuelle
 * Date: 15/04/2020
 */

namespace App\Model;

/**
 *
 * This class only make a PDO object instanciation. Use it as below :
 *
 * <pre>
 *  $db = new Connection();
 *  $conn = $db->getPdoConnection();
 * </pre>
 */
class EventManager extends AbstractManager
{
    /**
     *
     */
    const TABLE = 'event';

    /**
     *  Initializes this class.
     */
    public function __construct()
    {
        parent::__construct(self::TABLE);
    }


    /**
     * @return mixed
     */
    public function selectNextEvent()
    {
        // prepared request
        $statement = $this->pdo->prepare("SELECT title, description, location, picture, date as ordered_date,
                DATE_FORMAT(date, '%d/%m/%Y') as date 
                FROM $this->table ORDER BY ordered_date DESC LIMIT 1");
        $statement->execute();
        return $statement->fetch();
    }
    
    
    public function selectAll(): array
    {
        return $this->pdo->query("SELECT id, title, description, location, picture, date as ordered_date,
                DATE_FORMAT(date, '%d/%m/%Y') as date 
                FROM " . $this->table . " ORDER BY ordered_date DESC")->fetchAll();
    }

    /**
     * @param int $id
     */
    public function delete(int $id): void
    {
        // prepared request
        $statement = $this->pdo->prepare("DELETE FROM " . self::TABLE . " WHERE id=:id");
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->execute();
    }

    /**
     * @param array $event
     * @return bool
     */
    public function insert(array $event):bool
    {
        $statement = $this->pdo->prepare("INSERT INTO " . self::TABLE .
            " (`title`, `description`, `picture`, `date`, `location`) 
            VALUES 
            (:title, :description, :picture, :date, :location)");

        $statement->bindValue('title', $event['title'], \PDO::PARAM_STR);
        $statement->bindValue('description', $event['description'], \PDO::PARAM_STR);
        $statement->bindValue('picture', $event['picture'], \PDO::PARAM_STR);
        $statement->bindValue('date', $event['date'], \PDO::PARAM_STR);
        $statement->bindValue('location', $event['location'], \PDO::PARAM_STR);
      
        return $statement->execute();
    }

    public function updateEvent(array $event): bool
    {
        $statement = $this->pdo->prepare("UPDATE " . self::TABLE . " SET `title` = :title, 
        `description` = :description, `picture` = :picture, `date` = :date, `location` = :location WHERE id=:id");
        $statement->bindValue('title', $event['title'], \PDO::PARAM_STR);
        $statement->bindValue('description', $event['description'], \PDO::PARAM_STR);
        $statement->bindValue('picture', $event['picture'], \PDO::PARAM_STR);
        $statement->bindValue('date', $event['date']);
        $statement->bindValue('location', $event['location'], \PDO::PARAM_STR);
        $statement->bindValue('id', $event['id'], \PDO::PARAM_INT);

        return $statement->execute();
    }
}
