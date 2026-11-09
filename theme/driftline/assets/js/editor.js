/* Registers the theme's server-rendered blocks in the editor so they preview instead of showing as unsupported. */
( function ( wp ) {
	var el = wp.element.createElement;
	var blocks = {
		'driftline/header': 'Driftline header',
		'driftline/footer': 'Driftline footer',
		'driftline/form': 'Driftline form'
	};
	Object.keys( blocks ).forEach( function ( name ) {
		wp.blocks.registerBlockType( name, {
			apiVersion: 3,
			title: blocks[ name ],
			category: 'theme',
			icon: 'layout',
			attributes: name === 'driftline/form' ? { type: { type: 'string', default: 'enquiry' } } : {},
			supports: { html: false },
			edit: function ( props ) {
				return el( 'div', wp.blockEditor.useBlockProps(), el( wp.serverSideRender, { block: name, attributes: props.attributes } ) );
			},
			save: function () { return null; }
		} );
	} );
} )( window.wp );
