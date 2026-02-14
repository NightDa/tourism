<?php
function getSEOTags($page)
{
    $seo = [
        'index' => [
            'title' => 'Travol Morocco - Discover Morocco',
            'description' => 'Professional Moroccan tour agency offering unforgettable experiences',
            'keywords' => 'Morocco tours, Marrakech, Sahara desert'
        ],
        'excursions' => [
            'title' => 'Morocco Excursions & Tours - Travol Morocco',
            'description' => 'Discover our curated selection of day tours and excursions',
            'keywords' => 'Morocco excursions, day tours, Marrakech tours'
        ]
    ];
    return $seo[$page] ?? $seo['index'];
}
