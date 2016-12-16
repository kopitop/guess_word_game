<img id="image" src="{{ '/' . config('room.upload-path') . $round->room_id . '/' . $round->image }}">
<input id="answer" type="text" class="form-control" placeholder="{{ trans('front-end/room.guesser.type-answer') }}">
<h3 id="result"></h3>
<a id="submit-answer" href="javascript:;" class="pull-right btn btn-success">
    {{ trans('front-end/room.buttons.submit') }}
</a>
