/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */
import { __ } from "@wordpress/i18n";

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import { useBlockProps, BlockControls } from "@wordpress/block-editor";
import {
	Placeholder,
	ToolbarGroup,
	ToolbarButton,
} from "@wordpress/components";
import { stretchWide, edit } from "@wordpress/icons";

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import "./editor.scss";

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @return {WPElement} Element to render.
 */
export default function Edit() {
	const editBanners = () => {
		window.open("/wp-admin/edit.php?post_type=wpdev_banner", "_blank");
	};

	return (
		<div {...useBlockProps()}>
			<BlockControls>
				<ToolbarGroup>
					<ToolbarButton
						icon={edit}
						label={__("Edit Banners", "block-banner-plugin")}
						onClick={editBanners}
					/>
				</ToolbarGroup>
			</BlockControls>
			<Placeholder
				icon={stretchWide}
				label={__("Block Banner Plugin", "block-banner-plugin")}
				instructions={__("If enabled, your banner will be displayed here.")}
			/>
		</div>
	);
}
