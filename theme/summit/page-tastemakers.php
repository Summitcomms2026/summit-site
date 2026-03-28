<?php
/**
 * Template: Tastemakers
 * Location: page-tastemakers.php
 *
 * WordPress template hierarchy: page-{slug}.php → page.php → index.php
 * Matches the WordPress page with slug: tastemakers  (URL: /tastemakers)
 *
 * The podcast_season and podcast_episode CPTs have has_archive => false.
 * This page template owns the Tastemakers series landing display.
 * Individual season pages are handled by single-podcast_season.php.
 * Episode pages are handled by single-podcast_episode.php.
 *
 * Component sequence:
 *   1. Series hero — page title, series description, platform links
 *   2. Active season block — artwork, thesis, trailer, season CTA
 *   3. Episode grid — first 6 episodes from active season (ordered by ep number)
 *   4. Past seasons grid
 *   5. Related articles from active season  → parts/related/articles.php
 *   6. Subscribe module                     → parts/global/subscribe.php
 *   7. CTA footer                           → parts/global/cta-footer.php
 *
 * Query strategy:
 *   Active season: meta_query filters for ps_is_featured = 1.
 *   meta_key at the top level is used exclusively for orderby (ps_season_number DESC).
 *   These two roles — filtering and ordering — must be separated to avoid PHP
 *   silently overwriting the duplicate array key and dropping the filter.
 *
 *   Episode query: meta_query filters for ep_season = active season ID.
 *   meta_key at the top level orders by ep_episode_number ASC.
 *   Same separation principle applies.
 *
 * SEO & Schema — not injected here.
 * Output handled in inc/seo.php via standard page SEO logic.
 * Schema: schema.org/PodcastSeries applied to the page in inc/seo.php.
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

get_header();

// ── Page context ──────────────────────────────────────────────────────────
the_post(); // Consume the wp_page post for get_the_title() / get_the_content()

// ─────────────────────────────────────────────────────────────────────────
// Active (featured) season query
//
// meta_query: filters results to seasons where ps_is_featured = '1'
// meta_key:   used only for orderby — tells WP which meta value to sort on
// These are intentionally separate. Combining them via a single meta_key
// causes PHP to silently drop the first key, losing the is_featured filter.
// ─────────────────────────────────────────────────────────────────────────
$active_season_query = new WP_Query( [
    'post_type'      => 'podcast_season',
    'posts_per_page' => 1,
    'no_found_rows'  => true,
    'meta_query'     => [
        [
            'key'     => 'ps_is_featured',
            'value'   => '1',
            'compare' => '=',
        ],
    ],
    'orderby'        => 'meta_value_num',
    'meta_key'       => 'ps_season_number',
    'order'          => 'DESC',
] );

$active_season = null;
if ( $active_season_query->have_posts() ) {
    $active_season_query->the_post();
    $active_season = get_post();
    wp_reset_postdata();
}

// Fallback: most recent season by date if none is flagged active
if ( ! $active_season ) {
    $fallback_query = new WP_Query( [
        'post_type'      => 'podcast_season',
        'posts_per_page' => 1,
        'no_found_rows'  => true,
        'orderby'        => 'date',
        'order'          => 'DESC',
    ] );
    if ( $fallback_query->have_posts() ) {
        $fallback_query->the_post();
        $active_season = get_post();
        wp_reset_postdata();
    }
}

// ─────────────────────────────────────────────────────────────────────────
// Episodes for active season
//
// meta_query: filters to episodes belonging to this season (ep_season = ID)
// meta_key:   used only for orderby — sorts by ep_episode_number ASC
// ─────────────────────────────────────────────────────────────────────────
$active_episodes = [];
if ( $active_season ) {
    $ep_query = new WP_Query( [
        'post_type'      => 'podcast_episode',
        'posts_per_page' => -1,
        'no_found_rows'  => true,
        'meta_query'     => [
            [
                'key'     => 'ep_season',
                'value'   => $active_season->ID,
                'compare' => '=',
                'type'    => 'NUMERIC',
            ],
        ],
        'orderby'        => 'meta_value_num',
        'meta_key'       => 'ep_episode_number',
        'order'          => 'ASC',
    ] );
    if ( $ep_query->have_posts() ) {
        while ( $ep_query->have_posts() ) {
            $ep_query->the_post();
            $active_episodes[] = get_post();
        }
        wp_reset_postdata();
    }
}

// ── Past seasons (all seasons except the active one) ──────────────────────
$past_season_args = [
    'post_type'      => 'podcast_season',
    'posts_per_page' => -1,
    'no_found_rows'  => true,
    'orderby'        => 'meta_value_num',
    'meta_key'       => 'ps_season_number',
    'order'          => 'DESC',
];
if ( $active_season ) {
    $past_season_args['post__not_in'] = [ $active_season->ID ];
}
$past_season_query = new WP_Query( $past_season_args );
$past_seasons      = [];
if ( $past_season_query->have_posts() ) {
    while ( $past_season_query->have_posts() ) {
        $past_season_query->the_post();
        $past_seasons[] = get_post();
    }
    wp_reset_postdata();
}

// ── Active season field retrieval ─────────────────────────────────────────
$season_number   = $active_season ? get_field( 'ps_season_number',    $active_season->ID ) : null;
$season_subtitle = $active_season ? get_field( 'ps_subtitle',         $active_season->ID ) : '';
$season_theme    = $active_season ? get_field( 'ps_theme',            $active_season->ID ) : '';
$season_thesis   = $active_season ? get_field( 'ps_thesis',           $active_season->ID ) : '';
$season_trailer  = $active_season ? get_field( 'ps_trailer_embed',    $active_season->ID ) : '';
$season_artwork  = $active_season ? get_field( 'ps_artwork',          $active_season->ID ) : null;
$featured_ep     = $active_season ? get_field( 'ps_featured_episode', $active_season->ID ) : null;
$season_articles = $active_season ? get_field( 'ps_related_articles', $active_season->ID ) : [];
$platform_links  = $active_season ? get_field( 'ps_platform_links',   $active_season->ID ) : [];
$season_url      = $active_season ? get_permalink( $active_season->ID ) : '';

$s_label = $season_number ? 'Season ' . absint( $season_number ) : '';

$platform_names = [
    'apple'   => 'Apple Podcasts',
    'spotify' => 'Spotify',
    'overcast'=> 'Overcast',
    'pocket'  => 'Pocket Casts',
    'amazon'  => 'Amazon Music',
    'youtube' => 'YouTube',
    'rss'     => 'RSS',
    'other'   => 'Listen',
];
?>

<main id="main" class="site-main site-main--tastemakers" role="main">

    <!-- ── 1. Series hero ───────────────────────────────────────────────── -->
    <section class="podcast-hero" aria-labelledby="tastemakers-title">
        <div class="container container--medium">

            <header class="podcast-hero__header">
                <p class="podcast-hero__eyebrow">A Summit Media Property</p>
                <h1 class="podcast-hero__title" id="tastemakers-title">
                    <?php echo esc_html( get_the_title() ); ?>
                </h1>
                <?php
                $series_desc = get_the_content();
                if ( $series_desc ) :
                    echo apply_filters( 'the_content', $series_desc ); // phpcs:ignore WordPress.Security.EscapeOutput
                else : ?>
                <p class="podcast-hero__description">
                    Conversations on luxury, culture, technology and the forces
                    shaping how sophisticated audiences assign value.
                </p>
                <?php endif; ?>
            </header>

            <?php if ( ! empty( $platform_links ) ) : ?>
            <nav class="podcast-hero__platforms" aria-label="Listen on your preferred platform">
                <ul class="platform-list" role="list">
                    <?php foreach ( $platform_links as $pf ) :
                        if ( empty( $pf['platform_url'] ) ) { continue; }
                        $pl_label = $platform_names[ $pf['platform_name'] ] ?? 'Listen';
                    ?>
                    <li>
                        <a href="<?php echo esc_url( $pf['platform_url'] ); ?>"
                           class="btn btn--secondary platform-list__btn"
                           rel="noopener noreferrer"
                           target="_blank">
                            <?php echo esc_html( $pl_label ); ?>
                            <span class="screen-reader-text"> (opens in new tab)</span>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </nav>
            <?php endif; ?>

        </div>
    </section>

    <!-- ── 2. Active season block ───────────────────────────────────────── -->
    <?php if ( $active_season ) : ?>
    <section class="season-block season-block--active" aria-labelledby="active-season-heading">
        <div class="container container--wide">
            <div class="season-block__layout">

                <!-- Season artwork -->
                <?php if ( is_array( $season_artwork ) && ! empty( $season_artwork['url'] ) ) : ?>
                <figure class="season-block__artwork" aria-hidden="true">
                    <img src="<?php echo esc_url( $season_artwork['url'] ); ?>"
                         alt="<?php echo esc_attr( $season_artwork['alt'] ?: get_the_title( $active_season->ID ) . ' artwork' ); ?>"
                         width="<?php echo absint( $season_artwork['width']  ?? 600 ); ?>"
                         height="<?php echo absint( $season_artwork['height'] ?? 600 ); ?>"
                         loading="lazy">
                </figure>
                <?php elseif ( has_post_thumbnail( $active_season->ID ) ) : ?>
                <figure class="season-block__artwork" aria-hidden="true">
                    <?php echo get_the_post_thumbnail( $active_season->ID, 'medium_large' ); ?>
                </figure>
                <?php endif; ?>

                <!-- Season information -->
                <div class="season-block__info">

                    <p class="season-block__label"><?php echo esc_html( $s_label ); ?></p>

                    <h2 class="season-block__title" id="active-season-heading">
                        <a href="<?php echo esc_url( $season_url ); ?>">
                            <?php echo esc_html( get_the_title( $active_season->ID ) ); ?>
                        </a>
                    </h2>

                    <?php if ( $season_subtitle ) : ?>
                    <p class="season-block__subtitle"><?php echo esc_html( $season_subtitle ); ?></p>
                    <?php endif; ?>

                    <?php if ( $season_theme ) : ?>
                    <p class="season-block__theme"><?php echo esc_html( $season_theme ); ?></p>
                    <?php endif; ?>

                    <?php if ( $season_thesis ) : ?>
                    <div class="season-block__thesis">
                        <?php echo wp_kses_post( wpautop( $season_thesis ) ); ?>
                    </div>
                    <?php endif; ?>

                    <?php if ( $season_trailer ) : ?>
                    <div class="season-block__trailer">
                        <?php if ( filter_var( $season_trailer, FILTER_VALIDATE_URL ) ) :
                            printf(
                                '<div class="season-block__trailer-wrap"><iframe src="%s" allow="autoplay" loading="lazy" title="%s — Trailer"></iframe></div>',
                                esc_url( $season_trailer ),
                                esc_attr( get_the_title( $active_season->ID ) )
                            );
                        else :
                            echo wp_kses_post( $season_trailer );
                        endif; ?>
                    </div>
                    <?php endif; ?>

                    <a href="<?php echo esc_url( $season_url ); ?>"
                       class="btn btn--primary season-block__cta">
                        Explore <?php echo $s_label ? esc_html( $s_label ) : 'This Season'; ?>
                    </a>

                </div>

            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- ── 3. Episode grid (active season — first 6) ────────────────────── -->
    <?php if ( ! empty( $active_episodes ) ) :
        $display_eps = array_slice( $active_episodes, 0, 6 );
    ?>
    <section class="episode-index" aria-labelledby="episode-index-heading">
        <div class="container container--wide">

            <header class="episode-index__header">
                <h2 class="episode-index__heading" id="episode-index-heading">
                    <?php echo $s_label ? esc_html( $s_label ) . ' Episodes' : 'Episodes'; ?>
                </h2>
                <?php if ( count( $active_episodes ) > 6 ) : ?>
                <a href="<?php echo esc_url( $season_url ); ?>"
                   class="episode-index__view-all">
                    View all <?php echo count( $active_episodes ); ?> episodes
                </a>
                <?php endif; ?>
            </header>

            <ul class="episode-grid" role="list">
                <?php foreach ( $display_eps as $ep ) :
                    $ep_deck  = get_field( 'ep_deck',           $ep->ID );
                    $ep_guest = get_field( 'ep_guest_name',     $ep->ID );
                    $ep_co    = get_field( 'ep_guest_company',  $ep->ID );
                    $ep_num   = get_field( 'ep_episode_number', $ep->ID );
                    $ep_dur   = get_field( 'ep_duration',       $ep->ID );
                    $ep_label = ( $season_number && $ep_num )
                                ? 'S' . absint( $season_number ) . ' E' . absint( $ep_num )
                                : ( $ep_num ? 'Episode ' . absint( $ep_num ) : '' );
                    $ep_thumb = get_the_post_thumbnail( $ep->ID, 'medium' );
                    $is_featured = $featured_ep instanceof WP_Post
                                   && (int) $featured_ep->ID === (int) $ep->ID;
                ?>
                <li class="episode-card <?php echo $is_featured ? 'episode-card--featured' : ''; ?>">
                    <a href="<?php echo esc_url( get_permalink( $ep->ID ) ); ?>"
                       class="episode-card__link"
                       aria-label="<?php echo esc_attr( 'Listen: ' . get_the_title( $ep->ID ) ); ?>">

                        <?php if ( $ep_thumb ) : ?>
                        <figure class="episode-card__media" aria-hidden="true">
                            <?php echo $ep_thumb; ?>
                        </figure>
                        <?php endif; ?>

                        <div class="episode-card__body">
                            <div class="episode-card__meta">
                                <?php if ( $ep_label ) : ?>
                                <span class="episode-card__number"><?php echo esc_html( $ep_label ); ?></span>
                                <?php endif; ?>
                                <?php if ( $ep_dur ) : ?>
                                <span class="episode-card__duration"><?php echo esc_html( $ep_dur ); ?></span>
                                <?php endif; ?>
                            </div>
                            <h3 class="episode-card__title">
                                <?php echo esc_html( get_the_title( $ep->ID ) ); ?>
                            </h3>
                            <?php if ( $ep_guest ) : ?>
                            <p class="episode-card__guest">
                                <?php echo esc_html( $ep_guest );
                                echo $ep_co ? ' &mdash; ' . esc_html( $ep_co ) : ''; ?>
                            </p>
                            <?php endif; ?>
                            <?php if ( $ep_deck ) : ?>
                            <p class="episode-card__deck"><?php echo esc_html( $ep_deck ); ?></p>
                            <?php endif; ?>
                            <span class="episode-card__cta" aria-hidden="true">Listen Now</span>
                        </div>

                    </a>
                </li>
                <?php endforeach; ?>
            </ul>

            <?php if ( count( $active_episodes ) > 6 ) : ?>
            <div class="episode-index__footer">
                <a href="<?php echo esc_url( $season_url ); ?>" class="btn btn--secondary">
                    View All <?php echo count( $active_episodes ); ?> Episodes
                </a>
            </div>
            <?php endif; ?>

        </div>
    </section>
    <?php endif; ?>

    <!-- ── 4. Past seasons ──────────────────────────────────────────────── -->
    <?php if ( ! empty( $past_seasons ) ) : ?>
    <section class="past-seasons" aria-labelledby="past-seasons-heading">
        <div class="container container--wide">

            <header class="past-seasons__header">
                <h2 class="past-seasons__heading" id="past-seasons-heading">Previous Seasons</h2>
            </header>

            <ul class="season-grid" role="list">
                <?php foreach ( $past_seasons as $ps ) :
                    $ps_num      = get_field( 'ps_season_number', $ps->ID );
                    $ps_subtitle = get_field( 'ps_subtitle',      $ps->ID );
                    $ps_theme    = get_field( 'ps_theme',         $ps->ID );
                    $ps_artwork  = get_field( 'ps_artwork',       $ps->ID );
                    $ps_label    = $ps_num ? 'Season ' . absint( $ps_num ) : get_the_title( $ps->ID );
                ?>
                <li class="season-card">
                    <a href="<?php echo esc_url( get_permalink( $ps->ID ) ); ?>"
                       class="season-card__link"
                       aria-label="<?php echo esc_attr( 'Explore ' . $ps_label ); ?>">

                        <?php if ( is_array( $ps_artwork ) && ! empty( $ps_artwork['url'] ) ) : ?>
                        <figure class="season-card__artwork" aria-hidden="true">
                            <img src="<?php echo esc_url( $ps_artwork['url'] ); ?>"
                                 alt="<?php echo esc_attr( $ps_artwork['alt'] ?: $ps_label . ' artwork' ); ?>"
                                 width="<?php echo absint( $ps_artwork['width']  ?? 400 ); ?>"
                                 height="<?php echo absint( $ps_artwork['height'] ?? 400 ); ?>"
                                 loading="lazy">
                        </figure>
                        <?php elseif ( has_post_thumbnail( $ps->ID ) ) : ?>
                        <figure class="season-card__artwork" aria-hidden="true">
                            <?php echo get_the_post_thumbnail( $ps->ID, 'medium' ); ?>
                        </figure>
                        <?php endif; ?>

                        <div class="season-card__body">
                            <p class="season-card__label"><?php echo esc_html( $ps_label ); ?></p>
                            <h3 class="season-card__title">
                                <?php echo esc_html( $ps_subtitle ?: get_the_title( $ps->ID ) ); ?>
                            </h3>
                            <?php if ( $ps_theme ) : ?>
                            <p class="season-card__theme"><?php echo esc_html( $ps_theme ); ?></p>
                            <?php endif; ?>
                            <span class="season-card__cta" aria-hidden="true">Explore Season</span>
                        </div>

                    </a>
                </li>
                <?php endforeach; ?>
            </ul>

        </div>
    </section>
    <?php endif; ?>

    <!-- ── 5. Related articles from active season ───────────────────────── -->
    <?php if ( ! empty( $season_articles ) && is_array( $season_articles ) ) :
        set_query_var( 'summit_related_articles', $season_articles );
        set_query_var( 'summit_related_heading',  'Related Reading' );
        get_template_part( 'parts/related/articles' );
    endif; ?>

    <!-- ── 6. Subscribe ─────────────────────────────────────────────────── -->
    <?php
    set_query_var( 'summit_subscribe_heading', 'Subscribe to Tastemakers' );
    set_query_var( 'summit_subscribe_sub',     'New conversations on luxury, culture and the forces shaping taste — available on all major platforms.' );
    get_template_part( 'parts/global/subscribe' );
    ?>

    <!-- ── 7. CTA footer ────────────────────────────────────────────────── -->
    <?php get_template_part( 'parts/global/cta-footer' ); ?>

</main>

<?php get_footer(); ?>
