document.addEventListener("DOMContentLoaded", () => {
	const banner = document.querySelector(
		".wp-block-block-banner-plugin-banner-placeholder",
	);

	if (banner) {
		const cookieExpiration = banner.getAttribute(
			"data-banner-cookie-expiration",
		);
		const cookieName = banner.getAttribute("data-banner-cookie-name");

		const showBanner = () => {
			// Add the banner to the top of the page.
			banner.style.display = "block";

			// Set the cookie.
			if (!cookieExpiration) {
				return;
			}
			document.cookie = `${cookieName}=1; max-age=${filterCookieExpiration(
				cookieExpiration,
			)}; path=/`;
		};

		const filterCookieExpiration = (cookieExpiration) => {
			if (cookieExpiration === "session" || cookieExpiration === "") {
				return "";
			}

			// Convert cookie days to seconds
			return cookieExpiration * 24 * 60 * 60;
		};

		// Check if the cookie exists.
		if (cookieExpiration) {
			const cookieValue = document.cookie.replace(
				new RegExp(
					"(?:(?:^|.*;\\s*)" +
						encodeURIComponent(cookieName).replace(/[\-\.\+\*]/g, "\\$&") +
						"\\s*\\=\\s*([^;]*).*$)|^.*$",
				),
				"$1",
			);

			if (cookieValue !== "1") {
				showBanner();
			}
		} else {
			showBanner();
		}

		// Add an event listener to the close button.
		const closeButton = banner.querySelector(
			"button.wp-block-block-banner-plugin-close-button",
		);

		if (closeButton) {
			closeButton.addEventListener("click", () => {
				banner.style.display = "none";
			});
		}
	}
});
