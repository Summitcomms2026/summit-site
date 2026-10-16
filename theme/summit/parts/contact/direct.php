<?php
/**
 * Part: Contact — direct contact
 * Location: parts/contact/direct.php
 * Used by: page-contact.php via [summit_contact_direct]
 *
 * Only Summit's established public contact details: the WhatsApp number
 * the header and menu already publish, and LinkedIn from the navigation
 * config. Nothing else is invented here.
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

$linkedin = summit_nav( 'social' )[0] ?? null;
?>
<ul class="ct-direct" role="list">
    <li>
        <span class="ct-direct__label"><?php esc_html_e( 'WhatsApp', 'summit' ); ?></span>
        <a href="https://wa.me/61430388662" rel="noopener">+61&nbsp;430&nbsp;388&nbsp;662</a>
    </li>
    <?php if ( $linkedin ) : ?>
    <li>
        <span class="ct-direct__label"><?php echo esc_html( $linkedin['label'] ); ?></span>
        <a href="<?php echo esc_url( summit_nav_url( $linkedin ) ); ?>" rel="noopener" target="_blank"><?php esc_html_e( 'Summit Communication Group', 'summit' ); ?><span class="screen-reader-text"> <?php esc_html_e( '(opens in a new tab)', 'summit' ); ?></span></a>
    </li>
    <?php endif; ?>
</ul>
