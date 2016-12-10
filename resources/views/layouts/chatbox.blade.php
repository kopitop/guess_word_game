<div class="chat_area">
	<h2 id="word">{{ $data['current_round']->isDrawer() ? $data['current_round']->word->content : 'Please waiting...' }}</h2>
    <ul class="list-unstyled">
        <li class="left clearfix">
            @if ($data['current_round']->isDrawer())
            <div id="wPaint" style="position:relative; width:500px; height:200px; background-color:#7a7a7a; margin:20px auto;"></div>
            @endif
        </li>
    </ul>
</div>
<!--chat_area-->
<div class="message_write">
    <a id="send-image" href="javascript:;" class="pull-right btn btn-success">
        {{ trans('front-end/room.buttons.send') }}
    </a>
</div>
<!--message_section-->