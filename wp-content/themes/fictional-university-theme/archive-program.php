<?php get_header(); 

pageBanner(array(
    'title'     => 'All Programs',
    'subtitle'  => 'There is something for everyone. Have a look around.'
));

?>

<div class="container container--narrow page-section">
  
<ul class="link-list min-list">
<?php
    if (have_posts()) {
        while(have_posts()) { the_post(); ?>
            <li><a href="<?php the_permalink() ?>"><?php the_title() ?></a></li>
        <?php }
    } else {
        echo 'No post found!';
    }

    echo paginate_links();
?>
</ul>

</div>

<?php get_footer(); ?>
