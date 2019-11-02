const algolia = require('algoliasearch');
const client = algolia('89688AGZTW', 'b16047035e242f87109b3247c9de1f1b');
let index = {};

export const AlgoliaSearch = {
    data() {
        return {}
    },
    methods : {
        $_initIndex(indexName) {
            if (!index.hasOwnProperty('indexName') || index.indexName !== indexName) index = client.initIndex(indexName);
        },
        $_search(indexName, searchableAttr, search, cb) {
            this.$_initIndex(indexName);

            index.search(
                {
                    query                        : search,
                    restrictSearchableAttributes : searchableAttr
                })
                .then(({hits} = {}) => {
                    if (undefined !== cb) cb(hits);
                })
                .catch(e => {
                    console.log(e);
                    console.log(e.debugData);
                });
        }
    }
};

