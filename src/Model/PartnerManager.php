<?php
/**
 * Created by PhpStorm.
 * User: Mao
 * Date: 15/04/2020
 */

namespace App\Model;

/**
 *
 */
class PartnerManager extends AbstractManager
{
    /**
     *
     */
    const TABLE = 'partner';

    /**
     *  Initializes this class.
     */
    public function __construct()
    {
        parent::__construct(self::TABLE);
    }

    public function selectAll(): array
    {
        return $this->pdo->query('SELECT * FROM ' . $this->table)->fetchAll();
    }
}
