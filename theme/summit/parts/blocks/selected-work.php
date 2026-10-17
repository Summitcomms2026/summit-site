<?php
/**
 * Part: Selected Work Panel
 * Location: parts/blocks/selected-work.php
 *
 * Selected Work & Research — the evidence of what Summit has made and
 * argued: client work beside its published research. Dark editorial strip
 * with four cards; a card shows a client's logo, or a typographic mark for
 * Summit's own work (The Luxury 100's hundred places; Touch Me Not's title).
 * Default state: equal-height cards with centred logo only.
 * Hover state: active card grows taller, reveals category, title,
 * body copy and directional arrow, and releases a muted colour of its
 * own material (rose, lilac, metal, gold) — quiet at rest.
 *
 * ACF fields (from front page):
 *   home_work_headline text         optional
 *   home_work_cases    relationship  optional — case_study, max 4
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

$headline = get_field( 'home_work_headline' ) ?: 'Selected Work & Research';
$img_dir  = get_template_directory_uri() . '/assets/img/';

// Hardcoded cards matching approved comp.
// ACF relationship (home_work_cases) can override when populated with
// full artwork — for now, always use the approved reference set.
$cards = [
        [
            'category' => 'Brand Strategy',
            'title'    => 'Teddy William',
            'desc'     => 'Teddy William produces garments of the very highest order. Many are leaders in their respective fields, accustomed to exacting standards, discerning taste, and the quiet assurance of bespoke done properly.',
            'url'      => ( get_post_type_archive_link( 'case_study' ) ?: home_url( '/work-showcase/' ) ) /* no case study page yet */,
            'logo'     => $img_dir . 'teddywilliam_white.png',
            'tone'     => 'rose',   // cloth and tailoring warmth
        ],
        [
            'category' => 'Digital Transformation',
            'title'    => 'Kaleida Studios',
            'desc'     => 'Kaleida Studios is a science-driven media studio creating intelligent, emotionally resonant narratives across film, audio, and immersive formats. They explore the deepest questions shaping the human experience in a technological age.',
            'url'      => home_url( '/work-showcase/kaleida-studios/' ),
            'logo'     => $img_dir . 'kaleidastudios_white.png',
            'tone'     => 'lilac',  // projected light, the studio's medium
        ],
        [
            'category' => 'Research',
            'title'    => 'The Luxury 100',
            'desc'     => 'An annual, governed reading of durable luxury objects. In the 2027 reading, ninety-nine places are held and one chair is left empty.',
            'url'      => home_url( '/research/luxury-100' ),
            'mark'     => 'l100',
            'tone'     => 'metal',  // the empty chair's patina
        ],
        [
            'category' => 'Book',
            'title'    => 'Touch Me Not',
            'desc'     => 'The Mystery of Luxury — a book by Gregory Gray and Olivia Dhordain, and the argument behind Summit’s research.',
            'url'      => home_url( '/research/touch-me-not' ),
            'mark'     => 'tmn',
            'tone'     => 'gold',   // the title page: ink and gold on paper
        ],
    ];

if ( empty( $cards ) ) {
    return;
}
?>

<section class="selected-work section--dark" aria-labelledby="selected-work-heading">
    <div class="container container--wide">

        <header class="selected-work__header">
            <h2 class="selected-work__headline" id="selected-work-heading" data-reveal>
                <?php echo esc_html( $headline ); ?>
            </h2>
            <a href="<?php echo esc_url( get_post_type_archive_link( 'case_study' ) ?: home_url( '/work' ) ); ?>"
               class="btn btn--secondary" data-reveal data-reveal-delay="1">
                All Work
            </a>
        </header>

        <div class="selected-work__grid">

            <?php foreach ( $cards as $i => $card ) : ?>
            <a href="<?php echo esc_url( $card['url'] ); ?>"
               class="sw-card sw-card--<?php echo esc_attr( $card['tone'] ?? 'plain' ); ?>" data-reveal data-reveal-delay="<?php echo $i + 1; ?>"
               aria-label="<?php echo esc_attr( 'View: ' . $card['title'] ); ?>">

                <span class="sw-card__category"><?php echo esc_html( $card['category'] ); ?></span>

                <figure class="sw-card__logo" aria-hidden="true">
                    <?php if ( 'l100' === ( $card['mark'] ?? '' ) ) : ?>
                    <span class="sw-mark sw-mark--l100">
                        <svg viewBox="0 0 99 99" focusable="false"><?php
                        for ( $p = 0; $p < 100; $p++ ) {
                            if ( 82 === $p ) {
                                continue; // rank 83, the empty chair
                            }
                            printf( '<rect x="%d" y="%d" width="7" height="7"/>', ( $p % 10 ) * 10, intdiv( $p, 10 ) * 10 );
                        }
                        ?><rect class="sw-mark__empty" x="20.5" y="80.5" width="6" height="6"/></svg>
                        <span class="sw-mark__name">The Luxury 100</span>
                    </span>
                    <?php elseif ( 'tmn' === ( $card['mark'] ?? '' ) ) : ?>
                    <span class="sw-mark sw-mark--tmn">
                        <span class="sw-mark__title">Touch Me Not</span>
                        <span class="sw-mark__rule"></span>
                        <span class="sw-mark__sub">The Mystery of Luxury</span>
                    </span>
                    <?php else : ?>
                    <img src="<?php echo esc_url( $card['logo'] ); ?>"
                         alt="" loading="lazy">
                    <?php endif; ?>
                </figure>

                <div class="sw-card__reveal">
                    <h3 class="sw-card__title"><?php echo esc_html( $card['title'] ); ?></h3>
                    <p class="sw-card__desc"><?php echo esc_html( $card['desc'] ); ?></p>
                </div>

                <span class="sw-card__arrow" aria-hidden="true">&rarr;</span>

            </a>
            <?php endforeach; ?>

        </div>

    </div>
</section>
