<?php

/* Setting Up Custom Posts Type */
function university_post_types() {
    
    # <!-- EVENTS POST TYPE -->
    $argsEvents = array(
        # If we are creating a user with a role of i.e 'Event Planner' use this 'capability_type' and 'map_meta_cap' parameter
        'capability_type' => 'event',
        'map_meta_cap'    => true,
        'supports'        => array('title', 'editor', 'excerpt'),
        'rewrite'         => array('slug' => 'events'),
        'has_archive'     => true,
        'public'          => true,
        'labels'          => array(
            'name'          => 'Events',
            'add_new_item'  => 'Add New Event',
            'edit_item'     => 'Edit Event',
            'all_items'     => 'All Events',
            'singular_name' => 'Event'
        ),
        'menu_icon'         => 'dashicons-calendar'
    );

    register_post_type('event', $argsEvents);


    # <!-- PROGRAMS POST TYPE -->
    $argsProgrmas = array(
        'supports'    => array('title'),
        'rewrite'     => array('slug' => 'programs'),
        'has_archive' => true,
        'public'      => true,
        'labels'      => array(
            'name'          => 'Programs',
            'add_new_item'  => 'Add New Program',
            'edit_item'     => 'Edit Program',
            'all_items'     => 'All Programs',
            'singular_name' => 'Program'
        ),
        'menu_icon'   => 'dashicons-awards'
    );

    register_post_type('program', $argsProgrmas);


    # <!-- PROFESSOR POST TYPE -->
    $argsProfessor = array(
        'show_in_rest' => true,
        'supports'     => array('title', 'editor', 'thumbnail'),
        'public'       => true,
        'labels'       => array(
            'name'          => 'Professors',
            'add_new_item'  => 'Add New Professor',
            'edit_item'     => 'Edit Professor',
            'all_items'     => 'All Professors',
            'singular_name' => 'Program'
        ),
        'menu_icon'   => 'dashicons-welcome-learn-more'
    );

    register_post_type('professor', $argsProfessor);


    # <!-- CAMPUS POST TYPE -->
    $argsCampus = array(
        # If we are creating a user with a role of i.e 'Campus Manager' use this 'capability_type' and 'map_meta_cap' parameter
        'capability_type' => 'campus',
        'map_meta_cap'    => true,
        'supports'    => array('title', 'editor', 'excerpt'),
        'rewrite'     => array('slug' => 'campuses'),
        'has_archive' => true,
        'public'      => true,
        'labels'      => array(
            'name'          => 'Campuses',
            'add_new_item'  => 'Add New Campus',
            'edit_item'     => 'Edit Campus',
            'all_items'     => 'All Campuses',
            'singular_name' => 'Campus'
        ),
        'menu_icon'   => 'dashicons-location-alt'
    );

    register_post_type('campus', $argsCampus);


    # <!-- MY NOTES POST TYPE -->
    $argsNotes = array(
        # Because Custom Post Type inherits the permission of the default Blog post type
        # We need a unique capability type like "note" instead of default blog "post"
        # If we are giving the user permission to manipulate specific custom post type like this
        # Use this 'capability_type' and 'map_meta_cap' parameter
        'capability_type' => 'note',
        'map_meta_cap'    => true,
        'show_in_rest'    => true,
        'supports'        => array('title', 'editor'),
        'public'          => false,
        'show_ui'         => true,
        'labels'          => array(
            'name'          => 'Notes',
            'add_new_item'  => 'Add New Note',
            'edit_item'     => 'Edit Note',
            'all_items'     => 'All Notes',
            'singular_name' => 'Note'
        ),
        'menu_icon'   => 'dashicons-welcome-write-blog'
    );

    register_post_type('note', $argsNotes);


    # <!-- LIKE POST TYPE -->
    $argsLikes = array(
        'supports'        => array('title'),
        'public'          => false,
        'show_ui'         => true,
        'labels'          => array(
            'name'          => 'Likes',
            'add_new_item'  => 'Add New Like',
            'edit_item'     => 'Edit Like',
            'all_items'     => 'All Likes',
            'singular_name' => 'Like'
        ),
        'menu_icon'   => 'dashicons-heart'
    );

    register_post_type('like', $argsLikes);

}

// Action hook 'init'.
add_action('init', 'university_post_types');