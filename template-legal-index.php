<?php
/*
 * Template Name: Юридические документы
 */

get_header();

$legal_docs = gorizont_legal_docs(get_the_ID());
?>
<main id="primary" class="site-main">
    <section class="legal" aria-labelledby="legal-title">
        <div class="container">
            <nav class="legal__breadcrumbs" aria-label="Хлебные крошки">
                <a href="<?php echo esc_url(home_url('/')); ?>">Главная</a>
                <span aria-hidden="true">/</span>
                <span aria-current="page"><?php the_title(); ?></span>
            </nav>

            <header class="legal__header">
                <p class="legal__label">Правовая информация</p>
                <h1 id="legal-title" class="legal__title">
                    <?php the_title(); ?>
                    <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/icons/line-decor.svg'); ?>" alt="" class="h2-decor" aria-hidden="true">
                </h1>
            </header>

            <?php if ($legal_docs) : ?>
                <ol class="legal-list">
                    <?php foreach ($legal_docs as $index => $doc) : ?>
                        <li class="legal-list__item">
                            <span class="legal-list__num"><?php echo esc_html(sprintf('%02d', $index + 1)); ?></span>
                            <h2 class="legal-list__title"><?php echo esc_html(get_the_title($doc)); ?></h2>
                            <a class="legal-list__link" href="<?php echo esc_url(get_permalink($doc)); ?>">
                                Смотреть документ
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ol>
            <?php else : ?>
                <p>Документы скоро появятся.</p>
            <?php endif; ?>
        </div>
    </section>
</main>
<?php
get_footer();
