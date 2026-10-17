<?php
/**
 * The Future of Luxury — long-form rhythm
 * Location: inc/fol-rhythm.php
 *
 * A long essay is calmer than the archive, but it still needs tempo. This
 * file gives the reading column a few restrained, editorially chosen
 * interruptions, all made from the essay's existing content (no new
 * fields, no rewritten articles; the stored post content is never
 * altered — every change is applied as the essay is shown):
 *
 *   Pull quote   one of the essay's own quotations, chosen per essay
 *                (summit_fol_pull_quotes()), set as the essay's single
 *                pull quote. Never invented, never more than one.
 *   Interlude    in a long essay (2,400 words or more), one small fragment
 *                of its opener's plate — the family's construction and
 *                the folio, in the plate's colour family — placed before
 *                the section nearest the middle of the argument, clear of
 *                the pull quote.
 *   Echo         the plate's colour family (or its subject trace) as the
 *                only colour in the reading column: the pull quote's rule
 *                and the interlude. Body text stays ink on paper.
 *   Recovery     where a lost image was essential evidence and Summit
 *                holds the original, the essay shows the original
 *                (summit_fol_recovered_images()).
 *   Outline      an essay with H3 sections and no H2 is shown one level up
 *   Absence      the note acknowledging lost photographs is shown only
 *                where they were the substance of the essay
 *                (summit_fol_absence_noted()); elsewhere they are omitted
 *                silently (assets/js/fol.js).
 *
 * Triage of the lost images: scratch/art-direction-pass-06/essay-image-triage.md
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

/**
 * Pull quotes: essay slug => the position (1-based) of the blockquote to
 * set as the essay's one pull quote. Art direction, not a rule.
 */
function summit_fol_pull_quotes(): array {
    return [
        'the-creative-director-delusion'                => 2, // "the sharper variable is trust" — Summit's own line
        'can-sothebys-bet-big-on-debt-and-survive'      => 1, // leverage "works well until it doesn't" — the thesis
        'watches-and-wonders-time-desire-and-horology'  => 3, // "Just being old and rare isn't enough anymore."
        'gen-z-wont-buy-your-myth'                      => 2, // trust reallocated to peers — the evidence
    ];
}

/**
 * Essential evidence Summit holds the originals of: essay slug =>
 * [ image position (1-based) => theme asset ].
 */
function summit_fol_recovered_images(): array {
    $characteristics = [];
    for ( $n = 1; $n <= 14; $n++ ) {
        $characteristics[ $n ] = sprintf( 'fol/characteristics/%02d.jpg', $n );
    }
    return [
        'the-characteristics-of-luxury-a-visual-conversation' => $characteristics,
    ];
}

/**
 * Essays whose lost photographs were the substance of the piece — the
 * objects reviewed, the works discussed — and so are acknowledged.
 */
function summit_fol_absence_noted(): array {
    return [
        'watches-and-wonders-time-desire-and-horology',
        'dslcollection-chinese-contemporary-art-enters-digital-era',
    ];
}

/**
 * The essay's echo colour family: its plate family, or its subject trace.
 */
function summit_fol_echo( int $post_id ): array {
    $family = function_exists( 'summit_editorial_art_family' ) ? summit_editorial_art_family( $post_id ) : 'culture';
    $art    = function_exists( 'summit_editorial_art_key' ) ? summit_editorial_art_key( $post_id ) : [];
    $trace  = isset( $art[2] ) ? (string) sanitize_hex_color( $art[2] ) : '';
    return [ 'family' => $family, 'trace' => $trace ];
}

add_filter( 'body_class', static function ( array $classes ): array {
    if ( is_singular( 'article' ) ) {
        $echo      = summit_fol_echo( get_queried_object_id() );
        $classes[] = 'fol-echo--' . sanitize_html_class( $echo['family'] );
    }
    return $classes;
} );

