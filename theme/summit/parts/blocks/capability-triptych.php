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
        'thumb' => $img_dir . 'whatwedo-brandstrategy.jpg',
    ],
    [
        'title' => get_field( 'home_cap_2_title' ) ?: 'Experience Design',
        'desc'  => get_field( 'home_cap_2_desc' )  ?: 'Physical and digital experiences that reflect brand truth, delight discerning audiences and create lasting impressions.',
        'url'   => get_field( 'home_cap_2_url' )   ?: home_url( '/what-we-do/experience-design' ),
        'thumb' => $img_dir . 'whatwedo-experiencedesign.jpg',
    ],
    [
        'title' => get_field( 'home_cap_3_title' ) ?: 'Digital Transformation',
        'desc'  => get_field( 'home_cap_3_desc' )  ?: 'Platforms, systems and digital infrastructure built for premium performance, operational elegance and measurable growth.',
        'url'   => get_field( 'home_cap_3_url' )   ?: home_url( '/what-we-do/digital-transformation' ),
        'thumb' => $img_dir . 'whatwedo-digitaltransformation.jpg',
    ],
];
?>

<section class="capability-triptych section--tinted section--padded" aria-labelledby="capabilities-heading">
    <div class="container container--wide">

        <header class="capability-triptych__header">
            <h2 class="capability-triptych__headline" id="capabilities-heading" data-reveal>What We Do</h2>
            <a href="<?php echo esc_url( home_url( '/what-we-do' ) ); ?>"
               class="btn btn--primary" data-reveal data-reveal-delay="1">
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
                <?php if ( ! empty( $pillar['thumb'] ) ) : ?>
                <figure class="capability-triptych__thumb" aria-hidden="true">
                    <img src="<?php echo esc_url( $pillar['thumb'] ); ?>"
                         alt="" width="117" height="117" loading="lazy">
                </figure>
                <?php endif; ?>
                <h3 class="capability-triptych__title">
                    <?php echo esc_html( $pillar['title'] ); ?>
                </h3>
                <div class="capability-triptych__desc">
                    <?php echo wp_kses_post( wpautop( $pillar['desc'] ) ); ?>
                </div>
                <a href="<?php echo esc_url( $pillar['url'] ); ?>"
                   class="capability-triptych__link">
                    Learn More
                </a>
            </div>
            <?php endforeach; ?>

        </div>

    </div>
</section>
