<?php

namespace App\DataTables;

use App\Models\User;
use App\Models\Results;
use Yajra\Datatables\Services\DataTable;
use Illuminate\Support\Collection;
use Yajra\Datatables\Facades\Datatables;
use DB;

class HighScoreDataTable extends DataTable
{
    /**
     * Display ajax response.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function ajax()
    {
        $drawer = DB::table('results')
            ->join('users', 'users.id' , '=', 'results.drawer_id')
            ->select('users.id as uid', 'users.name as name', DB::raw('sum(results.is_correct) as correct_times'))
            ->groupBy('results.drawer_id');

        $guesser = DB::table('results')
            ->join('users', 'users.id' , '=', 'results.guesser_id')
            ->select('users.id as uid', 'users.name as name', DB::raw('sum(results.is_correct) as correct_times'))
            ->groupBy('results.guesser_id');

        $results = $drawer->union($guesser)->get();

        $data = $results->groupBy('uid');

        foreach ($data as $key => $value) {
            $items[$key]['name'] = $value->first()->name;
            $items[$key]['userId'] = $value->first()->uid;
            $items[$key]['correctDraw'] = $value->first()->correct_times;
            $items[$key]['correctGuess'] = $value->last()->correct_times;
            $items[$key]['correctTimes'] = $value->sum('correct_times');
        }

        return Datatables::of(collect($items))
            ->editColumn(
                'name', function ($user) {
                    return link_to_action('Web\UsersController@show', $user['name'], ['id' => $user['userId']]);
                }
                
            )
            ->make(true);
    }

    /**
     * Get the query object to be processed by dataTables.
     *
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder|\Illuminate\Support\Collection
     */
    public function query()
    {
        $query = User::query();

        return $this->applyScopes($query);
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\Datatables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
                    ->columns($this->getColumns())
                    ->ajax('')
                    ->parameters([
                        'dom' => 'Bfrtip',
                        'buttons' => ['csv', 'excel', 'pdf', 'print', 'reset', 'reload'],
                    ]);
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            'name',
            'correctDraw',
            'correctGuess',
            'correctTimes',
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'highscoredatatables_' . time();
    }
}
