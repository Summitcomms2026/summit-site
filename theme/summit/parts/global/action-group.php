<?php
/**
 * Part: Action Group
 * Location: parts/global/action-group.php
 *
 * A row of actions (see parts/global/action.php). Contextual CTAs and
 * utility actions may share a group; utilities are set apart from CTAs
 * by space alone.
 *
 * Usage:
 *   get_template_part( 'parts/global/action-group', null, [
 *       'label'   => 'Page actions',
 *       'actions' => [
 *           [ 'type' => 'cta', 'label' => 'Request a Private Briefing', 'url' => … ],
 *           [ 'mode' => 'share' ],
 *       ],
 *   ] );
 *
 * Share: where the Web Share API exists, one "Share" opens the native
 * sheet. Elsewhere "Share" discloses a quiet fallback set — Copy Link,
 * Email, LinkedIn — beside it. Without JavaScript the fallback links
 * (Email, LinkedIn) are simply shown.
 *
 * $args:
 *   actions  array of action $args
 *   label    accessible name for the group (optional)
 *   class    extra CSS class(es)
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

$actions = $args['actions'] ?? [];
if ( ! $actions ) {
    return;
}

$group_label = $args['label'] ?? '';
$group_class = trim( 'action-group ' . ( $args['class'] ?? '' ) );
?>
<div class="<?php echo esc_attr( $group_class ); ?>"<?php if ( $group_label ) : ?> role="group" aria-label="<?php echo esc_attr( $group_label ); ?>"<?php endif; ?>>
    <?php foreach ( $actions as $action ) :
        $action = wp_parse_args( $action, [ 'type' => 'utility', 'mode' => 'link' ] );

        if ( 'share' === $action['mode'] ) :
            $fallback_id = wp_unique_id( 'action-share-fallback-' );
    ?>
        <span class="action-share" data-action-share>
            <?php
            get_template_part( 'parts/global/action', null, array_merge( $action, [
                'aria_label' => $action['aria_label'] ?? __( 'Share this page', 'summit' ),
            ] ) );
            ?>
            <span class="action-share__fallback" id="<?php echo esc_attr( $fallback_id ); ?>">
                <?php
                get_template_part( 'parts/global/action', null, [ 'mode' => 'copy' ] );
                get_template_part( 'parts/global/action', null, [ 'mode' => 'email' ] );
                get_template_part( 'parts/global/action', null, [ 'mode' => 'linkedin' ] );
                ?>
            </span>
        </span>
    <?php else :
            get_template_part( 'parts/global/action', null, $action );
        endif;
    endforeach; ?>
</div>
