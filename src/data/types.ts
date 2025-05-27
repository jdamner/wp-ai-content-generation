import type { Block, BlockInstance } from '@wordpress/blocks';

interface AIRequest {
	prompt: string;
	components: Pick<
		Block< any >,
		| 'name'
		| 'attributes'
		| 'example'
		| 'description'
		| 'keywords'
		| 'category'
	>[];
}

type ComponentValue = {
	name: string;
	attributes?: {
		readonly [ x: string ]: string | number | boolean | undefined;
	};
};

type ValidAIResponse = {
	error?: never;
	message?: never;
	components: Array< ComponentValue >;
};
type ErrorAIResponse = {
	error: true;
	message: string;
	components?: never;
};

export type AIResponse = ValidAIResponse | ErrorAIResponse;

export type AiHandler = ( request: AIRequest ) => Promise< AIResponse >;
export type ComponentParser<
	T extends Record< string, any > = { [ key: string ]: any },
> = ( component: ComponentValue ) => BlockInstance< T >;
