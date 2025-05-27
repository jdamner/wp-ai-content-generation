import { parse } from './parser';
import { sendToAi } from './ai-request';
import { select } from '@wordpress/data';
import { store as blocksStore } from '@wordpress/blocks';

export const request = async ( prompt: string ) => {
	const components = select( blocksStore.name ).getBlockTypes();
	const response = await sendToAi( {
		prompt,
		components: components.map(
			( {
				name,
				attributes,
				example,
				description,
				keywords,
				category,
			} ) => ( {
				name,
				attributes,
				example,
				description,
				keywords,
				category,
			} )
		),
	} );
	if ( response.error ) {
		throw new Error( response.message );
	}

	return response.components.map( parse );
};
