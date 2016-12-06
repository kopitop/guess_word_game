@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-4">
            <div class="panel panel-default room-list">
                <h3>ROOM LIST</h3>
                <div class="well">
                    <div class="list-group">
                        <a href="#" class="list-group-item">ROOM 1<span class="pull-right">Full</span></a>
                        <a href="#" class="list-group-item">ROOM 2<span class="pull-right">Full</span></a>
                        <a href="#" class="list-group-item">ROOM 3<span class="pull-right">Full</span></a>
                    </div>
                </div>
                
                <nav aria-label="Page navigation">
                  <ul class="pagination">
                    <li>
                      <a href="#" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                      </a>
                    </li>
                    <li><a href="#">1</a></li>
                    <li><a href="#">2</a></li>
                    <li><a href="#">3</a></li>
                    <li><a href="#">4</a></li>
                    <li><a href="#">5</a></li>
                    <li>
                      <a href="#" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                      </a>
                    </li>
                  </ul>
                </nav>
                <div class="action-room">
                    <a class="btn btn-primary">Create Room</a>
                    <a class="btn btn-default">Join Room</a>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="panel panel-default">
                <h3>LEADERBOARD</h3>
                <canvas id="myChart" width="400" height="400"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection
