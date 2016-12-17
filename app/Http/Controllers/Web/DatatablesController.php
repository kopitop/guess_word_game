<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\DataTables\HighScoreDataTable;

class DataTablesController extends BaseController
{
    public function index(HighScoreDataTable $highScore)
    {
        return $highScore->render('front-end/highscore');
    }
}
