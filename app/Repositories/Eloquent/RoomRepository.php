<?php  

namespace App\Repositories\Eloquent;

use App\Repositories\Eloquent\BaseRepository;
use App\Repositories\Contracts\RoomRepositoryInterface;
use App\Exceptions\RoomException;
use Auth;
use App\Models\Result;

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

    /**
     * Show a room
     *
     * @param array $input
     *
     * @return mixed
     */
    public function showRoom($id)
    {   
        $data['room'] = $this->model->findOrFail($id);
        $data['result'] = $data['room']->results()->first();
        $data['drawer'] = $data['result']->drawer;
        $data['guesser'] = $data['result']->guesser;

        return $data;
    }

    /**
     * Quit a room
     *
     * @param var $id
     *
     * @return mixed
     */
    public function quitRoom($id)
    {   
        $data['room'] = $this->model->findOrFail($id);
        $data['result'] = $data['room']->results()->first();

        //If there is not any result, throw exception
        if (!$data['result']) {
            throw new RoomException(trans('front-end/room.exception.failed'));
        }

        //If user is not in the room 
        if (!$data['result']->isJoining()) {
            throw new RoomException(trans('front-end/room.exception.permission'));
        }

        //Update player slots
        if ($data['result']->isDrawer()) {
            $data['result']->fill([
                    'drawer_id' => null,
                ]);
        } else {
            $data['result']->fill([
                    'guesser_id' => null,
                ]);
        }

        if (!$data['result']->save()) {
            throw new RoomException(trans('front-end/room.exception.failed'), config('room.exception.failed'));
        }

        //Update state of the room
        $room['state'] = 
            $data['room']->state == (config('room.state.player-1-joined') & config('room.state.player-2-joined'))? 
            $data['room']->state ^ config('room.state.player-2-joined') :
            $data['room']->state ^ config('room.state.player-1-joined')
        ;

        //Update status of the room
        $room['status'] = --$data['room']->status;
        $data['room']->forceFill($room);

        if (!$data['room']->save()) {
            throw new RoomException(trans('front-end/room.exception.failed'), config('room.exception.failed'));
        }

        return true;
    }

    /**
     * Update state of a room
     *
     * @param var $id
     *
     * @return mixed
     */
    public function updateReadyState($id, $ready)
    {   
        $data['room'] = $this->model->findOrFail($id);

        //Can not update state when the room is playing
        if ($data['room']->status == config('room.status.playing')) {
            throw new RoomException(trans('front-end/room.exception.failed'), config('room.exception.failed'));
        }

        //Update state of the room
        if ($ready) {
            //Add a ready player
            $room['state'] = 
                $data['room']->state & config('room.state.player-1-ready') ?
                $data['room']->state | config('room.state.player-2-ready') :
                $data['room']->state | config('room.state.player-1-ready')
            ;
        } else {
            //Remove unready player
            $room['state'] = 
                ($data['room']->state & config('room.state.player-1-ready')
                &&
                $data['room']->state & config('room.state.player-2-ready'))
                ? 
                $data['room']->state ^ config('room.state.player-2-ready') :
                $data['room']->state ^ config('room.state.player-1-ready')
            ;
        }

        $data['room']->forceFill($room);

        if (!$data['room']->save()) {
            throw new RoomException(trans('front-end/room.exception.failed'), config('room.exception.failed'));
        }

        return $data['room']->state;
    }


    /**
     * Begin to play in a room
     *
     * @param var $id
     *
     * @return mixed
     */
    public function beginPlay($id)
    {   
        $data['room'] = $this->model->findOrFail($id);
        $data['result'] = $data['room']->results()->first();

        //Update status of the room
        $room['status'] = config('room.status.playing');
        $data['room']->forceFill($room);

        if (!$data['room']->save()) {
            throw new RoomException(trans('front-end/room.exception.failed'), config('room.exception.failed'));
        }

        //Update word for first round
        $result['word_id'] = Result::inRandomOrder()->first()->id;
        $data['result']->forceFill($result);

        if (!$data['result']->save()) {
            throw new RoomException(trans('front-end/room.exception.failed'), config('room.exception.failed'));
        }

        $data['word'] = $data['result']->word;

        return $data;
    }
}
