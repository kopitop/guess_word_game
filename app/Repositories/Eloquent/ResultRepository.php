<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Eloquent\ResultRepository;
use App\Repositories\Contracts\ResultRepositoryInterface;
use DB;

class ResultRepository extends BaseRepository implements ResultRepositoryInterface
{
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'App\Models\Result';
    }

    public function topWords($quantity)
    {
        return $this->model->select(DB::raw('count(*) as words_count'), 'word_id')
            ->groupBy('word_id')
            ->orderBy('words_count', 'desc')
            ->limit($quantity)
            ->get();
    }
}
