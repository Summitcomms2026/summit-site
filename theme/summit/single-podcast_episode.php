<?php
/**
 * Template: Podcast Episode Single
 * Location: single-podcast_episode.php
 * URL: /tastemakers/episode/[slug]
 *
 * Renders a single podcast episode with player, guest info,
 * transcript, and related content. Sections suppress cleanly
 * when their data is absent.
 *
 * ACF fields: ep_* prefix (see acf-fields.php)
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

get_header();
the_post();

$ep_id      = get_the_ID();
$ep_number  = get_field( 'ep_episode_number' );
$deck       = get_field( 'ep_deck' );
$duration   = get_field( 'ep_duration' );
$pull_quote = get_field( 'ep_pull_quote' );
$audio      = get_field( 'ep_audio_embed' );

// Guest fields.
$guest_name    = get_field( 'ep_guest_name' );
$guest_title   = get_field( 'ep_guest_title' );
$guest_company = get_field( 'ep_guest_company' );
$guest_image   = get_field( 'ep_guest_portrait' );
$guest_bio     = get_field( 'ep_guest_bio' );

// Transcript.
$transcript_type = get_field( 'ep_transcript_type' );
$transcript_text = get_field( 'ep_transcript_text' );

// Season context.
$season = get_field( 'ep_season' );
$season_number = $season ? get_field( 'ps_season_number', $season->ID ) : '';
$season_platforms = $season ? get_field( 'ps_platform_links', $season->ID ) : [];

// Related content.
$related_articles  = get_field( 'ep_related_articles' );
// Note: ep_related_episodes exists but is rendered via parts/related/episodes
// Note: no ep_related_cases field exists in the ACF registration
$related_download  = get_field( 'ep_related_download' );
?>

<main id="main" class="site-main" role="main">

    <!-- ── Hero ─────────────────────────────────────────────────────── -->
    <section class="section--dark section--padded" aria-labelledby="single-hero-title">
        <div class="container container--medium">
            <div class="single-hero">

                <p class="eyebrow">
                    <?php if ( $season_number ) : ?>
                        Season <?php echo esc_html( $season_number ); ?><?php if ( $ep_number ) echo ', '; ?>
                    <?php endif; ?>
                    <?php if ( $ep_number ) : ?>
                        Episode <?php echo esc_html( $ep_number ); ?>
                    <?php endif; ?>
                </p>

                <h1 class="single-hero__title" id="single-hero-title">
                    <?php the_title(); ?>
                </h1>

                <?php if ( $deck ) : ?>
                <p class="single-episode__deck"><?php echo esc_html( $deck ); ?></p>
                <?php endif; ?>

                <div class="meta-bar">
                    <?php if ( $guest_name ) : ?>
                    <span>with <?php echo esc_html( $guest_name ); ?></span>
                    <?php endif; ?>
                    <?php if ( $duration ) : ?>
                    <span class="meta-bar__separator"></span>
                    <span><?php echo esc_html( $duration ); ?></span>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </section>

    <!-- ── Audio Player ─────────────────────────────────────────────── -->
    <?php if ( $audio ) : ?>
    <section class="section--dark section--padded">
        <div class="container container--medium">
            <div class="embed-wrap">
                <?php echo $audio; // phpcs:ignore -- oEmbed output. ?>
            </div>

            <?php if ( ! empty( $season_platforms ) ) : ?>
            <ul class="platform-links" style="margin-top: var(--space-6);">
                <?php foreach ( $season_platforms as $link ) : ?>
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
    </section>
    <?php endif; ?>

    <!-- ── Guest ────────────────────────────────────────────────────── -->
    <?php if ( $guest_name ) : ?>
    <section class="section--padded">
        <div class="container container--medium">
            <div class="guest-card">

                <?php if ( $guest_image ) : ?>
                <figure class="guest-card__portrait">
                    <img src="<?php echo esc_url( $guest_image['url'] ); ?>"
                         alt="<?php echo esc_attr( $guest_image['alt'] ?? $guest_name ); ?>"
                         loading="lazy">
                </figure>
                <?php endif; ?>

                <div>
                    <h2 class="guest-card__name"><?php echo esc_html( $guest_name ); ?></h2>

                    <?php if ( $guest_title || $guest_company ) : ?>
                    <p class="guest-card__role">
                        <?php
                        $role_parts = array_filter( [ $guest_title, $guest_company ] );
                        echo esc_html( implode( ', ', $role_parts ) );
                        ?>
                    </p>
                    <?php endif; ?>

                    <?php if ( $guest_bio ) : ?>
                    <div class="guest-card__bio">
                        <?php echo wp_kses_post( wpautop( $guest_bio ) ); ?>
                    </div>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- ── Pull Quote ───────────────────────────────────────────────── -->
    <?php if ( $pull_quote ) : ?>
    <section class="section--tinted section--padded">
        <div class="container container--narrow">
            <blockquote class="pull-quote">
                <?php echo esc_html( $pull_quote ); ?>
            </blockquote>
        </div>
    </section>
    <?php endif; ?>

    <!-- ── Transcript ───────────────────────────────────────────────── -->
    <?php if ( $transcript_text ) : ?>
    <section class="section--padded">
        <div class="container container--narrow">
            <div class="transcript-toggle">
                <button class="transcript-toggle__trigger"
                        aria-expanded="false"
                        aria-controls="episode-transcript"
                        onclick="var c=document.getElementById('episode-transcript');var v=c.dataset.visible==='true';c.dataset.visible=v?'false':'true';this.setAttribute('aria-expanded',v?'false':'true');">
                    Transcript
                </button>
                <div class="transcript-toggle__content prose"
                     id="episode-transcript"
                     data-visible="false">
                    <?php echo wp_kses_post( $transcript_text ); ?>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- ── Related Content ──────────────────────────────────────────── -->
    <?php
    get_template_part( 'parts/related/articles', null, [
        'posts'    => $related_articles ?: [],
        'headline' => 'Related Reading',
    ] );

    get_template_part( 'parts/related/downloads', null, [
        'posts'    => $related_download ?: [],
        'headline' => 'Related Download',
    ] );
    ?>

    <!-- ── CTA Footer ───────────────────────────────────────────────── -->
    <?php get_template_part( 'parts/global/cta-footer' ); ?>

</main>

<?php get_footer(); ?>
