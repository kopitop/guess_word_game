<?php  

namespace App\Repositories\Eloquent;

use App\Repositories\Eloquent\BaseRepository;
use App\Repositories\Contracts\RoomRepositoryInterface;
use App\Exceptions\RoomException;
use Auth;

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
     * Create a room
     *
     * @param array $input
     *
     * @return mixed
     */
    public function createRoom($input)
    {   
        $data['description'] = $input['description'];
        $data['status'] = config('room.status.empty');

        return $this->model->create($data)
        	->results()->create([]);
    }

    /**
     * Join a room
     *
     * @param array $id
     *
     * @return mixed
     */
    public function joinRoom($id)
    {   
        $data['room'] = $this->model->findOrFail($id);
        $user = Auth::user();
        $data['result'] = $data['room']->results()->first();

        //If user has already joined the room 
        if ($data['result']->isJoining()) {
            return $data;
        }

        //If the room can not be joined (full/playing/closed)
        if (!$data['room']->canJoin()) {
            throw new RoomException(trans('front-end/room.exception.unavailable'), config('room.exception.unvailable'));
        }

        //Assign new player to a role in the room
        if ($data['result']->drawer_id) {
            $players['guesser_id'] = $user->id;
        } else {
            $players['drawer_id'] = $user->id;
        }

        $data['result']->fill($players);
        if (!$data['result']->save()) {
            throw new RoomException(trans('front-end/room.exception.failed'), config('room.exception.failed'));
        }

        //Update state of the room
        $room['state'] = 
        	$data['room']->state & config('room.state.player-1-joined') ? 
	        $data['room']->state | config('room.state.player-2-joined') :
	        $data['room']->state | config('room.state.player-1-joined')
        ;

        //Update status of the room
        $room['status'] = ++$data['room']->status;
        $data['room']->forceFill($room);
        if (!$data['room']->save()) {
            throw new RoomException(trans('front-end/room.exception.failed'), config('room.exception.failed'));
        }

        return $data;
    }

}
