/**
 * WordPress dependencies
 */
import { Disabled, PanelBody, CheckboxControl } from '@wordpress/components';
import { compose, ifCondition } from '@wordpress/compose';
import { withSelect, withDispatch } from '@wordpress/data';
import { PluginPostStatusInfo } from '@wordpress/edit-post';
import { InspectorControls } from '@wordpress/editor';
import { Component } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies.
 */
//import './editor.css';

export class SilentPublish extends Component {
	render() {
		const {
			is_published,
			meta: {
				'_silent-publish': SilentPublished,
			} = {},
			updateMeta,
		} = this.props;

		const SilentPublishCheckbox = <CheckboxControl
					label={ __( 'Silent publish?', 'silent-publish' ) }
//					help={ __( 'Publish withou pingbacks, trackbacks, or update service notifications?', 'silent-publish' ) }
					checked={ SilentPublished }
					onChange={ ( SilentPublished ) => {
						updateMeta( { '_silent-publish': SilentPublished || false } );
					} }
					/>;

		const SilentPublishField = is_published ?
					<Disabled className="silent-publish-disabled">{ SilentPublishCheckbox }</Disabled> : SilentPublishCheckbox;

		return (
			<PluginPostStatusInfo>
				{ SilentPublishField }
			</PluginPostStatusInfo>
		);
	}
}

export default compose( [
	withSelect( ( select ) => {
		const { getEditedPostAttribute, isCurrentPostPublished } = select( 'core/editor' );

		return {
			meta: getEditedPostAttribute( 'meta' ),
			is_published: isCurrentPostPublished(),
		};
	} ),
	withDispatch( ( dispatch, { meta } ) => {
		const { editPost } = dispatch( 'core/editor' );

		return {
			updateMeta( newMeta ) {
				newMeta['_silent-publish'] = newMeta['_silent-publish'] ? true : false;
				// Important: Old and new meta need to be merged in a non-mutating way!
				editPost( { meta: { ...meta, ...newMeta } } );
			},
		};
	} ),
	ifCondition( ( { is_published, meta } ) => ! is_published || meta['_silent-publish'] === true ),
] )( SilentPublish );
