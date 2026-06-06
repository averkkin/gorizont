module.exports = {
    plugins: {
        autoprefixer: {
            overrideBrowserslist: ['last 2 versions', '> 1%']
        },

        cssnano: {
            preset: [
                'default',
                {
                    discardComments: { removeAll: true },
                    reduceIdents: false,
                    mergeRules: false,
                    normalizeUrl: false,
                    calc: false
                }
            ]
        }
    }
};