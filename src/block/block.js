/**
 * BLOCK: Tweets Block
 *
 * Registering a basic block with Gutenberg.
 * Simple block, renders and saves the same content without any interactivity.
 */

//  Import CSS.
import './editor.scss';
import './style.scss';

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { useState, useEffect } = wp.element;
const apiFetch = wp.apiFetch; // Use WordPress apiFetch instead of native fetch.

registerBlockType( 'cgb/block-tweets-block', {
	title: __( 'Tweets Block' ),
	icon: 'twitter',
	category: 'common',
	keywords: [
		__( 'tweets-block' ),
		__( 'Gutenberg' ),
		__( 'create-guten-block' ),
	],

	edit: ( props ) => {
		const [ headersData, setHeadersData ] = useState( null );

		useEffect( () => {
			// Use apiFetch instead of native fetch.
			apiFetch( { url: 'https://httpbin.org/get', method: 'GET' } )
				.then( data => {
					setHeadersData( data.headers );
				} );
		}, [] );

		return (
			<div className={ props.className }>
				<h4>Headers from API</h4>
				{ headersData ? (
					<ul>
						{ Object.keys( headersData ).map( key => (
							<li key={ key }>
								<strong>{ key }</strong>: { headersData[ key ] }
							</li>
						) ) }
					</ul>
				) : (
					<p>Loading headers...</p>
				) }
			</div>
		);
	},

	save: () => {
		return null; // Dynamic content, rendered on server-side.
	},
} );
