<?php
/**
 * Part: Related Podcast Episodes
 * Location: parts/related/episodes.php
 *
 * Renders related episode cards. Accepts a single WP_Post or array via
 * set_query_var — handles both post_object and relationship field types.
 *
 * Usage:
 *   set_query_var( 'summit_related_episodes', get_field('ep_related_episodes') );
 *   set_query_var( 'summit_related_heading', 'More Episodes' ); // optional
 *   get_template_part( 'parts/related/episodes' );
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

$raw      = get_query_var( 'summit_related_episodes', [] );
$heading  = get_query_var( 'summit_related_heading',  'Related Episodes' );

// Normalise single post_object or relationship array.
if ( $raw instanceof WP_Post ) {
    $episodes = [ $raw ];
} elseif ( is_array( $raw ) ) {
    $episodes = array_filter( $raw, fn( $p ) => $p instanceof WP_Post );
} else {
    return;
}

if ( empty( $episodes ) ) {
    return;
}
?>

<section class="related-episodes" aria-labelledby="related-episodes-heading">
    <div class="container container--wide">

        <header class="related-episodes__header">
            <h2 class="related-episodes__heading" id="related-episodes-heading">
                <?php echo esc_html( $heading ); ?>
            </h2>
        </header>

        <ul class="episode-grid episode-grid--related" role="list">
            <?php foreach ( $episodes as $ep ) :
                $deck    = get_field( 'ep_deck',           $ep->ID );
                $guest   = get_field( 'ep_guest_name',     $ep->ID );
                $company = get_field( 'ep_guest_company',  $ep->ID );
                $ep_num  = get_field( 'ep_episode_number', $ep->ID );
                $dur     = get_field( 'ep_duration',       $ep->ID );
                $season  = get_field( 'ep_season',         $ep->ID );
                $thumb   = get_the_post_thumbnail( $ep->ID, 'medium' );

                $s_num    = ( $season instanceof WP_Post ) ? get_field( 'ps_season_number', $season->ID ) : null;
                $ep_label = ( $s_num && $ep_num )
                            ? 'S' . absint( $s_num ) . ' E' . absint( $ep_num )
                            : ( $ep_num ? 'Episode ' . absint( $ep_num ) : '' );
            ?>
            <li class="episode-card">
                <a href="<?php echo esc_url( get_permalink( $ep->ID ) ); ?>"
                   class="episode-card__link"
                   aria-label="<?php echo esc_attr( 'Listen: ' . get_the_title( $ep->ID ) ); ?>">

                    <?php if ( $thumb ) : ?>
                    <figure class="episode-card__media" aria-hidden="true">
                        <?php echo $thumb; ?>
                    </figure>
                    <?php endif; ?>

                    <div class="episode-card__body">
                        <div class="episode-card__meta">
                            <?php if ( $ep_label ) : ?>
                            <span class="episode-card__number"><?php echo esc_html( $ep_label ); ?></span>
                            <?php endif; ?>
                            <?php if ( $dur ) : ?>
                            <span class="episode-card__duration"><?php echo esc_html( $dur ); ?></span>
                            <?php endif; ?>
                        </div>

                        <h3 class="episode-card__title">
                            <?php echo esc_html( get_the_title( $ep->ID ) ); ?>
                        </h3>

                        <?php if ( $guest ) : ?>
                        <p class="episode-card__guest">
                            <?php echo esc_html( $guest );
                            echo $company ? ' &mdash; ' . esc_html( $company ) : ''; ?>
                        </p>
                        <?php endif; ?>

                        <?php if ( $deck ) : ?>
                        <p class="episode-card__deck"><?php echo esc_html( $deck ); ?></p>
                        <?php endif; ?>

                        <span class="episode-card__cta" aria-hidden="true">Listen Now</span>
                    </div>

                </a>
            </li>
            <?php endforeach; ?>
        </ul>

    </div>
</section>
