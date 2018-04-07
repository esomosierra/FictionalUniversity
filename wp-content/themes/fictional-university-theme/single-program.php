<?php get_header();

while(have_posts()) {  the_post(); pageBanner(); ?>
    
    <div class="container container--narrow page-section">
        <div class="metabox metabox--position-up metabox--with-home-link">
            <p>
                <a class="metabox__blog-home-link" href="<?php echo get_post_type_archive_link('program'); ?>"><i class="fa fa-home" aria-hidden="true"></i>
                All Programs</a>
                <span class="metabox__main"><?php the_title(); ?></span>
            </p>
        </div>

        <div class="generic-content"><?php the_field('main_body_content') ?></div>

        <?php

            # === CUSTOM QUERY TO BUILD RELATIONSHIP BETWEEN 'PROFESSOR' AND 'PROGRAM' CUSTOM POST TYPE === #

            # CUSTOM QUERY TO DISPLAY THE 'PROFESSOR' POST TYPE IN SINGLE 'PROGRAM' PAGE === #

            $args = array(
                'posts_per_page'  => 2,  # -1,  Will show all post of specific category or custom post type
                'post_type'       => 'professor',
                'orderby'         => 'title',
                'order'           => 'ASC',
                'meta_query'      => array(

                    # These arrays serves as a Filter in the database on what to display to the page.

                    # Algorithm:
                    # 1.) If the array of 'related_program' 'LIKE' or CONTAINS the id number of the current program post, display it to the page.
                    array(
                        'key' => 'related_program',
                        'compare' =>   'LIKE',
                        'value' => '"' . get_the_ID() . '"'
                    )
                )
            );

            $relatedProfessors = new WP_Query($args);

            if ($relatedProfessors->have_posts()) {
                echo '<hr class="section-break">';
                echo '<h2 class="headline headline--medium">' . get_the_title() . ' Professor(s)</h2>';
                # Loop the Event post type:
                if ($relatedProfessors->have_posts()) {
                    echo '<ul class="professor-cards">';
                    while ($relatedProfessors->have_posts()) { $relatedProfessors->the_post() ?>
                        <li class="professor-card__list-item">
                            <a class="professor-card" href="<?php the_permalink() ?>">
                                <img src="<?php the_post_thumbnail_url() ?>" alt="" class="professor-card__image">
                                <span class="professor-card__name"><?php the_title() ?></span>
                            </a>
                        </li>
                    <?php }
                    echo '</ul>';
                }
            }

            wp_reset_postdata();

            # === CUSTOM QUERY TO DISPLAY THE 'EVENT' POST TYPE IN THE SINGLE PROGRAM PAGE === #

            $today = date('Ymd');
            $args = array(
                'posts_per_page'  => 2,  # -1,  Will show all post of specific category or custom post type
                'post_type'       => 'event',
                'meta_key'        => 'event_date', # Name of custom field in custom post type 'Event'.
                'orderby'         => 'meta_value_num', # Returns the number type value. "meta_value" - Returns the string type value.
                'order'           => 'ASC',
                'meta_query'      => array(

                    # These arrays serves as a Filter in the database on what to display to the page.

                    # Algorithm:
                    # 1.) If the array of 'event_date' greater than or equal '>=' to current date '$today' and has a numeric type, display it to the page.
                    array(
                        'key'     => 'event_date', # Name of custom field in custom post type 'Event'.
                        'compare' => '>=', # ">=" Today or in the future. '<' is passed events so it should display in upcoming event.
                        'value'   => $today,
                        'type'    => 'numeric' # Because we are comparing numbers.
                    ),
                    # 2.) If the array of 'related_program' 'LIKE' or CONTAINS the id number of the current program post, display it to the page.
                    array(
                        'key' => 'related_program',
                        'compare' =>   'LIKE',
                        'value' => '"' . get_the_ID() . '"'
                    )
                )
            );

            $relatedPrograms = new WP_Query($args);

            if ($relatedPrograms->have_posts()) {
                echo '<hr class="section-break">';
                echo '<h2 class="headlin headlin--medium">Upcoming ' . get_the_title() . ' Events</h2>';
                # Loop the Event post type:
                if ($relatedPrograms->have_posts()) {
                    while ($relatedPrograms->have_posts()) { 
                        $relatedPrograms->the_post();
                        get_template_part('template-parts/content-event'); // Calling 'content-event.php' from folder 'template-parts'.
                    }
                }
            }
            
            wp_reset_postdata();


            /* Displaying Related Campuses */
            $relatedCampuses = get_field('related_campus');

            if ($relatedCampuses) {
                echo '<hr class="section-break">';
                echo '<h2 class="headline headline--medium">' . get_the_title() . ' is Available at these campus(es):</h2>';
                echo '<ul class="min-list link-list">';
                foreach($relatedCampuses as $campus) { ?>
                    <li><a href="<?php get_the_permalink($campus); ?>"><?php echo get_the_title($campus); ?></a></li>
                <?php }
                echo '</ul>';
            }

        ?>
        
    </div>
<?php }

get_footer();

?>