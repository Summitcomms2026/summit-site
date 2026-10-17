<?php
/**
 * Template: The Future of Luxury — an essay
 * Location: single-article.php
 * URL: /the-future-of-luxury/{slug}/
 *
 * The reading template. One column, set for long reading:
 *
 *   1. Head       rail (The Future of Luxury / category), headline,
 *                 standfirst, byline (author · date · reading time) and
 *                 the page's tools (Share — with Copy Link, Email and
 *                 LinkedIn — and Print, from the shared action system)
 *   2. Image      the featured image when its file exists and is strong
 *                 enough to lead (summit_fol_photo_direction()), with the
 *                 plate's family and folio as a small notation; otherwise
 *                 the essay's editorial plate (inc/editorial-art.php)
 *   3. Body       the_content, in the reading measure; inline images are
 *                 revealed when they load and withdrawn if they cannot
 *                 (assets/js/fol.js), so a dead remote image leaves no frame
 *   4. Foot       subject, the author, and the tools again
 *   5. Further reading  two or three real essays (parts/fol/related.php)
 *   6. Related work, episode or download, where the editors have set one
 *   7. Subscribe  #subscribe (art_subscribe_prompt, on by default)
 *   8. CTA footer
 *
 * Fields (art_*, see summit-core/acf-fields.php) are read, never altered.
 * SEO & Schema are handled in inc/seo.php.
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
    the_post();

    $art_id       = get_the_ID();
    $category     = summit_fol_category( $art_id );
    $category_url = $category ? get_term_link( $category ) : '';
    $standfirst   = summit_fol_standfirst( $art_id );
    $minutes      = summit_fol_read_time( $art_id );
    $author       = summit_fol_author( $art_id );
    $author_id    = (int) get_post_field( 'post_author', $art_id );
    $author_bio   = $author ? (string) get_the_author_meta( 'description', $author_id ) : '';
    $subtitle     = (string) get_field( 'art_subtitle' );
    $cornerstone  = (bool) get_field( 'art_cornerstone' );
    $source_note  = trim( (string) get_field( 'art_source_note' ) );
    $subscribe_on = get_field( 'art_subscribe_prompt' );
    $image        = summit_fol_visual( $art_id, 'full', [ 'sizes' => '(min-width: 1320px) 1272px, 100vw', 'fetchpriority' => 'high', 'loading' => 'eager' ], 'hero' );
    $archive      = get_post_type_archive_link( 'article' );

    // Import bookkeeping ("Imported from …") is an editor's note, not a reader's.
    if ( preg_match( '/^imported from\b/i', $source_note ) ) {
        $source_note = '';
    }

    $tools = [
        [ 'mode' => 'share' ],
        [ 'mode' => 'print' ],
    ];
?>

<main id="main" class="site-main fol fol-reading" role="main">

    <article <?php post_class( 'fol-article' ); ?>>

        <header class="fol-article__head">
            <div class="fol-wrap">
                <nav class="fol-article__rail" aria-label="<?php esc_attr_e( 'Breadcrumb', 'summit' ); ?>" data-reveal="fade">
                    <a href="<?php echo esc_url( $archive ); ?>"><?php esc_html_e( 'The Future of Luxury', 'summit' ); ?></a>
                    <?php if ( $category && ! is_wp_error( $category_url ) ) : ?>
                    <span aria-hidden="true">/</span>
                    <a href="<?php echo esc_url( $category_url ); ?>"><?php echo esc_html( $category->name ); ?></a>
                    <?php endif; ?>
                    <?php if ( $cornerstone ) : ?>
                    <span class="fol-article__flag"><?php esc_html_e( 'Essential reading', 'summit' ); ?></span>
                    <?php endif; ?>
                </nav>

                <h1 class="fol-article__title" data-reveal><?php the_title(); ?></h1>

                <?php if ( $subtitle ) : ?>
                <p class="fol-article__subtitle" data-reveal><?php echo esc_html( $subtitle ); ?></p>
                <?php endif; ?>

                <?php if ( $standfirst ) : ?>
                <p class="fol-article__standfirst" data-reveal data-reveal-delay="1"><?php echo esc_html( $standfirst ); ?></p>
                <?php endif; ?>

                <div class="fol-article__byline" data-reveal="fade" data-reveal-delay="2">
                    <p class="fol-article__meta">
                        <?php if ( $author ) : ?>
                        <span class="fol-article__author"><?php
                            /* translators: %s: author name */
                            echo esc_html( sprintf( __( 'By %s', 'summit' ), $author ) );
                        ?></span>
                        <?php endif; ?>
                        <time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date( 'j F Y' ) ); ?></time>
                        <?php if ( $minutes ) : ?>
                        <span><?php
                            /* translators: %d: minutes */
                            echo esc_html( sprintf( __( '%d min read', 'summit' ), $minutes ) );
                        ?></span>
                        <?php endif; ?>
                    </p>
                    <?php get_template_part( 'parts/global/action-group', null, [
                        'label'   => __( 'Share or print this essay', 'summit' ),
                        'actions' => $tools,
                        'class'   => 'fol-article__tools',
                    ] ); ?>
                </div>
            </div>
        </header>

        <?php if ( $image ) :
            // A photograph opens the essay as its subject; the plate's language
            // stays beside it as a small editorial notation (family, folio).
            $is_photo = false === strpos( $image, 'ed-art' );
            $notation = '';
            if ( $is_photo && function_exists( 'summit_editorial_art_geometry' ) ) {
                $family   = summit_editorial_art_family( $art_id );
                $number   = summit_editorial_art_number( $art_id );
                $notation = '<figcaption class="fol-article__notation" aria-hidden="true"><svg viewBox="0 0 100 100" focusable="false">' . summit_editorial_art_geometry( $family ) . '</svg>'
                    . ( $number ? '<span>' . esc_html( sprintf( 'No. %02d', $number ) ) . '</span>' : '' ) . '</figcaption>';
            }
        ?>
        <figure class="fol-article__image<?php echo $is_photo ? ' fol-article__image--photo' : ''; ?>" data-reveal="fade">
            <?php echo $image; // phpcs:ignore WordPress.Security.EscapeOutput -- wp_get_attachment_image ?>
            <?php echo $notation; // phpcs:ignore WordPress.Security.EscapeOutput -- static markup, escaped above ?>
        </figure>
        <?php endif; ?>

        <div class="fol-article__body fol-prose" data-fol-prose<?php echo in_array( get_post_field( 'post_name', $art_id ), summit_fol_absence_noted(), true ) ? ' data-fol-absence="note"' : ''; ?>>
            <?php the_content(); ?>
        </div>

        <footer class="fol-article__foot">
            <div class="fol-article__measure">
                <?php if ( $source_note ) : ?>
                <p class="fol-article__source"><?php echo esc_html( $source_note ); ?></p>
                <?php endif; ?>

                <dl class="fol-article__register">
                    <?php if ( $category && ! is_wp_error( $category_url ) ) : ?>
                    <div>
                        <dt><?php esc_html_e( 'Filed under', 'summit' ); ?></dt>
                        <dd><a href="<?php echo esc_url( $category_url ); ?>"><?php echo esc_html( $category->name ); ?></a></dd>
                    </div>
                    <?php endif; ?>
                    <?php if ( $author ) : ?>
                    <div>
                        <dt><?php esc_html_e( 'Author', 'summit' ); ?></dt>
                        <dd>
                            <span class="fol-article__author-name"><?php echo esc_html( $author ); ?></span>
                            <?php if ( $author_bio ) : ?>
                            <span class="fol-article__bio"><?php echo esc_html( $author_bio ); ?></span>
                            <?php endif; ?>
                        </dd>
                    </div>
                    <?php endif; ?>
                    <div>
                        <dt><?php esc_html_e( 'Published', 'summit' ); ?></dt>
                        <dd><time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date( 'j F Y' ) ); ?></time></dd>
                    </div>
                </dl>

                <?php get_template_part( 'parts/global/action-group', null, [
                    'label'   => __( 'Share or print this essay', 'summit' ),
                    'actions' => [
                        [ 'mode' => 'linkedin' ],
                        [ 'mode' => 'email' ],
                        [ 'mode' => 'copy' ],
                        [ 'mode' => 'print' ],
                    ],
                    'class'   => 'fol-article__tools fol-article__tools--end',
                ] ); ?>
            </div>
        </footer>

    </article>

    <?php
    get_template_part( 'parts/fol/related', null, [ 'post_id' => $art_id ] );

    // ACF post_object fields may hold one post, an ID or a list: the related
    // parts expect a list of published posts.
    $as_posts = static function ( $value ): array {
        $value = is_array( $value ) ? $value : ( $value ? [ $value ] : [] );
        return array_values( array_filter( array_map( 'get_post', $value ), static fn( $p ) => $p && 'publish' === $p->post_status ) );
    };

    get_template_part( 'parts/related/case-studies', null, [
        'posts'    => $as_posts( get_field( 'art_related_case' ) ),
        'headline' => __( 'Related Work', 'summit' ),
    ] );
    get_template_part( 'parts/related/episodes', null, [
        'posts'    => $as_posts( get_field( 'art_related_episode' ) ),
        'headline' => __( 'Related Episode', 'summit' ),
    ] );
    get_template_part( 'parts/related/downloads', null, [
        'posts'    => $as_posts( get_field( 'art_related_download' ) ),
        'headline' => __( 'Related Download', 'summit' ),
    ] );

    if ( false !== $subscribe_on ) {
        get_template_part( 'parts/fol/subscribe', null, [
            'source' => '/' . trim( (string) wp_parse_url( get_permalink(), PHP_URL_PATH ), '/' ),
        ] );
    }

    get_template_part( 'parts/global/cta-footer' );
    ?>

</main>

<?php
endwhile;
get_footer();
