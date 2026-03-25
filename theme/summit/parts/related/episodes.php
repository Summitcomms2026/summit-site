<?php
/**
 * Part: Related Episodes
 * Location: parts/related/episodes.php
 *
 * Renders related episode cards with thumbnails, season/episode labels,
 * guest info, and episode deck. Accepts posts via $args (WordPress 5.5+).
 *
 * Usage:
 *   get_template_part( 'parts/related/episodes', null, [
 *       'posts'    => $related_episodes,
 *       'headline' => 'More Episodes',
 *   ] );
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

$posts    = $args['posts']    ?? [];
$headline = $args['headline'] ?? 'More Episodes';

// Normalise: ACF post_object fields may return a single post.
if ( $posts instanceof WP_Post ) {
    $posts = [ $posts ];
}

if ( is_array( $posts ) ) {
    $posts = array_filter( $posts, fn( $p ) => $p instanceof WP_Post );
}

if ( empty( $posts ) ) {
    return;
}
?>

<section class="related-section section--tinted section--padded" aria-labelledby="related-episodes-heading">
    <div class="container container--wide">

        <h2 class="related-section__headline" id="related-episodes-heading">
            <?php echo esc_html( $headline ); ?>
        </h2>

        <ul class="episode-list" role="list">
            <?php foreach ( $posts as $episode ) :
                $ep_id   = $episode->ID;
                $deck    = get_field( 'ep_deck',           $ep_id );
                $guest   = get_field( 'ep_guest_name',     $ep_id );
                $company = get_field( 'ep_guest_company',  $ep_id );
                $ep_num  = get_field( 'ep_episode_number', $ep_id );
                $dur     = get_field( 'ep_duration',       $ep_id );
                $season  = get_field( 'ep_season',         $ep_id );
                $thumb   = get_the_post_thumbnail( $ep_id, 'medium' );

                $s_num    = ( $season instanceof WP_Post ) ? get_field( 'ps_season_number', $season->ID ) : null;
                $ep_label = ( $s_num && $ep_num )
                            ? 'S' . absint( $s_num ) . ' E' . absint( $ep_num )
                            : ( $ep_num ? 'Episode ' . absint( $ep_num ) : '' );
            ?>
            <li class="episode-card">
                <a href="<?php echo esc_url( get_permalink( $ep_id ) ); ?>"
                   class="episode-card__link"
                   aria-label="<?php echo esc_attr( 'Listen: ' . get_the_title( $ep_id ) ); ?>">

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
                            <?php echo esc_html( get_the_title( $ep_id ) ); ?>
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
