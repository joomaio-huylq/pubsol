<?php 

return [
    'admin' => [
        // Endpoint Milestones
        'milestones'=>[
            'fnc' => [
                'get' => 'milestone.milestone.list',
                'post' => 'milestone.milestone.list',
                'put' => 'milestone.milestone.update',
                'delete' => 'milestone.milestone.delete'
            ],
        ],
        'requests' => [
            'fnc' => [
                'get' => 'milestone.request.list',
                'post' => 'milestone.request.list',
                'put' => 'milestone.request.update',
                'delete' => 'milestone.request.delete'
            ],
            'parameters' => ['milestone_id'],
        ],
        'request' => [
            'fnc' => [
                'get' => 'milestone.request.detail',
                'post' => 'milestone.request.add',
                'put' => 'milestone.request.update',
                'delete' => 'milestone.request.delete'
            ],
            'parameters' => ['milestone_id','id'],
        ],
        'relate-notes' => [
            'fnc' => [
                'get' => 'milestone.note.list',
                'post' => 'milestone.note.list',
                'put' => 'milestone.note.update',
                'delete' => 'milestone.note.delete'
            ],
            'parameters' => ['request_id'],
        ],
        'relate-note' => [
            'fnc' => [
                'get' => 'milestone.note.detail',
                'post' => 'milestone.note.add',
                'put' => 'milestone.note.update',
                'delete' => 'milestone.note.delete'
            ],
            'parameters' => ['request_id', 'id'],
        ],
        'milestone' => [
            'fnc' => [
                'get' => 'milestone.milestone.detail',
                'post' => 'milestone.milestone.add',
                'put' => 'milestone.milestone.update',
                'delete' => 'milestone.milestone.delete'
            ],
            'parameters' => ['id'],
        ],
    ],
];
