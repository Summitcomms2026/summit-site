<?php
/**
 * Part: Selected Work Panel
 * Location: parts/blocks/selected-work.php
 *
 * Dark editorial strip with four project cards.
 * Default state: equal-height cards with centred logo only.
 * Hover state: active card grows taller, reveals category, title,
 * body copy and directional arrow.
 *
 * ACF fields (from front page):
 *   home_work_headline text         optional
 *   home_work_cases    relationship  optional — case_study, max 4
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

$headline = get_field( 'home_work_headline' ) ?: 'Selected Work';
$img_dir  = get_template_directory_uri() . '/assets/img/';

// Curated selection — ACF relationship field.
$acf_cases = get_field( 'home_work_cases' );

if ( ! empty( $acf_cases ) ) {
    // Build cards from ACF relationship posts.
    $cards = [];
    foreach ( $acf_cases as $case ) {
        $cs_id     = $case->ID;
        $svc_terms = get_the_terms( $cs_id, 'service_type' );
        $svc_label = ( ! is_wp_error( $svc_terms ) && ! empty( $svc_terms ) )
                     ? $svc_terms[0]->name : '';
        $cards[] = [
            'category' => $svc_label,
            'title'    => get_the_title( $cs_id ),
            'desc'     => get_field( 'cs_summary', $cs_id ) ?: '',
            'url'      => get_permalink( $cs_id ),
            'logo'     => get_the_post_thumbnail_url( $cs_id, 'medium' ) ?: '',
            'logo_alt' => '',
        ];
    }
} else {
    // Hardcoded fallback matching approved comp.
    $cards = [
        [
            'category' => 'Brand Strategy',
            'title'    => 'Teddy William',
            'desc'     => 'Teddy William produces garments of the very highest order. Many are leaders in their respective fields, accustomed to exacting standards, discerning taste, and the quiet assurance of bespoke done properly.',
            'url'      => home_url( '/work/teddy-william' ),
            'logo'     => $img_dir . 'teddywilliam.png',
            'logo_alt' => $img_dir . 'teddywilliam_white.png',
        ],
        [
            'category' => 'Digital Transformation',
            'title'    => 'Kaleida Studios',
            'desc'     => 'Kaleida Studios is a science-driven media studio creating intelligent, emotionally resonant narratives across film, audio, and immersive formats. They explore the deepest questions shaping the human experience in a technological age.',
            'url'      => home_url( '/work/kaleida-studios' ),
            'logo'     => $img_dir . 'kaleidastudios.png',
            'logo_alt' => $img_dir . 'kaleidastudios_white.png',
        ],
        [
            'category' => 'Experience Design',
            'title'    => 'The Fight Against Skin Cancer',
            'desc'     => 'In this short film, we interviewed Dr Leo Kim, his dedicated team, and three brave patients who shared their journeys through diagnosis, cancer removal and these stories are a powerful reminder of the human spirit.',
            'url'      => home_url( '/work/the-fight-against-skin-cancer' ),
            'logo'     => $img_dir . 'fightagainst.png',
            'logo_alt' => $img_dir . 'fightagainst-white.png',
        ],
        [
            'category' => 'Brand Strategy',
            'title'    => 'Tastemakers',
            'desc'     => 'Season one of this show investigate a simple question: why do certain brands become timeless tastemakers, commanding belief at a time when belief is harder to earn, easier to lose, and prohibitively expensive to fake?',
            'url'      => home_url( '/work/tastemakers' ),
            'logo'     => $img_dir . 'tastemakers.png',
            'logo_alt' => $img_dir . 'tastemakers_white.png',
        ],
    ];
}

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
                View Projects
            </a>
        </header>

        <div class="selected-work__grid">

            <?php foreach ( $cards as $i => $card ) : ?>
            <a href="<?php echo esc_url( $card['url'] ); ?>"
               class="sw-card" data-reveal data-reveal-delay="<?php echo $i + 1; ?>"
               aria-label="<?php echo esc_attr( 'View: ' . $card['title'] ); ?>">

                <span class="sw-card__category"><?php echo esc_html( $card['category'] ); ?></span>

                <figure class="sw-card__logo" aria-hidden="true">
                    <img src="<?php echo esc_url( $card['logo'] ); ?>"
                         alt="" loading="lazy">
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
