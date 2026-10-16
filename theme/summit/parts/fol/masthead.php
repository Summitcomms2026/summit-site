<?php
/**
 * Part: The Future of Luxury — masthead
 * Location: parts/fol/masthead.php
 * Used by: archive-article.php, taxonomy-article_category.php
 *
 * The publication's nameplate: a rail of quiet labels on a hairline, the
 * title at display scale, then the standfirst with its actions.
 *
 * $args:
 *   rail        array of short labels for the top rail
 *   title       the H1
 *   standfirst  one sentence
 *   actions     action $args for parts/global/action-group.php
 *   size        'cover' (the publication) | 'section' (a subject)
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

$a = wp_parse_args( $args ?? [], [ 'rail' => [], 'title' => '', 'standfirst' => '', 'actions' => [], 'size' => 'cover' ] );
?>
<header class="fol-masthead fol-masthead--<?php echo esc_attr( 'section' === $a['size'] ? 'section' : 'cover' ); ?>">
    <div class="fol-wrap">
        <?php if ( $a['rail'] ) : ?>
        <p class="fol-masthead__rail" data-reveal="fade">
            <?php foreach ( $a['rail'] as $item ) : ?>
            <span><?php echo wp_kses( $item, [ 'a' => [ 'href' => [] ] ] ); ?></span>
            <?php endforeach; ?>
        </p>
        <?php endif; ?>

        <h1 class="fol-masthead__title" data-reveal><?php echo esc_html( $a['title'] ); ?></h1>

        <div class="fol-masthead__foot">
            <?php if ( $a['standfirst'] ) : ?>
            <p class="fol-masthead__standfirst" data-reveal data-reveal-delay="1"><?php echo esc_html( $a['standfirst'] ); ?></p>
            <?php endif; ?>
            <?php if ( $a['actions'] ) : ?>
            <div class="fol-masthead__actions" data-reveal="fade" data-reveal-delay="2">
                <?php get_template_part( 'parts/global/action-group', null, [
                    'label'   => __( 'Section actions', 'summit' ),
                    'actions' => $a['actions'],
                ] ); ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</header>
