const defaultConfig = require("@wordpress/scripts/config/webpack.config");

var config = {
	...defaultConfig,
	entry: {
		...defaultConfig.entry(),
		"editor/index": "./src/editor/index.js",
		"frontend/index": "./src/frontend/index.js",
	},
};

// Return Configuration
module.exports = config;
