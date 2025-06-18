import domReady from '@wordpress/dom-ready';
import { registerPlugin } from '@wordpress/plugins';

import { Plugin } from './Plugin';

domReady( () => {
	registerPlugin( 'wp-ai-content-generation', {
		render: Plugin,
	} );
} );
