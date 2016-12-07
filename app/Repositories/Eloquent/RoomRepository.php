<?php  

namespace App\Repositories\Eloquent;

use App\Repositories\Eloquent\BaseRepository;
use App\Repositories\Contracts\RoomRepositoryInterface;
use App\Exceptions\RoomException;
use Auth;
use App\Models\Word;

class RoomRepository extends BaseRepository implements RoomRepositoryInterface
{
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'App\Models\Room';
    }

    /**
     * Show a room
     *
     * @param array $id
     *
     * @return mixed
     */
    public function showRoom($id)
    {   
        $data['room'] = $this->model->findOrFail($id);
        $user = Auth::user();
        $data['result'] = $data['room']->results()->first();


        //If there is not any result, create the first result
        if (!$data['result']) {
            $data['result'] = $data['room']->results()->create([
                'word_id' => Word::inRandomOrder()->first()->id,
            ]);
        }

        //If user has already joined the room 
        if ($user->id === $data['result']->drawer_id || $user->id === $data['result']->guesser_id) {
            return $data;
        }

        //If the room can not be joined
        if (!$data['room']->canBeJoined()) {
            throw new RoomException(trans('front-end/room.exception.unavailable'), config('room.exception.unvailable'));
        }

        //Assign new player to a role in the room
        if ($data['result']->drawer_id) {
            $player['guesser_id'] = $user->id;
        } else {
            $player['drawer_id'] = $user->id;
        }

        $data['result']->fill($player);
        if (!$data['result']->save()) {
            throw new RoomException(trans('front-end/room.exception.failed'), config('room.exception.failed'));
        }

        //Update status of the room
        $info['status'] = ++$data['room']->status;
        $data['room']->fill($info);
        if (!$data['room']->save()) {
            throw new RoomException(trans('front-end/room.exception.failed'), config('room.exception.failed'));
        }

        return $data;
    }

    /**
     * Show a room
     *
     * @param array $id
     *
     * @return mixed
     */
    public function getPlayers($id)
    {   
        $result = $this->model->findOrFail($id)
            ->results()->first();

        $data['drawer'] = $result->drawer;
        $data['guesser'] = $result->guesser;

        return $data;
    }
}
