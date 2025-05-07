<?php

return [
    // List of target sites and their base URLs (for scheduler or mass dispatch)
    'sites' => [
        'esmadrid'         => 'https://www.esmadrid.com/',
        'marbella'         => 'https://turismo.marbella.es/',
        'malaga'           => 'https://visita.malaga.eu/es/',
        'cordoba'          => 'https://www.turismodecordoba.org/',
        'costadelsol'      => 'https://www.visitacostadelsol.com/',
        'granada'          => 'https://turismo.granada.org/es',
        'saboramalaga'     => 'https://www.saboramalaga.es/es/',
        'valencia'         => 'https://www.visitvalencia.com/',
        'barcelonaturisme' => 'https://www.barcelonaturisme.com/wv3/es/',
        'cdmx'             => 'https://www.turismo.cdmx.gob.mx/',
        'cancun'           => 'https://www.turismocancun.mx/',
    ],

    // CSS selector maps per siteKey
    'esmadrid' => [
        'selectors' => [
            'name'        => 'h1.title, h1.page-title',
            'description' => '.intro > p, .description',
            'address'     => '.contact .address, .address-details',
            'phone'       => '.contact .phone',
            'hours'       => '.opening-hours li',
        ],
    ],

    'marbella' => [
        'selectors' => [
            'name'        => 'h1.titular, h1.entry-title',
            'description' => '.presentacion p, .content-description',
            'address'     => '.info-direccion',
            'phone'       => '.info-telefono',
            'hours'       => '.horario > li',
        ],
    ],

    'malaga' => [
        'selectors' => [
            'name'        => 'h1.section__title, .page-title h1',
            'description' => '.section__intro p, .description-text',
            'address'     => '.contact-info .address',
            'phone'       => '.contact-info .phone',
            'hours'       => '.opening-hours li',
        ],
    ],

    'cordoba' => [
        'selectors' => [
            'name'        => 'h1.titulo, .page-header h1',
            'description' => '.descripcion p, .intro-text',
            'address'     => '.datos-contacto .direccion',
            'phone'       => '.datos-contacto .telefono',
            'hours'       => '.horario li',
        ],
    ],

    'costadelsol' => [
        'selectors' => [
            'name'        => 'h1.heading, h1.page-title',
            'description' => '.text-intro p, .intro-text',
            'address'     => '.contacto .address',
            'phone'       => '.contacto .phone',
            'hours'       => '.opening-hours li',
        ],
    ],

    'granada' => [
        'selectors' => [
            'name'        => 'h1.title, h1.titulo',
            'description' => '.intro p, .text-content p',
            'address'     => '.contacto .direccion',
            'phone'       => '.contacto .telefono',
            'hours'       => '.horario li',
        ],
    ],

    'saboramalaga' => [
        'selectors' => [
            'name'        => 'h1.header-title, .page-title',
            'description' => '.entry-content p, .description-text',
            'address'     => '.contact-details .address',
            'phone'       => '.contact-details .phone',
            'hours'       => '.opening-hours li',
        ],
    ],

    'valencia' => [
        'selectors' => [
            'name'        => 'h1.main-title, .section-title',
            'description' => '.section-intro p, .description',
            'address'     => '.contact-info .address',
            'phone'       => '.contact-info .phone',
            'hours'       => '.opening-hours li',
        ],
    ],

    'barcelonaturisme' => [
        'selectors' => [
            'name'        => 'h1.headline, .pageTitle',
            'description' => '.wvp-summary p, .summary-text',
            'address'     => '.wvp-contact .address',
            'phone'       => '.wvp-contact .phone',
            'hours'       => '.opening-hours li',
        ],
    ],

    'cdmx' => [
        'selectors' => [
            'name'        => 'h1.page-title, .header h1',
            'description' => '.intro p, .description-text',
            'address'     => '.contacto .direccion',
            'phone'       => '.contacto .telefono',
            'hours'       => '.horario li',
        ],
    ],

    'cancun' => [
        'selectors' => [
            'name'        => 'h1.header-title, .page-title',
            'description' => '.page-content p, .intro-text',
            'address'     => '.contact-info .address',
            'phone'       => '.contact-info .phone',
            'hours'       => '.opening-hours li',
        ],
    ],
];
