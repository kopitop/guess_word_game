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
     * Join to a room
     *
     * @param array $id
     *
     * @return mixed
     */
    public function showRoom($id)
    {   
        $room = $this->model->findOrFail($id);

        if ($room->status == 0 || $room->status == 1) {
        	
        }

        return $room;

    }
}
