@extends('front-end.master')

@section('subview')
    <div class="col-md-4">
        <div class="panel panel-default profile-box">
            <h3>{{ trans('front-end/profile/show.labels.profile') }}</h3>
            <div class="well">
                <div>
                    <img src="{{ $user->avatar ? asset(($user->avatar_url)) : asset(config('user.default-avatar')) }}" class="avatar">
                </div>
                <br>
                <div>
                    <label>{{ trans('front-end/profile/show.labels.name') }}:
                        <i>{{ $user->name }}</i>
                    </label>
                </div>
                <div>
                    <label>{{ trans('front-end/profile/show.labels.email') }}:
                        <i>{{ $user->email }}</i>
                    </label>
                </div>
                <div>
                    <label>{{ trans('front-end/profile/show.labels.time-joined') }}:
                        <i>{{ date_format($user->created_at, "Y/m/d") }}</i>
                    </label>
                </div>
            </div>
            @if ($user->isCurrent())
            <div class="form-group">
                <a href="{{ action('Web\UsersController@edit', ['id' => $user->id]) }}">
                    <button class="btn btn-success" type="button">
                        {{ trans('front-end/profile/show.buttons.update') }}
                    </button>
                </a>
                <a href="{{ action('Web\UsersController@editPassword') }}">
                    <button class="btn btn-primary" type="button">
                        {{ trans('front-end/profile/show.buttons.change-password') }}
                    </button>
                </a>
            </div>
            @endif
        </div>
    </div>
@endsection
