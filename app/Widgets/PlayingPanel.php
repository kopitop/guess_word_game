<?php

namespace App\Widgets;

use Arrilot\Widgets\AbstractWidget;

class PlayingPanel extends AbstractWidget
{
    /**
     * The configuration array.
     *
     * @var array
     */
    protected $config = [];

    /**
     * Treat this method as a controller action.
     * Return view() or other content to display.
     */
    public function run($round, $room)
    {
        if ($room->status == config('room.status.playing')) {
            return view(getPlayingPanelView($round), [
                'round' => $round,
            ]);
        }

        return '';
    }
}
