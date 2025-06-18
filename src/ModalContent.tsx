import {
	Button,
	Spinner,
	TextareaControl,
	Notice,
	Flex,
} from '@wordpress/components';
import { useEffect, useState, useCallback } from 'react';
import { getBlockContent } from '@wordpress/blocks';
import { store as blockEditorStore } from '@wordpress/block-editor';
import { useDispatch } from '@wordpress/data';
import { get, post } from './data';
import { parse } from './data/parser';
import { AIResponse } from './data/types';

export const ModalContent: React.FC< { closeModal: () => void } > = ( {
	closeModal,
} ) => {
	const [ prompt, setPrompt ] = useState( '' );
	const [ response, setResponse ] = useState< AIResponse | null >( null );
	const [ isLoading, setIsLoading ] = useState( false );

	const handleGenerate = async () => {
		setIsLoading( true );
		setResponse( await post( { prompt } ) );
		setIsLoading( false );
	};

	const handleStatusCheck = useCallback( async () => {
		setIsLoading( true );
		if ( response?.id ) {
			try {
				const updatedResponse = await get( response.id );
				setResponse( updatedResponse );
			} catch ( error ) {
				setResponse( {
					status: 'error',
					message:
						error instanceof Error
							? error.message
							: 'An error occurred',
				} );
			}
		}
		setIsLoading( false );
	}, [ response ] );

	useEffect( () => {
		if (
			response?.id &&
			response.status !== 'complete' &&
			response.status !== 'error'
		) {
			const interval = setInterval( handleStatusCheck, 2000 );
			return () => clearInterval( interval );
		}
	}, [ response, handleStatusCheck ] );

	const dispatch = useDispatch( blockEditorStore );

	const blocks =
		response?.status === 'complete' &&
		response.components &&
		Array.isArray( response.components )
			? response.components.map( parse )
			: [];

	const populateEditor = () => {
		dispatch.insertBlocks( blocks );
		setResponse( null );
		setPrompt( '' );
		closeModal();
	};

	const showLoading =
		isLoading ||
		( response &&
			response.status !== 'complete' &&
			response.status !== 'error' );

	const helpText = () => {
		switch ( response?.status ) {
			case 'error':
				return <Notice status="error">{ response.message }</Notice>;
			case 'complete':
				return 'The AI has successfully generated content based on your request.';
			case 'pending':
				return 'Your request is waiting for a worker to pick it up. Please wait...';
			case 'content_analysis_pending':
				return 'The AI is analyzing your request...';
			case 'intent_analysis_pending':
				return 'The AI is analyzing the intent behind your request...';
			case 'components_pending':
				return 'The AI is selecting components based on your request...';
			default:
				return 'Enter a request to generate content using AI.';
		}
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
				help={ helpText() }
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
						disabled={ showLoading || ! prompt.trim() }
					>
						{ showLoading ? 'Generating...' : 'Generate Content' }
					</Button>
					{ showLoading && <Spinner /> }
				</Flex>

				{ blocks.length > 0 && (
					<Button variant="primary" onClick={ populateEditor }>
						Populate Editor
					</Button>
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
