import { Button, Modal } from '@wordpress/components';
import { PluginPostStatusInfo } from '@wordpress/editor';
import { ReactComponent as Icon } from './icon.svg';
import { useState } from 'react';
import { ModalContent } from './ModalContent';

export const Plugin: React.FC = () => {
	const [ isOpen, setIsOpen ] = useState( false );
	const toggleOpen = () => {
		setIsOpen( ! isOpen );
	};

	return (
		<>
			<PluginPostStatusInfo>
				<Button onClick={ toggleOpen } variant="primary" icon={ Icon }>
					Generate Content
				</Button>
			</PluginPostStatusInfo>
			{ isOpen && (
				<Modal
					size="fill"
					onRequestClose={ toggleOpen }
					title="AI Content Generation"
					icon={ <Icon /> }
				>
					<ModalContent closeModal={ toggleOpen } />
				</Modal>
			) }
		</>
	);
};
