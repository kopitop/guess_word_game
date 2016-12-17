<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Contracts\RoomRepositoryInterface as RoomRepository;
use App\Repositories\Contracts\MessageRepositoryInterface as MessageRepository;
use App\Http\Requests\StoreRoom;
use Exception;
use App\Exceptions\RoomException;
use Log;
use DB;
use App\DataTables\HighScoreDataTable;

class RoomsController extends BaseController
{
    public function __construct(RoomRepository $roomRepository, MessageRepository $messageRepository)
    {
        parent::__construct($roomRepository);
        $this->viewName = 'room';
        $this->viewData['title'] = trans('front-end/room.title');
        $this->messageRepository = $messageRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(HighScoreDataTable $highScore)
    {
        $this->viewData['rooms'] = $this->repository
            ->where('status', '!=', config('room.status.closed'))
            ->orderBy('id', 'desc')
            ->paginate();

        return $highScore->render('front-end.room.index', $this->viewData);
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
            $this->repository->create($input);
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
        try {
            parent::show($id);
        } catch (RoomException $e) {
            Log::debug($e);

            return redirect()->action('Web\RoomsController@index')
                ->withErrors($e->getMessage());
        }
        $this->viewData['messages'] = $this->messageRepository->getMessagesOnRoom($id)->get();

        return $this->viewRender();
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
        $dataResponse['status'] = 500; //System error

        try {
            $id = $request->id;
            $data = $this->repository->show($id);

            if ($data) {
                $dataResponse['status'] = 200; //OK
                $dataResponse['data'] = $data;
            }
        } catch (Exception  $e){
            Log::debug($e);
        }
        
        return response()->json($dataResponse);
    }


    /**
     * Reset state the specified room.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function resetState($id)
    {   
        return $this->repository->resetState($id);
    }

    /**
     * Update ready status of players in the specified room.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateReadyState(Request $request)
    {
        $dataResponse['status'] = 500; //System error
        DB::beginTransaction();
        try {
            $input = $request->only('id', 'ready');
            $state = $this->repository->updateReadyState($input);
            $dataResponse['status'] = 200; //OK
            $dataResponse['state'] = $state;
            DB::commit();
        } catch (Exception $e) {
            Log::debug($e);
            DB::rollback();
        }

        return response()->json($dataResponse);
    }

    /**
     * Begin to play in the specified room.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function beginPlay($id)
    {
        $dataResponse['status'] = 500; //System error
        DB::beginTransaction();
        try {
            $data = $this->repository->beginPlay($id);
            $dataResponse['status'] = 200; //OK
            $dataResponse['data'] = $data;
            DB::commit();
        } catch (RoomException $e) {
            Log::debug($e);
            DB::rollback();
        }

        return response()->json($dataResponse);
    }

    /**
     * Quit the specified room.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function quit(Request $request)
    {
        $dataResponse['status'] = 500; //System error
        DB::beginTransaction();
        try {
            $input = $request->all();
            if ($this->repository->quitRoom($input)) {
                $dataResponse['status'] = 200; //OK
                DB::commit();
            }
        } catch (Exception $e) {
            Log::debug($e);
            DB::rollback();
        }

        return response()->json($dataResponse);
    }

    /**
     * Post image after drawing
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function postImage(Request $request)
    {
        $dataResponse['status'] = 500; //Unspecified error
        DB::beginTransaction();
        try {
            $input = $request->only('id', 'image');
            $data = $this->repository->postImage($input);
            if ($data) {
                $dataResponse['status'] = 200; //OK
                $dataResponse['data'] = $data;
                DB::commit();
            }
        } catch (Exception $e) {
            Log::debug($e);
            DB::rollback();
        }

        return response()->json($dataResponse);
    }
    
    /**
     * Post answer
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function postAnswer(Request $request)
    {
        $dataResponse['status'] = 500; //Unspecified error
        DB::beginTransaction();
        try {
            $input = $request->only('id', 'answer');
            $data = $this->repository->postAnswer($input);
            if ($data) {
                $dataResponse['status'] = 200; //OK
                $dataResponse['data'] = $data;
                DB::commit();
            }
        } catch (Exception $e) {
            Log::debug($e);
            DB::rollback();
        }

        return response()->json($dataResponse);
    }

    /**
     * Get new round for the specified room.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function postNewRound(Request $request)
    {
        $dataResponse['status'] = 500; //Unspecified error
        try {
            $input = $request->only('id');
            if ($this->repository->createNewRound($input)) {
                $dataResponse['status'] = 200; //OK
            }
        } catch (Exception $e) {
            Log::debug($e);
        }

        return response()->json($dataResponse);
    }

    /**
     * Finish the game in the specified room.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function postFinish(Request $request)
    {
        $dataResponse['status'] = 500; //Unspecified error
        try {
            $input = $request->only('id');
            if($this->repository->finishRoom($input)) {
                $dataResponse['status'] = 200;
            }
        } catch (Exception $e) {
            Log::debug($e);
        }

        return response()->json($dataResponse);
    }
}
