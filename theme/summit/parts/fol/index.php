<?php
/**
 * Part: The Future of Luxury — Essays & Analysis
 * Location: parts/fol/index.php
 * Used by: archive-article.php
 *
 * The complete index of published essays, arranged under the section's
 * own categories (the article_category taxonomy, as the editors have
 * assigned it) — no invented groupings, no filters, no search. Each
 * category heads its essays, newest first, and leads to its own page.
 * Essays without a category are listed last under "Further essays".
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

$essays = get_posts( [
    'post_type'      => 'article',
    'post_status'    => 'publish',
    'posts_per_page' => -1,
    'orderby'        => [ 'date' => 'DESC', 'ID' => 'DESC' ],
    'no_found_rows'  => true,
] );
if ( ! $essays ) {
    return;
}

$groups = [];
$loose  = [];
foreach ( $essays as $essay ) {
    $category = summit_fol_category( $essay->ID );
    if ( $category ) {
        $groups[ $category->term_id ]['term']    = $category;
        $groups[ $category->term_id ]['posts'][] = $essay;
    } else {
        $loose[] = $essay;
    }
}
uasort( $groups, static fn( $a, $b ) => strcasecmp( wp_specialchars_decode( $a['term']->name ), wp_specialchars_decode( $b['term']->name ) ) );
if ( $loose ) {
    $groups['loose'] = [ 'term' => null, 'posts' => $loose ];
}
?>
<section class="fol-panel fol-index" id="essays-analysis" aria-labelledby="fol-index-heading">
    <div class="fol-wrap">
        <header class="fol-panel__head" data-reveal="fade">
            <p class="fol-panel__label"><?php esc_html_e( 'Index', 'summit' ); ?></p>
            <h2 class="fol-panel__title" id="fol-index-heading"><?php esc_html_e( 'Essays & Analysis', 'summit' ); ?></h2>
            <p class="fol-panel__count"><?php
                /* translators: %d: number of essays */
                echo esc_html( sprintf( _n( '%1$d essay', '%1$d essays', count( $essays ), 'summit' ), count( $essays ) ) );
            ?></p>
        </header>

        <div class="fol-index__groups">
            <?php foreach ( $groups as $group ) :
                $term = $group['term'];
                $hid  = wp_unique_id( 'fol-index-group-' );
            ?>
            <section class="fol-index__group" aria-labelledby="<?php echo esc_attr( $hid ); ?>" data-reveal="fade">
                <h3 class="fol-index__subject" id="<?php echo esc_attr( $hid ); ?>">
                    <?php if ( $term ) :
                        $link = get_term_link( $term ); ?>
                    <a href="<?php echo esc_url( is_wp_error( $link ) ? '' : $link ); ?>"><?php echo esc_html( $term->name ); ?></a>
                    <?php else : ?>
                    <?php esc_html_e( 'Further essays', 'summit' ); ?>
                    <?php endif; ?>
                    <span class="fol-index__tally"><?php echo esc_html( (string) count( $group['posts'] ) ); ?></span>
                </h3>
                <ol class="fol-index__list" role="list">
                    <?php foreach ( $group['posts'] as $essay ) :
                        $minutes = summit_fol_read_time( $essay->ID ); ?>
                    <li class="fol-index__entry">
                        <a href="<?php echo esc_url( get_permalink( $essay ) ); ?>">
                            <span class="fol-index__title"><?php echo esc_html( get_the_title( $essay ) ); ?></span>
                            <?php if ( $minutes ) : ?>
                            <span class="fol-index__time"><?php
                                /* translators: %d: minutes */
                                echo esc_html( sprintf( __( '%d min', 'summit' ), $minutes ) );
                            ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ol>
            </section>
            <?php endforeach; ?>
        </div>
    </div>
</section>
