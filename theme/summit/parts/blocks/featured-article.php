<?php
/**
 * Part: Featured Article Panel — Editorial Rail
 * Location: parts/blocks/featured-article.php
 *
 * Horizontal scrolling editorial rail showing article cards.
 * Design reference: panel7 — 1920x1226.
 *
 * Data strategy (3-tier fallback):
 *   1. ACF home_article_items relationship field (curated)
 *   2. WP_Query for latest articles
 *   3. Hardcoded placeholders matching approved comp
 *
 * ACF fields (from front page):
 *   home_article_headline text         optional
 *   home_article_items    relationship  optional — article
 *   home_article_item     post_object   optional — legacy
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

/* ── Data retrieval ─────────────────────────────────────────────────── */

$articles = get_field( 'home_article_items' );

// Legacy fallback — single post_object.
if ( empty( $articles ) ) {
    $single = get_field( 'home_article_item' );
    if ( $single ) {
        $articles = [ $single ];
    }
}

// Auto fallback — 9 most recent articles.
if ( empty( $articles ) ) {
    $article_q = new WP_Query( [
        'post_type'      => 'article',
        'posts_per_page' => 9,
        'orderby'        => 'date',
        'order'          => 'DESC',
        'no_found_rows'  => true,
    ] );
    $articles = $article_q->posts;
    wp_reset_postdata();
}

// Determine data source: live vs placeholder.
$using_placeholders = false;

if ( empty( $articles ) ) {
    $using_placeholders = true;

    // Hardcoded placeholders matching approved comp screenshots.
    $articles = [
        (object) [
            'ID'        => 0,
            'title'     => 'The Luxurification of Sport: Why Luxury\u{2019}s Biggest Bet Is on the Stadium',
            'category'  => 'Sport, Sponsorship & Status',
            'read_time' => 16,
            'excerpt'   => '',
            'image'     => '',
            'variant'   => 'overlay',
            'url'       => '#',
        ],
        (object) [
            'ID'        => 0,
            'title'     => 'The Creative Director Is Dead. Long Live the Creative Director.',
            'category'  => 'Brand & Positioning',
            'read_time' => 14,
            'excerpt'   => '',
            'image'     => '',
            'variant'   => 'overlay',
            'url'       => '#',
        ],
        (object) [
            'ID'        => 0,
            'title'     => 'BRABUS: How Black, Bold Engineering Became a Luxury Brand',
            'category'  => '',
            'read_time' => 0,
            'excerpt'   => "Let\u{2019}s explore what BRABUS GmbH calls it the 1-Second-Wow Effect.",
            'image'     => '',
            'variant'   => 'excerpt',
            'url'       => '#',
        ],
        (object) [
            'ID'        => 0,
            'title'     => 'Gen Z won\u{2019}t buy your myth: Why luxury must earn belief, not inheritance',
            'category'  => 'The New Luxury Customer',
            'read_time' => 0,
            'excerpt'   => '',
            'image'     => '',
            'variant'   => 'overlay',
            'url'       => '#',
        ],
        (object) [
            'ID'        => 0,
            'title'     => 'Hospitality Is Eating Luxury: Why Hotels Are the New Brand Platform',
            'category'  => 'Hospitality & Travel',
            'read_time' => 8,
            'excerpt'   => '',
            'image'     => '',
            'variant'   => 'overlay',
            'url'       => '#',
        ],
        (object) [
            'ID'        => 0,
            'title'     => 'Ralph Lauren: The Art of Atmosphere, Memory and American Desire',
            'category'  => 'Brand & Positioning',
            'read_time' => 6,
            'excerpt'   => '',
            'image'     => '',
            'variant'   => 'overlay',
            'url'       => '#',
        ],
    ];
}

$section_headline = get_field( 'home_article_headline' ) ?: 'The Future of Luxury';
?>

