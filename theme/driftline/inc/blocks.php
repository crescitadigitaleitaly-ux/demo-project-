<?php
/**
 * Server-rendered blocks: header, footer and the form layouts.
 *
 * The forms are front-end only in this concept demo. They validate in the browser and
 * show a confirmation, but nothing is stored or emailed until a back end is added.
 *
 * @package driftline
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'init',
	function () {
		$blocks = array(
			'driftline/header'  => array( 'dl_render_header', array() ),
			'driftline/footer'  => array( 'dl_render_footer', array() ),
			'driftline/form'    => array( 'dl_render_form', array( 'type' => array( 'type' => 'string', 'default' => 'enquiry' ) ) ),
		);
		foreach ( $blocks as $name => $def ) {
			register_block_type(
				$name,
				array(
					'api_version'     => 3,
					'render_callback' => $def[0],
					'attributes'      => $def[1],
					'supports'        => array( 'html' => false ),
				)
			);
		}
	}
);

add_action(
	'enqueue_block_editor_assets',
	function () {
		wp_enqueue_script( 'driftline-editor', DL_URI . '/assets/js/editor.js', array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-server-side-render' ), dl_asset_ver( '/assets/js/editor.js' ), true );
	}
);

/** Menu markup for a location, or an empty string. */
function dl_menu( $location ) {
	if ( ! has_nav_menu( $location ) ) {
		return '';
	}
	return wp_nav_menu(
		array(
			'theme_location' => $location,
			'container'      => false,
			'depth'          => 1,
			'echo'           => false,
			'fallback_cb'    => '__return_empty_string',
			'items_wrap'     => '<ul>%3$s</ul>',
		)
	);
}

function dl_render_header() {
	$name  = get_bloginfo( 'name' );
	$tag   = is_front_page() ? 'h1' : 'p';
	$left  = dl_menu( 'header-left' );
	$right = dl_menu( 'header-right' );
	$all   = '<ul>' . preg_replace( '#</?ul>#', '', $left . $right ) . '</ul>';
	ob_start();
	?>
<div class="dl-progress" aria-hidden="true"></div>
<div class="dl-header">
	<nav class="dl-nav dl-nav--left" aria-label="<?php esc_attr_e( 'Services', 'driftline' ); ?>"><?php echo $left; // phpcs:ignore ?></nav>
	<<?php echo $tag; // phpcs:ignore ?> class="dl-wordmark"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"<?php echo is_front_page() ? ' aria-current="page"' : ''; ?>><?php echo esc_html( $name ); ?></a></<?php echo $tag; // phpcs:ignore ?>>
	<nav class="dl-nav dl-nav--right" aria-label="<?php esc_attr_e( 'Shop and contact', 'driftline' ); ?>"><?php echo $right; // phpcs:ignore ?></nav>
	<button class="dl-burger" type="button" aria-expanded="false" aria-controls="dl-menu" aria-label="<?php esc_attr_e( 'Open menu', 'driftline' ); ?>"><span></span><span></span></button>
</div>
<div class="dl-menu" id="dl-menu">
	<nav aria-label="<?php esc_attr_e( 'Menu', 'driftline' ); ?>"><?php echo $all; // phpcs:ignore ?></nav>
	<p class="dl-menu__note"><?php echo esc_html( dl_demo_line() ); ?></p>
</div>
	<?php
	return ob_get_clean();
}

function dl_demo_line() {
	return 'Concept demo by Mouzzam Channa, not a live business.';
}

function dl_render_footer() {
	ob_start();
	?>
<div class="dl-footer">
	<div class="dl-footer__top">
		<div class="dl-footer__brand">
			<p class="dl-wordmark"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></p>
			<p><?php echo esc_html( get_bloginfo( 'description' ) ); ?></p>
		</div>
		<nav class="dl-footer__nav" aria-label="<?php esc_attr_e( 'Footer', 'driftline' ); ?>"><?php echo dl_menu( 'footer' ); // phpcs:ignore ?></nav>
	</div>
	<div class="dl-footer__bottom">
		<p>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php echo esc_html( get_bloginfo( 'name' ) ); ?>. Placeholder brand.</p>
		<p><?php echo esc_html( dl_demo_line() ); ?> Photos from Unsplash.</p>
	</div>
</div>
	<?php
	return ob_get_clean();
}

