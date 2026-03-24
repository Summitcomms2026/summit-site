<?php
/**
 * Template: Podcast Season Archive
 * Location: archive-podcast_season.php
 * URL: /tastemakers
 *
 * Displays the Tastemakers podcast landing with featured season
 * and season listing.
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

get_header();

// Find featured season — flagged first, then latest by season number.
$featured_season = null;

$featured_q = new WP_Query( [
    'post_type'      => 'podcast_season',
    'posts_per_page' => 1,
    'meta_key'       => 'ps_is_featured',
    'meta_value'     => '1',
    'no_found_rows'  => true,
] );
if ( $featured_q->have_posts() ) {
    $featured_season = $featured_q->posts[0];
}
wp_reset_postdata();

if ( ! $featured_season ) {
    $latest_q = new WP_Query( [
        'post_type'      => 'podcast_season',
        'posts_per_page' => 1,
        'meta_key'       => 'ps_season_number',
        'orderby'        => 'meta_value_num',
        'order'          => 'DESC',
        'no_found_rows'  => true,
    ] );
    if ( $latest_q->have_posts() ) {
        $featured_season = $latest_q->posts[0];
    }
    wp_reset_postdata();
}
?>

<main id="main" class="site-main" role="main">

    <!-- ── Archive Hero ─────────────────────────────────────────────── -->
    <section class="section--dark section--padded" aria-labelledby="archive-hero-heading">
        <div class="container container--medium">
            <div class="archive-hero">
                <p class="archive-hero__eyebrow">Tastemakers</p>
                <h1 class="archive-hero__headline" id="archive-hero-heading">
                    Conversations with the people shaping luxury
                </h1>
                <div class="archive-hero__body">
                    <p>A podcast exploring craft, culture and commerce with the founders, creative directors and strategists defining what luxury means today.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ── Featured Season ──────────────────────────────────────────── -->
    <?php if ( $featured_season ) :
        $fs_id       = $featured_season->ID;
        $fs_number   = get_field( 'ps_season_number', $fs_id );
        $fs_subtitle = get_field( 'ps_subtitle', $fs_id );
        $fs_theme    = get_field( 'ps_theme', $fs_id );
        $fs_thesis   = get_field( 'ps_thesis', $fs_id );
        $fs_artwork  = get_field( 'ps_artwork', $fs_id );
    ?>
    <section class="section--dark section--padded">
        <div class="container container--wide">
            <div class="single-season__artwork-layout">

                <?php if ( $fs_artwork ) : ?>
                <figure class="single-season__artwork" aria-hidden="true">
                    <img src="<?php echo esc_url( $fs_artwork['url'] ); ?>"
                         alt="<?php echo esc_attr( $fs_artwork['alt'] ?? '' ); ?>"
                         width="<?php echo esc_attr( $fs_artwork['width'] ?? '' ); ?>"
                         height="<?php echo esc_attr( $fs_artwork['height'] ?? '' ); ?>"
                         loading="lazy">
                </figure>
                <?php endif; ?>

                <div>
                    <?php if ( $fs_number ) : ?>
                    <p class="eyebrow">Season <?php echo esc_html( $fs_number ); ?></p>
                    <?php endif; ?>

                    <h2 class="single-hero__title" style="margin-top: var(--space-3);">
                        <?php echo esc_html( get_the_title( $fs_id ) ); ?>
                    </h2>

                    <?php if ( $fs_subtitle ) : ?>
                    <p class="single-hero__standfirst"><?php echo esc_html( $fs_subtitle ); ?></p>
                    <?php endif; ?>

                    <?php if ( $fs_thesis ) : ?>
                    <div class="single-season__thesis" style="margin-top: var(--space-4);">
                        <?php echo wp_kses_post( wpautop( $fs_thesis ) ); ?>
                    </div>
                    <?php endif; ?>

                    <div style="margin-top: var(--space-6);">
                        <a href="<?php echo esc_url( get_permalink( $fs_id ) ); ?>" class="btn btn--primary">
                            Explore Season
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- ── All Seasons ──────────────────────────────────────────────── -->
    <section class="section--padded">
        <div class="container container--wide">

            <?php if ( have_posts() ) : ?>

            <h2 class="related-section__headline">All Seasons</h2>

            <ul class="no-list flow">
                <?php while ( have_posts() ) : the_post();
                    $s_id       = get_the_ID();
                    $s_number   = get_field( 'ps_season_number' );
                    $s_subtitle = get_field( 'ps_subtitle' );
                    $s_artwork  = get_field( 'ps_artwork' );
                ?>
                <li class="season-card">
                    <a href="<?php the_permalink(); ?>"
                       class="season-card__link"
                       aria-label="<?php echo esc_attr( 'Explore: ' . get_the_title() ); ?>">

                        <?php if ( $s_artwork ) : ?>
                        <figure class="season-card__artwork" aria-hidden="true">
                            <img src="<?php echo esc_url( $s_artwork['url'] ); ?>"
                                 alt="<?php echo esc_attr( $s_artwork['alt'] ?? '' ); ?>"
                                 loading="lazy">
                        </figure>
                        <?php endif; ?>

                        <div>
                            <?php if ( $s_number ) : ?>
                            <p class="season-card__eyebrow">Season <?php echo esc_html( $s_number ); ?></p>
                            <?php endif; ?>

                            <h3 class="season-card__title"><?php the_title(); ?></h3>

                            <?php if ( $s_subtitle ) : ?>
                            <p class="season-card__subtitle"><?php echo esc_html( $s_subtitle ); ?></p>
                            <?php endif; ?>
                        </div>
                    </a>
                </li>
                <?php endwhile; ?>
            </ul>

            <?php else : ?>

            <div class="empty-state">
                <p class="empty-state__message">Podcast seasons will appear here as they are published.</p>
            </div>

            <?php endif; ?>

        </div>
    </section>

    <!-- ── CTA Footer ───────────────────────────────────────────────── -->
    <?php get_template_part( 'parts/global/cta-footer' ); ?>

</main>

<?php get_footer(); ?>
