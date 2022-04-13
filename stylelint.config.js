module.exports = {
	extends: [
		'@ntvr/stylelint-config-ntvr',
		'@ntvr/stylelint-config-ntvr-order',
	],
	plugins: ['stylelint-order'],
	rules: {
		'at-rule-no-unknown': [
			true,
			{
				ignoreAtRules: [
					'content',
					'each',
					'else',
					'else if',
					'error',
					'for',
					'function',
					'if',
					'include',
					'mixin',
					'return',
					'use',
				],
			},
		],
	},
};
