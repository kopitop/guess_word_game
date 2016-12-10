<div class="chat_area">
	<h2 id="word">{{ $data['current_round']->isDrawer() ? $data['current_round']->word->content : 'Please waiting...' }}</h2>
    <ul class="list-unstyled">
        <li class="left clearfix">
            @if ($data['current_round']->isDrawer())
                @if(!$data['current_round']->image)
                <div id="wPaint" style="position:relative; width:500px; height:200px; background-color:#7a7a7a; margin:20px auto;"></div>
                <h3 id="result"></h3>
                @elseif(!$data['current_round']->answer)
                <h3>Please waiting for the guesser</h3>
                <h3 id="result"></h3>
                @else
                <h3 id="result">Answer of guesser is {{ $data['current_round']->answer }} ,and the true answer is {{ $data['current_round']->word->content }}</h3>
                @endif

                @if (!is_null($data['current_round']->is_correct))
                <a href="javascript:;" id="new-round" class="btn btn-primary">
                    {{ trans('front-end/room.buttons.new-round') }}
                </a>
            @endif
            @else
                @if(!$data['current_round']->image)
                <h3>Please waiting for the drawer</h3>
                <h3 id="result"></h3>
                @elseif(!$data['current_round']->answer)
                <img id="image" src="{{ $data['current_round']->image }}">
                <input id="answer" type="text" name="answer" class="form-control" placeholder="Type your answer">
                <h3 id="result"></h3>
                @else
                <h3 id="result">Answer of guesser is {{ $data['current_round']->answer }} ,and the true answer is {{ $data['current_round']->word->content }}</h3>
                @endif
            @endif
        </li>
    </ul>
</div>
<!--chat_area-->
<div class="message_write">
    @if ($data['current_round']->isDrawer() && !$data['current_round']->image)
        <a id="send-image" href="javascript:;" class="pull-right btn btn-success">
            {{ trans('front-end/room.buttons.send') }}
        </a>
    @endif
    @if (!$data['current_round']->isDrawer() && $data['current_round']->image && !$data['current_round']->answer)
        <a id="submit-answer" href="javascript:;" class="pull-right btn btn-success">
            {{ trans('front-end/room.buttons.submit') }}
        </a>
    @endif
</div>
<!--message_section-->