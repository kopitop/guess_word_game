<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Contracts\RoomRepositoryInterface as RoomRepository;
use App\Http\Requests\RoomSubject;
use Exception;
use Log;

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
    public function store(Request $request)
    {
        try {
            $input =  $request->only('description');
            $room = $this->roomRepository->create($input);
        } catch (Exception $e) {
            Log::debug($e);

            return redirect()->action('Web\RoomsController@show', ['id' => $room->id])
                ->with('status', trans('front-end/room.create.success'));
        }

        return back()->withErrors(trans('front-end/room.create.failed'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $room = $this->roomRepository->find($id);
        if ($room->canBeJoin()) {
            $this->viewData['room'] = $this->roomRepository->showRoom($id);

            return view('front-end.room.detail', $this->viewData)
                ->with('status', trans('front-end/room.join.success'));
        }

        return back()->withErrors(trans('front-end/room.join.failed'));
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
}
