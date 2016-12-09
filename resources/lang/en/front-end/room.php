<?php

return [
    'title' => 'Room',
    'empty' => 'There is no available rooms, You can create the first one',
    'list' => 'List',
    'leaderboard' =>  'Leaderboard',
    'buttons' =>  [
        'create' => 'Create Room',
        'join' => 'Join Room',
        'quit' => 'Quit',
    ],
    'description' => 'Description',
    'status' => [
    	0 =>  'Empty',
    	1 =>  'Waiting',
        2 =>  'Full',
    	3 =>  'Playing',
    	4 =>  'Closed',
    ],
    'player' => 'Player',
    'history' => 'History',
    'word' => 'Word',
    'info' => 'Info',
    'exception' => [
        'unavailable' => 'You can not join this room',
        'failed' => 'A system error has occured, please contact admin',
    ],
    'create' => [
        'success' => 'You have created the room successfully',
        'failed' => 'Can not create new room, please try again',
    ],
    'join' => [
        'success' => 'Click ready button when you are ready to play',
        'failed' => 'There is something wrong, you can not join this room',
    ],
    'panel' => 'Panel'
];
