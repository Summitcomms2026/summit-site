<?php
/**
 * Part: Capability Triptych
 * Location: parts/blocks/capability-triptych.php
 *
 * Three ways into Summit: the Solutions lenses — By Industry, By Function,
 * By Need — each linking to its lens on the Solutions page, with a quiet
 * line to Programmes beneath. Labels and destinations come from the
 * navigation config; the triptych's composition and marks are unchanged.
 *
 * (The earlier home_cap_* fields — Brand Strategy, Experience Design,
 * Digital Transformation — are no longer read.)
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

$img_dir = get_template_directory_uri() . '/assets/img/';

// Three ways into Summit: the Solutions lenses, in the navigation's order.
// The abstract marks are the panel's own; the lines are the lenses' own
// grammar (parts/solutions/ways-in.php).
$solutions = summit_nav( 'primary' )['solutions'] ?? [];
$lines = [
    'By Industry' => __( 'Industry describes the context — from watches and jewellery to private banking, each world read on its own terms.', 'summit' ),
    'By Function' => __( 'Function describes the discipline — strategy, brand, editorial, experience, commerce, product and patrimony.', 'summit' ),
    'By Need'     => __( 'Need describes the question — build a maison, reposition one, strengthen an icon, read one before a transaction.', 'summit' ),
];
$marks = [ 'brand-strategy', 'experience-design', 'digital-transformation' ];

$pillars = [];
foreach ( array_values( $solutions['groups'] ?? [] ) as $i => $group ) {
    $pillars[] = [
        'title'   => $group['label'],
        'desc'    => $lines[ $group['label'] ] ?? '',
        'url'     => summit_nav_url( $solutions ) . '#' . sanitize_title( $group['label'] ),
        'icon'    => $img_dir . 'icon_' . ( $marks[ $i ] ?? $marks[0] ) . '.svg',
        'icon_on' => $img_dir . 'icon_' . ( $marks[ $i ] ?? $marks[0] ) . '-on.svg',
        'count'   => count( $group['children'] ?? [] ),
    ];
}
$programmes = summit_nav( 'primary' )['programmes'] ?? null;
?>

<section class="capability-triptych section--tinted section--padded" aria-labelledby="capabilities-heading">
    <div class="container container--wide">

        <header class="capability-triptych__header">
            <h2 class="capability-triptych__headline" id="capabilities-heading" data-reveal>Solutions</h2>
            <a href="<?php echo esc_url( home_url( '/solutions' ) ); ?>"
               class="btn btn--secondary" data-reveal data-reveal-delay="1">
                Explore Solutions
            </a>
        </header>

        <div class="capability-triptych__grid">

            <?php
            $pillar_delays = [ 1, 2, 3 ];
            foreach ( $pillars as $i => $pillar ) :
                $delay = $pillar_delays[ $i ] ?? 1;
            ?>
            <div class="capability-triptych__pillar" data-reveal data-reveal-delay="<?php echo $delay; ?>">
                <figure class="capability-triptych__thumb" aria-hidden="true">
                    <img class="capability-triptych__icon--off"
                         src="<?php echo esc_url( $pillar['icon'] ); ?>"
                         alt="" width="100" height="100" loading="lazy">
                    <img class="capability-triptych__icon--on"
                         src="<?php echo esc_url( $pillar['icon_on'] ); ?>"
                         alt="" width="100" height="100" loading="lazy">
                </figure>
                <span class="capability-triptych__number" aria-hidden="true">[ <?php echo $i + 1; ?> ]</span>
                <h3 class="capability-triptych__title">
                    <?php echo esc_html( $pillar['title'] ); ?>
                </h3>
                <div class="capability-triptych__desc">
                    <?php echo wp_kses_post( wpautop( $pillar['desc'] ) ); ?>
                </div>
                <a href="<?php echo esc_url( $pillar['url'] ); ?>" class="capability-triptych__link">
                    <?php
                    /* translators: %d: number of entries in the lens */
                    echo esc_html( sprintf( __( '%d ways in', 'summit' ), $pillar['count'] ) );
                    ?> <span aria-hidden="true">&rarr;</span>
                </a>
            </div>
            <?php endforeach; ?>

        </div>

        <?php if ( $programmes ) : ?>
        <p class="capability-triptych__also" data-reveal="fade">
            <?php esc_html_e( 'Questions that recur have a defined form.', 'summit' ); ?>
            <a href="<?php echo esc_url( summit_nav_url( $programmes ) ); ?>"><?php echo esc_html( $programmes['label'] ); ?> <span aria-hidden="true">&rarr;</span></a>
        </p>
        <?php endif; ?>

    </div>
</section>
