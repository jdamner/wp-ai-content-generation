import { Panel, PanelBody, Button, Modal } from '@wordpress/components';
import { PluginSidebar } from '@wordpress/editor';
import { ReactComponent as Icon } from './icon.svg';
import { useState } from 'react';

export const Plugin: React.FC = () => {
	// @todo - change the default state to false, this is just for testing
	const [ isOpen, setIsOpen ] = useState( true );
	const toggleOpen = () => {
		setIsOpen( ! isOpen );
	};

	return (
		<>
			<PluginSidebar
				icon={ Icon }
				name="wp-ai-content-generation"
				title="AI Content Generation"
			>
				<Panel>
					<PanelBody>
						<Button onClick={ toggleOpen } variant="primary">
							{ isOpen ? 'Close' : 'Open' } Panel
						</Button>
					</PanelBody>
				</Panel>
			</PluginSidebar>
			{ isOpen && (
				<Modal
					size="fill"
					onRequestClose={ toggleOpen }
					title="AI Content Generation"
					icon={ <Icon /> }
				>
					Hello World!
				</Modal>
			) }
		</>
	);
};
