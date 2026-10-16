<?php
/**
 * Template: The Future of Luxury — the publication
 * Location: archive-article.php
 * URL: /the-future-of-luxury/
 *
 * The article post type's archive (has_archive => 'the-future-of-luxury').
 * Single essays live at /the-future-of-luxury/{slug}/ (single-article.php);
 * subjects at /article-category/{term}/ (taxonomy-article_category.php).
 *
 * Panels:
 *   1. Masthead      nameplate, standfirst, the way to the index and subscribe
 *   2. Lead          the newest essay flagged art_featured, else the newest
 *   3. Latest        #latest — the next five, in two weights
 *   4. Essays & Analysis  #essays-analysis — the full index by category
 *   5. Independence  the section's editorial standing
 *   6. Subscribe     #subscribe
 *   7. CTA footer    the global Let's Talk footer
 *
 * The anchors are the navigation's own children (inc/navigation-config.php).
 * Queries are local to this template; the homepage modules and the mega
 * menu's editorial rail keep their own.
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

$lead   = summit_fol_lead();
$latest = get_posts( [
    'post_type'      => 'article',
    'post_status'    => 'publish',
    'posts_per_page' => 5,
    'post__not_in'   => $lead ? [ $lead->ID ] : [],
    'orderby'        => [ 'date' => 'DESC', 'ID' => 'DESC' ],
    'no_found_rows'  => true,
] );
$source = '/' . trim( (string) wp_parse_url( get_post_type_archive_link( 'article' ), PHP_URL_PATH ), '/' );

get_header();
?>

<main id="main" class="site-main fol" role="main">

    <?php get_template_part( 'parts/fol/masthead', null, [
        'rail'       => [ __( 'Summit Communication Group', 'summit' ), __( 'Essays and analysis', 'summit' ) ],
        'title'      => __( 'The Future of Luxury', 'summit' ),
        'standfirst' => __( 'Essays and analysis on the houses, objects and decisions reshaping luxury.', 'summit' ),
        'actions'    => [
            [ 'type' => 'cta', 'label' => __( 'Essays & Analysis', 'summit' ), 'url' => '#essays-analysis' ],
            [ 'type' => 'utility', 'mode' => 'link', 'icon' => false, 'label' => __( 'Subscribe', 'summit' ), 'url' => '#subscribe', 'class' => 'action--contextual' ],
        ],
    ] ); ?>

    <?php if ( $lead ) : ?>
    <section class="fol-panel fol-lead" aria-label="<?php esc_attr_e( 'Lead essay', 'summit' ); ?>">
        <div class="fol-wrap" data-reveal="fade">
            <?php get_template_part( 'parts/fol/story', null, [ 'post' => $lead, 'variant' => 'lead', 'level' => 2 ] ); ?>
        </div>
    </section>
    <?php endif; ?>

    <?php if ( $latest ) :
        $first = array_shift( $latest ); ?>
    <section class="fol-panel fol-latest" id="latest" aria-labelledby="fol-latest-heading">
        <div class="fol-wrap">
            <header class="fol-panel__head" data-reveal="fade">
                <h2 class="fol-panel__title" id="fol-latest-heading"><?php esc_html_e( 'Latest', 'summit' ); ?></h2>
            </header>
            <div class="fol-latest__grid">
                <div class="fol-latest__feature" data-reveal>
                    <?php get_template_part( 'parts/fol/story', null, [ 'post' => $first, 'variant' => 'feature' ] ); ?>
                </div>
                <?php if ( $latest ) : ?>
                <div class="fol-latest__list">
                    <?php foreach ( $latest as $i => $item ) : ?>
                    <div data-reveal data-reveal-delay="<?php echo esc_attr( (string) min( 4, $i + 1 ) ); ?>">
                        <?php get_template_part( 'parts/fol/story', null, [ 'post' => $item, 'variant' => 'item' ] ); ?>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <?php elseif ( ! $lead ) : ?>
    <section class="fol-panel fol-empty">
        <div class="fol-wrap">
            <p class="fol-empty__note"><?php esc_html_e( 'The first essays are in preparation.', 'summit' ); ?></p>
        </div>
    </section>
    <?php endif; ?>

    <?php get_template_part( 'parts/fol/index' ); ?>

    <?php get_template_part( 'parts/fol/independence' ); ?>

    <?php get_template_part( 'parts/fol/subscribe', null, [ 'source' => $source ] ); ?>

    <?php get_template_part( 'parts/global/cta-footer' ); ?>

</main>

<?php get_footer(); ?>
