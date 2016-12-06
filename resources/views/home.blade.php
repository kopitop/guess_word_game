@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-4">
            <div class="panel panel-default">
                <h3>ROOM LIST</h3>
                <ul class="well">
                    <li><a href="#">ROOM 1</a><span class="pull-right">Full</span></li>
                    <li><a href="#">ROOM 1</a><span class="pull-right">Full</span></li>
                    <li><a href="#">ROOM 1</a><span class="pull-right">Full</span></li>
                </ul>
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
                <a class="btn btn-primary">Create Room</a>
                <a class="btn btn-default">Join Room</a>
            </div>
        </div>
        <div class="col-md-8">
            <div class="panel panel-default">
                <h3>LEADERBOARD</h3>
            </div>
        </div>
    </div>
</div>
@endsection
