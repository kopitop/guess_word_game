<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Khill\Lavacharts\Lavacharts;
use App\Repositories\Contracts\ResultRepositoryInterface as ResultRepository;
use App\Repositories\Contracts\UserRepositoryInterface as UserRepository;

class HomeController extends BaseController
{
    public function __construct(ResultRepository $resultRepository) 
    {
        $this->resultRepository = $resultRepository;
    }

    public function index()
    {
        $chart = new Lavacharts; // See note below for Laravel
        //chart top word 
        $wordDataTable  = $chart->DataTable();
        $topWords = $this->resultRepository->topWords(config('word.top-words'));
        //add column for chart
        $wordDataTable->addStringColumn(trans('admin/home.chart.total'))
            ->addNumberColumn('Total');
            foreach ($topWords as $key => $value) {
                $wordDataTable->addRow([$value->word->content,  $value->words_count]);
            }

        $chart->BarChart(trans('admin/home.chart.total'), $wordDataTable, [
            'title' => trans('admin/home.chart.title'),
        ]);

        return view('admin.home', compact('chart'));
    }
}
