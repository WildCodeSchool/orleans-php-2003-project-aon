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
}
