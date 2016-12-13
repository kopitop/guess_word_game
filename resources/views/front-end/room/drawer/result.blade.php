<div class="alert alert-warning" role="alert">
    {{ trans('front-end/room.result.' . $round->is_correct) }}
</div>
<h3 id="result">
{!! trans('front-end/room.result-detail', [
    'answer' => $round->answer, 'true-answer' => $round->word->content
]) !!}
</h3>
<a href="javascript:;" id="new-round" class="btn btn-primary">
	{{ trans('front-end/room.buttons.new-round') }}
</a>
