<?php

namespace App\Model;

class AgeManager extends AbstractManager
{
    /**
     *
     */
    const TABLE = 'age';

    /**
     *  Initializes this class.
     */
    public function __construct()
    {
        parent::__construct(self::TABLE);
    }
}
