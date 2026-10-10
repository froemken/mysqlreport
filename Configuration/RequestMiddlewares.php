<?php

declare(strict_types=1);

return [
    'frontend' => [
        'stefanfroemken/mysqlreport/query-flusher' => [
            'target' => \StefanFroemken\Mysqlreport\Middleware\QueryFlusherMiddleware::class,
            'before' => [
                'typo3/cms-frontend/timetracker',
            ],
        ],
    ],
    'backend' => [
        'stefanfroemken/mysqlreport/query-flusher' => [
            'target' => \StefanFroemken\Mysqlreport\Middleware\QueryFlusherMiddleware::class,
            'before' => [
                'typo3/cms-core/normalized-params-attribute',
            ],
        ],
    ],
];
