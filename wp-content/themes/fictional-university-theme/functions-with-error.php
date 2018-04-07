<?php

require get_theme_file_path('/includes/search-route.php');

/* ENQUEUE JAVASCRIPT AND CSS */

add_action('wp_enqueue_scripts', 'enqueue_unversity_files');

function enqueue_unversity_files() {
    
    # Custom Fonts
    //wp_enqueue_style('font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
    //wp_enqueue_style('google-fonts', '//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i');
    
    # Main Style CSS
    wp_enqueue_style('university_main_styles', get_stylesheet_uri(), NULL, microtime());

    # microtime() -> Built-in function to avoid caching during development. Use temporarily in place of 'Version' parameter.
    # Don't ever use it production mode.
    //wp_enqueue_script( 'googleMap', '//maps.googleapis.com/maps/api/js?key=AIzaSyBOATgI5_7zrRzBfFKWiPGYy_7g_jPhKMM', NULL, '1.0', TRUE );
    wp_enqueue_script( 'main-university-js', get_theme_file_uri('js/scripts-bundled.js'), NULL, '1.0', TRUE );
    wp_enqueue_script( 'search', get_theme_file_uri('js/Search.js'), NULL, microtime(), TRUE );
    wp_enqueue_script( 'mynotes', get_theme_file_uri('js/MyNotes.js'), NULL, microtime(), TRUE );

    wp_register_script( 'googlemap', get_theme_file_uri() . '/js/google-map.js', array('jquery'), '1.0.0', true);
    wp_enqueue_script('googlemap');
    
    /* === THIS WILL BE USE TO CREATE A CUSTOM REST API ROUTE FOR GET/SEARCH REQUES  ===
    Created and Found in 'includes' folder 'search-route.php'
    */ 
    # Will output a little bit of javascript data into the html source of the webpage.
    # Argument 1: main-university-js -> The JS file that you want to be flexible.
    # Argument 2: universityData -> A variable name
    # Argument 3: array -> Array of data that we want to be available in javascript
    wp_localize_script('main-university-js', 'universityData', array(
        'root_url'  => get_site_url(),
        'nonce'     => wp_create_nonce('wp_rest') // THIS WILL BE USE IN CREATING AND UPDATING CUSTOM POST TYPE IN THE REST API.
    ));
}


/* ADD THEME SUPPORT FEATURES */

add_action('after_setup_theme', 'university_theme_support_features');

function university_theme_support_features() {

    add_theme_support( 'title-tag' ); # Activates the Title tag of the page.
    add_theme_support('post-thumbnails');
    add_image_size('professorLandscape', 400, 260, true);
    add_image_size('professorPortrait', 480, 650, true);
    add_image_size('pageBanner', 1500, 350, true);


    # Activates admin dashboard Menu:
    // register_nav_menu('headerMenuLocation', 'Header Menu Location'); // Register Header Navigation
    // register_nav_menu('footerLocationOne', 'Footer Location One'); // Register Footer Navigation One
    // register_nav_menu('footerLocationTwo', 'Footer Location Two'); // Register Footer Navigation Two
}


/* ACTION AND FUNCTION THAT MANIPULATES DEFAULT URL BASED QUERIES */
# is_main_query() -> Boolean: Returns true when the current query (such as within the loop) is the "main" query.

add_action('pre_get_posts', 'university_adjust_queries');

function university_adjust_queries($query) {
    
    # CHECKS THE 'EVENTS' CUSTOM POST TYPE.
    if ( !is_admin() && is_post_type_archive('event') && $query->is_main_query()) {
        $today = date('Ymd');
        $query->set('meta_key', 'event_date');
        $query->set('orderby', 'meta_value_num');
        $query->set('order', 'ASC');
        $query->set('meta_query', array(
                array(
                    'key'     => 'event_date', # Name of custom field in custom post type 'Event'.
                    'compare' => '>=', # ">=" Today or in the future. '<' is passed events so it should display in upcoming event.
                    'value'   => $today,
                    'type'    => 'numeric' # Because we are comparing numbers.
                )
            )
        );
    }

    # CHECKS THE 'PROGRAMS' CUSTOM POST TYPE.
    if ( !is_admin() && is_post_type_archive('program') && $query->is_main_query()) {
        $query->set('orderby', 'title');
        $query->set('order', 'ASC');
        $query->set('posts_per_page', -1);
    }
}


/* === 

    MODIFYING CUSTOM FIELDS REST API GETSON RESULTS DATA
    BY:
    ADDING CUSTOM FIELDS TO REST API GETJSON RESULTS DATA 

    http://amazing-college.local/wp-json/university/v1/search
    
=== */


add_action('rest_api_init', 'university_custom_rest_api');


