<h3 id="result">
    <div class="alert alert-warning" role="alert">
        {{ trans('front-end/room.result.' . $round->is_correct) }}
    </div>
    {!! trans('front-end/room.result-detail', [
        'answer' => $round->answer, 'true-answer' => $round->word->content
    ]) !!}
</h3>
