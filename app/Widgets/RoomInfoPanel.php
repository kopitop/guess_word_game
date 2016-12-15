<?php

namespace App\Widgets;

use Arrilot\Widgets\AbstractWidget;

class RoomInfoPanel extends AbstractWidget
{
    /**
     * Treat this method as a controller action.
     * Return view() or other content to display.
     */
    public function run($status)
    {
        if ($status == config('room.status.full') || $status == config('room.status.waiting')) {
        	return view('front-end.room.info-panel.quit');
        } elseif ($status == config('room.status.playing')) {
        	return view('front-end.room.info-panel.playing');
        }
    }
}