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
    public function show(int $id)
    {   
        $data['room'] = $this->model->with([
            'results' => function ($query) {
                $query->with('word');
            },
            'info', 
            'resultsCount'
        ])->findOrFail($id);
        
        $data['info'] = $data['room']->info->load('drawer', 'guesser');
        $data['current_round'] = $data['room']->results->last();
        $data['drawer'] = $data['info']->drawer;
        $data['guesser'] = $data['info']->guesser;
        
        return $data;
    }
}
