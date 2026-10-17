<?php
/**
 * Part: What's the Brief? (homepage)
 * Location: parts/blocks/capability-triptych.php
 *
 * Three ways into the work, in the Editorial Master's order (S01-P003):
 * By Need, By Function, By Industry — each with its line and its route to
 * the lens on the Solutions page. Set as a page of contents rather than a
 * feature selector: By Need, the usual way in, leads; Function and
 * Industry follow as the two secondary routes. Copy from
 * summit_home_panel().
 *
 * Until the field is set, the lenses come from the navigation config.
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

$panel = summit_home_panel( 'S01-P003' );

$routes = [];
foreach ( $panel['rows'] ?? [] as $row ) {
    $routes[] = [
        'title' => $row['title'],
        'desc'  => $row['line'],
        'url'   => summit_meta_url( $row['url'] ?? '/solutions' ),
        'link'  => $row['cta'] ?? $row['title'],
    ];
}
if ( ! $routes ) {
    $solutions = summit_nav( 'primary' )['solutions'] ?? [];
    foreach ( array_values( $solutions['groups'] ?? [] ) as $group ) {
        $routes[] = [
            'title' => $group['label'],
            'desc'  => '',
            'url'   => summit_nav_url( $solutions ) . '#' . sanitize_title( $group['label'] ),
            'link'  => $group['label'],
        ];
    }
}
if ( ! $routes ) {
    return;
}
$lead = array_shift( $routes );
?>

<section class="home-brief section--tinted" aria-labelledby="capabilities-heading">
    <div class="container container--wide home-brief__inner">

        <header class="home-brief__header">
            <?php if ( ! empty( $panel['eyebrow'] ) ) : ?>
            <p class="home-eyebrow" data-reveal="fade"><?php echo esc_html( $panel['eyebrow'] ); ?></p>
            <?php endif; ?>
            <h2 class="home-brief__headline" id="capabilities-heading" data-reveal><?php echo esc_html( $panel['headline'] ?? __( 'Solutions', 'summit' ) ); ?></h2>
            <?php foreach ( $panel['paras'] ?? [] as $para ) : ?>
            <p class="home-brief__intro" data-reveal data-reveal-delay="1"><?php echo esc_html( $para ); ?></p>
            <?php endforeach; ?>
        </header>

        <div class="home-brief__routes">
            <a class="home-brief__lead" href="<?php echo esc_url( $lead['url'] ); ?>" data-reveal data-reveal-delay="1">
                <h3 class="home-brief__title"><?php echo esc_html( $lead['title'] ); ?></h3>
                <?php if ( $lead['desc'] ) : ?>
                <p class="home-brief__line"><?php echo esc_html( $lead['desc'] ); ?></p>
                <?php endif; ?>
                <span class="home-brief__link"><?php echo esc_html( $lead['link'] ); ?></span>
            </a>

            <?php if ( $routes ) : ?>
            <ul class="home-brief__others" role="list">
                <?php foreach ( $routes as $i => $route ) : ?>
                <li data-reveal data-reveal-delay="<?php echo (int) min( 3, $i + 2 ); ?>">
                    <a class="home-brief__route" href="<?php echo esc_url( $route['url'] ); ?>">
                        <h3 class="home-brief__title"><?php echo esc_html( $route['title'] ); ?></h3>
                        <?php if ( $route['desc'] ) : ?>
                        <p class="home-brief__line"><?php echo esc_html( $route['desc'] ); ?></p>
                        <?php endif; ?>
                        <span class="home-brief__link"><?php echo esc_html( $route['link'] ); ?></span>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
            <?php endif; ?>
        </div>

    </div>
</section>
