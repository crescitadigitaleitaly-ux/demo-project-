<?php
/**
 * Starter content for the demo, written as normal block markup so every page
 * stays editable in the WordPress editor.
 *
 * @package driftline
 */

defined( 'ABSPATH' ) || exit;

/** Bundled demo images: key => [ file, alt text ]. Credits are in CREDITS.md. */
function dl_images() {
	return array(
		'home-automotive'        => array( 'home-automotive.webp', 'Grey custom coupe on a plain studio backdrop' ),
		'home-surfboards'        => array( 'home-surfboards.webp', 'Yellow longboard standing upright on a beach' ),
		'automotive-hero'        => array( 'automotive-hero.webp', 'Silver classic coupe parked in a dark concrete studio' ),
		'automotive-interior'    => array( 'automotive-interior.webp', 'Steering wheel and dashboard of a classic car' ),
		'automotive-headlight'   => array( 'automotive-headlight.webp', 'Round headlight on a dark blue classic car' ),
		'automotive-panel'       => array( 'automotive-panel.webp', 'Close view of a satin blue body panel' ),
		'automotive-door'        => array( 'automotive-door.webp', 'Door handle on a dark grey car' ),
		'automotive-bare-metal'  => array( 'automotive-bare-metal.webp', 'Bare car body on stands in a restoration workshop' ),
		'automotive-low-light'   => array( 'automotive-low-light.webp', 'Front corner of a bronze car in low light' ),
		'surfboards-hero'        => array( 'surfboards-hero.webp', 'Nose of a white surfboard lying on sand, in black and white' ),
		'surfboards-black-board' => array( 'surfboards-black-board.webp', 'Black surfboard standing against a white wall' ),
		'surfboards-rack'        => array( 'surfboards-rack.webp', 'White surfboards stacked upright in a rack' ),
		'surfboards-wooden-fin'  => array( 'surfboards-wooden-fin.webp', 'Wooden surfboard with a single fin lying on sand' ),
		'surfboards-shaping'     => array( 'surfboards-shaping.webp', 'Shaper running a power tool along a foam surfboard blank' ),
		'surfboards-sanding'     => array( 'surfboards-sanding.webp', 'Hands working on the rail of a surfboard' ),
		'surfboards-fins'        => array( 'surfboards-fins.webp', 'Fins on surfboards standing in a rack' ),
		'product-single-fin'     => array( 'product-single-fin.webp', 'White single fin surfboard lying in dry grass' ),
		'about-garage'           => array( 'about-garage.webp', 'White car in a garage, seen through a doorway' ),
	);
}

/* ---------- Block markup helpers ---------- */

function dl_img_id( $key ) {
	$map = get_option( 'dl_media', array() );
	return isset( $map[ $key ] ) ? (int) $map[ $key ] : 0;
}
function dl_img_url( $key, $size = 'full' ) {
	$id  = dl_img_id( $key );
	$url = $id ? wp_get_attachment_image_url( $id, $size ) : '';
	if ( ! $url ) {
		$imgs = dl_images();
		$url  = DL_URI . '/assets/img/' . $imgs[ $key ][0];
	}
	return $url;
}
function dl_img_alt( $key ) {
	$imgs = dl_images();
	return $imgs[ $key ][1];
}

