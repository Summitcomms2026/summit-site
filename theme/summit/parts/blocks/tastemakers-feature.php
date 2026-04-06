<?php
/**
 * Part: Tastemakers Feature Panel
 * Location: parts/blocks/tastemakers-feature.php
 *
 * Features a single podcast season on the homepage. Dark panel.
 *
 * Curated first: uses home_tastemakers_season post_object field.
 * Auto fallback: queries latest podcast_season by ps_is_featured
 *   flag, then by ps_season_number DESC.
 * Guard: section is not rendered when no seasons exist.
 *
 * ACF fields (from front page):
 *   home_tastemakers_headline text        optional
 *   home_tastemakers_body     textarea    optional
 *   home_tastemakers_season   post_object optional — podcast_season
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

// Curated selection.
$season = get_field( 'home_tastemakers_season' );

// Auto fallback — find featured season, then latest.
if ( empty( $season ) ) {
    $featured_q = new WP_Query( [
        'post_type'      => 'podcast_season',
        'posts_per_page' => 1,
        'meta_key'       => 'ps_is_featured',
        'meta_value'     => '1',
        'no_found_rows'  => true,
    ] );
    if ( $featured_q->have_posts() ) {
        $season = $featured_q->posts[0];
    }
    wp_reset_postdata();
}

if ( empty( $season ) ) {
    $latest_q = new WP_Query( [
        'post_type'      => 'podcast_season',
        'posts_per_page' => 1,
        'meta_key'       => 'ps_season_number',
        'orderby'        => 'meta_value_num',
        'order'          => 'DESC',
        'no_found_rows'  => true,
    ] );
    if ( $latest_q->have_posts() ) {
        $season = $latest_q->posts[0];
    }
    wp_reset_postdata();
}

// Guard — do not render without a season.
if ( empty( $season ) ) {
    return;
}

$headline  = get_field( 'home_tastemakers_headline' ) ?: 'Tastemakers';
$body      = get_field( 'home_tastemakers_body' );
$season_id = $season->ID;
$subtitle  = get_field( 'ps_subtitle', $season_id );
$theme     = get_field( 'ps_theme', $season_id );
$artwork   = get_field( 'ps_artwork', $season_id );
?>

<section class="tastemakers-feature section--padded" aria-labelledby="tastemakers-heading">
    <div class="container container--wide">

        <div class="tastemakers-feature__layout">

            <figure class="tastemakers-feature__artwork" aria-hidden="true" data-reveal="fade">
                <?php if ( $artwork ) : ?>
                <img src="<?php echo esc_url( $artwork['url'] ); ?>"
                     alt="<?php echo esc_attr( $artwork['alt'] ?? '' ); ?>"
                     width="<?php echo esc_attr( $artwork['width'] ?? '' ); ?>"
                     height="<?php echo esc_attr( $artwork['height'] ?? '' ); ?>"
                     loading="lazy">
                <?php else : ?>
                <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/tastemakers-showcover.jpg' ); ?>"
                     alt="" width="277" height="277" loading="lazy">
                <?php endif; ?>
            </figure>

            <div class="tastemakers-feature__content">

                <p class="tastemakers-feature__eyebrow" data-reveal="fade"><?php echo esc_html( $headline ); ?></p>

                <h2 class="tastemakers-feature__title" id="tastemakers-heading" data-reveal="fade">
                    <?php echo esc_html( get_the_title( $season_id ) ); ?>
                </h2>

                <?php if ( $subtitle ) : ?>
                <p class="tastemakers-feature__subtitle" data-reveal="fade" data-reveal-delay="1">
                    <?php echo esc_html( $subtitle ); ?>
                </p>
                <?php endif; ?>

                <?php if ( $theme ) : ?>
                <p class="tastemakers-feature__theme" data-reveal="fade" data-reveal-delay="1">
                    <?php echo esc_html( $theme ); ?>
                </p>
                <?php endif; ?>

                <?php if ( $body ) : ?>
                <div class="tastemakers-feature__body" data-reveal="fade" data-reveal-delay="2">
                    <?php echo wp_kses_post( wpautop( $body ) ); ?>
                </div>
                <?php endif; ?>

                <!-- Podcast platform icons -->
                <div class="tastemakers-feature__platforms" data-reveal="fade" data-reveal-delay="2">
                    <a href="https://open.spotify.com/" class="tastemakers-feature__platform-icon" aria-label="Spotify" rel="noopener" target="_blank">
                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 0C5.4 0 0 5.4 0 12s5.4 12 12 12 12-5.4 12-12S18.66 0 12 0zm5.521 17.34c-.24.359-.66.48-1.021.24-2.82-1.74-6.36-2.101-10.561-1.141-.418.122-.779-.179-.899-.539-.12-.421.18-.78.54-.9 4.56-1.021 8.52-.6 11.64 1.32.42.18.479.659.301 1.02zm1.44-3.3c-.301.42-.841.6-1.262.3-3.239-1.98-8.159-2.58-11.939-1.38-.479.12-1.02-.12-1.14-.6-.12-.48.12-1.021.6-1.141C9.6 9.9 15 10.561 18.72 12.84c.361.181.54.78.241 1.2zm.12-3.36C15.24 8.4 8.82 8.16 5.16 9.301c-.6.179-1.2-.181-1.38-.721-.18-.601.18-1.2.72-1.381 4.26-1.26 11.28-1.02 15.721 1.621.539.3.719 1.02.419 1.56-.299.421-1.02.599-1.559.3z"/></svg>
                    </a>
                    <a href="https://podcasts.apple.com/" class="tastemakers-feature__platform-icon" aria-label="Apple Podcasts" rel="noopener" target="_blank">
                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M5.34 0A5.328 5.328 0 000 5.34v13.32A5.328 5.328 0 005.34 24h13.32A5.328 5.328 0 0024 18.66V5.34A5.328 5.328 0 0018.66 0zm6.525 2.568c4.988 0 8.94 3.16 9.69 7.756l-.003.004a8.4 8.4 0 01-.186 3.092c-.19.72-.756.982-1.298.714-.524-.26-.676-.85-.508-1.546.24-.996.32-2.02.188-3.072-.644-4.108-4.478-7.05-8.53-6.556-4.186.51-7.216 4.174-6.72 8.394.278 2.368 1.422 4.252 3.26 5.602.26.19.48.402.476.728-.006.44-.372.804-.812.804a1.09 1.09 0 01-.628-.22C4.474 16.394 3.2 13.988 2.946 11.16c-.52-5.726 3.792-10.856 9.56-10.606.1.004.2.01.3.016l.059-.002zm-.076 3.772c3.472.016 6.32 2.64 6.564 6.076.037.512.01 1.02-.08 1.52-.152.862-.758 1.16-1.324.87-.476-.244-.622-.752-.506-1.378.058-.31.084-.632.074-.958-.128-3.086-2.86-5.456-5.98-5.18-2.944.26-5.226 2.834-5.08 5.82.072 1.49.612 2.8 1.58 3.876.23.258.392.53.326.876-.08.424-.46.72-.88.68a1.065 1.065 0 01-.558-.26C4.156 16.618 3.382 14.42 3.462 11.9c.156-4.894 4.422-8.296 8.326-7.56zm.148 3.836a3.04 3.04 0 012.826 3.236c-.02.348-.08.686-.186 1.014-.158.49-.514.706-.926.58-.378-.116-.554-.466-.48-.886.044-.24.076-.48.08-.726a1.54 1.54 0 00-1.396-1.608c-.876-.088-1.65.51-1.762 1.376a4.82 4.82 0 00-.046.732c-.002.066.008.134.012.2.07.89-.168 1.556-.866 2.044-.696.488-1.152 1.138-1.368 1.944-.378 1.406-.202 2.72.6 3.918.258.386.166.824-.148 1.066a.822.822 0 01-1.086-.12c-1.176-1.484-1.582-3.168-1.234-5.04.238-1.28.89-2.316 1.858-3.144.472-.404.532-.778.318-1.34-.764-2.004.16-4.26 2.164-4.98.26-.086.53-.144.804-.17l.104-.006c.244-.018.492-.01.732.03z"/></svg>
                    </a>
                    <a href="https://music.amazon.com/" class="tastemakers-feature__platform-icon" aria-label="Amazon Music" rel="noopener" target="_blank">
                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M13.958 10.09c0 1.232.029 2.256-.591 3.351-.502.891-1.301 1.438-2.186 1.438-1.214 0-1.922-.924-1.922-2.292 0-2.692 2.415-3.182 4.7-3.182v.685zm3.186 7.705a.66.66 0 01-.753.077c-1.058-.878-1.247-1.287-1.826-2.124-1.748 1.782-2.986 2.315-5.249 2.315-2.681 0-4.768-1.653-4.768-4.963 0-2.584 1.402-4.341 3.395-5.2 1.727-.756 4.14-.891 5.982-1.1v-.41c0-.753.058-1.643-.384-2.293-.385-.579-1.124-.82-1.775-.82-1.205 0-2.277.618-2.54 1.897-.054.285-.261.566-.546.58l-3.06-.33c-.258-.058-.543-.266-.47-.662C5.942 1.612 9.176 0 12.072 0c1.46 0 3.367.39 4.515 1.497 1.46 1.39 1.32 3.243 1.32 5.262v4.764c0 1.432.593 2.06 1.152 2.834.196.278.24.611-.01.819-.624.521-1.736 1.49-2.346 2.033l-.009-.006zm2.704-16.678C17.997.284 14.916-.395 12.166.202 7.299 1.189 3.449 4.558 1.654 8.932c-2.387 5.823-.758 12.558 4.18 16.655.212.176.476-.033.352-.238-1.475-2.574-2.427-5.5-2.427-8.564 0-5.552 3.202-10.443 7.844-12.744 2.36-1.17 5.255-1.59 7.766-.81.2.062.38-.137.272-.314z"/></svg>
                    </a>
                    <a href="https://www.iheart.com/" class="tastemakers-feature__platform-icon" aria-label="iHeart Radio" rel="noopener" target="_blank">
                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm0 18.77c-3.735 0-6.77-3.034-6.77-6.77S8.265 5.23 12 5.23s6.77 3.034 6.77 6.77-3.035 6.77-6.77 6.77zm0-11.54a4.77 4.77 0 100 9.54 4.77 4.77 0 000-9.54z"/></svg>
                    </a>
                </div>

                <div class="tastemakers-feature__actions" data-reveal="fade" data-reveal-delay="3">
                    <a href="<?php echo esc_url( get_permalink( $season_id ) ); ?>"
                       class="btn btn--primary">
                        Season One
                    </a>
                    <a href="<?php echo esc_url( home_url( '/tastemakers/' ) ); ?>"
                       class="btn btn--secondary">
                        View Episodes
                    </a>
                </div>

                <!-- Social icons -->
                <div class="tastemakers-feature__social" data-reveal="fade" data-reveal-delay="3">
                    <a href="https://www.linkedin.com/company/summitcomms/" class="tastemakers-feature__social-icon" aria-label="LinkedIn" rel="noopener" target="_blank">
                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                    </a>
                    <a href="https://www.instagram.com/summitcomms/" class="tastemakers-feature__social-icon" aria-label="Instagram" rel="noopener" target="_blank">
                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                    </a>
                    <a href="https://x.com/summitcomms" class="tastemakers-feature__social-icon" aria-label="X" rel="noopener" target="_blank">
                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                    </a>
                    <a href="https://www.facebook.com/summitcomms" class="tastemakers-feature__social-icon" aria-label="Facebook" rel="noopener" target="_blank">
                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    </a>
                </div>

            </div>

        </div>

    </div>
</section>
