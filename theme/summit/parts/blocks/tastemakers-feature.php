<?php
/**
 * Part: Tastemakers Feature Panel
 * Location: parts/blocks/tastemakers-feature.php
 *
 * Two-column layout matching approved 1920px comp:
 *   Left:  show branding, artwork, CTA + social, platform strip
 *   Right: season label, season title, thesis / body copy
 *
 * ACF fields (from front page):
 *   home_tastemakers_headline text        optional
 *   home_tastemakers_body     textarea    optional
 *   home_tastemakers_season   post_object optional — podcast_season
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

$img_dir = get_template_directory_uri() . '/assets/img/';

// Season data — ACF or hardcoded fallback.
$season = get_field( 'home_tastemakers_season' );

$season_title = 'The New Value Equation';
$season_label = 'Season One';
$season_url   = home_url( '/tastemakers/season-one/' );
$artwork_url  = get_template_directory_uri() . '/assets/img/tastemakers-showcover.jpg';

if ( ! empty( $season ) ) {
    $season_id    = $season->ID;
    $season_title = get_the_title( $season_id );
    $season_label = 'Season ' . ( get_field( 'ps_season_number', $season_id ) ?: 'One' );
    $season_url   = get_permalink( $season_id );
    $artwork_acf  = get_field( 'ps_artwork', $season_id );
    if ( $artwork_acf ) {
        $artwork_url = $artwork_acf['url'];
    }
}

$thesis_intro = 'What do we mean by luxury? It\'s that softly blinking light on an Apple monitor that tells you this thing is breathing. Luxury is the proof of effort and care beyond what is necessary.';

$thesis_body_1 = 'This is an adventure through the world of superior quality — and the forces re-organising modern luxury. From art to design, craft to scarcity, leadership to clientele, technology to experience. This series investigates a simple question: why do certain brands become timeless tastemakers, commanding belief at a time when belief is harder to earn, easier to lose, and prohibitively expensive to fake?';

$thesis_body_2 = 'In our episodes, we\'ll go where the story turns properly interesting: into the grey markets that ruthlessly re-price desire, into China\'s domestic luxury insurgency, into ateliers where time is the point, into boardrooms where taste meets discipline. We\'ll follow the trail from obsession to object, from standards to culture, from one unapologetic decision that makes a brand feel inevitable. Because in luxury, the product is never the whole story. The story is how we engage with the material world — and what we choose to believe it says about us.';

// Override with ACF if available.
$acf_body = get_field( 'home_tastemakers_body' );
?>

<section class="tastemakers-feature" aria-labelledby="tastemakers-heading">
    <div class="container container--wide">

        <!-- ── Top row: heading left, Season One button right ──── -->
        <header class="tm__header" data-reveal="fade">
            <h2 class="tm__heading">
                Tastemakers Podcast
                <svg class="tm__headphones" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 18v-6a9 9 0 0 1 18 0v6"/><path d="M21 19a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3v5zM3 19a2 2 0 0 0 2 2h1a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2H3v5z"/></svg>
            </h2>
            <a href="<?php echo esc_url( $season_url ); ?>"
               class="btn btn--secondary tm__btn-season" data-reveal data-reveal-delay="1">
                Season One
            </a>
        </header>

        <div class="tm__layout">

            <!-- ── Left: Show branding ──────────────────────────────── -->
            <div class="tm__show">

                <div class="tm__show-info" data-reveal="fade" data-reveal-delay="1">

                    <figure class="tm__artwork" aria-hidden="true">
                        <img src="<?php echo esc_url( $artwork_url ); ?>"
                             alt="" width="240" height="240" loading="lazy">
                    </figure>

                    <div class="tm__show-meta">
                        <p class="tm__host">Gregory Gray</p>
                        <figure class="tm__logo-mark" aria-hidden="true">
                            <img src="<?php echo esc_url( $img_dir . 'tm.png' ); ?>"
                                 alt="Tastemakers" loading="lazy">
                        </figure>
                        <p class="tm__tagline">A show on rewriting luxury for modern times</p>

                        <div class="tm__show-actions">
                            <a href="<?php echo esc_url( home_url( '/tastemakers/' ) ); ?>"
                               class="btn tm__btn-episodes">View Episodes</a>

                            <div class="tm__social">
                                <a href="https://www.instagram.com/summitcomms/" aria-label="Instagram" rel="noopener" target="_blank">
                                    <img src="<?php echo esc_url( $img_dir . 'icon_instagram.svg' ); ?>" alt="" width="20" height="20">
                                </a>
                                <a href="https://x.com/summitcomms" aria-label="X" rel="noopener" target="_blank">
                                    <img src="<?php echo esc_url( $img_dir . 'icon_x.svg' ); ?>" alt="" width="20" height="20">
                                </a>
                                <a href="https://www.linkedin.com/company/summitcomms/" aria-label="LinkedIn" rel="noopener" target="_blank">
                                    <img src="<?php echo esc_url( $img_dir . 'icon_linkedin.svg' ); ?>" alt="" width="20" height="20">
                                </a>
                                <a href="https://www.facebook.com/summitcomms" aria-label="Facebook" rel="noopener" target="_blank">
                                    <img src="<?php echo esc_url( $img_dir . 'icon_facebook.svg' ); ?>" alt="" width="20" height="20">
                                </a>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="tm__platforms" data-reveal="fade" data-reveal-delay="2">
                    <p class="tm__platforms-label">Listen or watch on your favourite platform</p>
                    <div class="tm__platforms-row">
                        <a href="https://podcasts.apple.com/" rel="noopener" target="_blank" class="tm__platform">
                            <img src="<?php echo esc_url( $img_dir . 'icon_apple.svg' ); ?>" alt="" width="18" height="18">
                            <span>Apple Podcasts</span>
                        </a>
                        <a href="https://open.spotify.com/" rel="noopener" target="_blank" class="tm__platform">
                            <img src="<?php echo esc_url( $img_dir . 'icon_spotify.svg' ); ?>" alt="" width="18" height="18">
                            <span>Spotify</span>
                        </a>
                        <a href="https://music.amazon.com/" rel="noopener" target="_blank" class="tm__platform">
                            <img src="<?php echo esc_url( $img_dir . 'icon_amazon-music.svg' ); ?>" alt="" width="18" height="18">
                            <span>Amazon Music</span>
                        </a>
                        <a href="https://www.iheart.com/" rel="noopener" target="_blank" class="tm__platform">
                            <img src="<?php echo esc_url( $img_dir . 'icon_iheartradio.svg' ); ?>" alt="" width="18" height="18">
                            <span>iHeart</span>
                        </a>
                        <div class="tm__platform-more">
                            <button type="button" class="tm__platform tm__platform-more-btn">
                                <img src="<?php echo esc_url( $img_dir . 'icon_more-dots.svg' ); ?>" alt="" width="18" height="18">
                                <span>More</span>
                            </button>
                            <div class="tm__more-dropdown">
                                <a href="#">RSS</a>
                                <a href="https://music.amazon.com/">Amazon Music</a>
                                <a href="https://www.iheart.com/">iHeart</a>
                                <a href="https://www.youtube.com/">YouTube</a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- ── Right: Season content ────────────────────────────── -->
            <div class="tm__season" data-reveal="fade" data-reveal-delay="1">

                <p class="tm__season-label">Season One</p>

                <h3 class="tm__season-title">The New Value Equation</h3>

                <?php if ( $acf_body ) : ?>
                    <div class="tm__thesis">
                        <?php echo wp_kses_post( wpautop( $acf_body ) ); ?>
                    </div>
                <?php else : ?>
                    <div class="tm__thesis">
                        <p class="tm__thesis-intro"><?php echo esc_html( $thesis_intro ); ?></p>
                        <p><?php echo esc_html( $thesis_body_1 ); ?></p>
                        <p><?php echo esc_html( $thesis_body_2 ); ?></p>
                    </div>
                <?php endif; ?>

            </div>

        </div>

    </div>
</section>
