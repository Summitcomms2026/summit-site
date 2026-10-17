<?php
/**
 * Part: Ways to Begin (homepage)
 * Location: parts/blocks/home-programmes.php
 * Used by: front-page.php
 *
 * The six Programmes in the Editorial Master's words (S01-P005,
 * summit_home_panel()): Maison Formation, the principal programme, stands
 * as the feature; the other five follow as a close register, each with
 * its line. The header's link leads to all Programmes.
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

$panel = summit_home_panel( 'S01-P005' );
if ( empty( $panel['rows'] ) ) {
    return;
}
$cta = $panel['ctas'][0] ?? null;
?>
<section class="home-programmes section--padded" aria-labelledby="home-programmes-heading">
    <div class="container container--wide">

        <header class="home-programmes__header">
            <div>
                <?php if ( ! empty( $panel['eyebrow'] ) ) : ?>
                <p class="home-eyebrow" data-reveal="fade"><?php echo esc_html( $panel['eyebrow'] ); ?></p>
                <?php endif; ?>
                <h2 class="home-programmes__headline" id="home-programmes-heading" data-reveal><?php echo esc_html( $panel['headline'] ); ?></h2>
                <?php foreach ( $panel['paras'] ?? [] as $para ) : ?>
                <p class="home-intro" data-reveal data-reveal-delay="1"><?php echo esc_html( $para ); ?></p>
                <?php endforeach; ?>
            </div>
            <?php if ( $cta ) : ?>
            <a href="<?php echo esc_url( summit_meta_url( $cta['url'] ) ); ?>" class="btn btn--secondary home-obs__btn" data-reveal data-reveal-delay="1"><?php echo esc_html( $cta['label'] ); ?></a>
            <?php endif; ?>
        </header>

        <?php
        $feature = $panel['rows'][0];
        $others  = array_slice( $panel['rows'], 1 );
        ?>
        <div class="home-programmes__body">
            <a class="home-programmes__feature" href="<?php echo esc_url( summit_meta_url( $feature['url'] ?? '/programmes' ) ); ?>" data-reveal data-reveal-delay="1">
                <?php echo summit_panel_visual( 'S01-P005', 'home-programmes__visual' ); // phpcs:ignore WordPress.Security.EscapeOutput -- one overarching photograph, if any ?>
                <span class="home-programmes__feature-name"><?php echo esc_html( $feature['title'] ); ?></span>
                <span class="home-programmes__feature-line"><?php echo esc_html( $feature['line'] ); ?></span>
            </a>

            <ul class="home-programmes__list" role="list">
                <?php foreach ( $others as $i => $row ) : ?>
                <li class="home-programmes__item" data-reveal data-reveal-delay="<?php echo (int) min( 6, $i + 2 ); ?>">
                    <a href="<?php echo esc_url( summit_meta_url( $row['url'] ?? '/programmes' ) ); ?>">
                        <span class="home-programmes__name"><?php echo esc_html( $row['title'] ); ?></span>
                        <span class="home-programmes__line"><?php echo esc_html( $row['line'] ); ?></span>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>

    </div>
</section>
