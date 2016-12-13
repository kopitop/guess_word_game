<?php

return [
    'list-limit' => 5,
    'status' => [
        'empty' => 0,
        'waiting' =>  1,
        'full' => 2,
        'playing' =>  3,
        'closed' =>  4,
    ],
    'state' =>  [
        'player-1-joined' => 1,
        'player-2-joined' => 2,
        'player-1-ready' => 4,
        'player-2-ready' => 8,
    ],
    'drawer' => [
        'view' => [
            'word' => 'front-end.room.drawer.word',
            'answer' => 'front-end.room.drawer.answer',
            'image' => 'front-end.room.drawer.image',
            'result' => 'front-end.room.drawer.result',
        ]
    ],
    'guesser' => [
        'view' => [
            'word' => 'front-end.room.guesser.word',
            'answer' => 'front-end.room.guesser.answer',
            'image' => 'front-end.room.guesser.image',
            'result' => 'front-end.room.guesser.result',
        ]
    ]
];
