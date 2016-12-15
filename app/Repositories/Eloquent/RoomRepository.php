<?php  

namespace App\Repositories\Eloquent;

use App\Repositories\Eloquent\BaseRepository;
use App\Repositories\Contracts\RoomRepositoryInterface;
use App\Exceptions\RoomException;
use Auth;
use App\Models\Word;
use Exception;

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
    public function create(array $input)
    {   
        $room['description'] = $input['description'];
        $room['status'] = config('room.status.empty');

        return $this->model->create($room)
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
        $data['result'] = $data['room']->results()->first();

        //If user has already joined the room
        if ($data['result']->isJoining()) {
            return $data;
        }

        //If the room can not be joined (full/playing/closed)
        if (!$data['room']->canJoin()) {
            throw new RoomException(trans('front-end/room.join.exception.unavailable'));
        }

        //Assign new player to a role in the room depending on the left role
        $user = Auth::user();
        if ($data['result']->drawer_id) {
            $players['guesser_id'] = $user->id;
        } else {
            $players['drawer_id'] = $user->id;
        }

        $data['result']->fill($players);
        if (!$data['result']->save()) {
            throw new RoomException(trans('front-end/room.join.exception.database'));
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

        //Save to database
        if (!$data['room']->save()) {
            throw new RoomException(trans('front-end/room.join.exception.database'));
        }

        return $data;
    }

    /**
     * Show a room
     *
     * @param int $id
     *
     * @return mixed
     */
    public function show($id)
    {   
        $data['room'] = $this->model->with([
            'results.word',
        ])->findOrFail($id);
        
        $data['info'] = $data['room']->results->first()->load('drawer', 'guesser');
        $data['current_round'] = $data['room']->results->last();
        $data['drawer'] = $data['info']->drawer;
        $data['guesser'] = $data['info']->guesser;
        
        return $data;
    }

    /**
     * Reset state a room
     *
     * @param int $id
     *
     * @return mixed
     */
    public function resetState($id)
    {   
        $room = $this->model->findOrFail($id);
        if ($room->status == config('room.status.playing') || $room->status == config('room.status.closed')) {
            return;
        }

        $data['state'] = 
            $room->state & config('room.state.player-1-ready') ?
            $room->state ^ config('room.state.player-1-ready') : $room->state;

        $room->forceFill($data);
        $room->save();

        return $room->state;
    }

    /**
     * Update state of a room
     *
     * @param var $id
     *
     * @return mixed
     */
    public function updateReadyState($input)
    {   
        $id = $input['id'];
        $ready = $input['ready'];
        $room = $this->model->findOrFail($id);

        //Can not update state when the room is playing or not enough players
        if ($room->status == config('room.status.playing') || $room->status == config('room.status.waiting')) {
            throw new Exception();
        }

        //Update state of the room
        if ($ready) {
            //Add a ready player
            $data['state'] = 
                $room->state & config('room.state.player-1-ready') ?
                $room->state | config('room.state.player-2-ready') :
                $room->state | config('room.state.player-1-ready')
            ;
        } else {
            //Remove unready player
            $data['state'] = 
                $room->state & config('room.state.player-1-ready') ?
                $room->state ^ config('room.state.player-1-ready') :
                $room->state
            ;
        }

        $room->forceFill($data);

        if (!$room->save()) {
            throw new Exception();
        }

        return $room->state;
    }

    /**
     * Begin to play in a room
     *
     * @param var $id
     *
     * @return mixed
     */
    public function beginPlay($input)
    {   
        $id = $input['id'];
        $data['room'] = $this->model->findOrFail($id);
        $data['result'] = $data['room']->results()->first();        

        //If neither player 1 nor player 2 unready throw Exception
        if (!$data['room']->states & config('room.state.player-2-ready')) {
            throw new Exception;
        }


        //Update status of the room
        $room['status'] = config('room.status.playing');
        $data['room']->forceFill($room);

        if (!$data['room']->save()) {
            throw new Exception;
        }

        //Update word for first round
        $result['word_id'] = Word::inRandomOrder()->first()->id;
        $data['result']->forceFill($result);

        if (!$data['result']->save()) {
            throw new Exception;
        }

        $data['word'] = $data['result']->word;

        return $data;
    }
}
