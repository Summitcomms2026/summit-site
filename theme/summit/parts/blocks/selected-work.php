<?php
/**
 * Part: Selected Work Panel
 * Location: parts/blocks/selected-work.php
 *
 * Research to Challenge — Summit's published research as a shelf of
 * publications in a dark field: Touch Me Not, the principal work, stands
 * as the dominant object; The Luxury 100 and The Observatory sit beside it
 * as two secondary volumes. Each object shows its own mark (or its cover,
 * once delivered) with its title and line set beneath — the copy is read,
 * not revealed on hover. Each keeps a low material signature.
 *
 * ACF fields (from front page):
 *   home_work_headline text         optional
 *   home_work_cases    relationship  optional — case_study, max 4
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

// Research to Challenge (S01-P006, summit_home_panel()): Touch Me Not,
// The Luxury 100 and The Observatory, each in its own mark. The earlier
// client cards are not part of the Editorial Master.
$panel    = summit_home_panel( 'S01-P006' );
$headline = $panel['headline'] ?? __( 'Research to Challenge', 'summit' );
$marks    = [
    'touch me not'    => [ 'mark' => 'tmn', 'tone' => 'gold' ],
    'the luxury 100'  => [ 'mark' => 'l100', 'tone' => 'metal' ],
    'the observatory' => [ 'mark' => 'obs', 'tone' => 'plain' ],
];
$cards = [];
foreach ( $panel['rows'] ?? [] as $row ) {
    $key     = strtolower( $row['title'] );
    $cards[] = array_merge( [ 'mark' => '', 'tone' => 'plain' ], $marks[ $key ] ?? [], [
        'title' => $row['title'],
        'desc'  => $row['line'],
        'url'   => summit_meta_url( $row['url'] ?? '/research' ),
    ] );
}
$cta = $panel['ctas'][0] ?? [ 'label' => __( 'Explore Research', 'summit' ), 'url' => '/research' ];

if ( empty( $cards ) ) {
    return;
}
?>

<section class="selected-work section--dark" aria-labelledby="selected-work-heading">
    <div class="container container--wide">

        <header class="selected-work__header">
            <div>
                <?php if ( ! empty( $panel['eyebrow'] ) ) : ?>
                <p class="home-eyebrow" data-reveal="fade"><?php echo esc_html( $panel['eyebrow'] ); ?></p>
                <?php endif; ?>
                <h2 class="selected-work__headline" id="selected-work-heading" data-reveal>
                    <?php echo esc_html( $headline ); ?>
                </h2>
                <?php foreach ( $panel['paras'] ?? [] as $para ) : ?>
                <p class="home-intro" data-reveal data-reveal-delay="1"><?php echo esc_html( $para ); ?></p>
                <?php endforeach; ?>
            </div>
            <a href="<?php echo esc_url( summit_meta_url( $cta['url'] ) ); ?>"
               class="btn btn--secondary" data-reveal data-reveal-delay="1">
                <?php echo esc_html( $cta['label'] ); ?>
            </a>
        </header>

        <div class="research-shelf">

            <?php foreach ( $cards as $i => $card ) : ?>
            <a href="<?php echo esc_url( $card['url'] ); ?>"
               class="research-shelf__item research-shelf__item--<?php echo esc_attr( $card['tone'] ?? 'plain' ); ?><?php echo 0 === $i ? ' research-shelf__item--lead' : ''; ?>" data-reveal data-reveal-delay="<?php echo $i + 1; ?>">

                <figure class="research-shelf__plate" aria-hidden="true">
                    <?php $cover = summit_panel_visual( 'S01-P006-' . ( $i + 1 ), 'sw-card__cover', 'medium_large' ); ?>
                    <?php if ( $cover ) : // the publication's own cover, once delivered ?>
                    <?php echo $cover; // phpcs:ignore WordPress.Security.EscapeOutput ?>
                    <?php elseif ( 'l100' === ( $card['mark'] ?? '' ) ) : ?>
                    <span class="sw-mark sw-mark--l100">
                        <svg viewBox="0 0 99 99" focusable="false"><?php
                        for ( $p = 0; $p < 100; $p++ ) {
                            if ( 82 === $p ) {
                                continue; // rank 83, the empty chair
                            }
                            printf( '<rect x="%d" y="%d" width="7" height="7"/>', ( $p % 10 ) * 10, intdiv( $p, 10 ) * 10 );
                        }
                        ?><rect class="sw-mark__empty" x="20.5" y="80.5" width="6" height="6"/></svg>
                    </span>
                    <?php elseif ( 'tmn' === ( $card['mark'] ?? '' ) ) : ?>
                    <span class="sw-mark sw-mark--tmn">
                        <span class="sw-mark__title">Touch Me Not</span>
                        <span class="sw-mark__rule"></span>
                        <span class="sw-mark__sub">The Mystery of Luxury</span>
                    </span>
                    <?php elseif ( 'obs' === ( $card['mark'] ?? '' ) ) : ?>
                    <span class="sw-mark sw-mark--obs">
                        <svg viewBox="0 0 200 200" focusable="false"><circle cx="100" cy="100" r="96"/><circle cx="100" cy="100" r="66"/><circle cx="100" cy="100" r="34"/><path d="M100 0v200M0 100h200"/></svg>
                    </span>
                    <?php endif; ?>
                </figure>

                <div class="research-shelf__caption">
                    <h3 class="research-shelf__title"><?php echo esc_html( $card['title'] ); ?></h3>
                    <p class="research-shelf__line"><?php echo esc_html( $card['desc'] ); ?></p>
                </div>

            </a>
            <?php endforeach; ?>

        </div>

    </div>
</section>
