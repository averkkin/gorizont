<?php
/*
 * Template Name: Юридический документ
 */

get_header();

$parent_id  = wp_get_post_parent_id(get_the_ID());
$legal_docs = $parent_id ? gorizont_legal_docs($parent_id) : array();
?>
<main id="primary" class="site-main">
    <section class="legal legal--doc" aria-labelledby="legal-title">
        <div class="container">
            <nav class="legal__breadcrumbs" aria-label="Хлебные крошки">
                <a href="<?php echo esc_url(home_url('/')); ?>">Главная</a>
                <span aria-hidden="true">/</span>
                <?php if ($parent_id) : ?>
                    <a href="<?php echo esc_url(get_permalink($parent_id)); ?>"><?php echo esc_html(get_the_title($parent_id)); ?></a>
                    <span aria-hidden="true">/</span>
                <?php endif; ?>
                <span aria-current="page"><?php the_title(); ?></span>
            </nav>

            <div class="legal-doc">
                <article class="legal-doc__body">
                    <header class="legal__header">
                        <p class="legal__label">Документ</p>
                        <h1 id="legal-title" class="legal__title legal__title--doc"><?php the_title(); ?></h1>
                    </header>

                    <div class="legal-doc__content">
                        <?php
                        while (have_posts()) :
                            the_post();
                            the_content();
                        endwhile;
                        ?>
                    </div>
                </article>

                <?php if ($legal_docs) : ?>
                    <aside class="legal-doc__aside" aria-label="Другие документы">
                        <p class="legal__label">Все документы</p>
                        <ol class="legal-doc__nav">
                            <?php foreach ($legal_docs as $doc) : ?>
                                <li>
                                    <a href="<?php echo esc_url(get_permalink($doc)); ?>"<?php echo $doc->ID === get_queried_object_id() ? ' class="is-active" aria-current="page"' : ''; ?>>
                                        <?php echo esc_html(get_the_title($doc)); ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ol>
                    </aside>
                <?php endif; ?>
            </div>
        </div>
    </section>
</main>
<?php
get_footer();
