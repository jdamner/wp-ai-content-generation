import type { AiHandler, AIResponse } from './types';
import apiFetch from '@wordpress/api-fetch';

const NAMESPACE = 'wp-ai-content-generation/v1';

export const post: AiHandler = async ( request ) => {
	const response = await apiFetch< AIResponse >( {
		path: `${ NAMESPACE }/generate`,
		method: 'POST',
		data: request,
	} );
	return response;
};

export const get = async ( id: string ): Promise< AIResponse > => {
	const response = await apiFetch< AIResponse >( {
		path: `${ NAMESPACE }/generate/${ id }`,
		method: 'GET',
	} );
	return response;
};

export const triggerWorker = () =>
	apiFetch( { path: `${ NAMESPACE }/trigger-worker` } );
