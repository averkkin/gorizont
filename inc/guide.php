<?php
/**
 * Страница «Обучение» в админке: видеоинструкции и пошаговые тексты со скриншотами.
 *
 * Тексты и главы — inc/guide/content.json (собирается из сценариев роликов, video-guide/build_guide.py).
 * Видео и картинки лежат вне public_html (wordpress_ew7ca/guide-media/) и отдаются только
 * вошедшему в админку пользователю через admin-post.php — по прямой ссылке их не открыть.
 */

if (!defined('ABSPATH')) {
    exit;
}

const GORIZONT_GUIDE_CAP = 'edit_posts';

function gorizont_guide_media_dir() {
    return trailingslashit(dirname(untrailingslashit(ABSPATH))) . 'guide-media/';
}

function gorizont_guide_media_url($file) {
    return add_query_arg(array('action' => 'gorizont_guide_media', 'f' => rawurlencode($file)), admin_url('admin-post.php'));
}

function gorizont_guide_content() {
    $path = get_template_directory() . '/inc/guide/content.json';
    $data = is_readable($path) ? json_decode((string) file_get_contents($path), true) : null;
    return is_array($data) && !empty($data['sections']) ? $data : array('sections' => array());
}

add_action('admin_menu', function () {
    add_menu_page('Обучение', 'Обучение', GORIZONT_GUIDE_CAP, 'gorizont-guide', 'gorizont_guide_render', 'dashicons-welcome-learn-more', 2);
});

/**
 * Отдача видео и картинок только вошедшим (admin-post.php без nopriv-обработчика), с поддержкой Range для перемотки.
 */
add_action('admin_post_gorizont_guide_media', function () {
    if (!current_user_can(GORIZONT_GUIDE_CAP)) {
        status_header(403);
        exit;
    }
    $file = isset($_GET['f']) ? sanitize_file_name(wp_unslash($_GET['f'])) : '';
    if (!preg_match('/^[a-z0-9-]+\.(mp4|jpg)$/', $file)) {
        status_header(404);
        exit;
    }
    $path = gorizont_guide_media_dir() . $file;
    if (!is_file($path)) {
        status_header(404);
        exit;
    }

    $size = filesize($path);
    $start = 0;
    $end = $size - 1;
    $type = substr($file, -4) === '.mp4' ? 'video/mp4' : 'image/jpeg';

    if (function_exists('session_write_close')) {
        session_write_close();
    }
    while (ob_get_level()) {
        ob_end_clean();
    }
    set_time_limit(0);

    header('Content-Type: ' . $type);
    header('Accept-Ranges: bytes');
    header('Cache-Control: private, max-age=86400');
    header('X-Content-Type-Options: nosniff');

    if (isset($_SERVER['HTTP_RANGE']) && preg_match('/bytes=(\d*)-(\d*)/', $_SERVER['HTTP_RANGE'], $m)) {
        if ($m[1] === '' && $m[2] !== '') { // последние N байт
            $start = max(0, $size - (int) $m[2]);
        } else {
            $start = (int) $m[1];
            if ($m[2] !== '') {
                $end = min((int) $m[2], $size - 1);
            }
        }
        if ($start > $end || $start >= $size) {
            status_header(416);
            header("Content-Range: bytes */$size");
            exit;
        }
        status_header(206);
        header("Content-Range: bytes $start-$end/$size");
    } else {
        status_header(200);
    }
    header('Content-Length: ' . ($end - $start + 1));

    $fh = fopen($path, 'rb');
    fseek($fh, $start);
    $left = $end - $start + 1;
    while ($left > 0 && !feof($fh) && !connection_aborted()) {
        $chunk = fread($fh, (int) min(1048576, $left));
        echo $chunk;
        flush();
        $left -= strlen($chunk);
    }
    fclose($fh);
    exit;
});

function gorizont_guide_time($sec) {
    $sec = (int) round($sec);
    return sprintf('%d:%02d', intdiv($sec, 60), $sec % 60);
}

function gorizont_guide_plural($n, $one, $few, $many) {
    $n = abs((int) $n) % 100;
    $n1 = $n % 10;
    if ($n > 10 && $n < 20) {
        return $many;
    }
    if ($n1 > 1 && $n1 < 5) {
        return $few;
    }
    return $n1 === 1 ? $one : $many;
}

