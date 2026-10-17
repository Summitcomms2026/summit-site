<?php
/**
 * Editorial handover — shared site behaviour
 * Location: inc/editorial.php
 *
 * What the Editorial Handover v1 content needs from the theme, whatever
 * family a page belongs to (the content itself is in site-content/, built by
 * tools/editorial-build.py):
 *
 *   Contact     summit_contact_channels() — the approved direct routes
 *   Links       summit_meta_url() — a page field's URL: in-page anchors and
 *               mailto:/tel: kept as they are, site paths made absolute
 *   Panels      each section built from a handover panel keeps its stable ID
 *               invisibly: id="s02-p006" in the content, and
 *               data-summit-panel="S02-P006" added as the page is shown
 *   Assets      assets/css/editorial.css and assets/js/editorial.js (FAQs,
 *               guides, the Demand Quality Check, the ed- chapters, the
 *               Let's Talk context)
 *   Check       [summit_demand_quality_check] — the Demand Quality Check
 *               (Pass 03, tables 15–16): five single-choice questions, a
 *               reading drawn from the answers, never a score
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

/**
 * Summit's approved direct routes (handover channel configuration). SMS,
 * WhatsApp and scheduling stay out until those services are confirmed.
 */
function summit_contact_channels(): array {
    return [
        [ 'label' => __( 'Australia', 'summit' ),      'display' => '+61 2 9127 9005',  'href' => 'tel:+61291279005' ],
        [ 'label' => __( 'United Kingdom', 'summit' ), 'display' => '+44 20 7011 8509', 'href' => 'tel:+442070118509' ],
        [ 'label' => __( 'Email', 'summit' ),          'display' => 'hello@summitcommunication.group', 'href' => 'mailto:hello@summitcommunication.group' ],
    ];
}

/**
 * A URL from a page field: anchors, mailto: and tel: as they are; a site
 * path through home_url().
 */
function summit_meta_url( string $url ): string {
    if ( '' === $url || '#' === $url[0] || preg_match( '#^(mailto:|tel:|https?://)#', $url ) ) {
        return $url;
    }
    return home_url( $url );
}

/**
 * The hero's actions from the page's own fields (summit_cta_*,
 * summit_secondary_*), or [] when the page sets none.
 */
function summit_meta_actions( int $page_id ): array {
    $actions = [];
    $label   = (string) get_post_meta( $page_id, 'summit_cta_label', true );
    $url     = (string) get_post_meta( $page_id, 'summit_cta_url', true );
    if ( $label && $url ) {
        $actions[] = [ 'type' => 'cta', 'label' => $label, 'url' => summit_meta_url( $url ) ];
    }
    $label = (string) get_post_meta( $page_id, 'summit_secondary_label', true );
    $url   = (string) get_post_meta( $page_id, 'summit_secondary_url', true );
    if ( $label && $url ) {
        $actions[] = [ 'type' => 'utility', 'mode' => 'link', 'icon' => false, 'label' => $label, 'url' => summit_meta_url( $url ), 'class' => 'action--contextual' ];
    }
    return $actions;
}

// Each handover panel's ID, kept with its section and never shown.
add_filter( 'the_content', static function ( string $html ): string {
    if ( false === strpos( $html, 'id="s' ) || ! class_exists( 'WP_HTML_Tag_Processor' ) ) {
        return $html;
    }
    $p = new WP_HTML_Tag_Processor( $html );
    while ( $p->next_tag( [ 'tag_name' => 'SECTION' ] ) ) {
        $id = (string) $p->get_attribute( 'id' );
        if ( preg_match( '/^(s\d{2}-p\d{3})$/', $id, $m ) ) {
            $p->set_attribute( 'data-summit-panel', strtoupper( $m[1] ) );
        }
    }
    return $p->get_updated_html();
}, 12 );

add_action( 'wp_enqueue_scripts', static function (): void {
    if ( ! is_singular( [ 'page', 'article' ] ) && ! is_post_type_archive( 'article' ) ) {
        return;
    }
    $v   = wp_get_theme()->get( 'Version' );
    $uri = get_template_directory_uri();
    wp_enqueue_style( 'summit-editorial', $uri . '/assets/css/editorial.css', [ 'summit-navigation' ], $v );
    wp_enqueue_script( 'summit-editorial', $uri . '/assets/js/editorial.js', [], $v, true );
}, 20 );

/**
 * The Demand Quality Check: questions, options and readings exactly as the
 * handover gives them (Pass 03, Enhance Desire Without Dilution).
 */
