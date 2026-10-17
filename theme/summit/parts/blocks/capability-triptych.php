<?php
/**
 * Part: Capability Triptych — What's the Brief?
 * Location: parts/blocks/capability-triptych.php
 *
 * Three ways into the work, in the Editorial Master's order (S01-P003):
 * By Need, By Function, By Industry — each with its line and its route to
 * the lens on the Solutions page. Copy from summit_home_panel(); the
 * triptych's composition and abstract marks are unchanged.
 *
 * Until the field is set, the lenses come from the navigation config.
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

$img_dir = get_template_directory_uri() . '/assets/img/';
$panel   = summit_home_panel( 'S01-P003' );
$marks   = [ 'brand-strategy', 'experience-design', 'digital-transformation' ];

$pillars = [];
foreach ( $panel['rows'] ?? [] as $i => $row ) {
    $pillars[] = [
        'title' => $row['title'],
        'desc'  => $row['line'],
        'url'   => summit_meta_url( $row['url'] ?? '/solutions' ),
        'link'  => $row['cta'] ?? $row['title'],
    ];
}
if ( ! $pillars ) {
    $solutions = summit_nav( 'primary' )['solutions'] ?? [];
    foreach ( array_values( $solutions['groups'] ?? [] ) as $group ) {
        $pillars[] = [
            'title' => $group['label'],
            'desc'  => '',
            'url'   => summit_nav_url( $solutions ) . '#' . sanitize_title( $group['label'] ),
            'link'  => $group['label'],
        ];
    }
}
?>

<section class="capability-triptych section--tinted section--padded" aria-labelledby="capabilities-heading">
    <div class="container container--wide">

        <header class="capability-triptych__header">
            <div class="capability-triptych__header-text">
                <?php if ( ! empty( $panel['eyebrow'] ) ) : ?>
                <p class="home-eyebrow" data-reveal="fade"><?php echo esc_html( $panel['eyebrow'] ); ?></p>
                <?php endif; ?>
                <h2 class="capability-triptych__headline" id="capabilities-heading" data-reveal><?php echo esc_html( $panel['headline'] ?? __( 'Solutions', 'summit' ) ); ?></h2>
                <?php foreach ( $panel['paras'] ?? [] as $para ) : ?>
                <p class="home-intro" data-reveal data-reveal-delay="1"><?php echo esc_html( $para ); ?></p>
                <?php endforeach; ?>
            </div>
        </header>

        <div class="capability-triptych__grid">

            <?php foreach ( $pillars as $i => $pillar ) :
                $mark = $marks[ $i ] ?? $marks[0];
            ?>
            <div class="capability-triptych__pillar" data-reveal data-reveal-delay="<?php echo (int) min( 3, $i + 1 ); ?>">
                <figure class="capability-triptych__thumb" aria-hidden="true">
                    <img class="capability-triptych__icon--off"
                         src="<?php echo esc_url( $img_dir . 'icon_' . $mark . '.svg' ); ?>"
                         alt="" width="100" height="100" loading="lazy">
                    <img class="capability-triptych__icon--on"
                         src="<?php echo esc_url( $img_dir . 'icon_' . $mark . '-on.svg' ); ?>"
                         alt="" width="100" height="100" loading="lazy">
                </figure>
                <span class="capability-triptych__number" aria-hidden="true">[ <?php echo (int) $i + 1; ?> ]</span>
                <h3 class="capability-triptych__title">
                    <?php echo esc_html( $pillar['title'] ); ?>
                </h3>
                <?php if ( $pillar['desc'] ) : ?>
                <div class="capability-triptych__desc">
                    <p><?php echo esc_html( $pillar['desc'] ); ?></p>
                </div>
                <?php endif; ?>
                <a href="<?php echo esc_url( $pillar['url'] ); ?>" class="capability-triptych__link">
                    <?php echo esc_html( $pillar['link'] ); ?> <span aria-hidden="true">&rarr;</span>
                </a>
            </div>
            <?php endforeach; ?>

        </div>

    </div>
</section>