function university_custom_rest_api() {
    
    # Adding Author Name and Author URI REST Field.
    register_rest_field('post', 'authorName', array(
        'get_callback' => function() { return get_the_author(); }
    ));

    # Adding User Count REST Field to be use in 'MyNotes' page.
    register_rest_field('note', 'userNoteCount', array(
        'get_callback' => function() { return count_user_posts(get_current_user_id(), 'note'); }
    ));
}



# === REUSABLE / FLEXIBLE FUNCTION CODE === #

function pageBanner($args = NULL) { # Array parameter
    
    /*  Checks if the title is exist inside array arguments of pageBanner() function call in page.php
        If exist show it, and if not set the default title of the page.
    */
    if (!$args['title']) {
        $args['title'] = get_the_title();
    }

    if (!$args['subtitle']) {
        $args['subtitle'] = get_field('page_banner_subtitle');
    }

    if (!$args['photo']) { # Checks if photo exist inside array arguments, if it is, display it.
        if (get_field('page_banner_background_image')) { # If photo does not exist inside array arguments? check if background image custom field has an image uploaded. If has, then use it.
            $args['photo'] = get_field('page_banner_background_image')[sizes]['pageBanner'];
        }
        else { # If background image custom field has no image uploaded,
            $args['photo'] = get_theme_file_uri('/images/ocean.jpg');
        }
    }

    ?>

    <div class="page-banner">
        <div class="page-banner__bg-image" style="background-image: url(<?php echo $args['photo'] ?>)"></div>
        <div class="page-banner__content container container--narrow">
            <h1 class="page-banner__title"><?php echo $args['title'] ?></h1>
            <div class="page-banner__intro">
                <p><?php echo $args['subtitle'] ?></p>
            </div>
        </div>
    </div>
<?php }


# ===    REDIRECT SUBSCRIBER ACCOUNTS OUT OF ADMIN AND ONTO HOMEPAGE AND REMOVE THE ADMIN BAR AT THE TOP   === #

add_action('admin_init', 'redirectSubscribersToFrontEnd');

function redirectSubscribersToFrontEnd() {
    $ourCurrentUser = wp_get_current_user();

    # Checks if current user has only one role and that is 'subscriber':
    if (count($ourCurrentUser->roles) == 1 AND $ourCurrentUser->roles[0] == 'subscriber') {
        wp_redirect(site_url('/')); // Redirect subscriber to the homepage.
        exit;
    }
}

add_action('wp_loaded', 'noSubscribersAdminBar');

function noSubscribersAdminBar() {
    $ourCurrentUser = wp_get_current_user();

    # Checks if current user has only one role and that is 'subscriber':
    if (count($ourCurrentUser->roles) == 1 AND $ourCurrentUser->roles[0] == 'subscriber') {
        show_admin_bar(false); // Remove admin bar at the top
    }
}


# === FORCE "NOTE" CUSTOM POST TYPE TO BE PRIVATE FOR SECURITY REASON === #  i.e: Not 'publish',  'draft' or  'trash' post.

# This will Force Wordpress to make the post in "NOTE" custom post type to be 'private' even if in "MyNotes.js" createNote() function
# We set the data to be send to the REST API to -- 'status' :  'publish' --

add_filter('wp_insert_post_data', 'makeNotePrivate', 10, 2);

function makeNotePrivate($data, $postarr) { // $postarr -> It will access the post ID.

    if ($data['post_type'] == 'note') { # Strictly check if this should apply just only to "NOTE" custom post type.
        if (count_user_posts(get_current_user_id(), 'note') > 4 AND !$postarr['ID']) {
            die('You have reached your note limit!');
        }

        # Strictly sanitize title and content field.
        $data['post_title'] = sanitize_text_field($data['post_title']);
        $data['post_content'] = sanitize_textarea_field($data['post_content']);
    }

    if ($data['post_type'] == 'note' AND $data['post_status'] != 'trash') { # Strictly check if this should apply just only to "NOTE" custom post type.
        $data['post_status'] = 'private';
    }
    return $data;
}




# === CUSTOMIZE LOGIN SCREEN === #

// Change WP logo link url
add_filter('login_headerurl', 'ourHeaderUrl');

function ourHeaderUrl() {
    return esc_url(site_url('/'));
}

// load CSS in WP Login page.
add_action('login_enqueue_scripts', 'ourLoginCSS');

function ourLoginCSS() {
    wp_enqueue_style('university_main_styles', get_stylesheet_uri(), NULL, microtime());
    wp_enqueue_style('google-fonts', '//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i');
}

// Change WP Logo or Brand Name title link
add_filter('login_headertitle', 'ourLoginTitle');

function ourLoginTitle() {
    return get_bloginfo('name');
}


# ===    SETTING UP ACF GOOGLE MAP KEY API   === #

// function universityMapKey($api) {
//     $api['key'] = 'AIzaSyBOATgI5_7zrRzBfFKWiPGYy_7g_jPhKMM';
//     return $api;
// }
// add_filter('acf/fields/google_map/api', 'universityMapKey');
