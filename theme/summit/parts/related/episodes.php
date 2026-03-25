<?php
/**
 * Part: Related Episodes
 * Location: parts/related/episodes.php
 *
 * Renders a related episode list from a supplied array of WP_Post objects.
 * Guard: suppresses output if no posts provided.
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
