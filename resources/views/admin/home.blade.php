@extends('admin.master')

@section('sub-view')
    <div class="panel panel-default">
        <div class="panel-heading">{{ trans('admin/home.labels.dashboard') }}</div>
        <div class="panel-body">
            <div class="row">
                <div id="poll_div"></div>
                {!! $chart->render('BarChart', trans('admin/home.chart.total'), 'poll_div') !!}
            </div>
            <hr>
        </div>
    </div>
@endsection
