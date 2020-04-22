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

    public function selectAll(): array
    {
        return $this->pdo->query("SELECT * FROM " . $this->table . " ORDER BY date DESC")->fetchAll();
    }
}
