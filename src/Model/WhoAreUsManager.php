<?php

namespace App\Model;

class WhoAreUsManager extends AbstractManager
{
    const TABLE = 'whoAreUS';

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
        return $this->pdo->query('SELECT * FROM ' . $this->table)->fetch();
    }

    /**
     * @param array $item
     * @return bool
     */
    public function update(array $item):bool
    {

        // prepared request
        $statement = $this->pdo->prepare("UPDATE " . self::TABLE . " SET `description` = :description,
         `picture`= :picture WHERE id=:id");
        $statement->bindValue('id', $item['id'], \PDO::PARAM_INT);
        $statement->bindValue('description', $item['description'], \PDO::PARAM_STR);
        $statement->bindValue('picture', $item['picture'], \PDO::PARAM_STR);


        return $statement->execute();
    }
}
