import { registerPlugin } from "@wordpress/plugins";
import { PluginDocumentSettingPanel } from "@wordpress/edit-post";
import { PanelRow, SelectControl } from "@wordpress/components";
import { useState, useEffect } from "@wordpress/element";
import { __ } from "@wordpress/i18n";
import { useEntityProp } from "@wordpress/core-data";
import { useSelect } from "@wordpress/data";

const WPDevBlockBannerSettings = function () {
	// Get the current post type.
	const postType = useSelect((select) => {
		return select("core/editor").getCurrentPostType();
	});

	// If the post type is not "post", return null and disable the panel.
	if (postType !== "wpdev_banner") {
		return null;
	}

	// Get the current post meta.
	const [meta, setMeta] = useEntityProp("postType", "wpdev_banner", "meta");

	return (
		<PluginDocumentSettingPanel
			name="wpdev-block-banner-plugin-settings"
			title={__("Banner Settings")}
			className="wpdev-block-banner-plugin-settings"
		>
			<PanelRow>
				<SelectControl
					label={__("Show:")}
					value={meta.wpdev_banner_show_on}
					options={[
						{ label: __("Not showing"), value: "" },
						{ label: __("Show Globally"), value: "all" },
					]}
					onChange={(value) => setMeta({ wpdev_banner_show_on: value })}
				/>
			</PanelRow>
			<PanelRow>
				<SelectControl
					label={__("Cookie Expiration:")}
					value={meta.wpdev_banner_cookie_expiration}
					options={[
						{ label: __("No cookie"), value: "" },
						{ label: __("Session"), value: "session" },
						{ label: __("1 Day"), value: "1" },
						{ label: __("1 Week"), value: "7" },
						{ label: __("1 Month"), value: "30" },
						{ label: __("1 Year"), value: "365" },
					]}
					onChange={(value) =>
						setMeta({ wpdev_banner_cookie_expiration: value })
					}
				/>
			</PanelRow>
		</PluginDocumentSettingPanel>
	);
};

registerPlugin("wpdev-block-banner-plugin-settings", {
	render: WPDevBlockBannerSettings,
});