function dl_b_p( $text, $class = '' ) {
	$attr = $class ? ' {"className":"' . $class . '"}' : '';
	$cls  = $class ? ' class="' . $class . '"' : '';
	return "<!-- wp:paragraph{$attr} -->\n<p{$cls}>{$text}</p>\n<!-- /wp:paragraph -->\n\n";
}
function dl_b_h( $text, $level = 2, $class = '' ) {
	$attrs = array();
	if ( 2 !== $level ) {
		$attrs['level'] = $level;
	}
	if ( $class ) {
		$attrs['className'] = $class;
	}
	$attr = $attrs ? ' ' . wp_json_encode( $attrs ) : '';
	$cls  = trim( 'wp-block-heading ' . $class );
	return "<!-- wp:heading{$attr} -->\n<h{$level} class=\"{$cls}\">{$text}</h{$level}>\n<!-- /wp:heading -->\n\n";
}
function dl_b_button( $text, $url, $outline = false ) {
	$attr = $outline ? ' {"className":"is-style-outline"}' : '';
	$cls  = $outline ? 'wp-block-button is-style-outline' : 'wp-block-button';
	$url  = esc_url( $url );
	return "<!-- wp:buttons -->\n<div class=\"wp-block-buttons\"><!-- wp:button{$attr} -->\n<div class=\"{$cls}\"><a class=\"wp-block-button__link wp-element-button\" href=\"{$url}\">{$text}</a></div>\n<!-- /wp:button --></div>\n<!-- /wp:buttons -->\n\n";
}
function dl_b_group( $inner, $class, $extra = array() ) {
	$attrs = array_merge(
		array(
			'className' => $class,
			'layout'    => array( 'type' => 'constrained' ),
		),
		$extra
	);
	$tag   = isset( $attrs['tagName'] ) ? $attrs['tagName'] : 'div';
	$id    = isset( $attrs['anchor'] ) ? ' id="' . $attrs['anchor'] . '"' : '';
	$align = isset( $attrs['align'] ) ? ' align' . $attrs['align'] : '';
	return '<!-- wp:group ' . wp_json_encode( $attrs ) . " -->\n<{$tag}{$id} class=\"wp-block-group{$align} {$class}\">" . trim( $inner ) . "</{$tag}>\n<!-- /wp:group -->\n\n";
}
function dl_b_section( $inner, $class = '', $extra = array() ) {
	return dl_b_group( $inner, trim( 'dl-section ' . $class ), array_merge( array( 'tagName' => 'section', 'align' => 'full' ), $extra ) );
}
/** Plain wrapper with no layout rules of its own, used for CSS grids. */
function dl_b_grid( $inner, $class, $align = 'wide' ) {
	$attrs = array(
		'align'     => $align,
		'className' => $class,
		'layout'    => array( 'type' => 'default' ),
	);
	return '<!-- wp:group ' . wp_json_encode( $attrs ) . " -->\n<div class=\"wp-block-group align{$align} {$class}\">" . trim( $inner ) . "</div>\n<!-- /wp:group -->\n\n";
}
function dl_b_image( $key, $size = 'large' ) {
	$id  = dl_img_id( $key );
	$url = esc_url( dl_img_url( $key, $size ) );
	$alt = esc_attr( dl_img_alt( $key ) );
	return '<!-- wp:image ' . wp_json_encode( array( 'id' => $id, 'sizeSlug' => $size, 'linkDestination' => 'none' ) ) . " -->\n<figure class=\"wp-block-image size-{$size}\"><img src=\"{$url}\" alt=\"{$alt}\" class=\"wp-image-{$id}\"/></figure>\n<!-- /wp:image -->\n\n";
}
function dl_b_cover( $key, $class, $inner ) {
	$id    = dl_img_id( $key );
	$url   = esc_url( dl_img_url( $key ) );
	$alt   = esc_attr( dl_img_alt( $key ) );
	$attrs = array(
		'url'                => dl_img_url( $key ),
		'id'                 => $id,
		'alt'                => dl_img_alt( $key ),
		'dimRatio'           => 40,
		'overlayColor'       => 'contrast',
		'isUserOverlayColor' => true,
		'align'              => 'full',
		'className'          => $class,
		'layout'             => array( 'type' => 'constrained' ),
	);
	return '<!-- wp:cover ' . wp_json_encode( $attrs ) . " -->\n<div class=\"wp-block-cover alignfull {$class}\"><img class=\"wp-block-cover__image-background wp-image-{$id}\" alt=\"{$alt}\" src=\"{$url}\" data-object-fit=\"cover\"/><span aria-hidden=\"true\" class=\"wp-block-cover__background has-contrast-background-color has-background-dim-40 has-background-dim\"></span><div class=\"wp-block-cover__inner-container\">" . trim( $inner ) . "</div></div>\n<!-- /wp:cover -->\n\n";
}
function dl_b_columns( $cols, $class, $align = 'wide' ) {
	$out = '';
	foreach ( $cols as $col ) {
		$width = isset( $col[1] ) ? $col[1] : '';
		$attr  = $width ? ' {"width":"' . $width . '"}' : '';
		$style = $width ? ' style="flex-basis:' . $width . '"' : '';
		$out  .= "<!-- wp:column{$attr} -->\n<div class=\"wp-block-column\"{$style}>" . trim( $col[0] ) . "</div>\n<!-- /wp:column -->\n\n";
	}
	return '<!-- wp:columns ' . wp_json_encode( array( 'align' => $align, 'className' => $class ) ) . " -->\n<div class=\"wp-block-columns align{$align} {$class}\">" . trim( $out ) . "</div>\n<!-- /wp:columns -->\n\n";
}
function dl_b_cards( $cards, $class = 'dl-cards' ) {
	$out = '';
	$n   = 0;
	foreach ( $cards as $card ) {
		++$n;
		$inner = dl_b_p( sprintf( '%02d', $n ), 'dl-card__num' ) . dl_b_h( $card[0], 3 ) . dl_b_p( $card[1] );
		$out  .= '<!-- wp:group {"className":"dl-card","layout":{"type":"default"}} -->' . "\n<div class=\"wp-block-group dl-card\">" . trim( $inner ) . "</div>\n<!-- /wp:group -->\n\n";
	}
	return dl_b_grid( $out, $class );
}
function dl_b_gallery( $keys ) {
	$out = '';
	foreach ( $keys as $key ) {
		$out .= dl_b_image( $key );
	}
	return dl_b_grid( $out, 'dl-gallery' );
}
function dl_b_hero( $key, $eyebrow, $title ) {
	return dl_b_cover( $key, 'dl-hero', dl_b_p( $eyebrow, 'dl-eyebrow' ) . dl_b_h( $title, 1, 'dl-display' ) );
}
function dl_b_intro( $eyebrow, $lead, $paras ) {
	$right = dl_b_p( $lead, 'dl-lead' );
	foreach ( (array) $paras as $p ) {
		$right .= is_array( $p ) ? dl_b_p( $p[0], $p[1] ) : dl_b_p( $p );
	}
	return dl_b_section( dl_b_columns( array( array( dl_b_p( $eyebrow, 'dl-eyebrow' ), '30%' ), array( $right, '70%' ) ), 'dl-split' ) );
}
function dl_b_cta( $eyebrow, $title, $text, $button, $url ) {
	$inner = dl_b_p( $eyebrow, 'dl-eyebrow' ) . dl_b_h( $title ) . dl_b_p( $text ) . dl_b_button( $button, $url, true );
	return dl_b_group( $inner, 'dl-cta', array( 'tagName' => 'section', 'align' => 'full' ) );
}
function dl_b_facts( $rows ) {
	$li = '';
	foreach ( $rows as $label => $value ) {
		$li .= '<li><strong>' . $label . '</strong><span>' . $value . '</span></li>';
	}
	return "<!-- wp:html -->\n<ul class=\"dl-facts\">{$li}</ul>\n<!-- /wp:html -->\n\n";
}
function dl_demo_tag( $text ) {
	return '<span class="dl-tag">' . $text . '</span>';
}

