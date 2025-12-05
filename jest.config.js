export default {
    testEnvironment: 'node',
    testEnvironmentOptions: {
        customExportConditions: ['node', 'node-addons']
    },
    transform: {},
    moduleFileExtensions: ['js', 'mjs'],
    testMatch: ['**/tests/js/**/*.test.js', '**/tests/js/**/*.test.mjs'],
    verbose: true,
    collectCoverageFrom: [
        'resources/js/**/*.js',
        '!resources/js/app.js',
        '!resources/js/bootstrap.js'
    ]
};
