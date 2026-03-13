import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl, Placeholder } from '@wordpress/components';

export default function Edit( { attributes, setAttributes } ) {
	const { metaKey, label } = attributes;
	const blockProps = useBlockProps();

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Meta Field Settings', 'woo-block-product-meta' ) }>
					<TextControl
						label={ __( 'Meta Key', 'woo-block-product-meta' ) }
						help={ __( 'Enter the product meta key to display (e.g. _sku, _weight).', 'woo-block-product-meta' ) }
						value={ metaKey }
						onChange={ ( value ) => setAttributes( { metaKey: value } ) }
					/>
					<TextControl
						label={ __( 'Label', 'woo-block-product-meta' ) }
						help={ __( 'Optional label shown before the value.', 'woo-block-product-meta' ) }
						value={ label }
						onChange={ ( value ) => setAttributes( { label: value } ) }
					/>
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps }>
				{ metaKey ? (
					<span className="woo-product-meta-block__preview">
						{ label && (
							<span className="woo-product-meta-block__label">{ label }</span>
						) }
						<span className="woo-product-meta-block__value">
							{ /* Value rendered server-side on the frontend */ }
							{ __( '(meta value will display here)', 'woo-block-product-meta' ) }
						</span>
					</span>
				) : (
					<Placeholder
						icon="tag"
						label={ __( 'Product Meta Block', 'woo-block-product-meta' ) }
						instructions={ __( 'Enter a meta key in the block settings panel to display a product meta field.', 'woo-block-product-meta' ) }
					/>
				) }
			</div>
		</>
	);
}
