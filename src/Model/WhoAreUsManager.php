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
     * @param array $whoAreUs
     * @return bool
     */
    public function update(array $whoAreUs):bool
    {

        // prepared request
        $statement = $this->pdo->prepare("UPDATE " . self::TABLE . " SET `description` = :description,
         `picture`= :picture WHERE id=1");
        $statement->bindValue('description', $whoAreUs['description'], \PDO::PARAM_STR);
        $statement->bindValue('picture', $whoAreUs['picture'], \PDO::PARAM_STR);


        return $statement->execute();
    }
}