function summit_dqc_spec(): array {
    return [
        'questions' => [
            [ 'key' => 'objective', 'text' => 'What is the main objective?', 'options' => [ 'Enter a new market', 'Reach better-qualified clients', 'Improve repeat relationships', 'Protect margin' ] ],
            [ 'key' => 'exposed', 'text' => 'What feels most exposed?', 'options' => [ 'Distribution control', 'Price integrity', 'Too many extensions', 'Control of the story' ] ],
            [ 'key' => 'business', 'text' => 'Where is most business done?', 'options' => [ 'Direct through the maison', 'Selective partners', 'Broad wholesale', 'Mixed or unclear' ] ],
            [ 'key' => 'story', 'text' => 'Who is telling the strongest story?', 'options' => [ 'Our house', 'Clients and collectors', 'Retailers or resale markets', 'Not sure' ] ],
            [ 'key' => 'decision', 'text' => 'Which decision comes next?', 'options' => [ 'New channel', 'New product line', 'Price change', 'Campaign or collaboration', 'None yet' ] ],
        ],
        // "What feels most exposed?" → Your main consideration
        'considerations' => [
            'Distribution control' => [ 'Distribution control', 'More reach may be useful. The question is whether you retain the client relationship and the rules under which the product is encountered.' ],
            'Price integrity'      => [ 'Price integrity', 'Pricing can reinforce a house’s position or make its proposition harder to believe. Review consistency before considering a change.' ],
            'Too many extensions'  => [ 'Product discipline', 'Each extension asks the strongest objects to lend it authority. Decide what the new line contributes before adding another name to the catalogue.' ],
            'Control of the story' => [ 'Control of the story', 'When the maison stops describing its own objects, others eventually do it for them. Begin with the evidence, archives and relationships the house can still claim.' ],
        ],
        // "Which decision comes next?" → Before you decide…
        'before' => [
            'New channel'               => 'Test who owns the relationship after the sale.',
            'New product line'          => 'Ask what the strongest existing object will have to carry.',
            'Price change'              => 'Examine pricing consistency across clients and channels.',
            'Campaign or collaboration' => 'Ask whether attention will accrue to the maison or the partner.',
            'None yet'                  => 'Start with the pressure that matters most before naming a solution.',
        ],
    ];
}

add_shortcode( 'summit_demand_quality_check', static function (): string {
    $spec    = summit_dqc_spec();
    $discuss = home_url( '/lets-talk?topic=demand-quality' );
    ob_start();
    ?>
    <div class="summit-dqc__tool" data-summit-dqc hidden>
        <form class="summit-dqc__form" novalidate>
            <p class="summit-dqc__progress" aria-live="polite" data-dqc-progress><?php echo esc_html( sprintf( '0 of %d answered', count( $spec['questions'] ) ) ); ?></p>
            <?php foreach ( $spec['questions'] as $q ) : ?>
            <fieldset class="summit-dqc__question">
                <legend><?php echo esc_html( $q['text'] ); ?></legend>
                <div class="summit-dqc__answers">
                    <?php foreach ( $q['options'] as $option ) : ?>
                    <label class="summit-dqc__answer">
                        <input type="radio" name="dqc-<?php echo esc_attr( $q['key'] ); ?>" value="<?php echo esc_attr( $option ); ?>">
                        <span><?php echo esc_html( $option ); ?></span>
                    </label>
                    <?php endforeach; ?>
                </div>
            </fieldset>
            <?php endforeach; ?>
            <div class="summit-dqc__actions">
                <button type="submit" class="btn btn--primary btn--compact"><?php esc_html_e( 'Review My Answers', 'summit' ); ?></button>
            </div>
        </form>
        <div class="summit-dqc__result" data-dqc-result tabindex="-1" hidden>
            <h4><?php esc_html_e( 'Your main consideration', 'summit' ); ?></h4>
            <p class="summit-dqc__lead" data-dqc-lead></p>
            <p data-dqc-consideration></p>
            <h4><?php esc_html_e( 'Before you decide…', 'summit' ); ?></h4>
            <p data-dqc-before></p>
            <div class="summit-dqc__actions">
                <a class="btn btn--primary btn--compact" href="<?php echo esc_url( $discuss ); ?>" data-dqc-discuss><?php esc_html_e( 'Discuss This With Summit', 'summit' ); ?></a>
                <button type="button" class="summit-guide__copy" data-dqc-copy><?php esc_html_e( 'Copy My Summary', 'summit' ); ?></button>
            </div>
            <p class="summit-guide__status" aria-live="polite" data-dqc-status></p>
        </div>
        <script type="application/json" data-dqc-spec><?php echo wp_json_encode( $spec, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG ); ?></script>
    </div>
    <div class="summit-dqc__fallback" data-dqc-fallback>
        <div class="summit-dqc__considerations">
            <?php foreach ( $spec['considerations'] as [ $title, $text ] ) : ?>
            <div class="summit-dqc__consideration">
                <h4><?php echo esc_html( $title ); ?></h4>
                <p><?php echo esc_html( $text ); ?></p>
            </div>
            <?php endforeach; ?>
        </div>
        <p class="summit-dqc__actions"><a class="btn btn--primary btn--compact" href="<?php echo esc_url( $discuss ); ?>"><?php esc_html_e( 'Discuss This With Summit', 'summit' ); ?></a></p>
    </div>
    <?php
    return (string) ob_get_clean();
} );

/**
 * A Home panel (Editorial Handover v1, S01-P001 … S01-P008) from the front
 * page's summit_home field: eyebrow, headline, paras, rows (title, line,
 * url, cta), ctas (label, url). [] when the field is not set.
 */
function summit_home_panel( string $id ): array {
    static $panels = null;
    if ( null === $panels ) {
        $front  = (int) get_option( 'page_on_front' );
        $panels = $front ? (array) json_decode( (string) get_post_meta( $front, 'summit_home', true ), true ) : [];
    }
    return $panels[ $id ] ?? [];
}
