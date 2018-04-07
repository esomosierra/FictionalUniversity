<?php get_header();

while(have_posts()) { 
    the_post(); 
    pageBanner();
?>


    <div class="container container--narrow page-section">

        <div class="generic-content">
            
            <div class="row group">

                <div class="one-third"><?php the_post_thumbnail('professorPortrait') ?></div>

                <div class="two-third">
                    <?php

                        # === CUSTOM QUERY TO BUILD RELATIONSHIP BETWEEN 'PROFESSOR' AND 'LIKE' CUSTOM POST TYPE === #

                        # CUSTOM QUERY TO DISPLAY THE 'LIKE' POST TYPE IN SINGLE PROFESSOR PAGE #
                        
                        $likeCount = new WP_Query(array(
                            'post_type'  => 'like',
                            'meta_query' => array(
                                array(
                                    'key'     => 'liked_professor_id',
                                    'compare' => '=',
                                    'value'   => get_the_ID() # Current Professor that user is viewing.
                                )
                            )
                        ));

                        # THIS CUSTOM QUERY WILL ONLY CONTAINS RESULTS IF THE CURRENT USER IS ALREADY LIKED THE CURRENT PROFESSOR. 

                        $is_existLike = 'no';

                        # Checks if the user is currently logged in before this custom query take effect.
                        if (is_user_logged_in()) {
                            $existQueryLike = new WP_Query(array(
                                'author'     => get_current_user_id(), # Whatever user is currently viewing the page.
                                'post_type'  => 'like',
                                'meta_query' => array(
                                    array(
                                        'key'     => 'liked_professor_id',
                                        'compare' => '=',
                                        'value'   => get_the_ID()
                                    )
                                )
                            ));
    
                            if ($existQueryLike->found_posts) { # Returns TRUE if '$existQuery' has already liked. FALSE otherwise
                                $is_existLike = 'yes';
                            }
                        }


                    ?>
                    
                    <span class="like-box" data-like="<?php echo $existQueryLike->posts[0]->ID; ?>" data-exists="<?php echo $is_existLike; ?>"
                    data-professor="<?php the_ID(); ?>">
                        <i class="fa fa-heart-o" aria-hidden="true"></i>
                        <i class="fa fa-heart" aria-hidden="true"></i>
                        <span class="like-count"><?php echo $likeCount->found_posts; ?></span>
                    </span>
                    <!-- 
                        data-like="<?php //echo $existQuery->posts[0]->ID ?> -> Return the ID of the post from array results '$existQueryLike' custom query.               This will then use in data: {'like': currentLikeBox.attr('data-like')} ajax request to DELETE Like post.
                        
                        data-exists="<?php //echo $is_existLike; ?> -> -> Return 'yes' if user already liked in. 'no' if does not.

                        data-professor="<?php //the_ID(); ?> -> Is the user currently logged in and viewing the specific professor.
                    -->

                    <?php the_content(); ?>
                </div>

            </div>

        </div>

        <?php

            $relatedPrograms = get_field('related_program');
            
            if ($relatedPrograms) {
                echo '<hr class="section-break">';
                echo '<h2 class="headline headline--medium">Subject(s) Taught:</h2>';
                echo '<ul class="link-list min-list">';
                foreach($relatedPrograms as $program) { ?>
                    <li><a href="<?php echo get_the_permalink($program) ?>"><?php echo get_the_title($program) ?></a></li>
                <?php }
                echo '</ul>';
            }
        ?>
    </div>
<?php }

get_footer();

?>