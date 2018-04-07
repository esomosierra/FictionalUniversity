<?php

add_action('rest_api_init', 'universityLikeRoutes');

function universityLikeRoutes(){
    register_rest_route('university/v1', 'manageLike', array(
        'methods' => 'POST',
        'callback' => createLike
    ));

    register_rest_route('university/v1', 'manageLike', array(
        'methods' => 'DELETE',
        'callback' => deleteLike
    ));
}

function createLike($data) {

    # RESTRICTING USER TO CREATE LIKE #
    
    if (is_user_logged_in()) {

        // This is the javascript ajax request in data: 'professorId': currentLikeBox.data('professor') in Like.js file.
        $professor = sanitize_text_field($data['professorId']);

        # CUSTOM QUERY TO BUILD RELATIONSHIP BETWEEN 'LIKE' AND 'PROFESSOR' CUSTOM POST TYPE IN SINGLE PROFESSOR PAGE.
        $existQueryLike = new WP_Query(array(
            'author'     => get_current_user_id(), # Whatever user is currently viewing the page.
            'post_type'  => 'like',
            'meta_query' => array(
                array(
                    'key'     => 'liked_professor_id',
                    'compare' => '=',
                    'value'   => $professor
                )
            )
        ));
        
        # If the current user has not already like the current professor which equal to '0',
        # And If the current user is doing and creating like exclusively just for this custom post type of "Professor" that has an ID on it, THEN:
        if ($existQueryLike->found_posts == 0 AND get_post_type($professor) == 'professor') {
            # CREATING A POST IN WORDPRESS #
            return wp_insert_post(array( // This will return an 'ID' if wp_insert_post() is successfull or true.
                // "IMPORTANT NOTE" : NONCE IS NEEDED IN AJAX REQUEST BEFORE THIS WILL TAKE EFFECT PROPERLY.
                // LINE: xhr.setRequestHeader('X-WP-None', universityData.nonce) in createLike() from Like.js file.
                'post_type'    => 'like',
                'post_status'  => 'publish',
                'post_title'   => '',
                'meta_input'   => array(
                    'liked_professor_id' => $professor
                )

            ));
        } else {
            die('Invalied professor id!');
        }

    } else {
        die('Only logged in users can create a like');
    }

    

}

function deleteLike($data) { // AJAX Request from data: {'like': currentLikeBox.attr('data-like')} in Like.js
    $likeId = sanitize_text_field($data['like']);

    # Checks if doing the delete is actually the current user and 
    # get_post_field('post_author', $likeId)
    # 'post_author' -> Is what information you want about that post.
    # '$likeId' -> The ID of the post that you want information about.
    # get_post_type($likeId) -> Whatever ID they trying to delete in 'like' custom post type
    if (get_current_user_id() == get_post_field('post_author', $likeId) AND get_post_type($likeId) == 'like') {
        wp_delete_post($likeId, true);
        return 'Congrats, like deleted.';
    } else {
        die('You do not have permission to delete that');
    }
}