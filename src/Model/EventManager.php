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
     * @param int $id
     */
    public function delete(int $id): void
    {
        // prepared request
        $statement = $this->pdo->prepare("DELETE FROM " . self::TABLE . " WHERE id=:id");
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->execute();
    }

}
