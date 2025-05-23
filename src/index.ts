import domReady from '@wordpress/dom-ready';
import { registerPlugin } from '@wordpress/plugins';
import { ReactComponent as Icon } from './icon.svg';
import { __ } from '@wordpress/i18n';

import './index.scss';
import { Plugin } from './Plugin';

domReady( () => {
	registerPlugin( 'wp-ai-content-generation', {
		icon: Icon,
		render: Plugin,
	} );
} );
