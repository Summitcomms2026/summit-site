<?php
/**
 * Template: Podcast Theme Taxonomy Archive
 * Location: taxonomy-podcast_theme.php
 *
 * WordPress template hierarchy:
 *   taxonomy-podcast_theme-{term-slug}.php
 *   → taxonomy-podcast_theme.php     ← this file
 *   → taxonomy.php
 *   → archive.php
 *   → index.php
 *
 * The taxonomy is registered with the key 'podcast_theme'.
 * Its rewrite slug is 'theme' (registered in summit-core/inc/taxonomies.php).
 * Public URL: /theme/{term-slug}
 *
 * Example: the "Pricing" term is accessible at /theme/pricing
 *
 * The podcast_theme taxonomy is assigned to both podcast_episode and
 * podcast_season post types. Rather than relying on WordPress's default
 * archive loop — which would return a mixed, undifferentiated set of post
 * types — this template runs two separate WP_Query instances:
 *
 *   $episode_query  — podcast_episode posts tagged with this term
 *   $season_query   — podcast_season posts tagged with this term
 *
 * This keeps episodes and seasons in clearly labelled separate sections,
 * avoids editors having to interpret a mixed-type grid, and gives precise
 * control over ordering (episodes by date DESC, seasons by number DESC).
 *
 * Component sequence:
 *   1. Theme hero — breadcrumb, term name, term description
 *   2. Theme filter nav — all terms, current highlighted
 *   3. Episodes with this theme (WP_Query: podcast_episode + tax_query)
 *   4. Seasons with this theme (WP_Query: podcast_season + tax_query)
 *   5. CTA footer → parts/global/cta-footer.php
 *
 * SEO & Schema — not injected here.
 * Output handled in inc/seo.php.
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

get_header();

$current_term = get_queried_object(); // WP_Term for this podcast_theme

// All podcast themes for the filter nav
$all_themes = get_terms( [ 'taxonomy' => 'podcast_theme', 'hide_empty' => true ] );

// ── Episodes with this theme ──────────────────────────────────────────────
$episode_query = new WP_Query( [
    'post_type'   => 'podcast_episode',
    'tax_query'   => [ [
        'taxonomy' => 'podcast_theme',
        'field'    => 'term_id',
        'terms'    => $current_term->term_id,
    ] ],
    'orderby'     => 'date',
    'order'       => 'DESC',
] );

// ── Seasons with this theme ───────────────────────────────────────────────
$season_query = new WP_Query( [
    'post_type'      => 'podcast_season',
    'no_found_rows'  => true,
    'tax_query'      => [ [
        'taxonomy' => 'podcast_theme',
        'field'    => 'term_id',
        'terms'    => $current_term->term_id,
    ] ],
    'orderby'        => 'meta_value_num',
    'meta_key'       => 'ps_season_number',
    'order'          => 'DESC',
] );
?>

<main id="main" class="site-main site-main--theme-archive" role="main">

    <!-- ── 1. Theme hero ────────────────────────────────────────────────── -->
    <section class="archive-hero archive-hero--theme"
             aria-labelledby="theme-hero-title">
        <div class="container container--medium">
            <header class="archive-hero__header">

                <nav class="archive-hero__breadcrumb" aria-label="Podcast navigation">
                    <a href="<?php echo esc_url( home_url( '/tastemakers' ) ); ?>">
                        Tastemakers
                    </a>
                    <span aria-hidden="true">/</span>
                    <span aria-current="page">
                        <?php echo esc_html( $current_term->name ); ?>
                    </span>
                </nav>

                <p class="archive-hero__eyebrow">Podcast Theme</p>

                <h1 class="archive-hero__title" id="theme-hero-title">
                    <?php echo esc_html( $current_term->name ); ?>
                </h1>

                <?php if ( $current_term->description ) : ?>
                <p class="archive-hero__sub">
                    <?php echo esc_html( $current_term->description ); ?>
                </p>
                <?php else : ?>
                <p class="archive-hero__sub">
                    Episodes and seasons exploring
                    <?php echo esc_html( strtolower( $current_term->name ) ); ?>.
                </p>
                <?php endif; ?>

            </header>
        </div>
    </section>

    <!-- ── 2. Theme filter nav ──────────────────────────────────────────── -->
    <?php if ( ! is_wp_error( $all_themes ) && ! empty( $all_themes ) ) : ?>
    <nav class="archive-filters archive-filters--themes"
         aria-label="Browse by theme">
        <div class="container container--wide">
            <ul class="archive-filters__list" role="list">

                <li class="archive-filters__item">
                    <a href="<?php echo esc_url( home_url( '/tastemakers' ) ); ?>"
                       class="archive-filters__btn">
                        All Episodes
                    </a>
                </li>

                <?php foreach ( $all_themes as $theme_term ) :
                    $is_active = (int) $theme_term->term_id === (int) $current_term->term_id;
                ?>
                <li class="archive-filters__item">
                    <a href="<?php echo esc_url( get_term_link( $theme_term ) ); ?>"
                       class="archive-filters__btn <?php echo $is_active ? 'is-active' : ''; ?>"
                       aria-current="<?php echo $is_active ? 'page' : 'false'; ?>">
                        <?php echo esc_html( $theme_term->name ); ?>
                    </a>
                </li>
                <?php endforeach; ?>

            </ul>
        </div>
    </nav>
    <?php endif; ?>

    <!-- ── 3. Episodes with this theme ──────────────────────────────────── -->
    <section class="archive-grid archive-grid--episodes"
             aria-labelledby="theme-episodes-heading">
        <div class="container container--wide">

            <header class="archive-grid__header">
                <h2 class="archive-grid__heading" id="theme-episodes-heading">
                    Episodes
                    <?php if ( $episode_query->found_posts ) : ?>
                    <span class="archive-grid__count">
                        <?php echo absint( $episode_query->found_posts ); ?>
                    </span>
                    <?php endif; ?>
                </h2>
            </header>

            <?php if ( $episode_query->have_posts() ) : ?>

            <ul class="episode-grid" role="list">
                <?php while ( $episode_query->have_posts() ) : $episode_query->the_post();
                    $ep_deck   = get_field( 'ep_deck' );
                    $ep_guest  = get_field( 'ep_guest_name' );
                    $ep_co     = get_field( 'ep_guest_company' );
                    $ep_num    = get_field( 'ep_episode_number' );
                    $ep_dur    = get_field( 'ep_duration' );
                    $ep_season = get_field( 'ep_season' );
                    $s_num     = ( $ep_season instanceof WP_Post )
                                 ? get_field( 'ps_season_number', $ep_season->ID ) : null;
                    $ep_label  = ( $s_num && $ep_num )
                                 ? 'S' . absint( $s_num ) . ' E' . absint( $ep_num )
                                 : ( $ep_num ? 'Episode ' . absint( $ep_num ) : '' );
                ?>
                <li class="episode-card" data-post-id="<?php the_ID(); ?>">
                    <a href="<?php the_permalink(); ?>"
                       class="episode-card__link"
                       aria-label="<?php echo esc_attr( 'Listen: ' . get_the_title() ); ?>">

                        <?php if ( has_post_thumbnail() ) : ?>
                        <figure class="episode-card__media" aria-hidden="true">
                            <?php the_post_thumbnail( 'medium' ); ?>
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
                            <h3 class="episode-card__title"><?php the_title(); ?></h3>
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
                <?php endwhile;
                wp_reset_postdata(); ?>
            </ul>

            <?php
            // Pagination applies to $episode_query (the main query-like object).
            // the_posts_pagination() uses the global $wp_query; since we used a
            // custom WP_Query we call paginate_links() manually if pagination is
            // needed. For now episodes are loaded in full (no posts_per_page limit)
            // so pagination is not required unless volume warrants it later.
            ?>

            <?php else : ?>
            <p class="archive-empty__message">
                No episodes tagged with this theme yet.
            </p>
            <?php wp_reset_postdata(); ?>
            <?php endif; ?>

        </div>
    </section>

    <!-- ── 4. Seasons with this theme ───────────────────────────────────── -->
    <?php if ( $season_query->have_posts() ) : ?>
    <section class="theme-seasons" aria-labelledby="theme-seasons-heading">
        <div class="container container--wide">

            <header class="theme-seasons__header">
                <h2 class="theme-seasons__heading" id="theme-seasons-heading">
                    Related Seasons
                </h2>
            </header>

            <ul class="season-grid" role="list">
                <?php while ( $season_query->have_posts() ) : $season_query->the_post();
                    $ps_num      = get_field( 'ps_season_number' );
                    $ps_subtitle = get_field( 'ps_subtitle' );
                    $ps_theme    = get_field( 'ps_theme' );
                    $ps_artwork  = get_field( 'ps_artwork' );
                    $ps_label    = $ps_num ? 'Season ' . absint( $ps_num ) : get_the_title();
                ?>
                <li class="season-card">
                    <a href="<?php the_permalink(); ?>"
                       class="season-card__link"
                       aria-label="<?php echo esc_attr( 'Explore ' . $ps_label ); ?>">

                        <?php if ( is_array( $ps_artwork ) && ! empty( $ps_artwork['url'] ) ) : ?>
                        <figure class="season-card__artwork" aria-hidden="true">
                            <img src="<?php echo esc_url( $ps_artwork['url'] ); ?>"
                                 alt="<?php echo esc_attr( $ps_artwork['alt'] ?: $ps_label . ' artwork' ); ?>"
                                 width="<?php echo absint( $ps_artwork['width']  ?? 400 ); ?>"
                                 height="<?php echo absint( $ps_artwork['height'] ?? 400 ); ?>"
                                 loading="lazy">
                        </figure>
                        <?php elseif ( has_post_thumbnail() ) : ?>
                        <figure class="season-card__artwork" aria-hidden="true">
                            <?php the_post_thumbnail( 'medium' ); ?>
                        </figure>
                        <?php endif; ?>

                        <div class="season-card__body">
                            <p class="season-card__label">
                                <?php echo esc_html( $ps_label ); ?>
                            </p>
                            <h3 class="season-card__title">
                                <?php echo esc_html( $ps_subtitle ?: get_the_title() ); ?>
                            </h3>
                            <?php if ( $ps_theme ) : ?>
                            <p class="season-card__theme">
                                <?php echo esc_html( $ps_theme ); ?>
                            </p>
                            <?php endif; ?>
                            <span class="season-card__cta" aria-hidden="true">
                                Explore Season
                            </span>
                        </div>

                    </a>
                </li>
                <?php endwhile;
                wp_reset_postdata(); ?>
            </ul>

        </div>
    </section>
    <?php endif; ?>

    <!-- ── 5. CTA footer ────────────────────────────────────────────────── -->
    <?php get_template_part( 'parts/global/cta-footer' ); ?>

</main>

<?php get_footer(); ?>
