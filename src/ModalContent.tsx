import {
	Button,
	Spinner,
	TextareaControl,
	Notice,
	Flex,
} from '@wordpress/components';
import { useState } from 'react';
import { request } from './data';
import { type BlockInstance, getBlockContent } from '@wordpress/blocks';
import { store as blockEditorStore } from '@wordpress/block-editor';
import { useDispatch } from '@wordpress/data';

export const ModalContent: React.FC< { closeModal: () => void } > = ( {
	closeModal,
} ) => {
	const [ blocks, setBlocks ] = useState< BlockInstance[] >( [] );
	const [ prompt, setPrompt ] = useState( '' );
	const [ error, setError ] = useState< string | null >( null );
	const [ isLoading, setIsLoading ] = useState( false );

	const handleGenerate = async () => {
		setIsLoading( true );
		setError( null );
		try {
			setBlocks( await request( prompt ) );
		} catch ( thrown ) {
			setBlocks( [] );
			setError( ( thrown as Error ).message );
		}
		setIsLoading( false );
	};

	const dispatch = useDispatch( blockEditorStore );

	const populateEditor = () => {
		dispatch.insertBlocks( blocks );
		setBlocks( [] );
		setPrompt( '' );
		closeModal();
	};

	const logBlocks = () => {
		console.log( 'Generated Blocks:', blocks ); // eslint-disable-line no-console
	};

	return (
		<>
			<TextareaControl
				label="Prompt"
				value={ prompt }
				onChange={ ( value ) => setPrompt( value ) }
				placeholder="Enter your prompt here..."
				rows={ 4 }
				__nextHasNoMarginBottom
				help={
					error ? (
						<Notice
							status="error"
							isDismissible
							onRemove={ () => setError( null ) }
						>
							{ error }
						</Notice>
					) : (
						'This is the prompt that will be sent to the AI model.'
					)
				}
			/>

			<Flex
				direction="row"
				gap={ 2 }
				justify="space-between"
				align="center"
			>
				<Flex
					direction="row"
					gap={ 2 }
					justify="flex-start"
					align="center"
					style={ { marginTop: '1rem' } }
				>
					<Button
						variant="primary"
						onClick={ handleGenerate }
						disabled={
							isLoading || blocks.length > 0 || ! prompt.trim()
						}
					>
						{ isLoading ? 'Generating...' : 'Generate Content' }
					</Button>
					{ isLoading && <Spinner /> }
				</Flex>

				{ blocks.length > 0 && (
					<Flex
						direction="row"
						gap={ 2 }
						justify="flex-end"
						align="center"
						style={ { marginTop: '1rem' } }
					>
						<Button variant="tertiary" onClick={ logBlocks }>
							Log Content
						</Button>
						<Button
							variant="secondary"
							onClick={ () => setBlocks( [] ) }
						>
							Clear Content
						</Button>
						<Button variant="primary" onClick={ populateEditor }>
							Populate Editor
						</Button>
					</Flex>
				) }
			</Flex>

			{ blocks.length > 0 && (
				<Flex
					direction="column"
					gap={ 2 }
					justify="flex-start"
					align="center"
					style={ { marginTop: '1rem' } }
				>
					<h2>Preview</h2>
					<iframe
						srcDoc={ blocks.map( getBlockContent ).join( '\n' ) }
						style={ {
							width: '100%',
							height: '400px',
							border: '1px solid #ccc',
						} }
						title="Preview"
					/>
				</Flex>
			) }
		</>
	);
};
