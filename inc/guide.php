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

function gorizont_guide_render() {
    if (!current_user_can(GORIZONT_GUIDE_CAP)) {
        wp_die('Недостаточно прав');
    }
    $content = gorizont_guide_content();
    ?>
    <div class="wrap gguide">
        <header class="gguide__head">
            <h1>Обучение</h1>
            <p>Как работать с сайтом: короткие ролики и пошаговые инструкции со скриншотами. В каждом разделе можно смотреть видео или читать текст — переключатель над роликом.</p>
        </header>

        <nav class="gguide__nav" aria-label="Разделы обучения">
            <?php foreach ($content['sections'] as $section) : ?>
                <a href="#g-<?php echo esc_attr($section['id']); ?>"><?php echo esc_html($section['title']); ?></a>
            <?php endforeach; ?>
        </nav>

        <?php foreach ($content['sections'] as $section) : ?>
            <section class="gguide__section" id="g-<?php echo esc_attr($section['id']); ?>">
                <h2><?php echo esc_html($section['title']); ?></h2>
                <p class="gguide__lead"><?php echo esc_html($section['lead']); ?></p>

                <?php foreach ($section['videos'] as $index => $video) : ?>
                    <article class="gguide__card" id="g-<?php echo esc_attr($video['id']); ?>" data-gguide-card>
                        <div class="gguide__card-head">
                            <div>
                                <h3><?php if (count($section['videos']) > 1) : ?><span class="gguide__num"><?php echo (int) $index + 1; ?></span><?php endif; ?><?php echo esc_html($video['title']); ?></h3>
                                <p><?php echo esc_html($video['desc']); ?></p>
                            </div>
                            <div class="gguide__switch" role="tablist" aria-label="Формат">
                                <button type="button" role="tab" aria-selected="true" data-gguide-tab="video">Видео · <?php echo esc_html(gorizont_guide_time($video['duration'])); ?></button>
                                <button type="button" role="tab" aria-selected="false" data-gguide-tab="text">Текст · <?php echo count($video['chapters']); ?> шагов</button>
                            </div>
                        </div>

                        <div class="gguide__pane" data-gguide-pane="video">
                            <video controls playsinline preload="none" poster="<?php echo esc_url(gorizont_guide_media_url($video['poster'])); ?>" src="<?php echo esc_url(gorizont_guide_media_url($video['file'])); ?>"></video>
                            <ol class="gguide__chapters">
                                <?php foreach ($video['chapters'] as $chapter) : ?>
                                    <li><button type="button" data-gguide-seek="<?php echo esc_attr($chapter['t']); ?>"><span><?php echo esc_html(gorizont_guide_time($chapter['t'])); ?></span><?php echo esc_html($chapter['title']); ?></button></li>
                                <?php endforeach; ?>
                            </ol>
                        </div>

                        <div class="gguide__pane" data-gguide-pane="text" hidden>
                            <ol class="gguide__steps">
                                <?php foreach ($video['chapters'] as $chapter) : ?>
                                    <li>
                                        <div class="gguide__step-text">
                                            <h4><?php echo esc_html($chapter['title']); ?></h4>
                                            <p><?php echo esc_html($chapter['text']); ?></p>
                                        </div>
                                        <a class="gguide__shot" href="<?php echo esc_url(gorizont_guide_media_url($chapter['img'])); ?>" target="_blank" rel="noopener">
                                            <img loading="lazy" src="<?php echo esc_url(gorizont_guide_media_url($chapter['img'])); ?>" alt="<?php echo esc_attr($chapter['title']); ?>" width="1280" height="800">
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ol>
                        </div>
                    </article>
                <?php endforeach; ?>
            </section>
        <?php endforeach; ?>

        <p class="gguide__foot">Если что-то в админке выглядит не так, как в ролике, или остался вопрос — напишите разработчику.</p>
    </div>
    <?php
}

