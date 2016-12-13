<div class="play_area">
    <ul class="list-unstyled">
        <li class="left clearfix">
            <!-- render playing panel depend on current round and current room -->
            @widget('playingPanel', [], $data['current_round'], $data['room'])
        </li>
    </ul>
</div>
<!--play_area-->
