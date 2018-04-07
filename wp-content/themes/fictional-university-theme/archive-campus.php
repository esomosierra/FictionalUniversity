<?php get_header(); 

pageBanner(array(
    'title'     => 'Our Campuses',
    'subtitle'  => 'We have several conveniently located campuses.'
));

?>

<div class="container container--narrow page-section">
  
    <div class="acf-map">
        <?php
            if (have_posts()) {
                while(have_posts()) { 
                    the_post(); 
                    $mapLocation = get_field('map_location');
                ?>
                    <div data-lat="<?php echo $mapLocation['lat'] ?>" data-lng="<?php echo $mapLocation['lang'] ?>" class="marker">
                    <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                    <?php echo $mapLocation['address']; ?>
                    </div>
                <?php }

                print_r($mapLocation);
            }
        ?>
    </div>

</div>

<?php get_footer(); ?>
