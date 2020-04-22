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
        $statement = $this->pdo->prepare("SELECT * FROM $this->table ORDER BY date DESC LIMIT 1");
        $statement->execute();
        return $statement->fetch();
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
}