add_action('admin_enqueue_scripts', function ($hook) {
    if ($hook !== 'toplevel_page_gorizont-guide') {
        return;
    }
    wp_register_style('gorizont-guide', false, array(), '1');
    wp_enqueue_style('gorizont-guide');
    wp_add_inline_style('gorizont-guide', <<<'CSS'
.gguide{max-width:1040px;--g-acc:#e8553d;--g-line:#dcdcde;--g-mut:#646970}
.gguide__head h1{font-size:28px;font-weight:700;margin:8px 0 6px}
.gguide__head p{font-size:15px;color:var(--g-mut);max-width:680px;margin:0 0 16px}
.gguide__nav{position:sticky;top:32px;z-index:5;display:flex;gap:6px;overflow-x:auto;padding:10px 0;margin:0 0 8px;background:#f0f0f1;scrollbar-width:none}
.gguide__nav::-webkit-scrollbar{display:none}
.gguide__nav a{flex:none;padding:7px 14px;border-radius:99px;background:#fff;border:1px solid var(--g-line);color:#1d2327;text-decoration:none;font-weight:500}
.gguide__nav a:hover,.gguide__nav a:focus{border-color:var(--g-acc);color:var(--g-acc);box-shadow:none}
.gguide__section{padding-top:18px;scroll-margin-top:90px}
.gguide__section>h2{font-size:22px;margin:0 0 4px}
.gguide__lead{color:var(--g-mut);font-size:14px;margin:0 0 14px}
.gguide__card{background:#fff;border:1px solid var(--g-line);border-radius:12px;padding:18px;margin:0 0 16px;scroll-margin-top:90px}
.gguide__card-head{display:flex;justify-content:space-between;align-items:flex-start;gap:16px;margin-bottom:14px}
.gguide__card-head h3{font-size:17px;margin:0 0 4px;display:flex;align-items:center;gap:8px}
.gguide__card-head p{margin:0;color:var(--g-mut)}
.gguide__num{display:inline-grid;place-items:center;width:24px;height:24px;border-radius:50%;background:var(--g-acc);color:#fff;font-size:13px;flex:none}
.gguide__switch{display:flex;flex:none;background:#f0f0f1;border-radius:8px;padding:3px}
.gguide__switch button{border:0;background:none;padding:7px 12px;border-radius:6px;cursor:pointer;font-size:13px;color:#1d2327;white-space:nowrap}
.gguide__switch button[aria-selected="true"]{background:#fff;box-shadow:0 1px 2px rgba(0,0,0,.12);font-weight:600}
.gguide video{display:block;width:100%;aspect-ratio:16/10;background:#1d2327;border-radius:8px}
.gguide__chapters{list-style:none;margin:12px 0 0;padding:0;display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:4px 12px}
.gguide__chapters button{display:flex;gap:10px;width:100%;text-align:left;background:none;border:0;padding:6px 8px;border-radius:6px;cursor:pointer;color:#1d2327;font-size:13px;line-height:1.35}
.gguide__chapters button:hover{background:#f6f7f7}
.gguide__chapters button.is-current{background:#fcefec;color:#b32d1a}
.gguide__chapters span{flex:none;min-width:34px;color:var(--g-acc);font-variant-numeric:tabular-nums;font-weight:600}
.gguide__steps{margin:0;padding:0;list-style:none;counter-reset:s}
.gguide__steps li{counter-increment:s;display:grid;grid-template-columns:minmax(0,5fr) minmax(0,6fr);gap:18px;align-items:start;padding:16px 0;border-top:1px solid #f0f0f1}
.gguide__steps li:first-child{border-top:0;padding-top:0}
.gguide__steps h4{font-size:15px;margin:0 0 6px;display:flex;gap:8px;align-items:baseline}
.gguide__steps h4::before{content:counter(s);flex:none;display:inline-grid;place-items:center;width:22px;height:22px;border-radius:50%;background:#f0f0f1;font-size:12px}
.gguide__steps p{margin:0;font-size:14px;line-height:1.55}
.gguide__shot{display:block;border:1px solid var(--g-line);border-radius:8px;overflow:hidden}
.gguide__shot img{display:block;width:100%;height:auto}
.gguide__foot{color:var(--g-mut);margin:24px 0}
@media (max-width:782px){
  .gguide__nav{top:46px}
  .gguide__card{padding:14px;border-radius:10px}
  .gguide__card-head{flex-direction:column;gap:10px}
  .gguide__switch{align-self:stretch}.gguide__switch button{flex:1}
  .gguide__steps li{grid-template-columns:1fr;gap:10px}
}
@media (max-width:600px){
  .gguide__nav{top:0}
  .gguide__chapters{grid-template-columns:1fr}
}
CSS
    );
    wp_register_script('gorizont-guide', false, array(), '1', true);
    wp_enqueue_script('gorizont-guide');
    wp_add_inline_script('gorizont-guide', <<<'JS'
(() => {
  const KEY = 'gguide-tab'
  let pref = 'video'
  try { pref = localStorage.getItem(KEY) || 'video' } catch (e) {}
  const setTab = (card, tab) => {
    card.querySelectorAll('[data-gguide-tab]').forEach((b) => b.setAttribute('aria-selected', String(b.dataset.gguideTab === tab)))
    card.querySelectorAll('[data-gguide-pane]').forEach((p) => { p.hidden = p.dataset.gguidePane !== tab })
    if (tab !== 'video') { const v = card.querySelector('video'); if (v) v.pause() }
  }
  document.querySelectorAll('[data-gguide-card]').forEach((card) => {
    setTab(card, pref)
    card.querySelectorAll('[data-gguide-tab]').forEach((b) => b.addEventListener('click', () => {
      setTab(card, b.dataset.gguideTab)
      try { localStorage.setItem(KEY, b.dataset.gguideTab) } catch (e) {}
    }))
    const video = card.querySelector('video')
    const chapters = [...card.querySelectorAll('[data-gguide-seek]')]
    chapters.forEach((b) => b.addEventListener('click', () => {
      video.currentTime = parseFloat(b.dataset.gguideSeek)
      video.play().catch(() => {})
    }))
    video.addEventListener('timeupdate', () => {
      let cur = null
      chapters.forEach((b) => { if (parseFloat(b.dataset.gguideSeek) <= video.currentTime + 0.05) cur = b })
      chapters.forEach((b) => b.classList.toggle('is-current', b === cur))
    })
    // одновременно играет только один ролик
    video.addEventListener('play', () => document.querySelectorAll('.gguide video').forEach((v) => { if (v !== video) v.pause() }))
  })
})()
JS
    );
});
