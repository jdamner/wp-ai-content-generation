import type { AiHandler, AIResponse } from './types';
import apiFetch from '@wordpress/api-fetch';

export const post: AiHandler = async ( request ) => {
	const response = await apiFetch< AIResponse >( {
		path: 'wp-ai-content-generation/v1/generate',
		method: 'POST',
		data: request,
	} );
	return response;
};

export const get = async ( id: string ): Promise< AIResponse > => {
	const response = await apiFetch< AIResponse >( {
		path: `wp-ai-content-generation/v1/generate/${ id }`,
		method: 'GET',
	} );
	return response;
};