/** Field definitions for both forms. */
function dl_form_defs() {
	return array(
		'enquiry' => array(
			'label'   => 'Enquiry',
			'button'  => 'Send enquiry',
			'success' => 'Demo form: nothing was sent. On the live site this enquiry would go to the workshop by email.',
			'note'    => 'Demo form. Nothing you type here is sent or stored.',
			'fields'  => array(
				'name'    => array( 'label' => 'Name', 'type' => 'text', 'required' => true, 'autocomplete' => 'name' ),
				'email'   => array( 'label' => 'Email', 'type' => 'email', 'required' => true, 'autocomplete' => 'email' ),
				'phone'   => array( 'label' => 'Phone', 'type' => 'tel', 'optional' => true, 'autocomplete' => 'tel' ),
				'service' => array(
					'label'    => 'What is it about?',
					'type'     => 'select',
					'required' => true,
					'options'  => array(
						'automotive' => 'An automotive build',
						'surfboards' => 'A custom surfboard',
						'product'    => 'A board from the shop',
						'course'     => 'The shaping course',
						'other'      => 'Something else',
					),
				),
				'message' => array( 'label' => 'Message', 'type' => 'textarea', 'required' => true, 'full' => true ),
			),
		),
		'course'  => array(
			'label'   => 'Course sign-up',
			'button'  => 'Request a place',
			'success' => 'Demo form: nothing was sent. On the live site this request would go to the workshop by email.',
			'note'    => 'Demo form. Nothing you type here is sent or stored, and no payment is taken.',
			'fields'  => array(
				'name'       => array( 'label' => 'Name', 'type' => 'text', 'required' => true, 'autocomplete' => 'name' ),
				'email'      => array( 'label' => 'Email', 'type' => 'email', 'required' => true, 'autocomplete' => 'email' ),
				'experience' => array(
					'label'    => 'Experience level',
					'type'     => 'select',
					'required' => true,
					'options'  => array(
						'none'    => 'I have never shaped a board',
						'some'    => 'I have done some woodwork or glassing',
						'shaped'  => 'I have shaped a board before',
					),
				),
				'date'       => array(
					'label'    => 'Preferred date',
					'type'     => 'select',
					'required' => true,
					'options'  => array(
						'2027-03-13' => 'Sat 13 and Sun 14 March 2027 (demo date)',
						'2027-04-17' => 'Sat 17 and Sun 18 April 2027 (demo date)',
						'either'     => 'Either date works',
					),
				),
				'message'    => array( 'label' => 'Anything we should know?', 'type' => 'textarea', 'optional' => true, 'full' => true ),
			),
		),
	);
}

function dl_render_form( $attrs ) {
	$type = isset( $attrs['type'] ) && 'course' === $attrs['type'] ? 'course' : 'enquiry';
	$defs = dl_form_defs();
	$def  = $defs[ $type ];
	$uid  = 'dl-' . $type;
	ob_start();
	?>
<form class="dl-form" id="<?php echo esc_attr( $uid ); ?>" method="get" action="#<?php echo esc_attr( $uid ); ?>" data-success="<?php echo esc_attr( $def['success'] ); ?>">
	<input type="hidden" name="product" value="">
	<p class="dl-form__picked" hidden></p>
	<?php
	foreach ( $def['fields'] as $key => $f ) :
		$id  = $uid . '-' . $key;
		$req = ! empty( $f['required'] );
		?>
	<p class="dl-field<?php echo ! empty( $f['full'] ) ? ' dl-field--full' : ''; ?>">
		<label for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $f['label'] ); ?><?php echo ! empty( $f['optional'] ) ? ' <small>(optional)</small>' : ''; ?></label>
		<?php if ( 'select' === $f['type'] ) : ?>
		<select id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $key ); ?>"<?php echo $req ? ' required' : ''; ?>>
			<option value="">Choose one</option>
			<?php foreach ( $f['options'] as $val => $text ) : ?>
			<option value="<?php echo esc_attr( $val ); ?>"><?php echo esc_html( $text ); ?></option>
			<?php endforeach; ?>
		</select>
		<?php elseif ( 'textarea' === $f['type'] ) : ?>
		<textarea id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $key ); ?>" rows="5" maxlength="3000"<?php echo $req ? ' required' : ''; ?>></textarea>
		<?php else : ?>
		<input type="<?php echo esc_attr( $f['type'] ); ?>" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $key ); ?>" maxlength="160" autocomplete="<?php echo esc_attr( $f['autocomplete'] ); ?>"<?php echo $req ? ' required' : ''; ?>>
		<?php endif; ?>
	</p>
	<?php endforeach; ?>
	<div class="dl-form__foot">
		<button class="dl-btn" type="submit"><?php echo esc_html( $def['button'] ); ?></button>
		<p><?php echo esc_html( $def['note'] ); ?></p>
	</div>
	<p class="dl-form__status" role="status" tabindex="-1" hidden></p>
</form>
	<?php
	return ob_get_clean();
}
