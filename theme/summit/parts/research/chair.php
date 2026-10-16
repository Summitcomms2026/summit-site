<?php
/**
 * Part: Research — the hundred places and the empty chair
 * Location: parts/research/chair.php
 * Used by: page-research.php via [summit_research_chair]
 *
 * The Luxury 100's own device, drawn from its 2027 cover: one hundred
 * places in a ten-by-ten field, ninety-nine held, one left empty. Read in
 * rank order, left to right, top to bottom. A text equivalent states the
 * meaning; the squares themselves are decorative.
 *
 * $args:
 *   empty    the rank left empty (default 83)
 *   places   the number of places (default 100)
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

$empty  = max( 1, (int) ( $args['empty'] ?? 83 ) );
$places = max( $empty, (int) ( $args['places'] ?? 100 ) );
?>
<figure class="res-chair">
    <div class="res-chair__field" aria-hidden="true">
        <?php for ( $i = 1; $i <= $places; $i++ ) : ?>
        <span class="res-chair__place<?php echo $i === $empty ? ' is-empty' : ''; ?>"><?php if ( $i === $empty ) : ?><span class="res-chair__rank"><?php echo esc_html( (string) $i ); ?></span><?php endif; ?></span>
        <?php endfor; ?>
    </div>
    <figcaption class="res-chair__caption">
        <?php
        /* translators: 1: number of places, 2: number held, 3: empty rank */
        echo esc_html( sprintf( __( '%1$d places. %2$d held. Rank %3$d left empty.', 'summit' ), $places, $places - 1, $empty ) );
        ?>
    </figcaption>
</figure>
