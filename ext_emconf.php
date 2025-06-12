<?php
declare(strict_types=1);

$EM_CONF[$_EXTKEY] = [
    'author'           => 'Daniel Ablass',
    'author_email'     => 'dn@phantasie-schmiede.de',
    'category'         => 'misc',
    'clearCacheOnLoad' => true,
    'constraints'      => [
        'conflicts' => [],
        'depends'   => [
            'php'            => '8.3',
            'psb_foundation' => '2.0',
        ],
        'suggests'  => [],
    ],
    'description'      => 'security enhancements for TYPO3-extensions',
    'state'            => 'stable',
    'title'            => 'PSBits | Security',
    'version'          => '1.0.0',
];
