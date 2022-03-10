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
                    'image' => get_the_post_thumbnail_url(0, 'professorLandscape')
                ));
            }

            if (get_post_type() == 'program') {
                // Add array to post
                array_push($results['programs'], array(
                    'title' => get_the_title(),
                    'permalink' => get_the_permalink(),
                    'id' => get_the_id()
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
                $eventDate = new DateTime(get_field('event_date'));
                $description = null;
                if (has_excerpt()) {
                    $description = get_the_excerpt();
                } else {
                    $description = wp_trim_words(get_the_content(), 18);
                }
                // Add array to post
                array_push($results['events'], array(
                    'title' => get_the_title(),
                    'permalink' => get_the_permalink(),
                    'month' => $eventDate->format('M'),
                    'day' => $eventDate->format('d'),
                    'description' => $description
                ));
            }
        }

        if ($results['programs']) {
            $programsMetaQuery = array('relation' => 'OR');
    
            foreach($results['programs'] as $item) {
                array_push($programsMetaQuery, array(
                    'key' => 'related_programs',
                    'compare' => 'LIKE',
                    'value' => '"' . $item['id'] . '"'
                ));
            }
    
            // Custom query for programs & professors relationships
            $programRelationshipQuery = new WP_Query(array(
                'post_type' => array('professor', 'event'),
                'meta_query' => $programsMetaQuery
            ));
    
            while($programRelationshipQuery->have_posts()) {
                $programRelationshipQuery->the_post();
    
                if (get_post_type() == 'professor') {
                    // Add array to post
                    array_push($results['professors'], array(
                        'title' => get_the_title(),
                        'permalink' => get_the_permalink(),
                        'image' => get_the_post_thumbnail_url(0, 'professorLandscape')
                    ));
                }
            }
    
            $results['professors'] = array_values(array_unique($results['professors'], SORT_REGULAR));
        }


        return $results;
    }
