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
    public function __construct(RoomRepository $roomRepository) {
 
        $this->repository = $roomRepository;
        $this->viewData['title'] = trans('front-end/room.title');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->viewData['rooms'] = $this->repository->orderBy('id', 'desc')->paginate(config('room.list-limit'));

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
            $this->repository->createRoom($input);
        } catch (Exception $e) {
            Log::debug($e);
            
            return back()->withErrors(trans('front-end/room.create.failed'));
        }

        return redirect()->action('Web\RoomsController@index')
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
        $this->viewData['data'] = $this->repository->showRoom($id);

        return view('front-end.room.detail', $this->viewData);
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
     * Join a room
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function join($id)
    {
        DB::beginTransaction();
        try {
            $data = $this->repository->joinRoom($id);
            DB::commit();

            return redirect()->action('Web\RoomsController@show', ['id' => $id])
                ->with('status', trans('front-end/room.join.success'));

        } catch (RoomException $e) {
            Log::debug($e);
            DB::rollback();

            return redirect()->action('Web\RoomsController@index')
                ->withErrors($e->getMessage());
        }
    }

    /**
     * Refresh the specified room.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function refresh(Request $request)
    {
        $data = $this->repository->showRoom($request->only('id')['id']);

        return response()->json($data);
    }

    /**
     * Quit the specified room.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function quit(Request $request)
    {
        DB::beginTransaction();
        try {
            $input = $request->only('id');
            $this->repository->quitRoom($input['id']);
            DB::commit();

            return response()->json(trans('front-end/room.quit.success'));
        } catch (RoomException $e) {
            Log::debug($e);
            DB::rollback();

            return redirect()->action('Web\RoomsController@index')
                ->withErrors($e->getMessage());
        }
        
    }

    /**
     * Update ready status of players in the specified room.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateReadyState(Request $request)
    {
        DB::beginTransaction();
        try {
            $input = $request->only('id', 'ready');
            $state = $this->repository->updateReadyState($input['id'], $input['ready']);
            DB::commit();

            return response()->json($state);
        } catch (RoomException $e) {
            Log::debug($e);
            DB::rollback();

            return response()->json(trans('front-end/room.update-state.failed'));
        }
        
    }

    /**
     * Begin to play in the specified room.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function beginPlay(Request $request)
    {
        DB::beginTransaction();
        try {
            $input = $request->only('id');
            $data = $this->repository->beginPlay($input['id']);
            DB::commit();

            return response()->json($data);
        } catch (RoomException $e) {
            Log::debug($e);
            DB::rollback();

            return response()->json(trans('front-end/room.init-play.failed'));
        }
        
    }

    /**
     * Begin to play in the specified room.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function postImage(Request $request)
    {
        DB::beginTransaction();
        try {
            $input = $request->only('id', 'image');
            $data = $this->repository->postImage($input['id'], $input['image']);
            DB::commit();

            return response()->json($data);
        } catch (RoomException $e) {
            Log::debug($e);
            DB::rollback();

            return response()->json(trans('front-end/room.init-play.failed'));
        }
    }

    /**
     * Begin to play in the specified room.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function postAnswer(Request $request)
    {
        DB::beginTransaction();
        try {
            $input = $request->only('id', 'answer');
            $data = $this->repository->postAnswer($input['id'], $input['answer']);
            DB::commit();

            return response()->json($data);
        } catch (RoomException $e) {
            Log::debug($e);
            DB::rollback();

            return response()->json(trans('front-end/room.init-play.failed'));
        }
    }

    /**
     * Begin to play in the specified room.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function postNewRound(Request $request)
    {
        DB::beginTransaction();
        try {
            $input = $request->only('id');
            $data = $this->repository->createNewRound($input['id']);
            DB::commit();

            return response()->json($data);
        } catch (RoomException $e) {
            Log::debug($e);
            DB::rollback();

            return response()->json(trans('front-end/room.init-play.failed'));
        }
    }

    /**
     * Begin to play in the specified room.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function postFinish(Request $request)
    {
        DB::beginTransaction();
        try {
            $input = $request->only('id');
            $data = $this->repository->finishRoom($input['id']);
            DB::commit();

            return response()->json($data);
        } catch (RoomException $e) {
            Log::debug($e);
            DB::rollback();

            return response()->json(trans('front-end/room.init-play.failed'));
        }
    }
}
