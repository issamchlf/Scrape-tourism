<?php

return [
    'sites' => [
        'esmadrid'         => 'https://www.esmadrid.com/',
        'malaga'           => 'https://visita.malaga.eu/es/',

    ],

    // Sitemap URLs (if available) for auto-discovery
    'sitemaps' => [
        'esmadrid'         => 'https://www.esmadrid.com/sitemap.xml',
        'malaga'           => 'https://visita.malaga.eu/sitemap.xml',

    ],

    // Regex patterns to identify detail pages in each domain
    'detail_pattern' => [
        'esmadrid'         => '#/que-ver-y-hacer/[^/]+$#',
        'marbella'         => '#/que-ver#',
        'malaga'           => '#/que-ver-y-hacer/[^/]+/[^/]+#',
        'cordoba'          => '#/que-ver/#',
        'costadelsol'      => '#/que-ver/#',
        'granada'          => '#/sitios[^/]+$#',
        'saboramalaga'     => '#/recetas/#',
        'valencia'         => '#/lugares[^/]+$#',
        'barcelonaturisme' => '#/horaris[^/]+$#',
        'cdmx'             => '#/eventos[^/]+$#',
        'cancun'           => '#/paquetes[^/]+$#',
    ],

    // Fallback selectors for all sites
    'fallback_selectors' => [
        'name'        => 'h1.title, h1.page-title, h1.titular, h1.entry-title, h1.section__title, h1.titulo, h1.heading, h1.header-title, h1.main-title, h1.headline, .pageTitle, .page-title h1, .section-title, [itemprop="name"], meta[property="og:title"]',
        'description' => '.intro > p, .description, .presentacion p, .content-description, .section__intro p, .description-text, .descripcion p, .intro-text, .text-intro p, .text-content p, .entry-content p, .section-intro p, .summary-text, .wvp-summary p, .page-content p, [itemprop="description"], meta[name="description"]',
        'address'     => '.contact .address, .address-details, .info-direccion, .contact-info .address, .datos-contacto .direccion, .contacto .address, .contacto .direccion, .contact-details .address, .wvp-contact .address, [itemprop="address"]',
        'phone'       => '.contact .phone, .info-telefono, .contact-info .phone, .datos-contacto .telefono, .contacto .phone, .contacto .telefono, .contact-details .phone, .wvp-contact .phone, [itemprop="telephone"]',
        'hours'       => '.opening-hours li, .horario > li, .horario li, [itemprop="openingHoursSpecification"]',
        'image'       => 'meta[property="og:image"], [itemprop="image"], .gallery img, .featured-image img, .slide img, .carousel img',
        'website'     => 'meta[property="og:url"], a[href^="http"], .external-link a, [itemprop="url"]',
        'category'    => '.breadcrumb li:last-child a, .category-tag, .tags a, [rel="tag"]',
        'rating'      => '[itemprop="aggregateRating"] [itemprop="ratingValue"], .rating .value, .stars .rating, .review-count',
        'latitude'    => '[itemprop="latitude"], meta[property="place:location:latitude"], [data-lat], .map-container[data-lat]',
        'longitude'   => '[itemprop="longitude"], meta[property="place:location:longitude"], [data-lng], .map-container[data-lng]',
    ],

    // Site-specific selectors merged with fallbacks
    'selectors' => [
        'esmadrid' => [
            'name'        => 'h1.title, h1.page-title',
            'description' => '.intro > p, .description',
            'address'     => '.contact .address, .address-details',
            'phone'       => '.contact .phone',
            'hours'       => '.opening-hours li',
        ],
        'marbella' => [
            'name'        => 'h1.titular, h1.entry-title',
            'description' => '.presentacion p, .content-description',
            'address'     => '.info-direccion',
            'phone'       => '.info-telefono',
            'hours'       => '.horario > li',
        ],
        'malaga' => [
            'name'        => 'h1, .title h1, .page-title h1, .section__title, .titulo',
            'description' => '.description p, .content p, .section__intro p, .text-content p, .intro p, .presentacion p, meta[name="description"]',
            'address'     => '.address, .location-info, .contact-info .address, [itemprop="address"], .datos-contacto .direccion',
            'phone'       => '.phone, .contact-info .phone, [itemprop="telephone"], .datos-contacto .telefono',
            'hours'       => '.hours, .schedule, .opening-hours, [itemprop="openingHoursSpecification"], .horario li',
            'image'       => '.gallery img, .featured-image img, .main-image img, .carousel img, [itemprop="image"], .imagen img, .foto img',
            'latitude'    => '[data-lat], .map-container[data-lat], meta[property="place:location:latitude"], [itemprop="latitude"], .coordenadas [data-lat]',
            'longitude'   => '[data-lng], .map-container[data-lng], meta[property="place:location:longitude"], [itemprop="longitude"], .coordenadas [data-lng]',
            'website'     => '.website-link, .external-link a, [itemprop="url"], meta[property="og:url"], .enlace-externo a',
            'category'    => '.breadcrumb li:last-child a, .category-tag, .tags a, [rel="tag"], .categoria',
            'rating'      => '[itemprop="aggregateRating"] [itemprop="ratingValue"], .rating .value, .stars .rating, .valoracion',
        ],

        'cordoba' => [
            'name'        => 'h1.titulo, .page-header h1',
            'description' => '.descripcion p, .intro-text',
            'address'     => '.datos-contacto .direccion',
            'phone'       => '.datos-contacto .telefono',
            'hours'       => '.horario li',
        ],

        'costadelsol' => [
            'name'        => 'h1.heading, h1.page-title',
            'description' => '.text-intro p, .intro-text',
            'address'     => '.contacto .address',
            'phone'       => '.contacto .phone',
            'hours'       => '.opening-hours li',
        ],

        'granada' => [
            'name'        => 'h1.title, h1.titulo',
            'description' => '.intro p, .text-content p',
            'address'     => '.contacto .direccion',
            'phone'       => '.contacto .telefono',
            'hours'       => '.horario li',
        ],

        'saboramalaga' => [
            'name'        => 'h1.header-title, .page-title',
            'description' => '.entry-content p, .description-text',
            'address'     => '.contact-details .address',
            'phone'       => '.contact-details .phone',
            'hours'       => '.opening-hours li',
        ],

        'valencia' => [
            'name'        => 'h1.main-title, .section-title',
            'description' => '.section-intro p, .description',
            'address'     => '.contact-info .address',
            'phone'       => '.contact-info .phone',
            'hours'       => '.opening-hours li',
        ],

        'barcelonaturisme' => [
            'name'        => 'h1.headline, .pageTitle',
            'description' => '.wvp-summary p, .summary-text',
            'address'     => '.wvp-contact .address',
            'phone'       => '.wvp-contact .phone',
            'hours'       => '.opening-hours li',
        ],

        'cdmx' => [
            'name'        => 'h1.page-title, .header h1',
            'description' => '.intro p, .description-text',
            'address'     => '.contacto .direccion',
            'phone'       => '.contacto .telefono',
            'hours'       => '.horario li',
        ],

        'cancun' => [
            'name'        => 'h1.header-title, .page-title',
            'description' => '.page-content p, .intro-text',
            'address'     => '.contact-info .address',
            'phone'       => '.contact-info .phone',
            'hours'       => '.opening-hours li',
        ],
    ],
];
