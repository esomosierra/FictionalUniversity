<?php get_header();

while(have_posts()) {  the_post(); pageBanner(); ?>
    
    <div class="container container--narrow page-section">
        <div class="metabox metabox--position-up metabox--with-home-link">
            <p>
                <a class="metabox__blog-home-link" href="<?php echo get_post_type_archive_link('campus'); ?>"><i class="fa fa-home" aria-hidden="true"></i>
                All Campuses</a>
                <span class="metabox__main"><?php the_title(); ?></span>
            </p>
        </div>

        <div class="generic-content">
            <?php the_content() ?>
            <div class="acf-map">
                <?php $mapLocation = get_field('map_location') ?>
                <div data-lat="<?php echo $mapLocation['lat'] ?>" data-lng="<?php echo $mapLocation['lang'] ?>" class="marker">
                <h3><?php the_title(); ?></h3>
                <?php echo $mapLocation['address']; ?>
                </div>

            </div>
        </div>

        <?php

            # === CUSTOM QUERY TO BUILD RELATIONSHIP BETWEEN 'PROGRAM' AND 'CAMPUS' CUSTOM POST TYPE === #
            
            #  CUSTOM QUERY TO DISPLAY THE 'PROGRAM' POST TYPE IN SINGLE CAMPUS PAGE 

            $argsPrograms = array(
                'posts_per_page'  => -1,  # -1,  Will show all post of specific category or custom post type
                'post_type'       => 'program',
                'orderby'         => 'title',
                'order'           => 'ASC',
                'meta_query'      => array(

                    # These arrays serves as a Filter in the database on what to display to the page.

                    # Algorithm:
                    # 1.) If the array of 'related_program' 'LIKE' or CONTAINS the id number of the current program post, display it to the page.
                    array(
                        'key' => 'related_campus',
                        'compare' =>   'LIKE',
                        'value' => '"' . get_the_ID() . '"'
                    )
                )
            );

            $relatedPrograms = new WP_Query($argsPrograms);

            if ($relatedPrograms->have_posts()) {
                echo '<hr class="section-break">';
                echo '<h2 class="headline headline--medium">Programs available at this campus</h2>';
                # Loop the Event post type:
                    echo '<ul class="min-list link-list">';
                    while ($relatedPrograms->have_posts()) { $relatedPrograms->the_post(); ?>
                        <li>
                            <a href="<?php the_permalink() ?>"><?php the_title(); ?></a>
                        </li>
                    <?php }
                    echo '</ul>';
            }

            wp_reset_postdata();
        ?>
        
    </div>
<?php }

get_footer();

?>