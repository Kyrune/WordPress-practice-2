<?php

add_action('rest_api_init', 'universityLikeRoutes');

function universityLikeRoutes() {
    register_rest_route('university/v1', 'manageLike', array(
        'methods' => 'POST',
        'callback' => x
    ));

    register_rest_route('university/v1', 'manageLike', array(
        'methods' => 'DELETE',
        'callback' => x
    ));
}


