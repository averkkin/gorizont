<?php
/**
 * Архив отзывов: все отзывы сеткой, скрины открываются в полном размере.
 */

get_header();
?>
<main id="primary" class="site-main">
    <section class="tour-reviews tour-reviews--page" aria-labelledby="reviews-page-title">
        <div class="container">
            <div class="tour-reviews__header">
                <h1 id="reviews-page-title" class="h2">Мы ценим ваше доверие</h1>
            </div>

            <?php if (have_posts()) : ?>
                <div class="reviews-wall">
                    <?php while (have_posts()) : the_post(); ?>
                        <div class="reviews-wall__item">
                            <?php get_template_part('template-parts/review-card'); ?>
                        </div>
                    <?php endwhile; ?>
                </div>

                <?php the_posts_pagination(array('mid_size' => 1)); ?>
                <?php get_template_part('template-parts/review-lightbox'); ?>
            <?php else : ?>
                <p>Отзывов пока нет.</p>
            <?php endif; ?>
        </div>
    </section>
</main>
<?php
get_footer();
