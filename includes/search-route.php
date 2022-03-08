<?php

    add_action('rest_api_init', 'universityRegisterSearch');

    function universityRegisterSearch() {
        register_rest_route('university/v1', 'search', array(
            'methods' => WP_REST_SERVER::READABLE,
            'callback' => 'universitySearchResults'
        ));
    }

    function universitySearchResults($data) {
        // 10 most recent posts from professors
        $mainQuery = new WP_Query(array(
            'post_type' => array('post', 'page', 'professor'),
            // Dynamic search
            's' => sanitize_text_field($data['term'])
        ));

        $professorResults = array();

        while($mainQuery->have_posts()) {
            $mainQuery->the_post();
            // Add array to post
            array_push($professorResults, array(
                'title' => get_the_title(),
                'permalink' => get_the_permalink()
            ));
        }

        return $professorResults;
    }
