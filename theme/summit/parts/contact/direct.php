<?php
/**
 * Part: Contact — direct contact
 * Location: parts/contact/direct.php
 * Used by: page-contact.php via [summit_contact_direct]
 *
 * Summit's approved direct routes (Editorial Handover v1, channel
 * configuration): the Australian and United Kingdom telephones, as
 * click-to-call links, and the studio's email. SMS and WhatsApp are not
 * offered until those services are confirmed.
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

$routes = summit_contact_channels();
?>
<ul class="ct-direct" role="list">
    <?php foreach ( $routes as $route ) : ?>
    <li>
        <span class="ct-direct__label"><?php echo esc_html( $route['label'] ); ?></span>
        <a href="<?php echo esc_url( $route['href'], [ 'tel', 'mailto' ] ); ?>"><?php echo esc_html( $route['display'] ); ?></a>
    </li>
    <?php endforeach; ?>
</ul>
