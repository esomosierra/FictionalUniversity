<?php get_header(); ?>

<!-- NOTE: HOMEPAGE IS POWERED BY THIS FRONT-PAGE.PHP -->

<div class="page-banner">
  <div class="page-banner__bg-image" style="background-image: url(<?php echo get_theme_file_uri('images/library-hero.jpg') ?>);"></div>
    <div class="page-banner__content container t-center c-white">
      <h1 class="headline headline--large">Welcome!</h1>
      <h2 class="headline headline--medium">We think you&rsquo;ll like it here.</h2>
      <h3 class="headline headline--small">Why don&rsquo;t you check out the <strong>major</strong> you&rsquo;re interested in?</h3>
      <a href="<?php echo get_post_type_archive_link('program') ?>" class="btn btn--large btn--blue">Find Your Major</a>
    </div>
  </div>

  <div class="full-width-split group">
    <div class="full-width-split__one">
      <div class="full-width-split__inner">
        <h2 class="headline headline--small-plus t-center">Upcoming Events</h2>
        
        <?php
            # CUSTOM QUERY TO DISPLAY THE 'EVENT' POST TYPE IN THE FRONT PAGE:
            $today = date('Ymd');
            $args = array(
                'posts_per_page'  => 2,  # -1,  Will show all post of specific category or custom post type
                'post_type'       => 'event',
                'meta_key'        => 'event_date', # Name of custom field in custom post type 'Event'.
                'orderby'         => 'meta_value_num', # Returns the number type value. "meta_value" - Returns the string type value.
                'order'           => 'ASC',
                'meta_query'      => array(
                    array(
                        'key'     => 'event_date', # Name of custom field in custom post type 'Event'.
                        'compare' => '>=', # ">=" Today or in the future. '<' is passed events so it should display in upcoming event.
                        'value'   => $today,
                        'type'    => 'numeric' # Because we are comparing numbers.
                    )
                )
            );

            $homepageEvents = new WP_Query($args);

            # Loop the Event post type:
            if ($homepageEvents->have_posts()) {
                while ($homepageEvents->have_posts()) { 
                  $homepageEvents->the_post();
                  get_template_part('template-parts/content', 'event'); // Calling 'content-event.php' from folder 'template-parts'.
                }
            }

            wp_reset_postdata();
        ?>
        
        <p class="t-center no-margin"><a href="<?php echo get_post_type_archive_link('event') ?>" class="btn btn--blue">View All Events</a></p>

      </div>
    </div>
    <div class="full-width-split__two">
      <div class="full-width-split__inner">
        <h2 class="headline headline--small-plus t-center">From Our Blogs</h2>
        <?php

            # CUSTOM QUERY TO DISPLAY REGULAR BLOG POSTS:
            $homepagePosts = new WP_Query(array(
                'posts_per_page' => 2
            ));

            if ($homepagePosts->have_posts()) {
                while($homepagePosts->have_posts()) {$homepagePosts->the_post(); ?>
                    <div class="event-summary">
                        <a class="event-summary__date event-summary__date--beige t-center" href="<?php the_permalink() ?>">
                          <span class="event-summary__month"><?php the_time('M') ?></span>
                          <span class="event-summary__day"><?php the_time('d') ?></span>  
                        </a>
                        <div class="event-summary__content">
                          <h5 class="event-summary__title headline headline--tiny">
                              <a href="<?php the_permalink() ?>"><?php the_title() ?></a>
                          </h5>
                          <p>
                              <?php  
                                  if (has_excerpt()) { # Checks if 'Excerpt' is activated in post 'Screen Option' checkbox and actually has content in the excerpt field. If there is? Show it.
                                      echo get_the_excerpt();
                                  } else {
                                      echo wp_trim_words( get_the_content(), 18 ); # Else: Trim the default content.
                                  }
                              ?> 
                              <a href="<?php the_permalink() ?>" class="nu gray">Read more</a>
                          </p>
                        </div>
                    </div>
                <?php }
            } else {
              echo 'No post found!';
            }

            wp_reset_postdata(); # Reset the post query. Best Practice.

        ?>
        
        <p class="t-center no-margin"><a href="<?php echo site_url('/blog') ?>" class="btn btn--yellow">View All Blog Posts</a></p>
      </div>
    </div>
  </div>

  <div class="hero-slider">
  <div class="hero-slider__slide" style="background-image: url(<?php echo get_theme_file_uri('images/bus.jpg'); ?>);">
    <div class="hero-slider__interior container">
      <div class="hero-slider__overlay">
        <h2 class="headline headline--medium t-center">Free Transportation</h2>
        <p class="t-center">All students have free unlimited bus fare.</p>
        <p class="t-center no-margin"><a href="#" class="btn btn--blue">Learn more</a></p>
      </div>
    </div>
  </div>
  <div class="hero-slider__slide" style="background-image: url(<?php echo get_theme_file_uri('images/apples.jpg'); ?>);">
    <div class="hero-slider__interior container">
      <div class="hero-slider__overlay">
        <h2 class="headline headline--medium t-center">An Apple a Day</h2>
        <p class="t-center">Our dentistry program recommends eating apples.</p>
        <p class="t-center no-margin"><a href="#" class="btn btn--blue">Learn more</a></p>
      </div>
    </div>
  </div>
  <div class="hero-slider__slide" style="background-image: url(<?php echo get_theme_file_uri('images/bread.jpg'); ?>);">
    <div class="hero-slider__interior container">
      <div class="hero-slider__overlay">
        <h2 class="headline headline--medium t-center">Free Food</h2>
        <p class="t-center">Fictional University offers lunch plans for those in need.</p>
        <p class="t-center no-margin"><a href="#" class="btn btn--blue">Learn more</a></p>
      </div>
    </div>
  </div>
</div>

<?php get_footer(); ?>