/* ---------- Pages ---------- */

/** All demo pages: slug => [ title, meta title, meta description, content callback ]. */
function dl_pages() {
	return array(
		'home'           => array( 'Home', 'Driftline Workshop | Custom automotive builds and handmade surfboards', 'Driftline Workshop rebuilds cars to order and shapes surfboards by hand. See both services, the shaping course and the shop.', 'dl_page_home' ),
		'automotive'     => array( 'Automotive', 'Custom automotive builds | Driftline Workshop', 'Restoration, bodywork, drivetrain, interior and wiring for one car at a time, planned and quoted stage by stage.', 'dl_page_automotive' ),
		'surfboards'     => array( 'Surfboards', 'Handmade surfboards | Driftline Workshop', 'Longboards, mid-lengths and custom shapes, each one shaped, glassed and finished by hand for one surfer.', 'dl_page_surfboards' ),
		'shaping-course' => array( 'Shaping course', 'Surfboard shaping course sign-up | Driftline Workshop', 'A two-day workshop where you shape your own surfboard from a foam blank with a shaper beside you. Request a place.', 'dl_page_course' ),
		'shop'           => array( 'The Single Fin 9\'2"', 'The Single Fin 9\'2" longboard | Driftline Workshop shop', 'A hand-shaped 9\'2" single fin longboard, made to order. View the specs and send an enquiry to order.', 'dl_page_product' ),
		'about'          => array( 'About', 'About the workshop and contact | Driftline Workshop', 'A small workshop that builds cars and surfboards to order. Read how we work and send an enquiry.', 'dl_page_about' ),
	);
}

