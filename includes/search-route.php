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
            'post_type' => array('post', 'page', 'professor', 'program', 'campus', 'event'),
            // Dynamic search
            's' => sanitize_text_field($data['term'])
        ));

        $results = array(
            'generalInfo' => array(),
            'professors' => array(),
            'programs' => array(),
            'events' => array(),
            'campuses' => array()
        );

        while($mainQuery->have_posts()) {
            $mainQuery->the_post();

            if (get_post_type() == 'post' OR get_post_type() == 'page') {
                // Add array to post
                array_push($results['generalInfo'], array(
                    'title' => get_the_title(),
                    'permalink' => get_the_permalink(),
                    'postType' => get_post_type(),
                    'authorName' => get_the_author()
                ));
            }

            if (get_post_type() == 'professor') {
                // Add array to post
                array_push($results['professors'], array(
                    'title' => get_the_title(),
                    'permalink' => get_the_permalink(),
                    'image' => get_the_post_thumbnail_url()
                ));
            }

            if (get_post_type() == 'campus') {
                // Add array to post
                array_push($results['campuses'], array(
                    'title' => get_the_title(),
                    'permalink' => get_the_permalink()
                ));
            }

            if (get_post_type() == 'event') {
                // Add array to post
                array_push($results['events'], array(
                    'title' => get_the_title(),
                    'permalink' => get_the_permalink()
                ));
            }
        }

        return $results;
    }
