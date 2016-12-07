@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
    	@include('layouts.messages')
	    @yield('subview')
    </div>
</div>
@endsection
