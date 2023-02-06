<?php

global $allowed_html;

$allowed_html = [
    'img' => [
        'title'             => [],
        'src'               => [],
        'alt'               => [],
        'width'             => [],
        'height'            => [],
        'class'             => [],
        'data-imgtype'      => [],
        'referrerpolicy'    => [],
    ],
    'style'                     => [],
    'div'                       => [
        'class'                 => [],
        'id'                    => [],
        'data-swiper-autoplay'  => [],
    ],
    'a' => [
        'href'      => [],
        'target'    => [],
    ],
    'p' => [
        'id'    => [],
    ],
    'span' => [
        'class' => [],
        'id'    => [],
    ],
    'br' => [],
    'iframe' => [
        'src'       => [],
        'width'     => [],
        'height'    => [],
        'style'     => [],
        'allow'     => [],
        'id'     => []

    ],
    'input' => [
        'class'     => [],
        'id'        => [],
        'type'      => [],
        'name'      => [],
        'checked'   => [],
        'value'     => [],
        'min'       => [],
        'max'       => [],
        'step'      => [],
    ],
    'textarea' => [
        'id'    => [],
        'name'    => [],
        'value'    => [],
        'rows'    => [],
        'cols'    => [],
    ]
];
