<?php

namespace App\Model;

class PoolManager extends AbstractManager
{
    /**
     *
     */
    const TABLE = 'pool';

    /**
     *  Initializes this class.
     */
    public function __construct()
    {
        parent::__construct(self::TABLE);
    }

    /**
     * @param array $pool
     * @return bool
     */
    public function insert(array $pool):int
    {
        $statement = $this->pdo->prepare("INSERT INTO " . self::TABLE .
            " (`pool_name`) 
            VALUES 
            (:new_pool)");

        $statement->bindValue('new_pool', $pool['new_pool'], \PDO::PARAM_STR);

        if ($statement->execute()) {
            return (int)$this->pdo->lastInsertId();
        }
    }
}
