<?php
/**
 * Part: The Future of Luxury — editorial independence
 * Location: parts/fol/independence.php
 * Used by: archive-article.php
 *
 * The section's standing statement on independence, set as a dark panel.
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;
?>
<section class="fol-panel fol-independence section--dark" aria-labelledby="fol-independence-heading">
    <div class="fol-wrap fol-independence__grid">
        <h2 class="fol-independence__title" id="fol-independence-heading" data-reveal><?php esc_html_e( 'Independent by design.', 'summit' ); ?></h2>
        <div class="fol-independence__text" data-reveal data-reveal-delay="1">
            <p><?php esc_html_e( 'The Future of Luxury is editorially independent. Coverage cannot be purchased.', 'summit' ); ?></p>
            <p><?php esc_html_e( 'Summit’s client editorial programmes are commissioned separately and do not determine what appears here.', 'summit' ); ?></p>
        </div>
    </div>
</section>
