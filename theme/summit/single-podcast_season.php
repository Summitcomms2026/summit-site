<?php
/**
 * Template: Podcast Season Single
 * Location: single-podcast_season.php
 * URL: /tastemakers/[season-slug]
 *
 * Renders a single podcast season with artwork, thesis,
 * episode listing, and related content.
 *
 * ACF fields: ps_* prefix (see acf-fields.php)
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

get_header();
the_post();

$ps_id     = get_the_ID();
$number    = get_field( 'ps_season_number' );
$subtitle  = get_field( 'ps_subtitle' );
$theme     = get_field( 'ps_theme' );
$thesis    = get_field( 'ps_thesis' );
$artwork   = get_field( 'ps_artwork' );
$trailer   = get_field( 'ps_trailer_embed' );
$platforms = get_field( 'ps_platform_links' );

// Related content.
$related_articles = get_field( 'ps_related_articles' );

// Episode query — all episodes for this season, ordered by episode number.
$episode_q = new WP_Query( [
    'post_type'      => 'podcast_episode',
    'posts_per_page' => -1,
    'meta_query'     => [
        [
            'key'   => 'ep_season',
            'value' => $ps_id,
        ],
    ],
    'orderby'        => 'meta_value_num',
    'meta_key'       => 'ep_episode_number',
    'order'          => 'ASC',
    'no_found_rows'  => true,
] );
$episodes = $episode_q->posts;
wp_reset_postdata();
?>

<main id="main" class="site-main" role="main">

    <!-- ── Hero ─────────────────────────────────────────────────────── -->
    <section class="section--dark section--padded" aria-labelledby="single-hero-title">
        <div class="container container--wide">
            <div class="single-season__artwork-layout">

                <?php if ( $artwork ) : ?>
                <figure class="single-season__artwork" aria-hidden="true">
                    <img src="<?php echo esc_url( $artwork['url'] ); ?>"
                         alt="<?php echo esc_attr( $artwork['alt'] ?? '' ); ?>"
                         width="<?php echo esc_attr( $artwork['width'] ?? '' ); ?>"
                         height="<?php echo esc_attr( $artwork['height'] ?? '' ); ?>"
                         loading="lazy">
                </figure>
                <?php endif; ?>

                <div>
                    <?php if ( $number ) : ?>
                    <p class="eyebrow">Season <?php echo esc_html( $number ); ?></p>
                    <?php endif; ?>

                    <h1 class="single-hero__title" id="single-hero-title" style="margin-top: var(--space-3);">
                        <?php the_title(); ?>
                    </h1>

                    <?php if ( $subtitle ) : ?>
                    <p class="single-hero__standfirst"><?php echo esc_html( $subtitle ); ?></p>
                    <?php endif; ?>

                    <?php if ( $theme ) : ?>
                    <p class="meta-bar" style="margin-top: var(--space-3);">
                        <span><?php echo esc_html( $theme ); ?></span>
                    </p>
                    <?php endif; ?>

                    <?php if ( ! empty( $platforms ) ) : ?>
                    <ul class="platform-links" style="margin-top: var(--space-6);">
                        <?php foreach ( $platforms as $link ) : ?>
                        <li class="platform-links__item">
                            <a href="<?php echo esc_url( $link['platform_url'] ); ?>"
                               target="_blank"
                               rel="noopener noreferrer">
                                <?php echo esc_html( $link['platform_name'] ); ?>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </section>

    <!-- ── Trailer ──────────────────────────────────────────────────── -->
    <?php if ( $trailer ) : ?>
    <section class="section--dark section--padded">
        <div class="container container--wide">
            <div class="embed-wrap">
                <?php echo $trailer; // phpcs:ignore -- oEmbed output. ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- ── Thesis ───────────────────────────────────────────────────── -->
    <?php if ( $thesis ) : ?>
    <section class="section--padded">
        <div class="container container--narrow">
            <div class="single-season__thesis">
                <?php echo wp_kses_post( wpautop( $thesis ) ); ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- ── Episode List ─────────────────────────────────────────────── -->
    <?php if ( ! empty( $episodes ) ) : ?>
    <section class="section--tinted section--padded" aria-labelledby="episodes-heading">
        <div class="container container--wide">

            <h2 class="related-section__headline" id="episodes-heading">Episodes</h2>

            <ul class="episode-list" role="list">
                <?php foreach ( $episodes as $episode ) :
                    $ep_id     = $episode->ID;
                    $ep_number = get_field( 'ep_episode_number', $ep_id );
                    $ep_guest  = get_field( 'ep_guest_name', $ep_id );
                    $ep_dur    = get_field( 'ep_duration', $ep_id );
                ?>
                <li class="episode-card">
                    <a href="<?php echo esc_url( get_permalink( $ep_id ) ); ?>"
                       class="episode-card__link"
                       aria-label="<?php echo esc_attr( 'Listen: ' . get_the_title( $ep_id ) ); ?>">

                        <?php if ( $ep_number ) : ?>
                        <span class="episode-card__number"><?php echo esc_html( $ep_number ); ?></span>
                        <?php endif; ?>

                        <div>
                            <p class="episode-card__title"><?php echo esc_html( get_the_title( $ep_id ) ); ?></p>
                            <?php if ( $ep_guest ) : ?>
                            <p class="episode-card__guest"><?php echo esc_html( $ep_guest ); ?></p>
                            <?php endif; ?>
                        </div>

                        <?php if ( $ep_dur ) : ?>
                        <span class="episode-card__duration"><?php echo esc_html( $ep_dur ); ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>

        </div>
    </section>
    <?php endif; ?>

    <!-- ── Related Articles ─────────────────────────────────────────── -->
    <?php
    get_template_part( 'parts/related/articles', null, [
        'posts'    => $related_articles ?: [],
        'headline' => 'Related Reading',
    ] );
    ?>

    <!-- ── CTA Footer ───────────────────────────────────────────────── -->
    <?php get_template_part( 'parts/global/cta-footer' ); ?>

</main>

<?php get_footer(); ?>