add_filter( 'the_content', static function ( string $html ): string {
    if ( ! is_singular( 'article' ) || ! in_the_loop() || ! is_main_query() ) {
        return $html;
    }
    $post_id = get_the_ID();
    $slug    = (string) get_post_field( 'post_name', $post_id );

    // Outline: an essay whose top-level sections were set as H3 with no H2
    // (the title is the page's H1) is shown one level up, so the outline has
    // no gap. The wording and the stored content are unchanged.
    if ( false === stripos( $html, '<h2' ) && false !== stripos( $html, '<h3' ) ) {
        $html = preg_replace( [ '#<(/?)h4([\s>])#i', '#<(/?)h3([\s>])#i' ], [ '<$1h3$2', '<$1h2$2' ], $html );
    }

    // Recovery: the original of an essential image, in its own frame.
    $recovered = summit_fol_recovered_images()[ $slug ] ?? [];
    $pull      = summit_fol_pull_quotes()[ $slug ] ?? 0;
    if ( ( $recovered || $pull ) && class_exists( 'WP_HTML_Tag_Processor' ) ) {
        $p = new WP_HTML_Tag_Processor( $html );
        $img = 0;
        $bq  = 0;
        while ( $p->next_tag() ) {
            $tag = $p->get_tag();
            if ( 'IMG' === $tag ) {
                $img++;
                if ( isset( $recovered[ $img ] ) ) {
                    $p->set_attribute( 'src', get_template_directory_uri() . '/assets/img/' . $recovered[ $img ] );
                    $p->remove_attribute( 'srcset' );
                    $p->remove_attribute( 'sizes' );
                    $p->set_attribute( 'width', '1600' );
                    $p->set_attribute( 'height', '765' );
                }
            } elseif ( 'BLOCKQUOTE' === $tag ) {
                $bq++;
                if ( $bq === $pull ) {
                    $p->add_class( 'fol-pull' );
                }
            }
        }
        $html = $p->get_updated_html();
    }

    // Interlude: one fragment of the plate, mid-argument, in a long essay.
    $words = str_word_count( wp_strip_all_tags( $html ) );
    if ( $words >= 2400 && preg_match_all( '#<h2[\s>]#i', $html, $m, PREG_OFFSET_CAPTURE ) ) {
        $pull_at = false;
        if ( preg_match( '#<blockquote[^>]*class="[^"]*fol-pull#', $html, $q, PREG_OFFSET_CAPTURE ) ) {
            $pull_at = str_word_count( wp_strip_all_tags( substr( $html, 0, $q[0][1] ) ) );
        }
        $best = null;
        $gap  = PHP_INT_MAX;
        foreach ( $m[0] as [ , $offset ] ) {
            $at = str_word_count( wp_strip_all_tags( substr( $html, 0, $offset ) ) );
            if ( $at < $words * 0.3 || $at > $words * 0.7 ) {
                continue;
            }
            if ( false !== $pull_at && abs( $at - $pull_at ) < 450 ) {
                continue;
            }
            $d = abs( $at - $words / 2 );
            if ( $d < $gap ) {
                $gap  = $d;
                $best = $offset;
            }
        }
        if ( null !== $best && function_exists( 'summit_editorial_art_geometry' ) ) {
            $echo   = summit_fol_echo( $post_id );
            $number = function_exists( 'summit_editorial_art_number' ) ? summit_editorial_art_number( $post_id ) : 0;
            $mark   = '<div class="fol-interlude fol-interlude--' . esc_attr( $echo['family'] ) . '"'
                . ( $echo['trace'] ? ' style="--fol-echo: ' . esc_attr( $echo['trace'] ) . ';"' : '' )
                . ' aria-hidden="true">'
                . '<svg class="fol-interlude__geo" viewBox="0 0 100 100" focusable="false">' . summit_editorial_art_geometry( $echo['family'] ) . '</svg>'
                . ( $number ? '<span class="fol-interlude__folio">' . esc_html( sprintf( 'No. %02d', $number ) ) . '</span>' : '' )
                . '</div>';
            $html = substr( $html, 0, $best ) . $mark . substr( $html, $best );
        }
    }

    return $html;
}, 20 );
