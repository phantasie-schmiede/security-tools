<?php

$EM_CONF[$_EXTKEY] = [
    'author'           => 'Daniel Ablass',
    'author_email'     => 'dn@phantasie-schmiede.de',
    'category'         => 'misc',
    'clearCacheOnLoad' => true,
    'constraints'      => [
        'conflicts' => [],
        'depends'   => [
            'foundation' => '3.0',
            'php'        => '8.3',
            'typo3'      => '13.4.0-13.4.99',
        ],
        'suggests'  => [],
    ],
    'description'      => 'security enhancements for TYPO3-extensions',
    'state'            => 'alpha',
    'title'            => 'PSBits | Security',
    'version'          => '0.1.0',
];
