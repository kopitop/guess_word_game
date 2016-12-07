<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Contracts\RoomRepositoryInterface as RoomRepository;
use App\Http\Requests\StoreRoom;
use Exception;
use App\Exceptions\RoomException;
use Log;
use DB;

class RoomsController extends BaseController
{
    /**
     * @var roomRepository
     */
    private $roomRepository;

    public function __construct(RoomRepository $roomRepository) {
 
        $this->roomRepository = $roomRepository;
        $this->viewData['title'] = trans('front-end/room.title');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->viewData['rooms'] = $this->roomRepository->paginate(config('room.list-limit'));

        return view('front-end.room.index', $this->viewData);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRoom $request)
    {
        try {
            $input =  $request->only('description');
            $input['status'] = config('room.status.empty');
            $room = $this->roomRepository->create($input);
        } catch (Exception $e) {
            Log::debug($e);
            
            return back()->withErrors(trans('front-end/room.create.failed'));
        }

        return redirect()->action('Web\RoomsController@show', ['id' => $room->id])
            ->with('status', trans('front-end/room.create.success'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        DB::beginTransaction();
        try{
            $this->viewData['data'] = $this->roomRepository->showRoom($id);
            DB::commit();

            return view('front-end.room.detail', $this->viewData);
        } catch (RoomException $e) {
            Log::debug($e);
            DB::rollback();

            return redirect()->action('Web\RoomsController@index')
                ->withErrors($e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Quit the specified room.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function quit(Request $request)
    {
        //
    }

    /**
     * Refresh the specified room.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function refresh(Request $request)
    {
        $players = $this->roomRepository->getPlayers($request->input('id'));

        return response()->json($players);
    }
}