function dl_contact_url( $args = array() ) {
	return add_query_arg( $args, home_url( '/about/' ) ) . '#contact';
}

function dl_page_home() {
	$out  = dl_b_cover( 'home-automotive', 'dl-panel', dl_b_h( 'Automotive', 2, 'dl-display' ) . dl_b_button( 'Learn more', home_url( '/automotive/' ), true ) );
	$out .= dl_b_cover( 'home-surfboards', 'dl-panel', dl_b_h( 'Surfboards', 2, 'dl-display' ) . dl_b_button( 'Learn more', home_url( '/surfboards/' ), true ) );

	$words = '';
	foreach ( array( 'Custom builds', 'Handmade surfboards', 'One workshop', 'Made to order' ) as $w ) {
		$words .= '<span>' . $w . '</span>';
	}
	$out .= "<!-- wp:html -->\n<div class=\"dl-marquee\" aria-hidden=\"true\"><div class=\"dl-marquee__track\">" . str_repeat( '<div class="dl-marquee__set">' . $words . '</div>', 4 ) . "</div></div>\n<!-- /wp:html -->\n\n";

	$out .= dl_b_section(
		dl_b_p( 'Driftline Workshop', 'dl-eyebrow' ) .
		dl_b_p( 'One workshop, two crafts. We rebuild cars to order and shape surfboards by hand, for people who plan to use them.', 'dl-statement' ),
		'dl-center dl-intro'
	);

	$tile = function ( $key, $eyebrow, $title, $text, $link, $url ) {
		return dl_b_image( $key ) . dl_b_p( $eyebrow, 'dl-eyebrow' ) . dl_b_h( $title, 3, 'dl-h2' ) . dl_b_p( $text, 'dl-muted' ) . dl_b_p( '<a class="dl-link" href="' . esc_url( $url ) . '">' . $link . '</a>' );
	};
	$out .= dl_b_section(
		dl_b_columns(
			array(
				array( $tile( 'surfboards-shaping', 'Course', 'Shape your own board', 'A weekend in the shaping bay with a shaper beside you. You leave with a board you made.', 'See the course', home_url( '/shaping-course/' ) ) ),
				array( $tile( 'product-single-fin', 'Shop', 'The Single Fin 9\'2"', 'Our house longboard, shaped to order. One model, made one at a time.', 'View the board', home_url( '/shop/' ) ) ),
			),
			'dl-tiles'
		),
		'dl-section--flush-top'
	);

	$out .= dl_b_cta( 'Contact / Order', 'Tell us what you want built', 'A car, a board or a place on the course. Send the details and we will reply with next steps.', 'Contact / Order', dl_contact_url() );
	return $out;
}

function dl_page_automotive() {
	$out  = dl_b_hero( 'automotive-hero', 'Service 01', 'Automotive' );
	$out .= dl_b_intro(
		'What we build',
		'We take on one car at a time and build it around how you plan to drive it.',
		array(
			'Most projects start with a classic that needs more than a respray: tired mechanicals, a worn interior, wiring that has been patched too many times. We strip the car back, agree a plan with you and rebuild it as one job in one workshop.',
			'You deal with the people doing the work, from the first call to the day you collect the keys.',
		)
	);
	$out .= dl_b_section(
		dl_b_grid( dl_b_p( 'Services', 'dl-eyebrow' ) . dl_b_h( 'What goes into a build', 2, 'dl-h2' ), 'dl-head' ) .
		dl_b_cards(
			array(
				array( 'Restoration and bodywork', 'Rust repair, panel work and paint, done in house. We photograph each stage so you can see what is under the finish.' ),
				array( 'Engine and drivetrain', 'Rebuilds, conversions and upgrades chosen for the way you drive. We talk through the trade-offs before any parts are ordered.' ),
				array( 'Interior and trim', 'Seats, dash, headlining and carpets, retrimmed in the materials you pick. Original details stay where they are worth keeping.' ),
				array( 'Wiring and electrics', 'New looms, modern charging and better lighting, hidden so the car still looks its age.' ),
			)
		),
		'dl-section--mist'
	);
	$out .= dl_b_section(
		dl_b_grid( dl_b_p( 'Gallery', 'dl-eyebrow' ) . dl_b_h( 'Details from the workshop', 2, 'dl-h2' ), 'dl-head' ) .
		dl_b_gallery( array( 'automotive-interior', 'automotive-bare-metal', 'automotive-headlight', 'automotive-panel', 'automotive-door', 'automotive-low-light' ) )
	);
	$out .= dl_b_section(
		dl_b_grid( dl_b_p( 'Process', 'dl-eyebrow' ) . dl_b_h( 'How a build works', 2, 'dl-h2' ), 'dl-head' ) .
		dl_b_cards(
			array(
				array( 'Talk it through', 'Send an enquiry with the car and what you have in mind. We reply with questions and a rough scope.' ),
				array( 'Agree the plan', 'We inspect the car, write up the work in stages and quote each stage before it starts.' ),
				array( 'Build and hand over', 'You get photo updates as each stage finishes. The car comes back with a record of everything we did.' ),
			),
			'dl-cards dl-cards--3'
		),
		'dl-section--flush-top'
	);
	$out .= dl_b_cta( 'Enquire', 'Have a build in mind?', 'Tell us about the car and what you want from it. We reply to every enquiry.', 'Start an enquiry', dl_contact_url( array( 'service' => 'automotive' ) ) );
	return $out;
}

