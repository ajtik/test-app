module.exports = {
	extends: [
		'airbnb',
		'plugin:jest/recommended',
		'plugin:testing-library/recommended',
	],
	parser: 'babel-eslint',
	rules: {
		indent: [
			'error',
			'tab',
			{
				SwitchCase: 1,
				VariableDeclarator: 1,
				outerIIFEBody: 1,
				MemberExpression: 1,
				FunctionDeclaration: {
					parameters: 1,
					body: 1
				},
				FunctionExpression: {
					parameters: 1,
					body: 1
				},
				CallExpression: {
					arguments: 1
				},
			},
		],
		'import/order': [
			'error',
			{
				groups: [
					'builtin',
					'external',
					'internal',
					['parent', 'sibling'],
					'index',
				],
				pathGroups: [
					{
						pattern: '..',
						group: 'parent',
					},
				],
				'newlines-between': 'always-and-inside-groups',
			},
		],
		'max-len': [
			'error',
			120,
			4,
			{
				'ignoreUrls': true,
				'ignoreComments': false,
				'ignoreRegExpLiterals': true,
				'ignoreStrings': true,
				'ignoreTemplateLiterals': true
			}
		],
		'no-tabs': 'off',
		'react/jsx-indent': [
			'error',
			'tab'
		],
		'react/jsx-indent-props': [
			'error',
			'tab'
		],
		'react/react-in-jsx-scope': 'off',
	},
	plugins: [
		'jest',
		'testing-library',
	],
	overrides: [
		{
			files: [
				'frontend/setupTests.js',
				'frontend/**/__tests__/**/*',
				'frontend/**/*.{spec,test}.*'
			],
			rules: {
				'import/no-extraneous-dependencies': [
					'error',
					{
						devDependencies: true,
					},
				],
			},
		},
	],
}
