<?php  

namespace App\Repositories\Contracts;

interface RoomRepositoryInterface extends RepositoryInterface
{
    /**
     * Join a room
     *
     * @param array $id
     *
     * @return mixed
     */
    public function joinRoom($id);
    /**
     * Show a room
     *
     * @param int $id
     *
     * @return mixed
     */
    public function show(int $id);
}
