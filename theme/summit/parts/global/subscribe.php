<?php
/**
 * Part: Subscribe Module
 * Location: parts/global/subscribe.php
 *
 * Newsletter capture block used on article singles, archive pages and
 * episode pages. When the ActiveCampaign shortcode is registered it
 * takes over automatically; until then a native form renders.
 *
 * Variables (optional):
 *   summit_subscribe_heading  string
 *   summit_subscribe_sub      string
 *   summit_subscribe_id       string  Unique ID for the form label (avoids duplicate IDs)
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

$heading  = get_query_var( 'summit_subscribe_heading', 'Stay ahead of luxury' );
$sub      = get_query_var( 'summit_subscribe_sub',     'Receive The Future of Luxury — strategic thinking on luxury, culture and the forces shaping how value is perceived.' );
$field_id = get_query_var( 'summit_subscribe_id',      'subscribe-email-' . get_the_ID() );
?>

<section class="subscribe-module" aria-labelledby="<?php echo esc_attr( $field_id . '-heading' ); ?>">
    <div class="container container--medium">
        <div class="subscribe-module__inner">

            <h2 class="subscribe-module__heading" id="<?php echo esc_attr( $field_id . '-heading' ); ?>">
                <?php echo esc_html( $heading ); ?>
            </h2>

            <?php if ( $sub ) : ?>
            <p class="subscribe-module__sub">
                <?php echo esc_html( $sub ); ?>
            </p>
            <?php endif; ?>

            <?php if ( shortcode_exists( 'summit_subscribe_form' ) ) :
                echo do_shortcode( '[summit_subscribe_form]' );
            else : ?>
            <form class="subscribe-module__form" action="#" method="post"
                  aria-label="Newsletter sign-up">
                <label for="<?php echo esc_attr( $field_id ); ?>" class="screen-reader-text">
                    Email address
                </label>
                <input type="email"
                       id="<?php echo esc_attr( $field_id ); ?>"
                       name="email"
                       placeholder="Your email address"
                       required
                       autocomplete="email"
                       class="subscribe-module__input">
                <button type="submit" class="btn btn--primary subscribe-module__btn">
                    Subscribe
                </button>
            </form>
            <?php endif; ?>

        </div>
    </div>
</section>