function dl_page_surfboards() {
	$out  = dl_b_hero( 'surfboards-hero', 'Service 02', 'Surfboards' );
	$out .= dl_b_intro(
		'What we shape',
		'Every board is shaped by hand for one surfer and the waves they ride most.',
		array(
			'Tell us your height, your weight, how long you have been surfing and where you paddle out. We suggest a shape and dimensions, you adjust them, and the board is shaped, glassed and finished in our bay.',
			'Nothing here comes off a production line, so a board takes a few weeks. We give you a date when you order.',
		)
	);
	$out .= dl_b_section(
		dl_b_grid( dl_b_p( 'Services', 'dl-eyebrow' ) . dl_b_h( 'Boards we make', 2, 'dl-h2' ), 'dl-head' ) .
		dl_b_cards(
			array(
				array( 'Longboards and logs', 'Single fins from 9\'0" up, with the weight and glide for small, clean days.' ),
				array( 'Mid-lengths', 'Boards between 6\'8" and 8\'0" for surfers who want paddle power and still want to turn.' ),
				array( 'Custom shapes', 'Bring a template, an old favourite or a sketch. We work out the numbers with you.' ),
				array( 'Repairs and restoration', 'Ding repair, fin box replacement and full restoration of older boards.' ),
			)
		),
		'dl-section--mist'
	);
	$out .= dl_b_section(
		dl_b_grid( dl_b_p( 'Gallery', 'dl-eyebrow' ) . dl_b_h( 'From the shaping bay', 2, 'dl-h2' ), 'dl-head' ) .
		dl_b_gallery( array( 'surfboards-black-board', 'surfboards-sanding', 'surfboards-rack', 'surfboards-wooden-fin', 'surfboards-shaping', 'surfboards-fins' ) )
	);
	$out .= dl_b_section(
		dl_b_grid( dl_b_p( 'Process', 'dl-eyebrow' ) . dl_b_h( 'How an order works', 2, 'dl-h2' ), 'dl-head' ) .
		dl_b_cards(
			array(
				array( 'Tell us how you surf', 'Your size, your experience and your local break. A photo of your current board helps.' ),
				array( 'Agree the shape', 'We send dimensions, fin setup and colour options. You sign off before we cut foam.' ),
				array( 'Shape, glass, collect', 'We shape and glass the board, let it cure, then call you to pick it up or arrange delivery.' ),
			),
			'dl-cards dl-cards--3'
		),
		'dl-section--flush-top'
	);
	$out .= dl_b_cta( 'Order', 'Ready to order a board?', 'Send your details and the kind of board you are after. You can also learn to shape one yourself on our course.', 'Start an order', dl_contact_url( array( 'service' => 'surfboards' ) ) );
	return $out;
}

