<?php
/**
 * Template: Work Showcase
 * Location: page-work.php
 *
 * WordPress template hierarchy: page-{slug}.php → page.php → index.php
 * Matches the WordPress page with slug: work  (URL: /work)
 *
 * This template owns the primary case_study archive display.
 * The case_study CPT has has_archive => false; all archive display
 * is controlled here via a custom WP_Query.
 *
 * Component sequence:
 *   1. Hero (from page fields or defaults)
 *   2. Filter bar — service_type, sector, format (URL-driven)
 *   3. Case study grid
 *   4. Pagination
 *   5. Showreel panel → parts/archive/showreel-panel.php
 *   6. Approach panel → parts/archive/approach-panel.php
 *   7. Download panel → parts/archive/download-panel.php
 *   8. Sector signals → parts/archive/sector-signals.php
 *   9. CTA footer → parts/global/cta-footer.php
 *
 * Filter mechanism:
 * Filtering is URL-driven using taxonomy query vars. Clicking a filter
 * term link navigates to that term's taxonomy archive, which is handled
 * by the standard WP taxonomy URL. This avoids JavaScript dependency and
 * keeps filter state in the URL (shareable, bookmarkable).
 *
 * SEO & Schema — not injected here.
 * Output handled in inc/seo.php via standard page SEO logic.
 * Schema type: CollectionPage + ItemList
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

get_header();

// ── Page context ──────────────────────────────────────────────────────────
the_post(); // page loop — consumes the wp_page post

$page_title = get_the_title();
$page_intro = get_the_content(); // optional editorial intro from page editor

// ── Pagination ────────────────────────────────────────────────────────────
$paged = ( get_query_var( 'paged' ) ) ? absint( get_query_var( 'paged' ) ) : 1;

// ── Case study query ──────────────────────────────────────────────────────
$cs_args = [
    'post_type'      => 'case_study',
    'posts_per_page' => 12,
    'paged'          => $paged,
    'orderby'        => [ 'menu_order' => 'ASC', 'date' => 'DESC' ],
];

$cs_query = new WP_Query( $cs_args );
?>

<main id="main" class="site-main site-main--work" role="main">

    <!-- ── 1. Hero ──────────────────────────────────────────────────────── -->
    <section class="archive-hero archive-hero--work" aria-labelledby="work-hero-title">
        <div class="container container--medium">
            <header class="archive-hero__header">
                <p class="archive-hero__eyebrow">Selected Work</p>
                <h1 class="archive-hero__title" id="work-hero-title" data-reveal>
                    <?php echo esc_html( $page_title ); ?>
                </h1>
                <?php if ( $page_intro ) : ?>
                <div class="archive-hero__intro" data-reveal data-reveal-delay="1">
                    <?php
                    /*
                     * Page body content is run through the_content filters.
                     * wp_kses_post is applied internally by wpautop and
                     * related filter pipeline.
                     */
                    echo apply_filters( 'the_content', $page_intro ); // phpcs:ignore
                    ?>
                </div>
                <?php else : ?>
                <p class="archive-hero__sub" data-reveal data-reveal-delay="1">
                    A curated selection of engagements across brand strategy,
                    experience design and digital transformation.
                </p>
                <?php endif; ?>
            </header>
        </div>
    </section>

    <!-- ── 2. Filter bar ────────────────────────────────────────────────── -->
    <?php
    $filter_config = [
        'service_type' => 'Services',
        'sector'       => 'Sector',
        'format'       => 'Format',
    ];

    $has_filters = false;
    foreach ( $filter_config as $tax_slug => $tax_label ) {
        $check = get_terms( [ 'taxonomy' => $tax_slug, 'hide_empty' => true, 'number' => 1 ] );
        if ( ! is_wp_error( $check ) && ! empty( $check ) ) {
            $has_filters = true;
            break;
        }
    }

    if ( $has_filters ) : ?>
    <nav class="archive-filters archive-filters--work" aria-label="Filter case studies">
        <div class="container container--wide">
            <ul class="archive-filters__list" role="list">

                <li class="archive-filters__item">
                    <a href="<?php echo esc_url( home_url( '/work' ) ); ?>"
                       class="archive-filters__btn archive-filters__btn--all <?php echo ! is_tax( array_keys( $filter_config ) ) ? 'is-active' : ''; ?>"
                       aria-current="<?php echo ! is_tax( array_keys( $filter_config ) ) ? 'true' : 'false'; ?>">
                        All Work
                    </a>
                </li>

                <?php foreach ( $filter_config as $tax_slug => $tax_label ) :
                    $terms = get_terms( [ 'taxonomy' => $tax_slug, 'hide_empty' => true ] );
                    if ( is_wp_error( $terms ) || empty( $terms ) ) { continue; }

                    foreach ( $terms as $term ) :
                        $is_active = is_tax( $tax_slug, $term->slug );
                ?>
                <li class="archive-filters__item">
                    <a href="<?php echo esc_url( get_term_link( $term ) ); ?>"
                       class="archive-filters__btn <?php echo $is_active ? 'is-active' : ''; ?>"
                       aria-current="<?php echo $is_active ? 'true' : 'false'; ?>">
                        <?php echo esc_html( $term->name ); ?>
                    </a>
                </li>
                <?php endforeach; endforeach; ?>

            </ul>
        </div>
    </nav>
    <?php endif; ?>

    <!-- ── 3. Case study grid ───────────────────────────────────────────── -->
    <section class="archive-grid archive-grid--work" aria-label="Case study index">
        <div class="container container--wide">

            <?php if ( $cs_query->have_posts() ) : ?>

            <ul class="case-study-grid" role="list">
                <?php $cs_card_index = 0; while ( $cs_query->have_posts() ) : $cs_query->the_post();
                    $cs_card_index++;
                    $summary   = get_field( 'cs_summary' );
                    $client    = get_field( 'cs_client' );
                    $year      = get_field( 'cs_year' );
                    $svc_terms = get_the_terms( get_the_ID(), 'service_type' );
                    $svc_label = ( ! is_wp_error( $svc_terms ) && ! empty( $svc_terms ) )
                                 ? $svc_terms[0]->name : '';
                ?>
                <li class="case-study-card" data-post-id="<?php the_ID(); ?>" data-reveal data-reveal-delay="<?php echo min( $cs_card_index, 3 ); ?>">
                    <a href="<?php the_permalink(); ?>"
                       class="case-study-card__link"
                       aria-label="<?php echo esc_attr( 'View case study: ' . get_the_title() ); ?>">

                        <?php if ( has_post_thumbnail() ) : ?>
                        <figure class="case-study-card__media" aria-hidden="true">
                            <?php the_post_thumbnail( 'large' ); ?>
                        </figure>
                        <?php endif; ?>

                        <div class="case-study-card__body">

                            <div class="case-study-card__meta">
                                <?php if ( $svc_label ) : ?>
                                <span class="case-study-card__service"><?php echo esc_html( $svc_label ); ?></span>
                                <?php endif; ?>
                                <?php if ( $year ) : ?>
                                <span class="case-study-card__year"><?php echo absint( $year ); ?></span>
                                <?php endif; ?>
                            </div>

                            <h2 class="case-study-card__title"><?php the_title(); ?></h2>

                            <?php if ( $client ) : ?>
                            <p class="case-study-card__client"><?php echo esc_html( $client ); ?></p>
                            <?php endif; ?>

                            <?php if ( $summary ) : ?>
                            <p class="case-study-card__summary"><?php echo esc_html( $summary ); ?></p>
                            <?php endif; ?>

                            <span class="case-study-card__cta" aria-hidden="true">View Project</span>

                        </div>

                    </a>
                </li>
                <?php endwhile; ?>
            </ul>

            <!-- ── 4. Pagination ──────────────────────────────────────── -->
            <?php
            $big = 999999;
            echo paginate_links( [
                'base'               => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
                'format'             => '?paged=%#%',
                'current'            => max( 1, $paged ),
                'total'              => $cs_query->max_num_pages,
                'prev_text'          => '<span aria-hidden="true">&larr;</span><span class="screen-reader-text"> Previous page</span>',
                'next_text'          => '<span class="screen-reader-text">Next page </span><span aria-hidden="true">&rarr;</span>',
                'type'               => 'list',
                'before_page_number' => '<span class="screen-reader-text">Page </span>',
            ] );
            ?>

            <?php else : ?>

            <div class="archive-empty">
                <p class="archive-empty__message">No work to display yet.</p>
                <a href="<?php echo esc_url( home_url( '/design-tomorrow' ) ); ?>" class="btn btn--secondary">
                    Start a Conversation
                </a>
            </div>

            <?php endif;
            wp_reset_postdata(); ?>

        </div>
    </section>

    <!-- ── 5. Showreel panel ──────────────────────────────────────────── -->
    <?php
    $work_page_id       = get_the_ID();
    $work_showreel      = get_field( 'work_showreel_embed', $work_page_id );
    $work_showreel_cap  = get_field( 'work_showreel_caption', $work_page_id );
    if ( $work_showreel ) :
        set_query_var( 'summit_showreel_embed',   $work_showreel );
        set_query_var( 'summit_showreel_caption',  $work_showreel_cap );
        get_template_part( 'parts/archive/showreel-panel' );
    endif;
    ?>

    <!-- ── 6. Approach panel ───────────────────────────────────────────── -->
    <?php
    $work_approach_h = get_field( 'work_approach_heading', $work_page_id );
    $work_approach_b = get_field( 'work_approach_body',    $work_page_id );
    if ( $work_approach_h || $work_approach_b ) :
        set_query_var( 'summit_approach_heading', $work_approach_h );
        set_query_var( 'summit_approach_body',    $work_approach_b );
        get_template_part( 'parts/archive/approach-panel' );
    endif;
    ?>

    <!-- ── 7. Download panel ───────────────────────────────────────────── -->
    <?php
    $work_download = get_field( 'work_featured_download', $work_page_id );
    if ( ! $work_download ) {
        $work_dl_q = new WP_Query( [
            'post_type'      => 'download',
            'posts_per_page' => 1,
            'orderby'        => 'date',
            'order'          => 'DESC',
            'no_found_rows'  => true,
        ] );
        $work_download = $work_dl_q->have_posts() ? $work_dl_q->posts[0] : null;
        wp_reset_postdata();
    }
    if ( $work_download ) :
        set_query_var( 'summit_download_post',   $work_download );
        set_query_var( 'summit_download_heading', 'Download' );
        get_template_part( 'parts/archive/download-panel' );
    endif;
    ?>

    <!-- ── 8. Sector signals ──────────────────────────────────────────── -->
    <?php
    $work_logos = get_field( 'work_sector_logos', $work_page_id );
    if ( $work_logos ) :
        set_query_var( 'summit_sector_logos', $work_logos );
        get_template_part( 'parts/archive/sector-signals' );
    endif;
    ?>

    <!-- ── 9. CTA footer ──────────────────────────────────────────────── -->
    <?php get_template_part( 'parts/global/cta-footer' ); ?>

</main>

<?php get_footer(); ?>
