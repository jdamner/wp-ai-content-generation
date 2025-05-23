import type { AiHandler, AIResponse } from './types';
import apiFetch from '@wordpress/api-fetch';

export const sendToAi: AiHandler = async ( request ) => {
	const response = await apiFetch< AIResponse >( {
		path: 'wp-ai-content-generation/v1/generate',
		method: 'POST',
		data: request,
	} );

	if ( response.error ) {
		throw new Error( response.message );
	}

	return response;
};