function dl_page_course() {
	$out  = dl_b_hero( 'surfboards-shaping', 'Course sign-up', 'Shaping course' );
	$out .= dl_b_intro(
		'Two-day workshop',
		'Shape a surfboard with your own hands over one weekend.',
		array(
			'You start with a foam blank and a template and finish with a shaped board that is ready for glassing. A shaper works next to you the whole time, and the group stays small so everyone gets help.',
			'You do not need any experience. If you can use a hand plane and a sanding block, you can do this.',
		)
	);
	$facts = dl_b_facts(
		array(
			'Length'     => 'Two days, Saturday and Sunday',
			'Next dates' => '13 and 14 March 2027 ' . dl_demo_tag( 'Demo date' ),
			'After that' => '17 and 18 April 2027 ' . dl_demo_tag( 'Demo date' ),
			'Group size' => 'Small group, one shaper',
			'Included'   => 'Blank, tools and safety gear',
			'Glassing'   => 'Quoted separately',
		)
	);
	$out .= dl_b_section(
		dl_b_columns(
			array(
				array( dl_b_image( 'surfboards-sanding' ), '50%' ),
				array( dl_b_p( 'The details', 'dl-eyebrow' ) . dl_b_h( 'What the weekend covers', 2, 'dl-h2' ) . $facts, '50%' ),
			),
			'dl-media dl-media--wide'
		),
		'dl-section--mist'
	);
	$out .= dl_b_section(
		dl_b_grid( dl_b_p( 'Programme', 'dl-eyebrow' ) . dl_b_h( 'Two days in the bay', 2, 'dl-h2' ), 'dl-head' ) .
		dl_b_cards(
			array(
				array( 'Day one: outline and foam', 'Pick a template, mark out the blank and cut the outline. Then learn the planer and take the foam down to thickness.' ),
				array( 'Day two: rails and finish', 'Shape the rails, blend the bottom contours and sand the board until it is ready for glass.' ),
				array( 'After the course', 'Take the shaped blank home, or leave it with us to be glassed and finished.' ),
			),
			'dl-cards dl-cards--3'
		)
	);
	$out .= dl_b_section(
		dl_b_p( 'Sign up', 'dl-eyebrow' ) . dl_b_h( 'Request a place', 2, 'dl-h2' ) .
		dl_b_p( 'Fill in the form and pick a date. We confirm places by email.', 'dl-muted' ) .
		"<!-- wp:driftline/form {\"type\":\"course\"} /-->\n\n",
		'dl-section--flush-top dl-formwrap',
		array( 'anchor' => 'signup' )
	);
	return $out;
}

function dl_page_product() {
	$name   = 'The Single Fin 9\'2"';
	$keys   = array( 'product-single-fin', 'surfboards-hero', 'surfboards-rack', 'surfboards-wooden-fin' );
	$thumbs = '';
	foreach ( $keys as $i => $key ) {
		$thumbs .= sprintf(
			'<li><button type="button" aria-pressed="%s" aria-label="Show image %d: %s" data-full="%s"><img src="%s" alt="%s" width="300" height="300" loading="lazy"/></button></li>',
			0 === $i ? 'true' : 'false',
			$i + 1,
			esc_attr( dl_img_alt( $key ) ),
			esc_url( dl_img_url( $key, 'large' ) ),
			esc_url( dl_img_url( $key, 'medium' ) ),
			esc_attr( dl_img_alt( $key ) )
		);
	}
	$main_id = dl_img_id( $keys[0] );
	$gallery = sprintf(
		"<!-- wp:html -->\n<div class=\"dl-pgallery\"><figure class=\"dl-pgallery__main\"><img src=\"%s\" alt=\"%s\" class=\"wp-image-%d\" width=\"768\" height=\"1024\" fetchpriority=\"high\"/></figure><ul class=\"dl-pgallery__thumbs\">%s</ul></div>\n<!-- /wp:html -->\n\n",
		esc_url( dl_img_url( $keys[0], 'large' ) ),
		esc_attr( dl_img_alt( $keys[0] ) ),
		$main_id,
		$thumbs
	);
	$summary = dl_b_p( 'Shop / Handmade surfboard', 'dl-eyebrow' ) .
		dl_b_h( $name, 1, 'dl-h1-plain' ) .
		dl_b_p( '$1,450 ' . dl_demo_tag( 'Demo price' ), 'dl-price' ) .
		dl_b_p( 'Our house longboard. A classic single fin log with a full nose, soft rails and enough weight to trim through flat sections.', 'dl-muted' ) .
		dl_b_button( 'Enquire to order', dl_contact_url( array( 'service' => 'product', 'product' => rawurlencode( $name ) ) ) ) .
		dl_b_p( 'This is a catalogue page with no online payment. Send an enquiry and we reply with availability and lead time.', 'dl-small dl-muted' ) .
		dl_b_p( 'Specs', 'dl-eyebrow' ) .
		dl_b_facts(
			array(
				'Length'    => '9\'2"',
				'Width'     => '22 3/4"',
				'Thickness' => '3"',
				'Fin'       => 'Single, 10" box',
				'Glassing'  => '6 oz deck and bottom, deck patch',
				'Finish'    => 'Gloss polish',
				'Build'     => 'Made to order ' . dl_demo_tag( 'Sample specs' ),
			)
		);
	$cols = '<!-- wp:columns {"align":"wide","className":"dl-product__top"} -->' . "\n<div class=\"wp-block-columns alignwide dl-product__top\"><!-- wp:column {\"width\":\"54%\"} -->\n<div class=\"wp-block-column\" style=\"flex-basis:54%\">" . trim( $gallery ) . "</div>\n<!-- /wp:column -->\n\n<!-- wp:column {\"width\":\"46%\",\"className\":\"dl-product__summary\"} -->\n<div class=\"wp-block-column dl-product__summary\" style=\"flex-basis:46%\">" . trim( $summary ) . "</div>\n<!-- /wp:column --></div>\n<!-- /wp:columns -->\n\n";
	$out  = dl_b_section( $cols, 'dl-product' );
	$out .= dl_b_intro(
		'About this board',
		'Built for knee-high to head-high days when you want to walk the board and take your time.',
		array(
			'The outline is wide through the middle with a rounded square tail. A gentle rocker keeps speed in weak waves, and the 50/50 rails hold a line while you cross-step to the nose.',
			'We shape each one to order, so you can change the length, the glassing weight and the colour. Tell us in your enquiry and we will confirm what is possible.',
		)
	);
	$out .= dl_b_cta( 'Order', 'Want this board made for you?', 'Send an enquiry with your height, weight and where you surf. We reply with a build slot and a final price.', 'Enquire to order', dl_contact_url( array( 'service' => 'product', 'product' => rawurlencode( $name ) ) ) );
	return $out;
}

