<?php
/**
 * Part: Page Hero (internal page shell)
 * Location: parts/global/page-hero.php
 * Used by: page-shell.php
 *
 * The opening grammar of a serious interior page:
 *
 *   eyebrow (section · group) ─────────────────── hairline
 *
 *   Title
 *
 *   ───────────────────────────────────────────── hairline
 *   standfirst                       CTA · utilities
 *
 * $args:
 *   eyebrow     array of strings — section, then optional sub-section
 *   title       H1 text
 *   standfirst  short standfirst (optional)
 *   intro       further opening paragraphs, blank-line separated (optional;
 *               defaults to the page's summit_intro field)
 *   tone        'paper' | 'dark' | 'tinted'
 *   actions     action $args for parts/global/action-group.php (optional)
 *   level       'parent' | 'child' (optional; worked out from the page)
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

$eyebrow    = array_filter( (array) ( $args['eyebrow'] ?? [] ) );
$title      = $args['title'] ?? '';
$standfirst = $args['standfirst'] ?? '';
$intro      = $args['intro'] ?? ( is_page() ? (string) get_post_meta( get_queried_object_id(), 'summit_intro', true ) : '' );
$intro      = array_filter( array_map( 'trim', preg_split( '/\n\s*\n/', $intro ) ) );
$tone       = in_array( $args['tone'] ?? '', [ 'dark', 'tinted' ], true ) ? $args['tone'] : 'paper';
$actions    = $args['actions'] ?? [];

$tone_class = [
    'paper'  => '',
    'dark'   => ' section--dark',
    'tinted' => ' section--tinted',
][ $tone ];

$has_foot = $standfirst || $actions;

// Parent pages open a territory; child pages are editorial measures (The
// Quiet System 12/13). A section's own page is a parent; its pages and the
// standalone pages are children. Long headings step down rather than
// filling four lines.
$here  = '/' . trim( (string) wp_parse_url( (string) get_permalink( get_queried_object_id() ), PHP_URL_PATH ), '/' );
$level = $args['level'] ?? ( in_array( $here, array_column( summit_nav( 'primary' ) ?: [], 'path' ), true ) ? 'parent' : 'child' );
$scale = ' shell-hero--' . ( 'parent' === $level ? 'parent' : 'child' ) . ( mb_strlen( (string) $title ) > 30 ? ' shell-hero--long' : '' );
?>
<header class="shell-hero shell-hero--<?php echo esc_attr( $tone ); ?><?php echo esc_attr( $tone_class . $scale ); ?><?php echo $has_foot ? '' : ' shell-hero--title-only'; ?>">
    <div class="container container--medium">

        <?php if ( $eyebrow ) : ?>
        <p class="shell-hero__eyebrow" data-reveal="fade">
            <?php foreach ( array_values( $eyebrow ) as $i => $part ) : ?>
                <?php if ( $i ) : ?><span class="shell-hero__eyebrow-sep" aria-hidden="true">/</span><?php endif; ?>
                <span><?php echo esc_html( $part ); ?></span>
            <?php endforeach; ?>
        </p>
        <?php endif; ?>

        <h1 class="shell-hero__title" data-reveal><?php echo esc_html( $title ); ?></h1>

        <?php if ( $has_foot ) : ?>
        <div class="shell-hero__foot" data-reveal data-reveal-delay="1">
            <?php if ( $standfirst && $intro ) : ?>
            <div class="shell-hero__opening">
                <p class="shell-hero__standfirst"><?php echo esc_html( $standfirst ); ?></p>
                <div class="shell-hero__intro">
                    <?php foreach ( $intro as $para ) : ?>
                    <p><?php echo esc_html( $para ); ?></p>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php elseif ( $standfirst ) : ?>
            <p class="shell-hero__standfirst"><?php echo esc_html( $standfirst ); ?></p>
            <?php endif; ?>

            <?php if ( $actions ) : ?>
            <div class="shell-hero__actions">
                <?php get_template_part( 'parts/global/action-group', null, [
                    'actions' => $actions,
                    'label'   => __( 'Page actions', 'summit' ),
                ] ); ?>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

    </div>
</header>
<?php echo summit_hero_visual(); // phpcs:ignore WordPress.Security.EscapeOutput -- built from escaped parts (inc/editorial.php) ?>
