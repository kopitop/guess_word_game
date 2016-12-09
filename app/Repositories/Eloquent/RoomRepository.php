<?php  

namespace App\Repositories\Eloquent;

use App\Repositories\Eloquent\BaseRepository;
use App\Repositories\Contracts\RoomRepositoryInterface;

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
}