function dl_page_about() {
	$out  = dl_b_hero( 'about-garage', 'About', 'The workshop' );
	$out .= dl_b_intro(
		'Who we are',
		'Driftline is a small workshop that builds cars and surfboards to order.',
		array(
			'The two crafts have more in common than they seem to. Both start with a shape, both depend on careful surface work, and both get judged the first time someone uses the result.',
			'We keep the workshop small so the people you talk to are the people doing the work.',
			array( 'This is placeholder copy for a concept site. The real story, names and dates will come from the business owner.', 'dl-small dl-muted' ),
		)
	);
	$out .= dl_b_section(
		dl_b_grid( dl_b_p( 'Values', 'dl-eyebrow' ) . dl_b_h( 'How we like to work', 2, 'dl-h2' ), 'dl-head' ) .
		dl_b_cards(
			array(
				array( 'One job at a time', 'We would sooner finish one build properly than run five at once. Your project has our attention until it leaves.' ),
				array( 'Plain quotes', 'You get a written scope and a price for each stage before work starts. If something changes, we tell you before we carry on.' ),
				array( 'Built to be used', 'Cars are for driving and boards are for surfing. We make choices that hold up in use.' ),
			),
			'dl-cards dl-cards--3'
		),
		'dl-section--mist'
	);
	$out .= dl_b_section(
		dl_b_columns(
			array(
				array( dl_b_image( 'automotive-bare-metal' ), '50%' ),
				array(
					dl_b_p( 'Visit', 'dl-eyebrow' ) . dl_b_h( 'Come and see the work', 2, 'dl-h2' ) .
					dl_b_p( 'Visits are by appointment so someone is free to show you around. The address, opening hours and a map will go here once the real details are supplied.' ) .
					dl_b_p( '<a class="dl-link" href="#contact">Ask about a visit</a>' ),
					'50%',
				),
			),
			'dl-media dl-media--wide'
		)
	);
	$out .= dl_b_section(
		dl_b_p( 'Contact / Order', 'dl-eyebrow' ) . dl_b_h( 'Tell us about your project', 2, 'dl-h2' ) .
		dl_b_p( 'Use this form for build enquiries, board orders and questions about the course.', 'dl-muted' ) .
		"<!-- wp:driftline/form {\"type\":\"enquiry\"} /-->\n\n",
		'dl-section--mist dl-formwrap',
		array( 'anchor' => 'contact' )
	);
	return $out;
}
