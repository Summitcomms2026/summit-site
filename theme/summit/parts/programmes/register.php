<?php
/**
 * Part: Programmes — the numbered register
 * Location: parts/programmes/register.php
 * Used by: page-programmes.php via [summit_programmes_register]
 *
 * The six programmes as a numbered register: number, name, one-sentence
 * proposition, the moment it answers, and the Solutions it draws on. On
 * desktop (assets/js/programmes.js) a held field beside the register shows
 * the moment of the programme in hover or focus; without JavaScript, and on
 * smaller screens, the moment reads inline in each entry.
 *
 * Names, order and destinations come from the navigation config; each
 * programme's proposition, moment and related Solutions are its own page's
 * fields (summit_summary, summit_moment, summit_related).
 *
 * $args:
 *   section   the Programmes section from summit_nav( 'primary' )
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

$section    = $args['section'] ?? [];
$programmes = array_values( $section['children'] ?? [] );

if ( ! $programmes ) {
    return;
}

// Solutions labels by path, so a programme can name what it draws on.
$solution_labels = [];
foreach ( summit_nav( 'primary' )['solutions']['groups'] ?? [] as $group ) {
    foreach ( $group['children'] as $child ) {
        $solution_labels[ $child['path'] ] = $child['label'];
    }
}

$entries = array_map( static function ( array $item ) use ( $solution_labels ): array {
    $page    = get_page_by_path( trim( $item['path'], '/' ), OBJECT, 'page' );
    $field   = static fn( string $key ): string => $page ? (string) get_post_meta( $page->ID, $key, true ) : '';
    $related = array_filter( array_map( 'trim', explode( ',', $field( 'summit_related' ) ) ), static fn( $p ) => isset( $solution_labels[ $p ] ) );
    return [
        'label'   => $item['label'],
        'url'     => summit_nav_url( $item ),
        'summary' => $field( 'summit_summary' ),
        'moment'  => $field( 'summit_moment' ),
        'related' => array_map( static fn( $p ) => [ 'label' => $solution_labels[ $p ], 'url' => home_url( $p ) ], array_values( $related ) ),
    ];
}, $programmes );
?>
<section class="prg-register" data-prg-register aria-labelledby="prg-register-heading">
    <h2 class="prg-register__heading" id="prg-register-heading"><?php esc_html_e( 'Six programmes', 'summit' ); ?></h2>

    <div class="prg-register__body">
        <ol class="prg-register__list" role="list">
            <?php foreach ( $entries as $i => $entry ) : ?>
            <li class="prg-entry" data-prg-entry>
                <span class="prg-entry__num" aria-hidden="true"><?php echo esc_html( sprintf( '%02d', $i + 1 ) ); ?></span>
                <h3 class="prg-entry__name">
                    <a href="<?php echo esc_url( $entry['url'] ); ?>"><?php echo esc_html( $entry['label'] ); ?><span class="prg-entry__arrow" aria-hidden="true">&rarr;</span></a>
                </h3>
                <?php if ( $entry['summary'] ) : ?>
                <p class="prg-entry__summary"><?php echo esc_html( $entry['summary'] ); ?></p>
                <?php endif; ?>
                <?php if ( $entry['moment'] ) : ?>
                <p class="prg-entry__moment"><?php echo esc_html( $entry['moment'] ); ?></p>
                <?php endif; ?>
                <?php if ( $entry['related'] ) : ?>
                <p class="prg-entry__draws">
                    <span class="prg-entry__draws-label"><?php esc_html_e( 'Draws on', 'summit' ); ?></span>
                    <?php foreach ( $entry['related'] as $r => $rel ) : ?><?php echo $r ? '<span aria-hidden="true"> · </span>' : ''; ?><a href="<?php echo esc_url( $rel['url'] ); ?>"><?php echo esc_html( $rel['label'] ); ?></a><?php endforeach; ?>
                </p>
                <?php endif; ?>
            </li>
            <?php endforeach; ?>
        </ol>

        <div class="prg-held" aria-hidden="true">
            <?php foreach ( $entries as $i => $entry ) : ?>
            <div class="prg-held__item" data-prg-held>
                <span class="prg-held__label"><?php echo esc_html( sprintf( /* translators: %s: programme number */ __( 'Programme %s', 'summit' ), sprintf( '%02d', $i + 1 ) ) ); ?></span>
                <span class="prg-held__name"><?php echo esc_html( $entry['label'] ); ?></span>
                <p class="prg-held__moment"><?php echo esc_html( $entry['moment'] ?: $entry['summary'] ); ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
