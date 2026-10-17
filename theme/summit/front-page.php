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
 *   5.  The Observatory   → parts/blocks/observatory-feature.php (the panel
 *                           position Tastemakers held)
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
        ?: 'Luxury endures on the quality of its decisions. Summit helps houses, institutions and investors make them.';
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
    <?php get_template_part( 'parts/blocks/observatory-feature' ); ?>

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
                    <?php $summit_sub = summit_subscribe_returned(); ?>
                    <form class="home-subscribe__form" action="<?php echo esc_url( summit_subscribe_action() ); ?>" method="post" aria-label="Newsletter sign-up" data-summit-subscribe>
                        <?php summit_subscribe_fields( '/' ); ?>
                        <div class="home-subscribe__fields" data-reveal data-reveal-delay="2">
                            <div class="home-subscribe__field">
                                <label for="sub-fname">First Name *</label>
                                <input type="text" id="sub-fname" name="enq_first_name" required autocomplete="given-name">
                            </div>
                            <div class="home-subscribe__field">
                                <label for="sub-sname">Surname *</label>
                                <input type="text" id="sub-sname" name="enq_surname" required autocomplete="family-name">
                            </div>
                            <div class="home-subscribe__field">
                                <label for="sub-email">Email *</label>
                                <input type="email" id="sub-email" name="enq_email" required autocomplete="email">
                            </div>
                            <div class="home-subscribe__field">
                                <label for="sub-mobile">Mobile</label>
                                <input type="tel" id="sub-mobile" name="enq_phone" autocomplete="tel">
                            </div>
                            <div class="home-subscribe__field">
                                <label for="sub-company">Company</label>
                                <input type="text" id="sub-company" name="enq_company" autocomplete="organization">
                            </div>
                            <div class="home-subscribe__field">
                                <label for="sub-position">Position</label>
                                <input type="text" id="sub-position" name="enq_position" autocomplete="organization-title">
                            </div>
                        </div>
                        <p class="home-subscribe__consent" data-reveal data-reveal-delay="3">By signing up you agree to receive emails from us. To opt out, click unsubscribe in the email footer.</p>
                        <button type="submit" class="btn home-subscribe__btn" data-reveal data-reveal-delay="3">Subscribe</button>
                        <p class="summit-subscribe-status<?php echo 'sent' === $summit_sub['state'] ? ' is-sent' : ( $summit_sub['state'] ? ' is-error' : '' ); ?>"<?php echo $summit_sub['state'] ? ' id="enquiry-status" tabindex="-1"' : ''; ?> role="status" aria-live="polite" data-subscribe-status><?php echo esc_html( $summit_sub['message'] ); ?></p>
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

    // The industries Summit's Solutions are read through (navigation
    // config), each with its page's line and image (summit_standfirst,
    // summit_visual — the Solutions industry family).
    $solution_groups = summit_nav( 'primary' )['solutions']['groups'] ?? [];
    $industry_group  = $solution_groups['by-industry'] ?? ( $solution_groups ? reset( $solution_groups ) : [] );
    $sectors = [];
    foreach ( $industry_group['children'] ?? [] as $item ) {
        $page      = get_page_by_path( trim( $item['path'], '/' ), OBJECT, 'page' );
        $sectors[] = [
            $item['label'],
            $page ? (string) get_post_meta( $page->ID, 'summit_standfirst', true ) : '',
            summit_nav_url( $item ),
            $page ? (string) get_post_meta( $page->ID, 'summit_visual', true ) : '',
        ];
    }
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
                <a href="<?php echo esc_url( summit_nav_url( summit_nav( 'cta' ) ) ); ?>"
                   class="sectors-grid-section__cta" data-reveal="fade" data-reveal-delay="1">
                    Let’s Talk
                </a>
            </header>

            <ul class="sectors-grid sectors-grid--industries" role="list">
                <?php
                $sector_delays = [ 1, 2, 3, 4, 5, 6, 1, 2, 3, 4, 5, 6 ];
                foreach ( $sectors as $si => $sector ) :
                    $sd = $sector_delays[ $si ] ?? 1;
                ?>
                <li class="sectors-grid__item<?php echo $sector[3] ? ' has-image' : ''; ?>" data-reveal="fade" data-reveal-delay="<?php echo $sd; ?>">
                    <a class="sectors-grid__link" href="<?php echo esc_url( $sector[2] ); ?>">
                    <?php if ( $sector[3] && ! is_numeric( $sector[3] ) ) : ?>
                    <img class="sectors-grid__image" src="<?php echo esc_url( $sector[3] ); ?>" alt="" width="614" height="767" loading="lazy" decoding="async">
                    <?php endif; ?>
                    <strong class="sectors-grid__item-title"><?php echo esc_html( $sector[0] ); ?></strong>
                    <span class="sectors-grid__item-desc"><?php echo esc_html( $sector[1] ); ?></span>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>

        </div>
    </section>

    <!-- ── 10. CTA Footer ─────────────────────────────────────────────────── -->
    <?php
    get_template_part( 'parts/global/cta-footer', null, [
        'headline' => 'Start the Conversation',
        'body'     => 'For questions of position, desire, stewardship and long-term value, begin with the question.',
        'label'    => "Let\u{2019}s Talk",
        'image'    => get_template_directory_uri() . '/assets/img/cta-mainimage.jpg',
    ] );
    ?>

</main>

<?php get_footer(); ?>
