<?php
/**
 * Template: The Future of Luxury — a subject
 * Location: taxonomy-article_category.php
 * URL: /article-category/{term-slug}/ (rewrite slug registered in summit-core/taxonomies.php)
 *
 * One of the section's categories, as reached from the Essays & Analysis
 * index or an essay's kicker. WordPress supplies the term's articles
 * through the main loop.
 *
 * Panels:
 *   1. Masthead      the publication's rail, the subject as the H1
 *   2. Essays        the subject's essays, newest first, with pagination
 *   3. Subjects      the other subjects, and the way back to the index
 *   4. Subscribe     #subscribe
 *   5. CTA footer
 *
 * SEO & Schema are handled in inc/seo.php.
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

$term     = get_queried_object();
$archive  = get_post_type_archive_link( 'article' );
$subjects = get_terms( [ 'taxonomy' => 'article_category', 'hide_empty' => true ] );
$source   = '/' . trim( (string) wp_parse_url( get_term_link( $term ), PHP_URL_PATH ), '/' );

get_header();
?>

<main id="main" class="site-main fol" role="main">

    <?php get_template_part( 'parts/fol/masthead', null, [
        'size'       => 'section',
        'rail'       => [ '<a href="' . esc_url( $archive ) . '">' . esc_html__( 'The Future of Luxury', 'summit' ) . '</a>', __( 'Subject', 'summit' ) ],
        'title'      => wp_specialchars_decode( $term->name, ENT_QUOTES ),
        'standfirst' => $term->description ? wp_strip_all_tags( $term->description ) : '',
        'actions'    => [
            [ 'type' => 'utility', 'mode' => 'link', 'icon' => false, 'label' => __( 'All essays', 'summit' ), 'url' => $archive . '#essays-analysis', 'class' => 'action--contextual' ],
        ],
    ] ); ?>

    <section class="fol-panel fol-subject" aria-label="<?php echo esc_attr( wp_specialchars_decode( $term->name, ENT_QUOTES ) ); ?>">
        <div class="fol-wrap">
            <?php if ( have_posts() ) : ?>
            <div class="fol-subject__grid">
                <?php $i = 0; while ( have_posts() ) : the_post(); ?>
                <div data-reveal data-reveal-delay="<?php echo esc_attr( (string) min( 3, $i++ % 3 ) ); ?>">
                    <?php get_template_part( 'parts/fol/story', null, [ 'post' => get_post(), 'variant' => 'feature', 'level' => 2, 'category' => false ] ); ?>
                </div>
                <?php endwhile; ?>
            </div>
            <?php
            the_posts_pagination( [
                'mid_size'           => 1,
                'prev_text'          => __( 'Newer', 'summit' ),
                'next_text'          => __( 'Older', 'summit' ),
                'screen_reader_text' => __( 'More essays', 'summit' ),
                'class'              => 'fol-pagination',
            ] );
            ?>
            <?php else : ?>
            <p class="fol-empty__note"><?php esc_html_e( 'No essays are filed under this subject yet.', 'summit' ); ?></p>
            <?php endif; ?>
        </div>
    </section>

    <?php if ( ! is_wp_error( $subjects ) && count( $subjects ) > 1 ) : ?>
    <nav class="fol-panel fol-subjects" aria-labelledby="fol-subjects-heading">
        <div class="fol-wrap">
            <h2 class="fol-subjects__heading" id="fol-subjects-heading" data-reveal="fade"><?php esc_html_e( 'Other subjects', 'summit' ); ?></h2>
            <ul class="fol-subjects__list" role="list" data-reveal="fade">
                <?php foreach ( $subjects as $subject ) :
                    if ( $subject->term_id === $term->term_id ) {
                        continue;
                    } ?>
                <li><a href="<?php echo esc_url( get_term_link( $subject ) ); ?>"><?php echo esc_html( wp_specialchars_decode( $subject->name, ENT_QUOTES ) ); ?></a></li>
                <?php endforeach; ?>
                <li class="fol-subjects__all"><a href="<?php echo esc_url( $archive . '#essays-analysis' ); ?>"><?php esc_html_e( 'The full index', 'summit' ); ?></a></li>
            </ul>
        </div>
    </nav>
    <?php endif; ?>

    <?php get_template_part( 'parts/fol/subscribe', null, [ 'source' => $source ] ); ?>

    <?php get_template_part( 'parts/global/cta-footer' ); ?>

</main>

<?php get_footer(); ?>
