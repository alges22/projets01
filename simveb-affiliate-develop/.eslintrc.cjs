/* eslint-env node */
require("@rushstack/eslint-patch/modern-module-resolution");

module.exports = {
	root: true,
	env: {
		browser: true,
		node: false,
	},
	plugins: ["frontmatter", "prettier-vue"],
	extends: [
		"plugin:vue/vue3-essential",
		"eslint:recommended",
		"@vue/eslint-config-prettier",
		"plugin:vuejs-accessibility/recommended",
	],
	rules: {
		"no-console": 2,
		"vue/multi-word-component-names": [
			"error",
			{
				ignores: ["default"],
			},
		],
	},
	parserOptions: {
		ecmaVersion: "latest",
	},
	overrides: [
		{
			files: ["*.md"],
			parser: "markdown-eslint-parser",
			extends: ["plugin:prettier/recommended", "plugin:md/recommended"],
			rules: {
				"prettier/prettier": ["error", { parser: "markdown" }],
			},
		},
		{
			files: ["*.vue"],
			extends: ["plugin:vue/vue3-recommended", "plugin:vuejs-accessibility/recommended", "prettier"],
			rules: {
				"vue/script-setup-uses-vars": "error",
				"vue/multi-word-component-names": "off",
				"vue-accessibility/label-has-for": "off",
			},
		},
	],
};
