<?php 

    require get_theme_file_path('/include/like-route.php');
    require get_theme_file_path('/includes/search-route.php');

    // Create a custom REST API
    function university_custom_rest() {
        register_rest_field('post', 'authorName', array(
            'get_callback' => function() {return get_the_author();}
        ));

        register_rest_field('note', 'userNoteCount', array(
            'get_callback' => function() {return count_user_posts(get_current_user_id(), 'note');}
        ));
    }

    add_action('rest_api_init', 'university_custom_rest');

    function pageBanner($args = NULL) {
        // Default title if no custom title is found
        if (!$args['title']) {
            $args['title'] = get_the_title();
        }
        // Default subtitle if no custom subtitle is found
        if (!$args['subtitle']) {
            $args['subtitle'] = get_field('page_banner_subtitle');
        }
        // Default photo for page banner if no custom photo is found
        if (!$args['photo']) {
            if (get_field('page_banner_background_image') AND !is_archive() AND !is_home()) {
                $args['photo'] = get_field('page_banner_background_image')['sizes']['pageBanner'];
            } else {
                $args['photo'] = get_theme_file_uri('/images/ocean.jpg');
            }
        }

        ?>
        <div class="page-banner">
            <div class="page-banner__bg-image" style="background-image: url(<?php echo $args['photo']; ?>)"></div>
                <div class="page-banner__content container container--narrow">
                    <h1 class="page-banner__title"><?php echo $args['title'] ?></h1>
                    <div class="page-banner__intro">
                    <p><?php echo $args['subtitle'] ?></p>
                </div>
            </div>
        </div>
   <?php }

    // Loads CSS/JS files
    function university_files() {
        wp_enqueue_script('main-university-js', get_theme_file_uri('/build/index.js'), array('jquery'), '1.0', true);
        wp_enqueue_style('custom-google-fonts', '//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i');
        wp_enqueue_style('font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
        wp_enqueue_style('university_main_styles', get_theme_file_uri('/build/style-index.css'));
        wp_enqueue_style('university_extra_styles', get_theme_file_uri('/build/index.css'));

        // Helps create dynamic url for search function
        wp_localize_script('main-university-js', 'universityData', array(
            'root_url' => get_site_url(),
            // Nonce = number once (run once)
            'nonce' => wp_create_nonce('wp_rest')
        ));
    }

    add_action('wp_enqueue_scripts', 'university_files');

    function university_features() {
        // Add header menu location in admin screen
        // register_nav_menu('headerMenuLocation', 'Header Menu Location');
        // Add footer menu location in admin screen
        // register_nav_menu('footerLocationOne', 'Footer Location One');
        // register_nav_menu('footerLocationTwo', 'Footer Location Two');
        // Creates a title in the browser tab
        add_theme_support('title-tag');
        // Add featured image for thumbnails
        add_theme_support('post-thumbnails');
        // Image sizes for professor images
        add_image_size('professorLandscape', 400, 260, true);
        add_image_size('professorPortrait', 480, 650, true);
        // Image size for page banner image
        add_image_size('pageBanner', 1500, 350, true);
    }

    add_action('after_setup_theme', 'university_features');

    // Query manipulation for Programs page
    function university_adjust_queries($query) {
        if (!is_admin() AND is_post_type_archive('program') AND is_main_query()) {
            $query->set('orderby', 'title');
            $query->set('order', 'ASC');
            $query->set('posts_per_page', -1);
        }
        
        // Query manipulation (includes pagination) (only for Events page)
        if (!is_admin() AND is_post_type_archive('event') AND $query->is_main_query()) {
            $today = date('Ymd');
            $query->set('meta_key', 'event_date');
            $query->set('orderby', 'meta_value_num');
            $query->set('order', 'ASC');
            // Prevent past event from showing
            $query->set('meta_query', array(
                array(
                  'key' => 'event_date',
                  'compare' => '>=',
                  'value' => $today,
                  'type' => 'numeric'
                )
              ));
        }
    }

    add_action('pre_get_posts', 'university_adjust_queries');

    // Redirect subscriber accounts out of admin and onto homepage
    add_action('admin_init', 'redirectSubsToFrontend');

    function redirectSubsToFrontend() {
        $ourCurrentUser = wp_get_current_user();

        if (count($ourCurrentUser->roles) == 1 AND $ourCurrentUser->roles[0] == 'subscriber') {
            wp_redirect(site_url('/'));
            exit;
        }
    }

    // Remove admin bar from homepage
    add_action('wp_loaded', 'noSubsAdminBar');

    function noSubsAdminBar() {
        $ourCurrentUser = wp_get_current_user();

        if (count($ourCurrentUser->roles) == 1 AND $ourCurrentUser->roles[0] == 'subscriber') {
            show_admin_bar(false);
        }
    }

    // Customize Login Screen logo url
    add_filter('login_headerurl', 'ourHeaderUrl');

    function ourHeaderUrl() {
        return esc_url(site_url('/'));
    }

    // Add CSS files to login page
    add_action('login_enqueue_scripts', 'ourLoginCSS');

    function ourLoginCSS() {
        wp_enqueue_style('custom-google-fonts', '//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i');
        wp_enqueue_style('font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
        wp_enqueue_style('university_main_styles', get_theme_file_uri('/build/style-index.css'));
        wp_enqueue_style('university_extra_styles', get_theme_file_uri('/build/index.css'));
    }

    // Change login title
    add_filter('login_headertitle', 'ourLoginTitle');

    function ourLoginTitle() {
        return get_bloginfo('name');
    }

    // Force note posts to be private
    add_filter('wp_insert_post_data', 'makeNotePrivate', 10, 2);

    function makeNotePrivate($data, $postarr) {
        // Ensures users notes are sanitized (removes html tags)
        if ($data['post_type'] == 'note') {
            // Limits notes per user
            if (count_user_posts(get_current_user_id(), 'note') > 4 AND !$postarr['ID']) {
                die("You have reached your note limit.");
            }

            $data['post_content'] = sanitize_textarea_field($data['post_content']);
            $data['post_title'] = sanitize_text_field($data['post_title']);
        }

        // Makes notes private
        if ($data['post_type'] == 'note' AND $data['post_status'] != 'trash') {
            $data['post_status'] = "private";
        }

        return $data;
    }

    // Adds post type of Events
    // function university_post_types() {
    //     register_post_type('event', array(
    //         // Sets post type to public in admin screen
    //         'public' => true,
    //         // Uses modern block editor screen.
    //         'show_in_rest' => true,
    //         // Sets name of post type in admin screen to Events
    //         'labels' => array(
    //             'name' => 'Events'
    //         ),
    //         // Icon for admin screen
    //         'menu_icon' => 'dashicons-calendar'
    //     ));
    // }

    // add_action('init', 'university_post_types');
