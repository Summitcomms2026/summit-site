<?php
/**
 * Template: Single Podcast Season
 * Location: single-podcast_season.php
 *
 * WordPress template hierarchy:
 *   single-podcast_season.php → single.php → index.php
 *
 * URL: /tastemakers/{season-slug}
 * (podcast_season CPT rewrite slug is 'tastemakers', registered in
 * summit-core/inc/post-types.php)
 *
 * This is the full season page. page-tastemakers.php shows a preview
 * (first 6 episodes, season block summary). This template shows the
 * complete season: full thesis, all episodes in order, guest universe,
 * related editorial, and subscribe route.
 *
 * Component sequence:
 *   1. Season hero — season number, title, subtitle, artwork
 *   2. Season thesis and trailer
 *   3. Platform links
 *   4. Featured episode highlight (ps_featured_episode)
 *   5. Full episode grid — all episodes ordered by ep_episode_number ASC
 *   6. Related articles  → parts/related/articles.php
 *   7. Subscribe         → parts/global/subscribe.php
 *   8. CTA footer        → parts/global/cta-footer.php
 *
 * Episode query:
 *   meta_query filters to episodes belonging to this season (ep_season = ID).
 *   meta_key at the top level orders by ep_episode_number ASC.
 *   Separation of filter (meta_query) and orderby (meta_key) is required —
 *   using meta_key twice in the same array causes PHP to silently discard
 *   the first entry.
 *
 * SEO & Schema — not injected here.
 * Output handled in inc/seo.php:
 *   - summit_seo_meta()         hooked on wp_head
 *   - summit_schema_season()    hooked on wp_head (schema.org/PodcastSeries)
 * ACF fields consumed: ps_seo_title, ps_meta_desc, ps_og_image
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
    the_post();

    // ── Field retrieval ───────────────────────────────────────────────────
    $season_number   = get_field( 'ps_season_number' );
    $season_subtitle = get_field( 'ps_subtitle' );
    $season_theme    = get_field( 'ps_theme' );
    $season_thesis   = get_field( 'ps_thesis' );
    $season_trailer  = get_field( 'ps_trailer_embed' );
    $season_artwork  = get_field( 'ps_artwork' );        // ACF image array
    $featured_ep     = get_field( 'ps_featured_episode' ); // WP_Post|null
    $related_arts    = get_field( 'ps_related_articles' ); // relationship → array
    $platform_links  = get_field( 'ps_platform_links' );  // repeater

    $s_label = $season_number ? 'Season ' . absint( $season_number ) : '';

    // ── Episode query for this season ─────────────────────────────────────
    // meta_query: filters to episodes belonging to this season
    // meta_key:   used only for orderby (ep_episode_number ASC)
    $ep_query = new WP_Query( [
        'post_type'      => 'podcast_episode',
        'posts_per_page' => -1,
        'no_found_rows'  => true,
        'meta_query'     => [
            [
                'key'     => 'ep_season',
                'value'   => get_the_ID(),
                'compare' => '=',
                'type'    => 'NUMERIC',
            ],
        ],
        'orderby'        => 'meta_value_num',
        'meta_key'       => 'ep_episode_number',
        'order'          => 'ASC',
    ] );

    $episodes = [];
    if ( $ep_query->have_posts() ) {
        while ( $ep_query->have_posts() ) {
            $ep_query->the_post();
            $episodes[] = get_post();
        }
        wp_reset_postdata();
    }

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

<main id="main" class="site-main site-main--season" role="main">

    <!-- ── 1. Season hero ───────────────────────────────────────────────── -->
    <section class="season-hero" aria-labelledby="season-hero-title">
        <div class="container container--wide">

            <nav class="season-hero__breadcrumb" aria-label="Season navigation">
                <a href="<?php echo esc_url( home_url( '/tastemakers' ) ); ?>">
                    Tastemakers
                </a>
                <span aria-hidden="true">/</span>
                <span aria-current="page">
                    <?php echo esc_html( $s_label ?: get_the_title() ); ?>
                </span>
            </nav>

            <div class="season-hero__layout">

                <!-- Season artwork -->
                <?php if ( is_array( $season_artwork ) && ! empty( $season_artwork['url'] ) ) : ?>
                <figure class="season-hero__artwork" aria-hidden="true">
                    <img src="<?php echo esc_url( $season_artwork['url'] ); ?>"
                         alt="<?php echo esc_attr( $season_artwork['alt'] ?: get_the_title() . ' artwork' ); ?>"
                         width="<?php echo absint( $season_artwork['width']  ?? 600 ); ?>"
                         height="<?php echo absint( $season_artwork['height'] ?? 600 ); ?>"
                         loading="eager">
                </figure>
                <?php elseif ( has_post_thumbnail() ) : ?>
                <figure class="season-hero__artwork" aria-hidden="true">
                    <?php the_post_thumbnail( 'large', [ 'loading' => 'eager' ] ); ?>
                </figure>
                <?php endif; ?>

                <!-- Season header -->
                <div class="season-hero__header">

                    <?php if ( $s_label ) : ?>
                    <p class="season-hero__label"><?php echo esc_html( $s_label ); ?></p>
                    <?php endif; ?>

                    <h1 class="season-hero__title" id="season-hero-title">
                        <?php the_title(); ?>
                    </h1>

                    <?php if ( $season_subtitle ) : ?>
                    <p class="season-hero__subtitle">
                        <?php echo esc_html( $season_subtitle ); ?>
                    </p>
                    <?php endif; ?>

                    <?php if ( $season_theme ) : ?>
                    <p class="season-hero__theme">
                        <?php echo esc_html( $season_theme ); ?>
                    </p>
                    <?php endif; ?>

                    <?php if ( ! empty( $episodes ) ) : ?>
                    <p class="season-hero__count">
                        <?php echo absint( count( $episodes ) ); ?> episodes
                    </p>
                    <?php endif; ?>

                </div>

            </div>

        </div>
    </section>

    <!-- ── 2. Season thesis and trailer ─────────────────────────────────── -->
    <?php if ( $season_thesis || $season_trailer ) : ?>
    <section class="season-thesis" aria-labelledby="season-thesis-heading">
        <div class="container container--narrow">

            <?php if ( $season_thesis ) : ?>
            <div class="season-thesis__body wysiwyg">
                <?php echo wp_kses_post( wpautop( $season_thesis ) ); ?>
            </div>
            <?php endif; ?>

            <?php if ( $season_trailer ) : ?>
            <div class="season-thesis__trailer">
                <h2 class="screen-reader-text" id="season-thesis-heading">
                    Season trailer
                </h2>
                <?php if ( filter_var( $season_trailer, FILTER_VALIDATE_URL ) ) :
                    printf(
                        '<div class="season-thesis__trailer-wrap"><iframe src="%s" allow="autoplay" loading="lazy" title="%s — Trailer"></iframe></div>',
                        esc_url( $season_trailer ),
                        esc_attr( get_the_title() )
                    );
                else :
                    echo wp_kses_post( $season_trailer );
                endif; ?>
            </div>
            <?php endif; ?>

        </div>
    </section>
    <?php endif; ?>

    <!-- ── 3. Platform links ────────────────────────────────────────────── -->
    <?php if ( ! empty( $platform_links ) ) : ?>
    <section class="season-platforms" aria-label="Listen on your preferred platform">
        <div class="container container--narrow">
            <ul class="platform-list" role="list">
                <?php foreach ( $platform_links as $pf ) :
                    if ( empty( $pf['platform_url'] ) ) { continue; }
                    $pl_label = $platform_names[ $pf['platform_name'] ] ?? 'Listen';
                ?>
                <li>
                    <a href="<?php echo esc_url( $pf['platform_url'] ); ?>"
                       class="btn btn--secondary"
                       rel="noopener noreferrer"
                       target="_blank">
                        <?php echo esc_html( $pl_label ); ?>
                        <span class="screen-reader-text"> (opens in new tab)</span>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </section>
    <?php endif; ?>

    <!-- ── 4. Featured episode highlight ────────────────────────────────── -->
    <?php if ( $featured_ep instanceof WP_Post ) :
        $feat_deck    = get_field( 'ep_deck',           $featured_ep->ID );
        $feat_guest   = get_field( 'ep_guest_name',     $featured_ep->ID );
        $feat_co      = get_field( 'ep_guest_company',  $featured_ep->ID );
        $feat_dur     = get_field( 'ep_duration',       $featured_ep->ID );
        $feat_ep_num  = get_field( 'ep_episode_number', $featured_ep->ID );
        $feat_label   = ( $season_number && $feat_ep_num )
                        ? 'S' . absint( $season_number ) . ' E' . absint( $feat_ep_num )
                        : ( $feat_ep_num ? 'Episode ' . absint( $feat_ep_num ) : '' );
        $feat_thumb   = get_the_post_thumbnail( $featured_ep->ID, 'large' );
    ?>
    <section class="featured-episode" aria-labelledby="featured-episode-title">
        <div class="container container--wide">

            <p class="featured-episode__eyebrow">Featured Episode</p>

            <a href="<?php echo esc_url( get_permalink( $featured_ep->ID ) ); ?>"
               class="featured-episode__link">

                <?php if ( $feat_thumb ) : ?>
                <figure class="featured-episode__media" aria-hidden="true">
                    <?php echo $feat_thumb; ?>
                </figure>
                <?php endif; ?>

                <div class="featured-episode__body">
                    <div class="featured-episode__meta">
                        <?php if ( $feat_label ) : ?>
                        <span class="featured-episode__number">
                            <?php echo esc_html( $feat_label ); ?>
                        </span>
                        <?php endif; ?>
                        <?php if ( $feat_dur ) : ?>
                        <span class="featured-episode__duration">
                            <?php echo esc_html( $feat_dur ); ?>
                        </span>
                        <?php endif; ?>
                    </div>
                    <h2 class="featured-episode__title" id="featured-episode-title">
                        <?php echo esc_html( get_the_title( $featured_ep->ID ) ); ?>
                    </h2>
                    <?php if ( $feat_guest ) : ?>
                    <p class="featured-episode__guest">
                        <?php echo esc_html( $feat_guest );
                        echo $feat_co ? ' &mdash; ' . esc_html( $feat_co ) : ''; ?>
                    </p>
                    <?php endif; ?>
                    <?php if ( $feat_deck ) : ?>
                    <p class="featured-episode__deck">
                        <?php echo esc_html( $feat_deck ); ?>
                    </p>
                    <?php endif; ?>
                    <span class="featured-episode__cta" aria-hidden="true">
                        Listen Now
                    </span>
                </div>

            </a>

        </div>
    </section>
    <?php endif; ?>

    <!-- ── 5. Full episode grid ─────────────────────────────────────────── -->
    <?php if ( ! empty( $episodes ) ) : ?>
    <section class="episode-index episode-index--full"
             aria-labelledby="episode-index-heading">
        <div class="container container--wide">

            <header class="episode-index__header">
                <h2 class="episode-index__heading" id="episode-index-heading">
                    All Episodes
                    <span class="episode-index__count">
                        <?php echo absint( count( $episodes ) ); ?>
                    </span>
                </h2>
            </header>

            <ul class="episode-grid episode-grid--full" role="list">
                <?php foreach ( $episodes as $ep ) :
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
                <li class="episode-card <?php echo $is_featured ? 'episode-card--featured' : ''; ?>"
                    data-post-id="<?php echo absint( $ep->ID ); ?>">
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
                                <span class="episode-card__number">
                                    <?php echo esc_html( $ep_label ); ?>
                                </span>
                                <?php endif; ?>
                                <?php if ( $ep_dur ) : ?>
                                <span class="episode-card__duration">
                                    <?php echo esc_html( $ep_dur ); ?>
                                </span>
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
                            <p class="episode-card__deck">
                                <?php echo esc_html( $ep_deck ); ?>
                            </p>
                            <?php endif; ?>
                            <span class="episode-card__cta" aria-hidden="true">
                                Listen Now
                            </span>
                        </div>

                    </a>
                </li>
                <?php endforeach; ?>
            </ul>

        </div>
    </section>
    <?php endif; ?>

    <!-- ── 6. Related articles ──────────────────────────────────────────── -->
    <?php if ( ! empty( $related_arts ) && is_array( $related_arts ) ) :
        set_query_var( 'summit_related_articles', $related_arts );
        set_query_var( 'summit_related_heading',  'Related Reading' );
        get_template_part( 'parts/related/articles' );
    endif; ?>

    <!-- ── 7. Subscribe ─────────────────────────────────────────────────── -->
    <?php
    set_query_var( 'summit_subscribe_heading',
        $s_label ? 'Subscribe to Tastemakers' : 'Subscribe' );
    set_query_var( 'summit_subscribe_sub',
        'New conversations on luxury, culture and the forces shaping taste — available on all major platforms.' );
    get_template_part( 'parts/global/subscribe' );
    ?>

    <!-- ── 8. CTA footer ────────────────────────────────────────────────── -->
    <?php get_template_part( 'parts/global/cta-footer' ); ?>

</main>

<?php
endwhile;
get_footer();
