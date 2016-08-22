<?php
return array(
    'ctrl' => array(
        'title'	=> 'Profile',
        'label' => 'uid',
        'crdate' => 'crdate',
        'hideTable' => TRUE,
    ),
    'interface' => array(
        'showRecordFieldList' => '',
    ),
    'columns' => array(
        'query_id' => array(
            'exclude' => 0,
            'config' => array(
                'type' => 'passthrough',
            ),
        ),
        'mode' => array(
            'exclude' => 0,
            'config' => array(
                'type' => 'passthrough',
            ),
        ),
        'unique_call_identifier' => array(
            'exclude' => 0,
            'config' => array(
                'type' => 'passthrough',
            ),
        ),
        'duration' => array(
            'exclude' => 0,
            'config' => array(
                'type' => 'passthrough',
            ),
        ),
        'query' => array(
            'exclude' => 0,
            'config' => array(
                'type' => 'passthrough',
            ),
        ),
        'query_type' => array(
            'exclude' => 0,
            'config' => array(
                'type' => 'passthrough',
            ),
        ),
    ),
    'types' => array(
        '1' => array('showitem' => ''),
    ),
    'palettes' => array(
        '1' => array('showitem' => ''),
    ),
);
