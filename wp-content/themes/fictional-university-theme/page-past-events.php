<?php get_header(); 

pageBanner(array(
    'title' => 'Past Events',
    'subtitle' => 'Recap of our past events.'
));

?>

<div class="container container--narrow page-section">
  <?php
      
      # Custom Query to display the Past Events in 'Event' post type in the past events page:
      $today = date('Ymd');
      $args = array(
          # get_query_var('paged', 1) -> Get all sorts of information about the current url. i.e 'paged'
          # 1 is the fallback or default value to be use in case wordpress can't find the paged number dynamically.
          'paged'           => get_query_var('paged', 1),
          //'posts_per_page'  => 1,  # -1,  Will show all post of specific category or custom post type
          'post_type'       => 'event',
          'meta_key'        => 'event_date', # Name of custom field in custom post type 'Event'.
          'orderby'         => 'meta_value_num', # Returns the number type value. "meta_value" - Returns the string type value.
          'order'           => 'ASC',
          'meta_query'      => array(
              array(
                  'key'     => 'event_date', # Name of custom field in custom post type 'Event'.
                  'compare' => '<', # "<" is passed events so it should display in past events page.
                  'value'   => $today,
                  'type'    => 'numeric' # Because we are comparing numbers.
              )
          )
      );

      # CUSTOM QUERY TO DISPLAY PAST EVENTS:
      $pastEvents = new WP_Query($args);

      if ($pastEvents->have_posts()) {
            while($pastEvents->have_posts()) { 
                $pastEvents->the_post();
                get_template_part('template-parts/content-event'); // Calling 'content-event.php' from folder 'template-parts'.
            }
      }

      echo paginate_links(array( # Custom query pagination
          'total' => $pastEvents->max_num_pages
      ));

      wp_reset_postdata();
  ?>

</div>

<?php get_footer(); ?>
