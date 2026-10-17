<?php
/**
 * Part: Solutions — three ways in
 * Location: parts/solutions/ways-in.php
 * Used by: page-solutions.php via [summit_solutions_ways]
 *
 * By Industry, By Function and By Need as one panel: a lens index on a
 * black field whose content changes in place (assets/js/solutions.js).
 * Each lens has its own mode:
 *
 *   By Industry  a sector register beside a visual field that follows the
 *                sector in hover or focus
 *   By Function  six numbered rows, each with one line
 *   By Need      "Start with the question." — ten oversized rows and a held
 *                statement for the active question
 *
 * Copy: each lens's line, and the By Need lens's heading and introduction,
 * come from the Solutions page's "summit_lenses" field (Editorial Handover
 * v1: S02-P001, S02-P002); the earlier lines stand in until it is set.
 *
 * Sources: labels, order and destinations from the navigation config. Each
 * entry's line is its own page's "summit_summary" field; each sector's image
 * is its page's "summit_visual" field (attachment ID or URL). Until an image
 * is set, the field holds a typographic fallback — the sector's numeral and
 * name — in the same frame, so an image later replaces it without any
 * change of layout. (The sourcing brief lives outside the theme.)
 *
 * Without JavaScript: the lens names are in-page links and the three
 * panels read in sequence.
 *
 * $args:
 *   section   the Solutions section from summit_nav( 'primary' )
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

$section = $args['section'] ?? [];
$groups  = $section['groups'] ?? [];

if ( ! $groups ) {
    return;
}

// The index's own grammar: who each lens is for, and what it describes.
$lenses = [
    'by-industry' => [ __( 'Your world', 'summit' ), __( 'Industry describes the context.', 'summit' ) ],
    'by-function' => [ __( 'Your responsibility', 'summit' ), __( 'Function describes the discipline.', 'summit' ) ],
    'by-need'     => [ __( 'Your question', 'summit' ), __( 'Need describes the question.', 'summit' ) ],
];
$overview = get_page_by_path( trim( $section['path'] ?? '/solutions', '/' ), OBJECT, 'page' );
$copy     = $overview ? (array) json_decode( (string) get_post_meta( $overview->ID, 'summit_lenses', true ), true ) : [];
foreach ( $copy as $key => $lens_copy ) {
    if ( isset( $lenses[ $key ] ) && ! empty( $lens_copy['line'] ) ) {
        $lenses[ $key ][1] = $lens_copy['line'];
    }
}
$need_title = $copy['by-need']['title'] ?? __( 'Start with the question.', 'summit' );
$need_intro = $copy['by-need']['intro'] ?? '';

// An entry's own page fields
$entry = static function ( array $item ): array {
    $page    = get_page_by_path( trim( $item['path'], '/' ), OBJECT, 'page' );
    $summary = $page ? (string) get_post_meta( $page->ID, 'summit_summary', true ) : '';
    $image   = '';
    if ( $page ) {
        $visual = get_post_meta( $page->ID, 'summit_visual', true );
        if ( $visual && is_numeric( $visual ) ) {
            $image = wp_get_attachment_image( (int) $visual, 'large', false, [ 'alt' => '', 'loading' => 'lazy', 'decoding' => 'async' ] );
        } elseif ( $visual ) {
            $image = '<img src="' . esc_url( $visual ) . '" alt="" loading="lazy" decoding="async">';
        }
    }
    return [ 'label' => $item['label'], 'url' => summit_nav_url( $item ), 'path' => $item['path'], 'summary' => $summary, 'image' => $image ];
};

$num = static fn( int $i ): string => sprintf( '%02d', $i );
?>
<section class="sx-ways sx-panel-wrap" data-sx-ways aria-labelledby="sx-ways-heading">
    <h2 class="sx-label" id="sx-ways-heading"><?php esc_html_e( 'Three ways in', 'summit' ); ?></h2>

    <ul class="sx-lenses" data-sx-lenses>
        <?php $n = 0; foreach ( $groups as $key => $group ) : $n++; ?>
        <li>
            <a class="sx-lens" data-sx-lens href="#<?php echo esc_attr( $key ); ?>">
                <span class="sx-lens__num" aria-hidden="true"><?php echo esc_html( $num( $n ) ); ?></span>
                <span class="sx-lens__kicker"><?php echo esc_html( $lenses[ $key ][0] ?? '' ); ?></span>
                <span class="sx-lens__title"><?php echo esc_html( $group['label'] ); ?></span>
                <span class="sx-lens__key"><?php echo esc_html( $lenses[ $key ][1] ?? '' ); ?></span>
            </a>
        </li>
        <?php endforeach; ?>
    </ul>

    <div class="sx-panels">
        <?php foreach ( $groups as $key => $group ) :
            $items = array_map( $entry, $group['children'] ); ?>

        <section class="sx-lenspanel" id="<?php echo esc_attr( $key ); ?>"<?php echo 'by-need' === $key ? ' data-sx-default' : ''; ?>>
            <h3 class="sx-lenspanel__title"><?php echo esc_html( $group['label'] ); ?></h3>

            <?php if ( 'by-industry' === $key ) : ?>
            <div class="sx-industry">
                <ul class="sx-sectors" role="list">
                    <?php foreach ( $items as $i => $item ) : ?>
                    <li>
                        <a class="sx-sector" data-sx-sector href="<?php echo esc_url( $item['url'] ); ?>">
                            <span class="sx-num" aria-hidden="true"><?php echo esc_html( $num( $i + 1 ) ); ?></span>
                            <span class="sx-sector__label"><?php echo esc_html( $item['label'] ); ?></span>
                            <span class="sx-arrow" aria-hidden="true">&rarr;</span>
                            <?php if ( $item['image'] ) : // Touch: each industry carries its own image in the row ?>
                            <span class="sx-sector__thumb" aria-hidden="true"><?php echo $item['image']; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped above ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <div class="sx-visual" aria-hidden="true">
                    <?php foreach ( $items as $i => $item ) : ?>
                    <figure class="sx-visual__frame" data-sx-frame>
                        <?php if ( $item['image'] ) : ?>
                            <?php echo $item['image']; // phpcs:ignore WordPress.Security.EscapeOutput -- wp_get_attachment_image / escaped above ?>
                        <?php else : ?>
                        <div class="sx-fallback">
                            <span class="sx-fallback__num"><?php echo esc_html( $num( $i + 1 ) ); ?></span>
                            <span class="sx-fallback__name"><?php echo esc_html( $item['label'] ); ?></span>
                        </div>
                        <?php endif; ?>
                    </figure>
                    <?php endforeach; ?>
                </div>
            </div>

            <?php elseif ( 'by-function' === $key ) : ?>
            <ul class="sx-functions" role="list">
                <?php foreach ( $items as $i => $item ) : ?>
                <li>
                    <a class="sx-function" href="<?php echo esc_url( $item['url'] ); ?>">
                        <span class="sx-num" aria-hidden="true"><?php echo esc_html( $num( $i + 1 ) ); ?></span>
                        <span class="sx-function__name"><?php echo esc_html( $item['label'] ); ?></span>
                        <?php if ( $item['summary'] ) : ?>
                        <span class="sx-function__line"><?php echo esc_html( $item['summary'] ); ?></span>
                        <?php endif; ?>
                        <span class="sx-arrow" aria-hidden="true">&rarr;</span>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>

            <?php else : ?>
            <h4 class="sx-need-intro"><?php echo esc_html( $need_title ); ?></h4>
            <?php if ( $need_intro ) : ?>
            <p class="sx-need-support"><?php echo esc_html( $need_intro ); ?></p>
            <?php endif; ?>
            <div class="sx-need">
                <ul class="sx-needs" role="list">
                    <?php foreach ( $items as $i => $item ) : ?>
                    <li>
                        <a class="sx-need__link" data-sx-need href="<?php echo esc_url( $item['url'] ); ?>">
                            <span class="sx-num" aria-hidden="true"><?php echo esc_html( $num( $i + 1 ) ); ?></span>
                            <span class="sx-need__label"><?php echo esc_html( $item['label'] ); ?></span>
                            <?php if ( $item['summary'] ) : ?>
                            <span class="sx-need__line"><?php echo esc_html( $item['summary'] ); ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <div class="sx-statement" aria-hidden="true">
                    <?php foreach ( $items as $i => $item ) : ?>
                    <div class="sx-statement__item" data-sx-statement>
                        <span class="sx-statement__num"><?php echo esc_html( $num( $i + 1 ) ); ?></span>
                        <p class="sx-statement__line"><?php echo esc_html( $item['summary'] ?: $item['label'] ); ?></p>
                        <a class="sx-statement__link" href="<?php echo esc_url( $item['url'] ); ?>" tabindex="-1"><?php echo esc_html( $item['label'] ); ?> &rarr;</a>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </section>

        <?php endforeach; ?>
    </div>
</section>
