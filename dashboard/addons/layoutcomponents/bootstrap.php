<?php


// REST
if (YXORP_API_REQUEST) {

    $app->on('yxorp.rest.init', function ($routes) {
        $routes['layout-components'] = 'LayoutComponents\\Controller\\RestApi';
    });
}

// ADMIN
if (YXORP_ADMIN && !YXORP_API_REQUEST) {

    include_once(__DIR__ . '/admin.php');
}