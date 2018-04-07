<?php get_header();

while(have_posts()) { 
    the_post(); 
    pageBanner(/*array( # Argument to be use in pageBanner() function in functions.php
        'title' => 'Hello there this is the title',
        'subtitle' => 'Hi this is a subtitle',
        'photo' => 'https://images.pexels.com/photos/132037/pexels-photo-132037.jpeg?auto=compress&cs=tinysrgb&h=350'
    )*/); 
    ?>    

    <div class="container container--narrow page-section">
        
        <?php
            /*
                THIS IS THE BREADCRUMB SECTION OF THE PAGE:

                === Getting the Parent ID of a child page ===

                get_the_ID() -> Returns the numerical ID of the current page.
                wp_get_post_parent_id() -> Returns the numerical PARENT ID of the current page. @param: 'get_the_ID()'
                It wil return 0 or FALSE if a page does not have a parent page.
            */
            $theParent = wp_get_post_parent_id(get_the_ID());

            if ( $theParent ) { # Checks if the page has a parent page. If TRUE show the breadcrumb, Else do not show it: ?>
                <div class="metabox metabox--position-up metabox--with-home-link">
                    <p><a class="metabox__blog-home-link" href="<?php echo get_permalink($theParent); ?>"><i class="fa fa-home" aria-hidden="true"></i>
                    Back to <?php echo get_the_title($theParent) ?></a>
                    <span class="metabox__main"><?php the_title() ?></span></p>
                </div>
            <?php }
        ?>

        
        
        <!-- This sidebar menu of a page will show, If the page is a parent and has a child pages. -->
        <?php
            # get_pages() -> Returns an array of pages
            # @param 'child_of => get_the_ID' -> Returns the parent ID of the current page if it is a child page
            $testArray = get_pages(array('child_of' => get_the_ID() ));
            
            if ($theParent || $testArray) : ?>
                <div class="page-links">
                <h2 class="page-links__title">
                    <a href="<?php echo get_permalink($theParent) ?>"><?php echo get_the_title($theParent) ?></a>
                </h2>
                <ul class="min-list">
                <?php

                if ($theParent) { # Checks if the page is a parent page, If it is?
                    $findChildrenOf = $theParent; // Get and store the value of the parent id in a variable '$findChildrenOf' to be use in 'wp_list_pages()' function's parameters 'child_of'.
                } else {
                        $findChildrenOf = get_the_ID();
                }

                # Display the list of parent's child pages.
                wp_list_pages(array(
                        'title_li' => NULL,
                        'child_of' => $findChildrenOf,
                        'sort_column' => 'menu_order' // Displays the page order in the front end.
                ));

                ?>

                </ul>
                </div>
        <?php endif ?>
        

        <div class="generic-content">
            <?php get_search_form(); ?>
        </div>

    </div>

<?php }

get_footer();

?>