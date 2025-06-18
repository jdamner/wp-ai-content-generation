import type { BlockInstance } from '@wordpress/blocks';

interface AIRequest {
	prompt: string;
}

type ComponentValue = {
	name: string;
	attributes: Array< { name: string; value: any } >;
	innerBlocks: Array< ComponentValue >;
};

type ValidAIResponse = {
	status: 'complete';
	components: Array< ComponentValue >;
};

type ErrorAIResponse = {
	status: 'error';
	message: string;
};

type PendingAIResponse = {
	status:
		| 'pending'
		| 'content_analysis_pending'
		| 'intent_analysis_pending'
		| 'components_pending';
};

type BaseAiResponse = {
	id?: string;
};

export type AIResponse = BaseAiResponse &
	( ValidAIResponse | ErrorAIResponse | PendingAIResponse );
export type AiHandler = ( request: AIRequest ) => Promise< AIResponse >;

export type ComponentParser<
	T extends Record< string, any > = { [ key: string ]: any },
> = ( component: ComponentValue ) => BlockInstance< T >;