function gorizont_guide_render() {
    if (!current_user_can(GORIZONT_GUIDE_CAP)) {
        wp_die('Недостаточно прав');
    }
    $content = gorizont_guide_content();

    // Плоский список уроков с номерами — для навигации «предыдущий / следующий»
    $lessons = array();
    foreach ($content['sections'] as $section) {
        foreach ($section['videos'] as $video) {
            $video['section'] = $section['title'];
            $lessons[] = $video;
        }
    }
    $total = count($lessons);
    $minutes = (int) ceil(array_sum(array_column($lessons, 'duration')) / 60);
    ?>
    <div class="wrap gguide" data-gguide data-user="<?php echo (int) get_current_user_id(); ?>">
        <h1 class="screen-reader-text">Обучение</h1>

        <header class="gguide__hero">
            <div class="gguide__hero-text">
                <span class="gguide__eyebrow">Обучение</span>
                <h2>Как работать с сайтом</h2>
                <p>Короткие уроки по каждому разделу админки. Смотрите видео или читайте пошаговую инструкцию со скриншотами — как удобнее.</p>
            </div>
            <div class="gguide__hero-stats">
                <div><b><?php echo (int) $total; ?></b><span><?php echo esc_html(gorizont_guide_plural($total, 'урок', 'урока', 'уроков')); ?></span></div>
                <div><b><?php echo (int) $minutes; ?></b><span><?php echo esc_html(gorizont_guide_plural($minutes, 'минута', 'минуты', 'минут')); ?></span></div>
            </div>
        </header>

        <div class="gguide__layout">
            <aside class="gguide__side">
                <details class="gguide__menu" open data-gguide-menu>
                    <summary><span>Уроки</span><span class="gguide__menu-current" data-gguide-current></span></summary>
                    <nav aria-label="Уроки">
                        <?php $n = 0; foreach ($content['sections'] as $section) : ?>
                            <div class="gguide__group">
                                <span class="gguide__group-title"><?php echo esc_html($section['title']); ?></span>
                                <?php foreach ($section['videos'] as $video) : $n++; ?>
                                    <a class="gguide__link" href="#<?php echo esc_attr($video['id']); ?>" data-gguide-link="<?php echo esc_attr($video['id']); ?>">
                                        <span class="gguide__check" aria-hidden="true"><?php echo (int) $n; ?></span>
                                        <span class="gguide__link-title"><?php echo esc_html($video['title']); ?></span>
                                        <span class="gguide__link-time"><?php echo esc_html(gorizont_guide_time($video['duration'])); ?></span>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>
                    </nav>
                </details>
            </aside>

            <main class="gguide__main">
                <?php foreach ($lessons as $i => $video) :
                    $prev = $lessons[$i - 1] ?? null;
                    $next = $lessons[$i + 1] ?? null;
                    ?>
                    <article class="gguide__lesson" id="<?php echo esc_attr($video['id']); ?>" data-gguide-lesson="<?php echo esc_attr($video['id']); ?>" data-title="<?php echo esc_attr($video['title']); ?>">
                        <div class="gguide__lesson-head">
                            <div>
                            <span class="gguide__crumb"><?php echo esc_html($video['section']); ?> · урок <?php echo (int) $i + 1; ?> из <?php echo (int) $total; ?></span>
                            <h3><?php echo esc_html($video['title']); ?></h3>
                            <p><?php echo esc_html($video['desc']); ?></p>
                            </div>

                        <div class="gguide__toolbar">
                            <div class="gguide__switch" role="tablist" aria-label="Формат урока">
                                <button type="button" role="tab" aria-selected="true" data-gguide-tab="video"><span class="dashicons dashicons-controls-play"></span>Видео <small><?php echo esc_html(gorizont_guide_time($video['duration'])); ?></small></button>
                                <button type="button" role="tab" aria-selected="false" data-gguide-tab="text"><span class="dashicons dashicons-text-page"></span>Текст <small><?php echo count($video['chapters']); ?> <?php echo esc_html(gorizont_guide_plural(count($video['chapters']), 'шаг', 'шага', 'шагов')); ?></small></button>
                            </div>
                        </div>
                        </div>

                        <div class="gguide__pane" data-gguide-pane="video">
                            <div class="gguide__player">
                                <video controls playsinline preload="none" poster="<?php echo esc_url(gorizont_guide_media_url($video['poster'])); ?>" data-src="<?php echo esc_url(gorizont_guide_media_url($video['file'])); ?>"></video>
                            </div>
                            <div class="gguide__chapters">
                                <span class="gguide__chapters-title">Шаги в ролике — нажмите, чтобы перейти</span>
                                <ol>
                                    <?php foreach ($video['chapters'] as $c => $chapter) : ?>
                                        <li><button type="button" data-gguide-seek="<?php echo esc_attr($chapter['t']); ?>"><span class="gguide__t"><?php echo esc_html(gorizont_guide_time($chapter['t'])); ?></span><span><?php echo esc_html($chapter['title']); ?></span></button></li>
                                    <?php endforeach; ?>
                                </ol>
                            </div>
                        </div>

                        <div class="gguide__pane" data-gguide-pane="text" hidden>
                            <ol class="gguide__steps">
                                <?php foreach ($video['chapters'] as $c => $chapter) : ?>
                                    <li class="gguide__step">
                                        <div class="gguide__step-text">
                                            <span class="gguide__step-num"><?php echo (int) $c + 1; ?></span>
                                            <div>
                                                <h4><?php echo esc_html($chapter['title']); ?></h4>
                                                <p><?php echo esc_html($chapter['text']); ?></p>
                                            </div>
                                        </div>
                                        <button type="button" class="gguide__shot" data-gguide-zoom="<?php echo esc_url(gorizont_guide_media_url($chapter['img'])); ?>" aria-label="Увеличить скриншот: <?php echo esc_attr($chapter['title']); ?>">
                                            <img loading="lazy" src="<?php echo esc_url(gorizont_guide_media_url($chapter['img'])); ?>" alt="" width="1280" height="800">
                                            <span class="gguide__zoom"><span class="dashicons dashicons-search"></span></span>
                                        </button>
                                    </li>
                                <?php endforeach; ?>
                            </ol>
                        </div>

                        <nav class="gguide__pager" aria-label="Соседние уроки">
                            <?php if ($prev) : ?>
                                <a href="#<?php echo esc_attr($prev['id']); ?>" class="gguide__pager-prev"><span>← Предыдущий</span><b><?php echo esc_html($prev['title']); ?></b></a>
                            <?php else : ?><span></span><?php endif; ?>
                            <?php if ($next) : ?>
                                <a href="#<?php echo esc_attr($next['id']); ?>" class="gguide__pager-next"><span>Следующий урок →</span><b><?php echo esc_html($next['title']); ?></b></a>
                            <?php else : ?>
                                <span class="gguide__pager-end">Это последний урок. Если остался вопрос — напишите разработчику.</span>
                            <?php endif; ?>
                        </nav>
                    </article>
                <?php endforeach; ?>
            </main>
        </div>

        <dialog class="gguide__lightbox" data-gguide-lightbox>
            <form method="dialog"><button class="gguide__lightbox-close" aria-label="Закрыть">×</button></form>
            <img alt="">
        </dialog>
    </div>
    <?php
}