<section class="home-article section--padded" aria-labelledby="home-article-heading">
    <div class="container container--wide">

        <!-- ── Top row: heading + subline / All Articles button ──────── -->
        <header class="home-article__header" data-reveal>
            <div class="home-article__header-text">
                <h2 class="home-article__section-headline" id="home-article-heading">
                    <?php echo esc_html( $section_headline ); ?>
                </h2>
                <p class="home-article__sub">
                    News and media published by Summit Communication Group exclusively here, first.
                </p>
            </div>
            <a href="<?php echo esc_url( get_post_type_archive_link( 'article' ) ?: home_url( '/the-future-of-luxury/' ) ); ?>"
               class="home-article__cta">
                All Articles
            </a>
        </header>

        <!-- ── Rail: horizontal scroll container ────────────────────── -->
        <div class="home-article__rail" data-reveal="fade" data-reveal-delay="1">
            <?php
            foreach ( $articles as $ai => $art_post ) :

                if ( $using_placeholders ) {
                    $art_title  = $art_post->title;
                    $cat_label  = $art_post->category;
                    $read_time  = $art_post->read_time;
                    $standfirst = $art_post->excerpt;
                    $variant    = $art_post->variant;
                    $art_url    = $art_post->url;
                    $has_image  = false;
                    $image_html = '';
                } else {
                    $art_id     = $art_post->ID;
                    $art_title  = get_the_title( $art_id );
                    $cat_terms  = get_the_terms( $art_id, 'article_category' );
                    $cat_label  = ( ! is_wp_error( $cat_terms ) && ! empty( $cat_terms ) )
                                  ? $cat_terms[0]->name : '';
                    $read_time  = get_field( 'art_read_time', $art_id );
                    if ( ! $read_time && function_exists( 'summit_estimated_read_time' ) ) {
                        $read_time = summit_estimated_read_time( $art_id );
                    }
                    $standfirst = get_field( 'art_standfirst', $art_id );
                    $art_url    = get_permalink( $art_id );
                    $has_image  = has_post_thumbnail( $art_id );
                    $image_html = $has_image ? get_the_post_thumbnail( $art_id, 'large' ) : '';

                    // Determine variant: if standfirst exists and position is 3rd (index 2), use excerpt
                    $variant = 'overlay';
                    if ( $standfirst && $ai === 2 ) {
                        $variant = 'excerpt';
                    }
                }

                // Truncate title for display
                $display_title = mb_strlen( $art_title ) > 40
                    ? mb_substr( $art_title, 0, 37 ) . "\u{2026}"
                    : $art_title;
            ?>
            <a href="<?php echo esc_url( $art_url ); ?>"
               class="home-article__card<?php echo $variant === 'excerpt' ? ' home-article__card--excerpt' : ''; ?>"
               aria-label="<?php echo esc_attr( 'Read: ' . $art_title ); ?>">

                <figure class="home-article__card-media" aria-hidden="true">
                    <?php if ( $has_image && $image_html ) : ?>
                        <?php echo $image_html; ?>
                    <?php endif; ?>
                </figure>

                <?php if ( $variant === 'excerpt' ) : ?>
                <!-- White excerpt panel -->
                <div class="home-article__card-excerpt-panel">
                    <h3 class="home-article__card-title"><?php echo esc_html( $display_title ); ?></h3>
                    <?php if ( $standfirst ) : ?>
                    <p class="home-article__card-standfirst"><?php echo esc_html( wp_trim_words( $standfirst, 20, "\u{2026}" ) ); ?></p>
                    <?php endif; ?>
                    <span class="home-article__card-arrow" aria-hidden="true">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"><path d="M5 12h14M12 5l7 7-7 7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </span>
                </div>
                <?php else : ?>
                <!-- Image overlay caption -->
                <div class="home-article__card-overlay">
                    <h3 class="home-article__card-title"><?php echo esc_html( $display_title ); ?></h3>
                    <div class="home-article__card-meta">
                        <?php if ( $cat_label ) : ?>
                        <span class="home-article__card-cat"><?php echo esc_html( $cat_label ); ?></span>
                        <?php endif; ?>
                        <?php if ( $read_time ) : ?>
                        <span><?php echo absint( $read_time ); ?> min read</span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>

            </a>
            <?php endforeach; ?>
        </div>

        <!-- ── Bottom row: progress bar / arrows ────────────────────── -->
        <footer class="home-article__footer">
            <div class="home-article__progress">
                <div class="home-article__progress-track">
                    <div class="home-article__progress-thumb"></div>
                </div>
            </div>
            <nav class="home-article__nav" aria-label="Article navigation">
                <button class="home-article__arrow home-article__arrow--prev" aria-label="Previous articles" type="button">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none"><path d="M19 12H5M12 19l-7-7 7-7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
                <button class="home-article__arrow home-article__arrow--next" aria-label="Next articles" type="button">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none"><path d="M5 12h14M12 5l7 7-7 7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </nav>
        </footer>

    </div>
</section>

<script>
/**
 * Article Rail — scroll, progress bar, arrow state management.
 * Vanilla JS, no dependencies. Matches scroll-animate.js conventions.
 */
(function () {
    'use strict';

    var rail  = document.querySelector('.home-article__rail');
    var prev  = document.querySelector('.home-article__arrow--prev');
    var next  = document.querySelector('.home-article__arrow--next');
    var thumb = document.querySelector('.home-article__progress-thumb');

    if (!rail) return;

    var smooth = !window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    /* ── Scroll amount: one card width + gap ──────────────────────── */
    function getScrollStep() {
        var card = rail.querySelector('.home-article__card');
        if (!card) return rail.clientWidth;
        var style = getComputedStyle(rail);
        var gap   = parseInt(style.columnGap || style.gap, 10) || 24;
        return card.offsetWidth + gap;
    }

    /* ── Arrow visibility ─────────────────────────────────────────── */
    function updateControls() {
        var scrollLeft = rail.scrollLeft;
        var maxScroll  = rail.scrollWidth - rail.clientWidth;

        // Arrow states
        if (prev) {
            prev.style.display = scrollLeft <= 2 ? 'none' : '';
        }
        if (next) {
            next.style.display = scrollLeft >= maxScroll - 2 ? 'none' : '';
        }

        // Progress thumb
        if (thumb && maxScroll > 0) {
            var ratio    = rail.clientWidth / rail.scrollWidth;
            var position = scrollLeft / maxScroll;
            var trackEl  = thumb.parentElement;
            var trackW   = trackEl ? trackEl.offsetWidth : 0;
            var thumbW   = Math.max(trackW * ratio, 40);
            var thumbX   = (trackW - thumbW) * position;

            thumb.style.width     = thumbW + 'px';
            thumb.style.transform = 'translateX(' + thumbX + 'px)';
        }
    }

    /* ── Arrow click handlers ─────────────────────────────────────── */
    if (prev) {
        prev.addEventListener('click', function () {
            rail.scrollBy({ left: -getScrollStep(), behavior: smooth ? 'smooth' : 'auto' });
        });
    }
    if (next) {
        next.addEventListener('click', function () {
            rail.scrollBy({ left: getScrollStep(), behavior: smooth ? 'smooth' : 'auto' });
        });
    }

    /* ── Sync on scroll ───────────────────────────────────────────── */
    var ticking = false;
    rail.addEventListener('scroll', function () {
        if (!ticking) {
            requestAnimationFrame(function () {
                updateControls();
                ticking = false;
            });
            ticking = true;
        }
    }, { passive: true });

    /* ── Initial state ────────────────────────────────────────────── */
    updateControls();

    // Re-sync on resize
    window.addEventListener('resize', function () {
        updateControls();
    }, { passive: true });

})();
</script>
