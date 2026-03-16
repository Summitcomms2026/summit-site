<?php
/**
 * Template: Single Podcast Episode
 * Location: single-podcast_episode.php
 *
 * WordPress template hierarchy:
 *   single-podcast_episode.php → single.php → index.php
 *
 * Component sequence (summit_page_specs.md §13):
 *   1.  Episode hero — title, deck, guest summary, S/E label
 *   2.  Player block — audio embed + platform links
 *   3.  Guest module — portrait, name, title, company, bio
 *   4.  Show notes
 *   5.  Pull quote
 *   6.  Transcript (collapsible via <details>)
 *   7.  Related episodes  → parts/related/episodes.php
 *   8.  Related articles  → parts/related/articles.php
 *   9.  Subscribe         → parts/global/subscribe.php
 *   10. CTA footer        → parts/global/cta-footer.php
 *
 * SEO & Schema — not injected here.
 * Output handled in inc/seo.php:
 *   - summit_seo_meta()        hooked on wp_head
 *   - summit_schema_episode()  hooked on wp_head (schema.org/PodcastEpisode + AudioObject)
 * ACF fields consumed: ep_seo_title, ep_meta_desc, ep_og_image, ep_canonical
 * Schema fields: get_the_title(), get_the_date('c'), ep_audio_embed,
 *                ep_guest_name, ep_duration, ep_og_image, get_permalink()
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
    the_post();

    // ── Field retrieval ───────────────────────────────────────────────────
    $season           = get_field( 'ep_season' );            // WP_Post
    $ep_number        = get_field( 'ep_episode_number' );
    $deck             = get_field( 'ep_deck' );
    $duration         = get_field( 'ep_duration' );
    $guest_name       = get_field( 'ep_guest_name' );
    $guest_title      = get_field( 'ep_guest_title' );
    $guest_company    = get_field( 'ep_guest_company' );
    $guest_bio        = get_field( 'ep_guest_bio' );
    $guest_portrait   = get_field( 'ep_guest_portrait' );    // ACF image array
    $audio_embed      = get_field( 'ep_audio_embed' );
    $video_embed      = get_field( 'ep_video_embed' );
    $platform_links   = get_field( 'ep_platform_links' );    // repeater
    $sponsor_credits  = get_field( 'ep_sponsor_credits' );
    $show_notes       = get_field( 'ep_show_notes' );
    $transcript       = get_field( 'ep_transcript' );
    $transcript_vis   = get_field( 'ep_transcript_visible' );
    $pull_quote       = get_field( 'ep_pull_quote' );
    $related_articles = get_field( 'ep_related_articles' );  // relationship → array
    $related_episodes = get_field( 'ep_related_episodes' );  // relationship → array

    // Season context for breadcrumb and S/E label
    $season_number = ( $season instanceof WP_Post ) ? get_field( 'ps_season_number', $season->ID ) : null;
    $season_title  = ( $season instanceof WP_Post ) ? get_the_title( $season->ID ) : '';
    $season_url    = ( $season instanceof WP_Post ) ? get_permalink( $season->ID ) : '';

    $ep_label = '';
    if ( $season_number && $ep_number ) {
        $ep_label = 'S' . absint( $season_number ) . ' E' . absint( $ep_number );
    } elseif ( $ep_number ) {
        $ep_label = 'Episode ' . absint( $ep_number );
    }

    // Platform label map
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

<main id="main" class="site-main site-main--episode" role="main">

    <!-- ── 1. Episode hero ──────────────────────────────────────────────── -->
    <section class="episode-hero" aria-labelledby="episode-hero-title">
        <div class="container container--medium">

            <nav class="episode-hero__breadcrumb" aria-label="Episode navigation">
                <a href="<?php echo esc_url( home_url( '/tastemakers' ) ); ?>">Tastemakers</a>
                <?php if ( $season_url && $season_title ) : ?>
                <span aria-hidden="true">/</span>
                <a href="<?php echo esc_url( $season_url ); ?>"><?php echo esc_html( $season_title ); ?></a>
                <?php endif; ?>
            </nav>

            <header class="episode-hero__header">

                <?php if ( $ep_label ) : ?>
                <p class="episode-hero__label"><?php echo esc_html( $ep_label ); ?></p>
                <?php endif; ?>

                <h1 class="episode-hero__title" id="episode-hero-title">
                    <?php the_title(); ?>
                </h1>

                <?php if ( $deck ) : ?>
                <p class="episode-hero__deck"><?php echo esc_html( $deck ); ?></p>
                <?php endif; ?>

                <div class="episode-hero__meta">
                    <?php if ( $guest_name ) : ?>
                    <span class="episode-hero__guest">
                        <?php echo esc_html( $guest_name );
                        if ( $guest_title && $guest_company ) {
                            echo ' &mdash; ' . esc_html( $guest_title ) . ', ' . esc_html( $guest_company );
                        } ?>
                    </span>
                    <?php endif; ?>
                    <?php if ( $duration ) : ?>
                    <span class="episode-hero__duration"><?php echo esc_html( $duration ); ?></span>
                    <?php endif; ?>
                    <time class="episode-hero__date"
                          datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
                        <?php echo esc_html( get_the_date( 'j F Y' ) ); ?>
                    </time>
                </div>

            </header>

            <?php if ( has_post_thumbnail() ) : ?>
            <figure class="episode-hero__artwork" aria-hidden="true">
                <?php the_post_thumbnail( 'large', [ 'loading' => 'eager' ] ); ?>
            </figure>
            <?php endif; ?>

        </div>
    </section>

    <!-- ── 2. Player block ──────────────────────────────────────────────── -->
    <?php if ( $audio_embed ) : ?>
    <section class="episode-player" aria-labelledby="episode-player-heading">
        <div class="container container--medium">

            <h2 class="screen-reader-text" id="episode-player-heading">Listen to this episode</h2>

            <div class="episode-player__embed">
                <?php
                if ( filter_var( $audio_embed, FILTER_VALIDATE_URL ) ) {
                    if ( preg_match( '/\.(mp3|m4a|ogg|wav)(\?.*)?$/i', $audio_embed ) ) {
                        // Direct audio file → native <audio>
                        printf(
                            '<audio controls preload="metadata" class="episode-player__audio"><source src="%s" type="audio/mpeg"><p>Your browser does not support audio playback. <a href="%s">Download the episode</a>.</p></audio>',
                            esc_url( $audio_embed ),
                            esc_url( $audio_embed )
                        );
                    } else {
                        // JW Player or streaming URL → iframe
                        printf(
                            '<div class="episode-player__iframe-wrap"><iframe src="%s" allow="autoplay" loading="lazy" title="Listen to %s"></iframe></div>',
                            esc_url( $audio_embed ),
                            esc_attr( get_the_title() )
                        );
                    }
                } else {
                    // Editor-entered embed code (Spotify widget, etc.)
                    echo wp_kses_post( $audio_embed );
                }
                ?>
            </div>

            <?php if ( ! empty( $platform_links ) ) : ?>
            <nav class="episode-player__platforms" aria-label="Listen on other platforms">
                <ul class="episode-player__platform-list" role="list">
                    <?php foreach ( $platform_links as $pf ) :
                        if ( empty( $pf['platform_url'] ) ) { continue; }
                        $pl_label = $platform_names[ $pf['platform_name'] ] ?? 'Listen';
                    ?>
                    <li>
                        <a href="<?php echo esc_url( $pf['platform_url'] ); ?>"
                           class="episode-player__platform-link"
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
    <?php endif; ?>

    <!-- ── 3. Guest module ──────────────────────────────────────────────── -->
    <?php if ( $guest_name ) : ?>
    <section class="guest-module" aria-labelledby="guest-module-heading">
        <div class="container container--medium">
            <div class="guest-module__inner">

                <?php if ( is_array( $guest_portrait ) && ! empty( $guest_portrait['url'] ) ) : ?>
                <figure class="guest-module__portrait" aria-hidden="true">
                    <img src="<?php echo esc_url( $guest_portrait['url'] ); ?>"
                         alt="<?php echo esc_attr( $guest_portrait['alt'] ?: 'Portrait of ' . $guest_name ); ?>"
                         width="<?php echo absint( $guest_portrait['width']  ?? 240 ); ?>"
                         height="<?php echo absint( $guest_portrait['height'] ?? 240 ); ?>"
                         loading="lazy">
                </figure>
                <?php endif; ?>

                <div class="guest-module__body">
                    <h2 class="screen-reader-text" id="guest-module-heading">About the guest</h2>
                    <p class="guest-module__name"><?php echo esc_html( $guest_name ); ?></p>
                    <?php if ( $guest_title || $guest_company ) : ?>
                    <p class="guest-module__role">
                        <?php
                        if ( $guest_title && $guest_company ) {
                            echo esc_html( $guest_title ) . ', ' . esc_html( $guest_company );
                        } else {
                            echo esc_html( $guest_title ?: $guest_company );
                        }
                        ?>
                    </p>
                    <?php endif; ?>
                    <?php if ( $guest_bio ) : ?>
                    <p class="guest-module__bio"><?php echo esc_html( $guest_bio ); ?></p>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- ── 4. Show notes ────────────────────────────────────────────────── -->
    <?php if ( $show_notes ) : ?>
    <section class="show-notes" aria-labelledby="show-notes-heading">
        <div class="container container--narrow">
            <h2 class="show-notes__heading" id="show-notes-heading">Show Notes</h2>
            <div class="show-notes__body wysiwyg">
                <?php echo wp_kses_post( $show_notes ); ?>
            </div>
            <?php if ( $sponsor_credits ) : ?>
            <p class="show-notes__sponsor"><?php echo esc_html( $sponsor_credits ); ?></p>
            <?php endif; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- ── 5. Pull quote ────────────────────────────────────────────────── -->
    <?php if ( $pull_quote ) : ?>
    <aside class="pull-quote-block" aria-label="Episode quote">
        <div class="container container--medium">
            <blockquote class="pull-quote-block__quote">
                <p><?php echo esc_html( $pull_quote ); ?></p>
                <?php if ( $guest_name ) : ?>
                <footer>
                    <cite><?php echo esc_html( $guest_name ); ?></cite>
                </footer>
                <?php endif; ?>
            </blockquote>
        </div>
    </aside>
    <?php endif; ?>

    <!-- ── 6. Transcript ────────────────────────────────────────────────── -->
    <?php if ( $transcript && $transcript_vis !== false ) : ?>
    <section class="transcript-block" aria-labelledby="transcript-heading">
        <div class="container container--narrow">
            <details class="transcript-block__details">
                <summary class="transcript-block__toggle">
                    <h2 class="transcript-block__heading" id="transcript-heading">
                        Full Transcript
                    </h2>
                </summary>
                <div class="transcript-block__body wysiwyg wysiwyg--transcript">
                    <?php echo wp_kses_post( $transcript ); ?>
                </div>
            </details>
        </div>
    </section>
    <?php endif; ?>

    <!-- ── 7. Related episodes ──────────────────────────────────────────── -->
    <?php if ( ! empty( $related_episodes ) ) :
        set_query_var( 'summit_related_episodes', $related_episodes );
        set_query_var( 'summit_related_heading',  'More Episodes' );
        get_template_part( 'parts/related/episodes' );
    endif; ?>

    <!-- ── 8. Related articles ──────────────────────────────────────────── -->
    <?php if ( ! empty( $related_articles ) ) :
        set_query_var( 'summit_related_articles', $related_articles );
        set_query_var( 'summit_related_heading',  'Related Reading' );
        get_template_part( 'parts/related/articles' );
    endif; ?>

    <!-- ── 9. Subscribe ─────────────────────────────────────────────────── -->
    <?php
    set_query_var( 'summit_subscribe_heading', 'Subscribe to Tastemakers' );
    set_query_var( 'summit_subscribe_sub',
        'New conversations on luxury, culture and the forces shaping taste — available on all major platforms.' );
    get_template_part( 'parts/global/subscribe' );
    ?>

    <!-- ── 10. CTA footer ───────────────────────────────────────────────── -->
    <?php get_template_part( 'parts/global/cta-footer' ); ?>

</main>

<?php
endwhile;
get_footer();
