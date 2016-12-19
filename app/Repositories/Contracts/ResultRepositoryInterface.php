<?php

namespace App\Repositories\Contracts;

interface ResultRepositoryInterface extends RepositoryInterface
{
    /**
     * Join a room
     *
     * @param int $quantity
     *
     * @return mixed
     */
    public function topWords($quantity);
}
