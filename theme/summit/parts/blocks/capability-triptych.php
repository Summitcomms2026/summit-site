<?php
/**
 * Part: Capability Triptych
 * Location: parts/blocks/capability-triptych.php
 *
 * Three-column service pillars: Brand Strategy, Experience Design,
 * Digital Transformation. Each pillar has a title, description and
 * link to its service page.
 *
 * Always renders — all fields have hardcoded defaults.
 *
 * ACF fields (from front page):
 *   home_cap_intro   textarea optional
 *   home_cap_N_title text     optional (N = 1, 2, 3)
 *   home_cap_N_desc  textarea optional
 *   home_cap_N_url   url      optional
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

$intro = get_field( 'home_cap_intro' );

$img_dir = get_template_directory_uri() . '/assets/img/';

$pillars = [
    [
        'title' => get_field( 'home_cap_1_title' ) ?: 'Brand Strategy',
        'desc'  => get_field( 'home_cap_1_desc' )  ?: 'Positioning, identity and narrative frameworks that give luxury brands strategic clarity and cultural relevance.',
        'url'   => get_field( 'home_cap_1_url' )   ?: home_url( '/what-we-do/brand-strategy' ),
        'icon'     => $img_dir . 'icon_brand-strategy.svg',
        'icon_on'  => $img_dir . 'icon_brand-strategy-on.svg',
    ],
    [
        'title' => get_field( 'home_cap_2_title' ) ?: 'Experience Design',
        'desc'  => get_field( 'home_cap_2_desc' )  ?: 'Physical and digital experiences that reflect brand truth, delight discerning audiences and create lasting impressions.',
        'url'   => get_field( 'home_cap_2_url' )   ?: home_url( '/what-we-do/experience-design' ),
        'icon'     => $img_dir . 'icon_experience-design.svg',
        'icon_on'  => $img_dir . 'icon_experience-design-on.svg',
    ],
    [
        'title' => get_field( 'home_cap_3_title' ) ?: 'Digital Transformation',
        'desc'  => get_field( 'home_cap_3_desc' )  ?: 'Platforms, systems and digital infrastructure built for premium performance, operational elegance and measurable growth.',
        'url'   => get_field( 'home_cap_3_url' )   ?: home_url( '/what-we-do/digital-transformation' ),
        'icon'     => $img_dir . 'icon_digital-transformation.svg',
        'icon_on'  => $img_dir . 'icon_digital-transformation-on.svg',
    ],
];
?>

<section class="capability-triptych section--tinted section--padded" aria-labelledby="capabilities-heading">
    <div class="container container--wide">

        <header class="capability-triptych__header">
            <h2 class="capability-triptych__headline" id="capabilities-heading" data-reveal>What We Do</h2>
            <a href="<?php echo esc_url( home_url( '/what-we-do' ) ); ?>"
               class="btn btn--secondary" data-reveal data-reveal-delay="1">
                Explore Services
            </a>
        </header>

        <div class="capability-triptych__grid">

            <?php
            $pillar_delays = [ 1, 2, 3 ];
            foreach ( $pillars as $i => $pillar ) :
                $delay = $pillar_delays[ $i ] ?? 1;
            ?>
            <div class="capability-triptych__pillar" data-reveal data-reveal-delay="<?php echo $delay; ?>">
                <figure class="capability-triptych__thumb" aria-hidden="true">
                    <img class="capability-triptych__icon--off"
                         src="<?php echo esc_url( $pillar['icon'] ); ?>"
                         alt="" width="100" height="100" loading="lazy">
                    <img class="capability-triptych__icon--on"
                         src="<?php echo esc_url( $pillar['icon_on'] ); ?>"
                         alt="" width="100" height="100" loading="lazy">
                </figure>
                <span class="capability-triptych__number" aria-hidden="true">[ <?php echo $i + 1; ?> ]</span>
                <h3 class="capability-triptych__title">
                    <?php echo esc_html( $pillar['title'] ); ?>
                </h3>
                <div class="capability-triptych__desc">
                    <?php echo wp_kses_post( wpautop( $pillar['desc'] ) ); ?>
                </div>
                <a href="<?php echo esc_url( $pillar['url'] ); ?>" class="capability-triptych__link">
                    Learn More <span aria-hidden="true">&rarr;</span>
                </a>
            </div>
            <?php endforeach; ?>

        </div>

    </div>
</section>