add_action('admin_enqueue_scripts', function ($hook) {
    if ($hook !== 'toplevel_page_gorizont-guide') {
        return;
    }
    $assets = get_template_directory_uri() . '/assets/';
    wp_register_style('gorizont-guide', false, array('dashicons'), '3');
    wp_enqueue_style('gorizont-guide');
    $css = <<<'CSS'
@font-face{font-family:GManrope;src:url(__A__fonts/Manrope-Regular.woff) format('woff');font-weight:400;font-display:swap}
@font-face{font-family:GManrope;src:url(__A__fonts/Manrope-Medium.woff) format('woff');font-weight:500;font-display:swap}
@font-face{font-family:GManrope;src:url(__A__fonts/Manrope-SemiBold.woff) format('woff');font-weight:600;font-display:swap}
@font-face{font-family:GManrope;src:url(__A__fonts/Manrope-Bold.woff) format('woff');font-weight:700;font-display:swap}
#wpcontent:has(.gguide){background:#f5f1ea}
.gguide{--acc:#F8C471;--acc-h:#f6cc88;--dark:#141414;--brown:#302822;--mut:#7E7E81;--line:#e7e0d6;--card:#fff;--r:18px;
  position:relative;isolation:isolate;margin:20px 20px 40px 0;font-family:GManrope,-apple-system,"Segoe UI",sans-serif;color:var(--dark)}
/* фирменные топографические линии по фону страницы, еле заметно */
#wpbody-content:has(.gguide){overflow-x:clip}
.gguide::before{content:"";position:absolute;inset:-20px 0 -40px -20px;z-index:-1;background:url(__A__img/icons/pattern.svg) center top/1920px auto repeat-y;opacity:.035;pointer-events:none}
.gguide *{box-sizing:border-box}
.gguide h2,.gguide h3,.gguide h4{font-family:inherit;color:inherit}
.gguide button{font-family:inherit}

/* Шапка: фирменный коричневый + топографические линии + зерно */
.gguide__hero{position:relative;isolation:isolate;overflow:hidden;display:flex;justify-content:space-between;align-items:flex-end;gap:32px;padding:34px 40px;border-radius:24px;color:#fff;background:var(--brown)}
.gguide__hero::before{content:"";position:absolute;inset:0;z-index:-1;background:var(--acc);opacity:.22;
  -webkit-mask:url(__A__img/icons/pattern.svg) right -260px top -420px/1500px auto no-repeat;mask:url(__A__img/icons/pattern.svg) right -260px top -420px/1500px auto no-repeat}
.gguide__hero::after{content:"";position:absolute;inset:0;z-index:-1;background:url(__A__img/images/texture.png) repeat;background-size:134px;opacity:.3;mix-blend-mode:overlay;pointer-events:none}
.gguide__eyebrow{display:inline-block;padding:5px 12px;border-radius:99px;background:var(--acc);color:var(--dark);font-size:12px;font-weight:700;letter-spacing:.06em;text-transform:uppercase}
.gguide__hero h2{font-size:34px;line-height:1.1;font-weight:700;margin:14px 0 10px;color:#fff}
.gguide__hero p{font-size:15px;line-height:1.55;margin:0;max-width:600px;color:rgba(255,255,255,.78)}
.gguide__hero-stats{display:flex;gap:32px;flex:none}
.gguide__hero-stats b{display:block;font-size:38px;line-height:1;font-weight:700;color:var(--acc)}
.gguide__hero-stats span{font-size:13px;color:rgba(255,255,255,.72)}

/* Сетка */
.gguide__layout{display:grid;grid-template-columns:300px minmax(0,1fr);gap:24px;margin-top:24px;align-items:start}
.gguide__side{position:sticky;top:52px}
.gguide__menu{background:var(--card);border:1px solid var(--line);border-radius:var(--r);padding:10px}
.gguide__menu summary{display:none}
.gguide__group{padding:6px 0}
.gguide__group+.gguide__group{border-top:1px solid var(--line)}
.gguide__group-title{display:block;padding:8px 10px 6px;font-size:12px;font-weight:700;letter-spacing:.05em;text-transform:uppercase;color:var(--mut)}
.gguide__link{display:grid;grid-template-columns:28px 1fr auto;align-items:center;gap:10px;padding:9px 10px;border-radius:12px;color:var(--dark);text-decoration:none;font-size:14px;line-height:1.3;font-weight:500;transition:background .2s}
.gguide__link:hover{background:#f7f3ec;color:var(--dark)}
.gguide__link:focus{box-shadow:0 0 0 2px var(--acc);outline:0}
.gguide__link.is-active{background:var(--acc);color:var(--dark)}
.gguide__check{display:grid;place-items:center;width:28px;height:28px;border-radius:50%;border:1.5px solid var(--line);font-size:12px;font-weight:700;color:var(--mut);background:#fff}
.gguide__link.is-active .gguide__check{border-color:var(--dark);color:var(--dark)}
.gguide__link-time{font-size:12px;color:var(--mut);font-variant-numeric:tabular-nums}
.gguide__link.is-active .gguide__link-time{color:var(--brown)}

/* Урок */
.gguide__main{container-type:inline-size}
.gguide__lesson{background:var(--card);border:1px solid var(--line);border-radius:var(--r);padding:28px 30px}
.gguide.is-js .gguide__lesson:not(.is-active){display:none}
.gguide:not(.is-js) .gguide__lesson+.gguide__lesson{margin-top:20px}
.gguide__lesson-head{display:flex;flex-wrap:wrap;justify-content:space-between;align-items:flex-end;gap:16px 24px}
.gguide__crumb{font-size:13px;font-weight:600;color:var(--mut)}
.gguide__lesson-head h3{font-size:26px;line-height:1.2;font-weight:700;margin:6px 0 6px}
.gguide__lesson-head p{margin:0;font-size:15px;color:var(--mut)}
.gguide__toolbar{flex:none}
.gguide__switch{display:inline-flex;padding:4px;border-radius:14px;background:#f3eee6}
.gguide__switch button{display:inline-flex;align-items:center;gap:6px;border:0;background:none;padding:9px 16px;border-radius:10px;cursor:pointer;font-size:14px;font-weight:600;color:var(--dark);transition:background .2s,box-shadow .2s}
.gguide__switch button small{font-size:12px;font-weight:500;color:var(--mut)}
.gguide__switch button .dashicons{font-size:18px;width:18px;height:18px}
.gguide__switch button[aria-selected="true"]{background:#fff;box-shadow:0 1px 3px rgba(20,20,20,.12)}
.gguide__pane{margin-top:22px}

.gguide__pane[data-gguide-pane="video"]{display:grid;grid-template-columns:minmax(0,1fr);gap:18px}
.gguide__pane[hidden]{display:none !important}
.gguide__player{border-radius:16px;overflow:hidden;background:var(--dark);align-self:start}
.gguide__player video{display:block;width:100%;aspect-ratio:16/10;background:var(--dark)}
.gguide__chapters{min-width:0}
.gguide__chapters-title{display:block;font-size:13px;font-weight:600;color:var(--mut);margin-bottom:8px}
.gguide__chapters ol{list-style:none;margin:0;padding:0;display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:6px}
.gguide__chapters button{display:flex;align-items:flex-start;gap:10px;width:100%;text-align:left;border:1px solid var(--line);background:#fff;padding:9px 12px;border-radius:12px;cursor:pointer;font-size:13px;font-weight:500;line-height:1.35;color:var(--dark);transition:background .2s,border-color .2s}
.gguide__chapters button:hover{border-color:var(--acc)}
.gguide__chapters button.is-current{background:#fff6e6;border-color:var(--acc)}
.gguide__t{flex:none;min-width:38px;padding:2px 6px;border-radius:6px;background:#f3eee6;font-size:12px;font-weight:700;text-align:center;font-variant-numeric:tabular-nums}
.gguide__chapters button.is-current .gguide__t{background:var(--acc)}

/* Широкий экран: шаги — колонкой справа от видео, как плейлист */
@container (min-width:1000px){
  .gguide__pane[data-gguide-pane="video"]{grid-template-columns:minmax(0,1fr) 300px;align-items:start}
  .gguide__chapters{display:flex;flex-direction:column;align-self:stretch;min-height:0;position:relative}
  .gguide__chapters ol{grid-template-columns:1fr;overflow:auto;position:absolute;top:26px;bottom:0;left:0;right:0;padding-right:4px;align-content:start}
}
@container (min-width:1300px){.gguide__pane[data-gguide-pane="video"]{grid-template-columns:minmax(0,1fr) 340px}}

.gguide__steps{list-style:none;margin:0;padding:0;display:grid;gap:14px}
.gguide__step{display:grid;grid-template-columns:minmax(0,4fr) minmax(0,7fr);gap:28px;align-items:start;padding:20px;border:1px solid var(--line);border-radius:16px}
.gguide__step-text{display:flex;gap:14px;align-items:flex-start;padding-top:4px}
.gguide__step-num{flex:none;display:grid;place-items:center;width:32px;height:32px;border-radius:50%;background:var(--acc);font-weight:700;font-size:14px}
.gguide__step h4{font-size:17px;line-height:1.3;font-weight:700;margin:4px 0 8px}
.gguide__step p{margin:0;font-size:15px;line-height:1.6;color:#3a3530}
.gguide__shot{position:relative;display:block;width:100%;padding:0;border:1px solid var(--line);border-radius:12px;overflow:hidden;background:#fff;cursor:zoom-in}
.gguide__shot img{display:block;width:100%;height:auto;transition:transform .4s}
.gguide__shot:hover img{transform:scale(1.015)}
.gguide__zoom{position:absolute;right:10px;top:10px;display:grid;place-items:center;width:34px;height:34px;border-radius:50%;background:var(--acc);color:var(--dark);opacity:0;transition:opacity .2s}
.gguide__shot:hover .gguide__zoom,.gguide__shot:focus .gguide__zoom{opacity:1}
@container (min-width:1300px){.gguide__step{grid-template-columns:minmax(0,3fr) minmax(0,7fr)}}

.gguide__pager{display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-top:26px;padding-top:22px;border-top:1px solid var(--line)}
.gguide__pager a{display:flex;flex-direction:column;gap:4px;padding:14px 18px;border-radius:14px;border:1px solid var(--line);text-decoration:none;color:var(--dark);transition:border-color .2s,background .2s}
.gguide__pager a:hover{border-color:var(--acc);color:var(--dark)}
.gguide__pager a span{font-size:12px;font-weight:600;color:var(--mut)}
.gguide__pager a b{font-size:15px}
.gguide__pager-next{text-align:right;grid-column:2;background:var(--acc);border-color:var(--acc) !important}
.gguide__pager-next:hover{background:var(--acc-h)}
.gguide__pager-next span{color:var(--brown) !important}
.gguide__pager-end{grid-column:2;align-self:center;text-align:right;font-size:13px;color:var(--mut)}

.gguide__lightbox{padding:0;border:0;border-radius:14px;max-width:min(96vw,1600px);background:transparent;overflow:visible}
.gguide__lightbox::backdrop{background:rgba(20,16,14,.85)}
.gguide__lightbox img{display:block;max-width:100%;max-height:90vh;border-radius:14px}
.gguide__lightbox-close{position:absolute;top:-14px;right:-14px;width:36px;height:36px;border:0;border-radius:50%;background:var(--acc);font-size:22px;line-height:1;cursor:pointer;z-index:1}

@media (min-width:1700px){.gguide__layout{grid-template-columns:330px minmax(0,1fr)}}
/* Планшет и телефон */
@media (max-width:1100px){
  .gguide__hero{flex-direction:column;align-items:flex-start}
  .gguide__layout{grid-template-columns:1fr}
  .gguide__side{position:sticky;top:46px;z-index:20}
  .gguide__menu{padding:0;box-shadow:0 8px 24px -12px rgba(48,40,34,.35)}
  .gguide__menu summary{display:flex;justify-content:space-between;align-items:center;gap:10px;padding:14px 16px;cursor:pointer;list-style:none;font-weight:700}
  .gguide__menu summary::-webkit-details-marker{display:none}
  .gguide__menu summary::after{content:"▾";color:var(--mut)}
  .gguide__menu[open] summary::after{content:"▴"}
  .gguide__menu-current{flex:1;text-align:right;font-weight:500;color:var(--mut);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
  .gguide__menu nav{padding:0 10px 10px;max-height:60vh;overflow:auto}
}
@media (max-width:782px){
  .gguide{margin:12px 10px 30px 0}
  .gguide__hero{padding:24px 20px;border-radius:20px;gap:18px}
  .gguide__hero h2{font-size:26px}
  .gguide__hero-stats b{font-size:30px}
  .gguide__lesson{padding:18px 16px}
  .gguide__lesson-head h3{font-size:21px}
  .gguide__toolbar,.gguide__switch{width:100%}
  .gguide__switch button{flex:1;justify-content:center}
  .gguide__player{border-radius:12px;margin:0 -6px}
  .gguide__chapters ol{grid-template-columns:1fr}
  .gguide__step{grid-template-columns:1fr;gap:12px;padding:14px}
  .gguide__pager{grid-template-columns:1fr}
  .gguide__pager-next,.gguide__pager-end{grid-column:1}
}
@media (max-width:600px){.gguide__side{top:0}}
CSS;
    wp_add_inline_style('gorizont-guide', str_replace('__A__', esc_url($assets), $css));

    wp_register_script('gorizont-guide', false, array(), '3', true);
    wp_enqueue_script('gorizont-guide');
    wp_add_inline_script('gorizont-guide', <<<'JS'
(() => {
  const root = document.querySelector('[data-gguide]')
  if (!root) return
  root.classList.add('is-js')
  const lessons = [...root.querySelectorAll('[data-gguide-lesson]')]
  const links = [...root.querySelectorAll('[data-gguide-link]')]
  const KEY = 'gguide-' + root.dataset.user
  const store = { get: (k, d) => { try { return JSON.parse(localStorage.getItem(KEY + k)) ?? d } catch (e) { return d } },
                  set: (k, v) => { try { localStorage.setItem(KEY + k, JSON.stringify(v)) } catch (e) {} } }
  let tabPref = store.get('-tab', 'video')
  const menu = root.querySelector('[data-gguide-menu]')
  const narrow = matchMedia('(max-width:1100px)')

  const setTab = (les, tab) => {
    les.querySelectorAll('[data-gguide-tab]').forEach((b) => b.setAttribute('aria-selected', String(b.dataset.gguideTab === tab)))
    les.querySelectorAll('[data-gguide-pane]').forEach((p) => { p.hidden = p.dataset.gguidePane !== tab })
    if (tab !== 'video') les.querySelector('video').pause()
  }

  const show = (id, scroll) => {
    const les = lessons.find((l) => l.dataset.gguideLesson === id) || lessons[0]
    lessons.forEach((l) => { const on = l === les; l.classList.toggle('is-active', on); if (!on) l.querySelector('video').pause() })
    links.forEach((l) => l.classList.toggle('is-active', l.dataset.gguideLink === les.dataset.gguideLesson))
    root.querySelector('[data-gguide-current]').textContent = les.dataset.title
    const v = les.querySelector('video')
    if (!v.getAttribute('src')) v.setAttribute('src', v.dataset.src) // видео грузится, только когда урок открыт
    setTab(les, tabPref)
    store.set('-last', les.dataset.gguideLesson)
    if (narrow.matches) menu.open = false
    if (scroll) root.querySelector('.gguide__layout').scrollIntoView({ behavior: 'smooth', block: 'start' })
  }

  lessons.forEach((les) => {
    const video = les.querySelector('video')
    les.querySelectorAll('[data-gguide-tab]').forEach((b) => b.addEventListener('click', () => {
      tabPref = b.dataset.gguideTab; store.set('-tab', tabPref); setTab(les, tabPref)
    }))
    const chapters = [...les.querySelectorAll('[data-gguide-seek]')]
    chapters.forEach((b) => b.addEventListener('click', () => { video.currentTime = parseFloat(b.dataset.gguideSeek); video.play().catch(() => {}) }))
    let last = null
    video.addEventListener('timeupdate', () => {
      let cur = null
      chapters.forEach((b) => { if (parseFloat(b.dataset.gguideSeek) <= video.currentTime + 0.05) cur = b })
      if (cur === last) return
      last = cur
      chapters.forEach((b) => b.classList.toggle('is-current', b === cur))
      // в колонке-плейлисте держим текущий шаг на виду
      const list = cur && cur.closest('ol')
      if (list && list.scrollHeight > list.clientHeight) list.scrollTo({ top: cur.parentElement.offsetTop - list.clientHeight / 3, behavior: 'smooth' })
    })
  })

  // Увеличение скриншотов
  const lb = root.querySelector('[data-gguide-lightbox]')
  root.querySelectorAll('[data-gguide-zoom]').forEach((b) => b.addEventListener('click', () => {
    lb.querySelector('img').src = b.dataset.gguideZoom
    lb.showModal()
  }))
  lb.addEventListener('click', (e) => { if (e.target === lb || e.target.tagName === 'IMG') lb.close() })

  window.addEventListener('hashchange', () => show(location.hash.slice(1), true))
  if (narrow.matches) menu.open = false
  show(location.hash.slice(1) || store.get('-last', ''), false)
})()
JS
    );
});
