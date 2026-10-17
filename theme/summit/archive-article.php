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
 * Copy: Editorial Handover v1, Pass 09 (S09-P002, P003, P005, P009, P019,
 * P020, P021, P023, P025, P026, P028). Panels that describe features the archive does not
 * have (search, sort, a monthly digest, a confirmation email) are held and
 * recorded in editorial-handover/v1/panel-map.json.
 *
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
        'rail'       => [ __( 'Summit Communication Group', 'summit' ), __( 'Beyond the Headlines', 'summit' ) ],
        'title'      => __( 'The Future of Luxury', 'summit' ),
        'standfirst' => __( 'Luxury has never lacked for announcements. Understanding what they mean takes rather longer.', 'summit' ),
        'intro'      => __( 'The Future of Luxury examines the commercial decisions, cultural disputes and institutional choices shaping the houses we follow.', 'summit' ),
        'actions'    => [
            [ 'type' => 'cta', 'label' => __( 'Explore Essays & Analysis', 'summit' ), 'url' => '#essays-analysis' ],
            [ 'type' => 'utility', 'mode' => 'link', 'icon' => false, 'label' => __( 'Subscribe', 'summit' ), 'url' => '#subscribe', 'class' => 'action--contextual' ],
        ],
    ] ); ?>

    <?php if ( $lead ) : ?>
    <section class="fol-panel fol-lead" aria-label="<?php esc_attr_e( 'Lead essay', 'summit' ); ?>">
        <div class="fol-wrap" data-reveal="fade">
            <p class="fol-panel__label"><?php esc_html_e( 'From the Archive', 'summit' ); ?></p>
            <?php get_template_part( 'parts/fol/story', null, [ 'post' => $lead, 'variant' => 'lead', 'level' => 2 ] ); ?>
        </div>
    </section>
    <?php endif; ?>

    <?php if ( $latest ) :
        $first = array_shift( $latest ); ?>
    <section class="fol-panel fol-latest" id="latest" aria-labelledby="fol-latest-heading">
        <div class="fol-wrap">
            <header class="fol-panel__head" data-reveal="fade">
                <p class="fol-panel__label"><?php esc_html_e( 'Latest', 'summit' ); ?></p>
                <h2 class="fol-panel__title" id="fol-latest-heading"><?php esc_html_e( 'What Matters Now', 'summit' ); ?></h2>
                <p class="fol-panel__intro"><?php esc_html_e( 'Not every development deserves an essay. The ones that do tend to reveal something larger than the day’s news.', 'summit' ); ?></p>
                <p class="fol-panel__intro"><?php esc_html_e( 'The latest published arguments, selected from The Future of Luxury.', 'summit' ); ?></p>
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

    <?php
    // Questions readers ask (S09-P009, S09-P026, S09-P028, S09-P020, S09-P021)
    $fol_questions = [
        __( 'Are these breaking-news reports?', 'summit' ) => __( 'Not principally. Events are the starting point. The value lies in the argument developed from the available evidence, not in being first to repeat an announcement.', 'summit' ),
        __( 'What will I receive?', 'summit' )             => __( 'New essays and, if selected, a digest of recent arguments. The publication is editorial; readers should not expect a daily industry-news feed.', 'summit' ),
        __( 'Do I need to become a client?', 'summit' )    => __( 'No. The publication is open to readers who are interested in luxury as a business, a culture and an institution.', 'summit' ),
        __( 'Can I quote or share an essay?', 'summit' )   => __( 'Readers may share links freely. Short quotations should credit the author and link to the original page. Republishing substantial passages or imagery requires permission.', 'summit' ),
        __( 'Does an article imply a company is a Summit client?', 'summit' ) => __( 'No. A maison mentioned in an essay is a subject of editorial analysis, not an implied client, commercial partner or endorsement.', 'summit' ),
    ];
    ?>
    <section class="fol-panel fol-questions" aria-labelledby="fol-questions-heading">
        <div class="fol-wrap">
            <h2 class="sr-only" id="fol-questions-heading"><?php esc_html_e( 'Questions', 'summit' ); ?></h2>
            <div class="summit-faq">
                <?php foreach ( $fol_questions as $q => $a ) : ?>
                <details class="summit-faq__item"><summary><?php echo esc_html( $q ); ?></summary><p><?php echo esc_html( $a ); ?></p></details>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <?php get_template_part( 'parts/fol/subscribe', null, [ 'source' => $source ] ); ?>

    <?php
    // A Question of Your Own? (S09-P025)
    get_template_part( 'parts/global/cta-footer', null, [
        'headline' => __( 'A Question of Your Own?', 'summit' ),
        'body'     => __( 'An essay can clarify the issue. A conversation can clarify the decision. Tell us what you are weighing. We will listen first and offer our perspective, whether or not we go on to work together.', 'summit' ),
        'label'    => __( 'Start a Conversation', 'summit' ),
        'url'      => home_url( '/lets-talk?topic=the-future-of-luxury' ),
    ] );
    ?>

</main>

<?php get_footer(); ?>
