<?php

return [
    'title' => 'Room',
    'empty' => 'There is no available rooms, You can create the first one.',
    'list' => 'List',
    'leaderboard' =>  'Leaderboard',
    'buttons' => [
        'create' => 'Create Room',
        'join' => 'Join Room',
        'quit' => 'Quit',
        'send' => 'Send',
        'ready' => 'Ready',
        'submit' => 'Submit',
        'new-round' => 'New Round',
        'finish' => 'Finish',
    ],
    'description' => 'Description',
    'status' => [
        0 =>  'Empty',
        1 =>  'Waiting',
        2 =>  'Full',
        3 =>  'Playing',
        4 =>  'Closed',
    ],
    'create' => [
        'success' => 'You have created the room successfully.',
        'failed' => 'Can not create new room, please try again.',
    ],
    'join' => [
        'success' => 'Click ready button when you are ready to play.',
        'failed' => 'There is something wrong, you can not join this room.',
        'exception' => [
            'unavailable' => 'You can not join this room.',
            'database' => 'A system error has occured.'
        ]
    ],
    'waiting' => 'Please wait for a moment',
    'player' => 'Player',
    'history' => 'History',
    'info' => 'Info',
    'panel' => 'Panel',
    'word' => 'Word',
    'guesser' => [
        'waiting' => 'Please waiting for the drawer',
        'type-answer' => 'Type your answer here :D'
    ],
    'drawer' => [
        'waiting' => 'Your image has been sent, please wait for the response',
    ],
    'result-detail' => 'The answer of the guesser is <strong>:answer</strong>, and the correct answer is <strong>:true-answer</strong>',
    'result' => [
        0 => 'The answer is wrong, try hard next time',
        1 => 'Congratulation !!!',
    ],
];
