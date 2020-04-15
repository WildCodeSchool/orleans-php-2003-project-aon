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
class ActivityManager extends AbstractManager
{
    /**
     *
     */
    const TABLE = 'activity';

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
    public function selectActivitiesToBeDisplayed(): array
    {
        return $this->pdo->query('SELECT * FROM ' . $this->table . ' WHERE to_be_displayed=1')->fetchAll();
    }
}
