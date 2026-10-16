<?php
/**
 * Template: Homepage
 * Location: front-page.php
 *
 * WordPress template hierarchy: front-page.php takes precedence when a
 * static front page is set in Settings > Reading.
 *
 * Component sequence (matches approved design comp):
 *   1.  Hero              → parts/blocks/hero.php (full-bleed photo)
 *   2.  Value Proposition → inline (centered statement)
 *   3.  Capability Triptych → parts/blocks/capability-triptych.php
 *   4.  Selected Work     → parts/blocks/selected-work.php
 *   5.  Tastemakers       → parts/blocks/tastemakers-feature.php
 *   6.  Download          → parts/blocks/download-feature.php
 *   7.  Featured Article  → parts/blocks/featured-article.php (3-up strip)
 *   8.  Subscribe         → parts/global/subscribe.php
 *   9.  Sectors Grid      → inline (World of Luxury)
 *  10.  CTA Footer        → parts/global/cta-footer.php (with bg image)
 *
 * ACF field group: group_summit_homepage (home_* prefix)
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

get_header();

the_post(); // consume the front-page post for ACF context
?>

<main id="main" class="site-main site-main--home" role="main">

    <!-- ── 1. Hero ────────────────────────────────────────────────────────── -->
    <?php get_template_part( 'parts/blocks/hero' ); ?>

    <!-- ── 2. Value Proposition (dark panel) ─────────────────────────────── -->
    <?php
    $vp_statement = get_field( 'home_vp_headline' )
        ?: 'We partner with luxury brands, creators and cultural institutions to define distinctive brand positions and create memorable client experiences. Our work sits between strategy, design and technology across product, place and culture.';
    ?>
    <section class="vp-block" aria-label="Value proposition">
        <div class="container container--wide">
            <p class="vp-block__statement" data-reveal>
                <?php echo esc_html( $vp_statement ); ?>
            </p>
        </div>
    </section>

    <!-- ── 3. Capability Triptych ─────────────────────────────────────────── -->
    <?php get_template_part( 'parts/blocks/capability-triptych' ); ?>

    <!-- ── 4. Selected Work ───────────────────────────────────────────────── -->
    <?php get_template_part( 'parts/blocks/selected-work' ); ?>

    <!-- ── 5. Tastemakers ─────────────────────────────────────────────────── -->
    <?php get_template_part( 'parts/blocks/tastemakers-feature' ); ?>

    <!-- ── 6. Download ────────────────────────────────────────────────────── -->
    <?php get_template_part( 'parts/blocks/download-feature' ); ?>

    <!-- ── 7. Featured Article (3-up strip) ───────────────────────────────── -->
    <?php get_template_part( 'parts/blocks/featured-article' ); ?>

    <!-- ── 8. Subscribe for Early Access ─────────────────────────────────── -->
    <section class="home-subscribe section--dark section--padded" aria-labelledby="home-subscribe-heading">
        <div class="container container--wide">
            <div class="home-subscribe__layout">

                <div class="home-subscribe__form-col">
                    <h2 class="home-subscribe__heading" id="home-subscribe-heading" data-reveal>Subscribe for Early Access</h2>
                    <p class="home-subscribe__sub" data-reveal data-reveal-delay="1">A subscriber-first publication covering the ideas, industries and individuals shaping the future of luxury.</p>

                    <?php if ( shortcode_exists( 'summit_subscribe_form' ) ) :
                        echo do_shortcode( '[summit_subscribe_form]' );
                    else : ?>
                    <form class="home-subscribe__form" action="#" method="post" aria-label="Newsletter sign-up">
                        <div class="home-subscribe__fields" data-reveal data-reveal-delay="2">
                            <div class="home-subscribe__field">
                                <label for="sub-fname">First Name *</label>
                                <input type="text" id="sub-fname" name="first_name" required autocomplete="given-name">
                            </div>
                            <div class="home-subscribe__field">
                                <label for="sub-sname">Surname *</label>
                                <input type="text" id="sub-sname" name="surname" required autocomplete="family-name">
                            </div>
                            <div class="home-subscribe__field">
                                <label for="sub-email">Email *</label>
                                <input type="email" id="sub-email" name="email" required autocomplete="email">
                            </div>
                            <div class="home-subscribe__field">
                                <label for="sub-mobile">Mobile</label>
                                <input type="tel" id="sub-mobile" name="mobile" autocomplete="tel">
                            </div>
                            <div class="home-subscribe__field">
                                <label for="sub-company">Company</label>
                                <input type="text" id="sub-company" name="company" autocomplete="organization">
                            </div>
                            <div class="home-subscribe__field">
                                <label for="sub-position">Position</label>
                                <input type="text" id="sub-position" name="position" autocomplete="organization-title">
                            </div>
                        </div>
                        <p class="home-subscribe__consent" data-reveal data-reveal-delay="3">By signing up you agree to receive emails from us. To opt out, click unsubscribe in the email footer.</p>
                        <button type="submit" class="btn home-subscribe__btn" data-reveal data-reveal-delay="3">Subscribe</button>
                    </form>
                    <?php endif; ?>
                </div>

                <div class="home-subscribe__info-col">
                    <h3 class="home-subscribe__info-heading" data-reveal>The Future of Luxury</h3>
                    <p class="home-subscribe__pub-intro" data-reveal data-reveal-delay="1"><strong>The Future of Luxury</strong> is a renowned digital platform for serious readers of luxury business, culture and wealth, published by Summit Communication Group.</p>
                    <ul class="home-subscribe__pub-details">
                        <li data-reveal data-reveal-delay="2">
                            <strong>History</strong>
                            <span>More than 120+ long-form articles and analyses published to date.</span>
                        </li>
                        <li data-reveal data-reveal-delay="3">
                            <strong>Audience</strong>
                            <span>Written for an international readership of over 2,400 CEOs, senior executives, private bankers and affluent decision-makers.</span>
                        </li>
                        <li data-reveal data-reveal-delay="4">
                            <strong>Distribution</strong>
                            <span>Published 44 times a year, with each edition released to subscribers first.</span>
                        </li>
                        <li data-reveal data-reveal-delay="5">
                            <strong>Content Focus</strong>
                            <span>Features business intelligence, cultural commentary, interviews and market insight from influential voices across luxury and art.</span>
                        </li>
                    </ul>
                </div>

            </div>
        </div>
    </section>

    <!-- ── 9. World of Luxury / Sectors Grid ──────────────────────────────── -->
    <?php
    $sectors_headline = get_field( 'home_sectors_headline' ) ?: 'The World of Luxury';
    $sectors_body     = get_field( 'home_sectors_body' ) ?: 'Our global talent network spans the divisions shaping the modern luxury economy.';

    $sectors = [
        [ 'Fashion & Leather Goods', 'Codes, desire and distinction shaped for brands that must endure.' ],
        [ 'Watches & Jewellery', 'Narratives measured in carats, complications and continental restraint.' ],
        [ 'Hospitality & Travel', 'Experiences designed to signal taste, trust and effortless privilege.' ],
        [ 'Beauty & Fragrance', 'Positioning beauty brands where ritual, identity and desire converge.' ],
        [ 'Automotive & Aviation', 'Velocity, elegance and engineering theatre shaped with continental confidence.' ],
        [ 'Art & Culture', 'Cultural strategy for institutions and family offices with serious ambition.' ],
        [ 'Wine & Spirits', 'Enduring brand worlds for producers of heritage, ritual and appetite.' ],
        [ 'Architecture & Interiors', 'Spatial brands shaped through discipline, atmosphere and refined distinction.' ],
        [ 'Sport & Lifestyle', 'Positioning for aspirational brands where performance meets cultural status.' ],
        [ 'Wealth & Finance', 'Trust architecture for firms serving affluent and influential audiences.' ],
        [ 'Technology', 'Strategic clarity for innovation brands seeking authority, not novelty.' ],
        [ 'Gastronomy', 'Brands with appetite, ceremony and unmistakable point of view.' ],
    ];
    ?>
    <section class="sectors-grid-section section--tinted section--padded" aria-labelledby="sectors-heading">
        <div class="container container--wide">

            <header class="sectors-grid-section__header">
                <div class="sectors-grid-section__header-text">
                    <h2 class="sectors-grid-section__headline" id="sectors-heading" data-reveal="fade">
                        <?php echo esc_html( $sectors_headline ); ?>
                    </h2>
                    <p class="sectors-grid-section__body" data-reveal="fade" data-reveal-delay="1">
                        <?php echo wp_strip_all_tags( $sectors_body ); ?>
                    </p>
                </div>
                <a href="<?php echo esc_url( home_url( '/design-tomorrow/' ) ); ?>"
                   class="sectors-grid-section__cta" data-reveal="fade" data-reveal-delay="1">
                    Get In Touch
                </a>
            </header>

            <ul class="sectors-grid" role="list">
                <?php
                $sector_delays = [ 1, 2, 3, 4, 5, 6, 1, 2, 3, 4, 5, 6 ];
                foreach ( $sectors as $si => $sector ) :
                    $sd = $sector_delays[ $si ] ?? 1;
                ?>
                <li class="sectors-grid__item" data-reveal="fade" data-reveal-delay="<?php echo $sd; ?>">
                    <strong class="sectors-grid__item-title"><?php echo esc_html( $sector[0] ); ?></strong>
                    <span class="sectors-grid__item-desc"><?php echo esc_html( $sector[1] ); ?></span>
                </li>
                <?php endforeach; ?>
            </ul>

        </div>
    </section>

    <!-- ── 10. CTA Footer ─────────────────────────────────────────────────── -->
    <?php
    get_template_part( 'parts/global/cta-footer', null, [
        'headline' => 'Start the Conversation',
        'body'     => 'We partner with ambitious brands to sharpen positioning, elevate creative expression and build digital experiences that deliver lasting value.',
        'label'    => "Let\u{2019}s Design Tomorrow",
        'image'    => get_template_directory_uri() . '/assets/img/cta-mainimage.jpg',
    ] );
    ?>

</main>

<?php get_footer(); ?>
