<?php

declare(strict_types=1);

use StefanFroemken\Mysqlreport\Middleware\QueryFlusherMiddleware;

return [
    'frontend' => [
        'stefanfroemken/mysqlreport/query-flusher' => [
            'target' => QueryFlusherMiddleware::class,
            'before' => [
                'typo3/cms-frontend/timetracker',
            ],
        ],
    ],
    'backend' => [
        'stefanfroemken/mysqlreport/query-flusher' => [
            'target' => QueryFlusherMiddleware::class,
            'before' => [
                'typo3/cms-core/normalized-params-attribute',
            ],
        ],
    ],
];
