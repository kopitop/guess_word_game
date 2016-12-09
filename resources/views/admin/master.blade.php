@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-3">
            @include('admin.includes.sidebar')
        </div>
        <div class="col-md-9">
            @include('layouts.includes.messages')
            @yield('sub-view')
        </div>
    </div>
</div>
@endsection
