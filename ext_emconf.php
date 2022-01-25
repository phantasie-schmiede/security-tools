<?php
declare(strict_types=1);

$EM_CONF['psb_security'] = [
    'author'           => 'Daniel Ablass',
    'author_email'     => 'dn@phantasie-schmiede.de',
    'category'         => 'misc',
    'clearCacheOnLoad' => true,
    'constraints'      => [
        'conflicts' => [
        ],
        'depends'   => [
            'php'            => '7.4',
            'psb_foundation' => '1.0',
        ],
        'suggests'  => [
        ],
    ],
    'description'      => 'security enhancements for TYPO3-extensions',
    'state'            => 'stable',
    'title'            => 'PSbits | Security',
    'version'          => '1.0.0',
];
