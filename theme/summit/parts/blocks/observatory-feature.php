<?php
/**
 * Part: The Observatory (homepage)
 * Location: parts/blocks/observatory-feature.php
 * Used by: front-page.php, in the panel position Tastemakers held
 *
 * What Valuation Misses (Editorial Handover v1, S01-P004), from
 * summit_home_panel(). What makes Summit different, shown rather than claimed: the Observatory
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
// Copy: What Valuation Misses (S01-P004). Its first CTA is the editorial
// link; the private briefing keeps the header's outlined button.
$panel        = summit_home_panel( 'S01-P004' );
$panel_ctas   = $panel['ctas'] ?? [];
$explore_cta  = $panel_ctas[0] ?? [ 'label' => $section['label'], 'url' => $section['path'] ];
$briefing_cta = $panel_ctas[1] ?? ( $briefing ? [ 'label' => $briefing['label'], 'url' => $briefing['path'] ] : null );

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
            <div>
                <?php if ( ! empty( $panel['eyebrow'] ) ) : ?>
                <p class="home-eyebrow" data-reveal="fade"><?php echo esc_html( $panel['eyebrow'] ); ?></p>
                <?php endif; ?>
                <h2 class="home-obs__heading" id="home-obs-heading" data-reveal><?php echo esc_html( $panel['headline'] ?? $section['label'] ); ?></h2>
            </div>
            <?php if ( $briefing_cta ) : ?>
            <a href="<?php echo esc_url( summit_meta_url( $briefing_cta['url'] ) ); ?>" class="btn btn--secondary home-obs__btn" data-reveal data-reveal-delay="1">
                <?php echo esc_html( $briefing_cta['label'] ); ?>
            </a>
            <?php endif; ?>
        </header>

        <div class="home-obs__layout">

            <div class="home-obs__text">
                <?php foreach ( $panel['paras'] ?? [] as $k => $para ) : ?>
                <p class="<?php echo $k ? 'home-obs__body' : 'home-obs__lead'; ?>" data-reveal><?php echo esc_html( $para ); ?></p>
                <?php endforeach; ?>
                <?php if ( $explore_cta ) : ?>
                <p class="home-obs__links" data-reveal="fade" data-reveal-delay="2">
                    <a href="<?php echo esc_url( summit_meta_url( $explore_cta['url'] ) ); ?>"><?php echo esc_html( $explore_cta['label'] ); ?> <span aria-hidden="true">&rarr;</span></a>
                </p>
                <?php endif; ?>
            </div>

            <?php $obs_visual = summit_panel_visual( 'S01-P004', 'home-obs__visual' ); ?>
            <?php if ( $obs_visual ) : // a delivered research image takes the instrument's place ?>
            <div class="home-obs__field home-obs__field--image" data-reveal="fade" data-reveal-delay="1"><?php echo $obs_visual; // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
            <?php else : ?>
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
            <?php endif; ?>
        </div>

    </div>
</section>
