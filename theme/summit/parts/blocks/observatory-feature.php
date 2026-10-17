<?php
/**
 * Part: The Observatory (homepage)
 * Location: parts/blocks/observatory-feature.php
 * Used by: front-page.php, in the panel position Tastemakers held
 *
 * What makes Summit different, shown rather than claimed: the Observatory
 * as a governed instrument — its statement, its field figures, and the way
 * to a private briefing — beside The Luxury 100 it reads around. In the
 * homepage's own grammar: header row with outlined button, then a two-
 * column layout: statement, figures in space (no boxes, no ruled grid),
 * one compact action and one editorial link. The figures mirror the Observatory overview's field
 * ledger (site-content/observatory/overview.blocks.html); update both.
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

$section  = summit_nav( 'primary' )['observatory'] ?? [ 'label' => 'The Observatory', 'path' => '/the-observatory' ];
$briefing = null;
foreach ( $section['children'] ?? [] as $child ) {
    if ( ! empty( $child['action'] ) ) {
        $briefing = $child;
    }
}
$figures = [
    [ '104', __( 'Entries', 'summit' ) ],
    [ '17', __( 'Sectors', 'summit' ) ],
    [ '102', __( 'Fully scored', 'summit' ) ],
    [ '2', __( 'Pending evidence', 'summit' ) ],
];
?>
<section class="home-obs" aria-labelledby="home-obs-heading">
    <div class="container container--wide">

        <header class="home-obs__header">
            <h2 class="home-obs__heading" id="home-obs-heading" data-reveal><?php echo esc_html( $section['label'] ); ?></h2>
            <?php if ( $briefing ) : ?>
            <a href="<?php echo esc_url( summit_nav_url( $briefing ) ); ?>" class="btn btn--secondary home-obs__btn" data-reveal data-reveal-delay="1">
                <?php echo esc_html( $briefing['label'] ); ?>
            </a>
            <?php endif; ?>
        </header>

        <div class="home-obs__layout">
            <div class="home-obs__text">
                <p class="home-obs__statement" data-reveal><?php esc_html_e( 'One governed field, read under one method.', 'summit' ); ?></p>
                <p class="home-obs__body" data-reveal data-reveal-delay="1"><?php esc_html_e( 'The Observatory reads the houses, objects and institutions around The Luxury 100 against the same five pillars, with its confidence declared. It classifies; it does not rank.', 'summit' ); ?></p>
                <p class="home-obs__links" data-reveal="fade" data-reveal-delay="2">
                    <a href="<?php echo esc_url( summit_nav_url( $section ) ); ?>"><?php echo esc_html( $section['label'] ); ?> <span aria-hidden="true">&rarr;</span></a>
                </p>
            </div>

            <div class="home-obs__field" data-reveal="fade" data-reveal-delay="1">
                <svg class="home-obs__instrument" viewBox="0 0 200 200" aria-hidden="true" focusable="false">
                    <circle cx="100" cy="100" r="96"/><circle cx="100" cy="100" r="66"/><circle cx="100" cy="100" r="34"/>
                    <path d="M100 0v200M0 100h200"/><circle class="home-obs__signal" cx="100" cy="100" r="1.8"/>
                </svg>
                <ul class="home-obs__figures" role="list">
                    <?php foreach ( $figures as [ $n, $label ] ) : ?>
                    <li><strong><?php echo esc_html( $n ); ?></strong><span><?php echo esc_html( $label ); ?></span></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

    </div>
</section>
