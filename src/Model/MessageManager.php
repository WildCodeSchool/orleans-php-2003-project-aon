<?php
/**
 * Created by PhpStorm.
 * User: sylvain
 * Date: 07/03/18
 * Time: 18:20
 * PHP version 7
 */

namespace App\Model;

/**
 *
 */
class MessageManager extends AbstractManager
{
    /**
     *
     */
    const TABLE = 'message';

    /**
     *  Initializes this class.
     */
    public function __construct()
    {
        parent::__construct(self::TABLE);
    }

    public function insert(array $message): int
    {
        // prepared request
        $statement = $this->pdo->prepare("INSERT INTO " . self::TABLE .
            " (`message`, `email`, `subject`, `name`) VALUES (:message, :email, :subject, :name)");
        $statement->bindValue('message', $message['message'], \PDO::PARAM_STR);
        $statement->bindValue('email', $message['email'], \PDO::PARAM_STR);
        $statement->bindValue('subject', $message['subjecte'], \PDO::PARAM_STR);
        $statement->bindValue('name', $message['name'], \PDO::PARAM_STR);
        if ($statement->execute()) {
            return (int)$this->pdo->lastInsertId();
        }
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
