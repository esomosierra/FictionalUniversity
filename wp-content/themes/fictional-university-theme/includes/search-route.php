<?php

/* === CREATING CUSTOM REST API ROUTE FOR GET/SEARCH REQUEST === */

// http://amazing-college.local/wp-json/university/v1/search

add_action('rest_api_init', 'unversityRegisterSearch');

function unversityRegisterSearch() {
    # 'unversity' -> Namespace # # 'search' -> Route's Name #
    register_rest_route('university/v1', 'search', array(
        'methods'  => WP_REST_SERVER::READABLE, // Synonyms to 'GET'.
        'callback' => 'universitySearchResults'
    ));
}

function universitySearchResults($data) { # MAIN QUERY
    
    # CUSTOM QUERY TO CREATE AND DISPLAY RAW JSON DATA #

    $mainQuery = new WP_Query(array(
        # Working with multiple posts type.
        'post_type' => array('post', 'page', 'professor', 'program', 'event', 'campus'), 
        
        # Query and keyword searching.
        # @param: '$data' -> Array that WP put together and will be used to access someone else who puts parameters in the url.
        's' => sanitize_text_field($data['term']) // 'term' -> variable to be use in the getJson url. 'Postman' chrome extension.
    ));

    $results = array(
        'generalInfo'  => array(),
        'professors'   => array(),
        'programs'     => array(),
        'events'       => array(),
        'campuses'     => array()
    );

    while($mainQuery->have_posts()) {
        $mainQuery->the_post();

        # PUT THE DATA INTO THEIR RESPECTIVE POST TYPE #

        if (get_post_type() == 'post' || get_post_type() == 'page') {
            array_push($results['generalInfo'], array(
                'title'      => get_the_title(),
                'permalink'  => get_the_permalink(),
                'postType'   => get_post_type(),
                'authorName' => get_the_author()
            ));
        }

        if (get_post_type() == 'professor') {
            array_push($results['professors'], array(
                'title'     => get_the_title(),
                'permalink' => get_the_permalink(),
                'image'     => get_the_post_thumbnail_url(0, 'professorLandscape')
            ));
        }

        if (get_post_type() == 'program') {

            # Variable that will holds 'Related Campus' post type array content data
            # This variable will then be use to pull related campuses of specific program
            $relatedCampuses = get_field('related_campus');

            if ($relatedCampuses) {
                foreach($relatedCampuses as $campus) {
                    array_push($results['campuses'], array(
                        'title' => get_the_title($campus),
                        'permalink' => get_the_permalink($campus)
                    ));
                }
            }

            array_push($results['programs'], array(
                'title'     => get_the_title(),
                'permalink' => get_the_permalink(),
                'id'        => get_the_ID()
            ));
        }

        if (get_post_type() == 'campus') {
            array_push($results['campuses'], array(
                'title'     => get_the_title(),
                'permalink' => get_the_permalink()
            ));
        }

        if (get_post_type() == 'event') {
            $eventDate = new DateTime(get_field('event_date'));
            $description = null;

            if (has_excerpt()) {
                $description = get_the_excerpt();
            } else {
                $description = wp_trim_words( get_the_content(), 18 );
            }

            array_push($results['events'], array(
                'title'     => get_the_title(),
                'permalink' => get_the_permalink(),
                'month'     => $eventDate->format('M'),
                'day'       => $eventDate->format('d'),
                'description' => $description
            ));
        }
        
    }


    # CUSTOM QUERY TO CREATE RELATIONSHIP BETWEEN RELATED POST TYPE #

    /**
     *  CASE SENARIO 1:
     * 
     *  What if Biology program/subject has a different branches? i.e: Human Biology, Marine Biology, Animal Biology etc.....
     *  This value=>$results['programs'][0]['id'] is not suitable because it just looks for the first element in the array
     */

     /* == The Solution == */

     if ($results['programs']) { # Checks if theres indeed a program to find a relationship with.

        # Create an array with a 'relation' => 'OR' filter:
        $programsMetaQuery = array('relation' => 'OR');

        # Next, Loop the $results['programs'] array at the top to have access to the current item using the variable "$item".
        foreach($results['programs'] as $item) {

        # Next, use ARRAY_PUSH() to dynamically add the individual element of "$results['programs']" array that we are looping in inside of "$programsMetaQuery" array. 
        # Using the "$item" variable, we now have access to the data of "$results['programs']" array.
            array_push($programsMetaQuery, array(
                'key'     => 'related_program',
                'compare' => 'LIKE',
                'value'   => '"' . $item['id'] . '"'
            ));
        }

        // Programs to Professors Relationship Query.
        $programRelationshipQuery = new WP_Query(array(
            'post_type'    => array('professor', 'event'),
            'meta_query'   => $programsMetaQuery # Array that holds the data element of $results['programs'] with a 'OR' relation filter.
            )
        );

        while($programRelationshipQuery->have_posts()) { $programRelationshipQuery->the_post();

            # Professor Post Type
            if (get_post_type() == 'professor') {
                array_push($results['professors'], array(
                    'title'     => get_the_title(),
                    'permalink' => get_the_permalink(),
                    'image'     => get_the_post_thumbnail_url(0, 'professorLandscape')
                ));
            }

            # Event Post Type
            if (get_post_type() == 'event') {
                $eventDate = new DateTime(get_field('event_date'));
                $description = null;
    
                if (has_excerpt()) {
                    $description = get_the_excerpt();
                } else {
                    $description = wp_trim_words( get_the_content(), 18 );
                }
    
                array_push($results['events'], array(
                    'title'     => get_the_title(),
                    'permalink' => get_the_permalink(),
                    'month'     => $eventDate->format('M'),
                    'day'       => $eventDate->format('d'),
                    'description' => $description
                ));
            }

        }

        /**
         *  CASE SENARIO 2:
         * 
         *  If the name or title of the 'Program' post type is also present or used within the content body of this page?
         *  If we perform a search for that specific words, there will be a posible duplicate results
         *  Because we already have a main query top above named "$mainQuery"
         *  And here below we create another query using "meta_query" parameter to connect the relationship of related post type
         * 
         *  To avoid duplicate display of data, we use:
         *  ARRAY_UNIQUE()-> Remove duplicate values from an array.
         *  ARRAY_VALUES()-> Optional, if you want to display just the value and not include the key.
         */

        $results['professors'] = array_values(array_unique($results['professors'], SORT_REGULAR));
        $results['events'] = array_values(array_unique($results['events'], SORT_REGULAR));
        
     }

    return $results;

     /**
         *  CASE SENARIO 3:
         * 
         *  If the name or title of the 'Program' page is also present or used within the content body of their related page?
         *  If we perform a search for that specific words, Wordpress by default will look to the default content editor and will display
         *  that specific title or name mentioned.
         * 
         *  To filter the search, so that the Program title or name will display to the page, 
         *  we will use custom field for content body of the 'Program' post type.
         *  Because Wordpress by default will not look the custom field created in Advanced Custom Field plugin. I
         *  Wordpress looks the default content editor.
         * 
     */

    wp_reset_postdata();

    

    // return $professors->posts; // Returns all posts related to 'Professor' post type.
}
