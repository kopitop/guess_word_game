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
    public function show($id);

    /**
     * Reset state a room
     *
     * @param int $id
     *
     * @return int
     */
    public function resetState($id);

    /**
     * Update state of a room
     *
     * @param array $input
     *
     * @return int
     */
    public function updateReadyState($input);

    /**
     * Begin to play in a room
     *
     * @param var $id
     *
     * @return mixed
     */
    public function beginPlay($id);

    /**
     * Quit a room
     *
     * @param array $input
     *
     * @return mixed
     */
    public function quitRoom($input);

    /**
     * Post an image
     *
     * @param array $input
     *
     * @return mixed
     */
    public function postImage($input);

    /**
     * Post an answer
     *
     * @param array $input
     *
     * @return mixed
     */
    public function postAnswer(array $input);

    /**
     * Create a new round
     *
     * @param var $id
     *
     * @return mixed
     */
    public function createNewRound($input);
    
    /**
     * Begin to play in a room
     *
     * @param var $id
     *
     * @return mixed
     */
    public function finishRoom($input);
}
