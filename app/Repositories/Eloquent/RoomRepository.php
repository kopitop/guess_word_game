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

        if (Auth::user()->id != $data['info']->drawer_id && Auth::user()->id != $data['info']->guesser_id) {
            throw new RoomException(trans('front-end/room.show.exception.permission'));
            
        }
        
        return $data;
    }

    /**
     * Reset state a room
     *
     * @param int $id
     *
     * @return int
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
    public function beginPlay($id)
    {   
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
        $result['word_id'] = app(Word::class)->inRandomOrder()->first()->id;
        $data['result']->forceFill($result);

        if (!$data['result']->save()) {
            throw new Exception;
        }

        $data['word'] = $data['result']->word;

        return $data;
    }

    /**
     * Quit a room
     *
     * @param var $id
     *
     * @return mixed
     */
    public function quitRoom($input)
    {   
        $id = $input['id'];
        $data['room'] = $this->model->findOrFail($id);
        $data['info'] = $data['room']->results->first();

        //If there is not any result, throw exception
        if ($data['room']->status == config('room.status.playing') || $data['room']->status == config('room.status.closed')) {
            throw new Exception;
        }

        //If there is not any result, throw exception
        if (!$data['info']) {
            throw new Exception;
        }

        //If user is not in the room 
        if (!$data['info']->isJoining()) {
            throw new Exception;
        }

        //Update player slots
        if ($data['info']->isDrawer()) {
            $data['info']->drawer_id = null;
        } else {
            $data['info']->guesser_id = null;
        }

        if (!$data['info']->save()) {
            throw new Exception;
        }

        //Remove ready state
        $room['state'] = $data['room']->state ^ config('room.state.player-1-ready');

        //Update join state of the room
        $room['state'] = 
            $data['room']->state & config('room.state.player-1-joined') && 
            $data['room']->state & config('room.state.player-2-joined') ? 
            $room['state'] ^ config('room.state.player-2-joined') :
            $room['state'] ^ config('room.state.player-1-joined')
        ;

        //If a player quit, minus one status of the room
        $room['status'] = --$data['room']->status;
        $data['room']->forceFill($room);

        if (!$data['room']->save()) {
            throw new Exception;
        }

        return true;
    }

    /**
     * Post an image
     *
     * @param var $id
     *
     * @return mixed
     */
    public function postImage($input)
    {   
        $id = $input['id'];
        $image = $input['image'];
        $data['room'] = $this->model->findOrFail($id);

        //If the room's not playing, throw Exception
        if ($data['room']->status != config('room.status.playing')) {
            throw new Exception();
        }

        $data['current_round'] = $data['room']->results->last();

        //Save image
        $path = config('room.upload-path').$data['room']->id;
        $data['current_round']->image = base64ToImage($image, $path);

        if (!$data['current_round']->save()) {
            throw new Exception();
        }

        return $data;
    }

    /**
     * Post an answer
     *
     * @param array $input
     *
     * @return mixed
     */
    public function postAnswer($input)
    {   
        $id = $input['id'];
        $answer = $input['answer'];
        $room = $this->model->findOrFail($id);
        if ($room->status != config('room.status.playing')) {
            throw new Exception;
        }

        $current_round = $room->results->last();

        //Update image
        $current_round->fill([
            'answer' => $answer,
            'is_correct' => $current_round->word->content == $answer
        ]);

        if (!$current_round->save()) {
            throw new Exception;
        }

        $data = [
            'room' => $room,
            'current_round' =>  $current_round,
        ];

        return $data;
    }

    /**
     * Create a new round
     *
     * @param var $id
     *
     * @return mixed
     */
    public function createNewRound($input)
    {   
        $id = $input['id'];
        $room = $this->model->findOrFail($id);

        if ($room->status != config('room.status.playing')) {
            throw new Exception;
        }
        
        $last_round = $room->results->last();

        if (is_null($last_round->is_correct)) {
            throw new Exception;
        }

        //Create a new round
        $room->results()->create([
            'drawer_id' => $last_round->guesser_id,
            'guesser_id' => $last_round->drawer_id,
            'word_id' => app(Word::class)->inRandomOrder()->first()->id,
        ]);

        return true;
    }
    
    /**
     * Begin to play in a room
     *
     * @param var $id
     *
     * @return mixed
     */
    public function finishRoom($input)
    {   
        $id = $input['id'];
        $room = $this->model->findOrFail($id);

        if ($room->status != config('room.status.playing')) {
            throw new Exception;
        }

        $room->status = config('room.status.closed');

        if (!$room->save()) {
            throw new Exception;
        }

        return true;
    }
}
