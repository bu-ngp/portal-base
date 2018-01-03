<?php
return [
    [
        'parttime_id' => 1,
        'person_id' => \wartron\yii2uuid\helpers\Uuid::str2uuid('2BB8A51CEDE711E7B78500FF1E4274A2'),
        'dolzh_id' => \wartron\yii2uuid\helpers\Uuid::str2uuid('1A1B22ABADE411E79AE500FF1E4274A2'),
        'podraz_id' => \wartron\yii2uuid\helpers\Uuid::str2uuid('1A1B22ACACE411E79AE500FF1E4274A2'),
        'parttime_begin' => date('Y-m-d'),
        'parttime_end' => date('Y-m-d', strtotime('+10 day')),
        'created_at' => time(),
        'updated_at' => time(),
        'created_by' => 'Гость',
        'updated_by' => 'Гость',
    ],
];