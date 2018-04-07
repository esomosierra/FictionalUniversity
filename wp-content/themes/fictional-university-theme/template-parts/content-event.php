<div class="event-summary">
    <a class="event-summary__date t-center" href="<?php the_permalink() ?>">
        <span class="event-summary__month">
            <?php
                $eventDate = new DateTime(get_field('event_date')); # Pull out and display the date field in Event custom post type.
                echo $eventDate->format('M');
            ?>
        </span>
        <span class="event-summary__day"><?php echo $eventDate->format('d'); ?></span>  
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
            <a href="<?php the_permalink() ?>" class="nu gray">Learn more</a>
        </p>
    </div>
</div>