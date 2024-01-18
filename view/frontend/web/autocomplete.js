/**
 * Documentation: https://www.algolia.com/doc/integration/magento-2/customize/custom-front-end-events/
 **/

/**
 * Autocomplete hook method
 * autocomplete.js documentation: https://github.com/algolia/autocomplete.js
 *
 * NOTE: This module is for demonstration purposes only and is intended to show how front end event hooks might be used.
 * Be mindful that introducing things like new sources and plugins can affect the layout of your final render.
 * Utilize DOM or CSS to control the final presentation as needed.
 **/

// NOTE: The algoliaRecentSearches dependency is optional and is only supplied for demonstration of inclusion of the recent searches plugin
define(['jquery', 'algoliaAnalytics', 'algoliaBundle', 'suggestionsHtml', 'algoliaRecentSearches', 'algoliaCommon'], function (
    $,
    algoliaAnalyticsWrapper,
    algoliaBundle,
    suggestionsHtml,
    algoliaRecentSearches
) {

    algolia.registerHook('afterAutocompletePlugins', (plugins, searchClient) => {
        // Modify an existing plugin like Query Suggestions (use Algolia instead of Magento)
        // See https://www.algolia.com/doc/guides/building-search-ui/ui-and-ux-patterns/query-suggestions/js/

        const pluginIndex = plugins.findIndex(plugin => plugin.name === 'aa.querySuggestionsPlugin');
        if (pluginIndex > -1) {
            // Replace the entire plugin
            plugins[pluginIndex] = algoliaBundle.createQuerySuggestionsPlugin.createQuerySuggestionsPlugin({
                searchClient,
                // Build your suggestions index per https://www.algolia.com/doc/guides/building-search-ui/ui-and-ux-patterns/query-suggestions/js/#implementing-query-suggestions
                indexName: 'magento2_1mcdocker1_default_products_query_suggestions',
                getSearchParams() {
                    return {hitsPerPage: algoliaConfig.autocomplete.nbOfProductsSuggestions};
                },
                transformSource({source}) {
                    return {
                        ...source,
                        getItemUrl({item}) {
                            return algoliaConfig.resultPageUrl + `?q=${item.query}`;
                        },
                        templates: {
                            noResults({html}) {
                                return suggestionsHtml.getNoResultHtml({html});
                            },
                            header({html, items}) {
                                return suggestionsHtml.getHeaderHtml({html});
                            },
                            item({item, components, html}) {
                                return suggestionsHtml.getItemHtml({item, components, html})
                            },
                            footer({html, items}) {
                                return suggestionsHtml.getFooterHtml({html})
                            },
                        },
                    };
                },
            });
        }


        // Install a new plugin like "recent searches"
        // See: https://www.algolia.com/doc/ui-libraries/autocomplete/api-reference/autocomplete-plugin-recent-searches/createLocalStorageRecentSearchesPlugin/

        const recentSearchesPlugin = algoliaRecentSearches.createLocalStorageRecentSearchesPlugin({
            key: 'navbar',
            transformSource({source}) {
                return {
                    ...source,
                    templates: {
                        ...source.templates,
                        noResults({html}) {
                            return suggestionsHtml.getNoResultHtml({html});
                        },
                        header: () => 'Recent searches',
                        item: ({item, html}) => {
                            // console.log("Item:", item);
                            return html`<a class="aa-ItemLink" href="${algoliaConfig.resultPageUrl}?q=${encodeURIComponent(item.label)}">${item.label}</a>`;
                        }
                    }
                }
            }
        });



        // Replace existing plugins completely (e.g. to replace query suggestions)
        // return [recentSearchesPlugin];

        // or add to existing plugins (requires additional front end formatting via CSS etc.)
         plugins.unshift(recentSearchesPlugin);

        return plugins;
    });

    algolia.registerHook(
        "afterAutocompleteSources", //after
        function (sources, searchClient) {
            console.log("In hook method to modify autocomplete data sources");
            console.log(sources);
            index = sources.findIndex(resp => resp.sourceId=='products');
            sources.push(...sources .splice(0, index));
            console.log("afterChange"+sources);
            return sources;
        }
    );

    algolia.registerHook("afterAutocompleteOptions", function (options) {
        console.log("In hook method to modify autocomplete options");
        console.log(options);

        // Modify autocomplete options
        // options.openOnFocus = true;

        return options;
    });

    /**
     * InstantSearch hook methods
     * IS.js v2 documentation: https://community.algolia.com/instantsearch.js/
     * IS.js v4 documentation: https://www.algolia.com/doc/api-reference/widgets/instantsearch/js/
     **/

    algolia.registerHook(
        "beforeInstantsearchInit",
        function (instantsearchOptions, algoliaBundle) {
            console.log("In method to modify instantsearch options");

            // Modify instant search options

            return instantsearchOptions;
        }
    );

    algolia.registerHook(
        "beforeWidgetInitialization",
        function (allWidgetConfiguration, algoliaBundle) {
            console.log("In hook method to modify instant search widgets");
            /*$.each(allWidgetConfiguration, function (widgetType) {
                if (widgetType == 'hits') {
                    var callbackTransform = allWidgetConfiguration[widgetType].transformItems;
                    allWidgetConfiguration[widgetType].transformItems = function(items) {
                        items = callbackTransform(items);
                        return items.map(function (item) {

                            // add your modification to item result
                            item.exampleNewVariable = 'This is an example that will be applied to all items.';

                            return item;
                        })
                    }
                }
            });*/
            //allWidgetConfiguration['configure'] = allWidgetConfiguration['configure'] || {}

            // change hitsPerPage
           // allWidgetConfiguration['configure'].hitsPerPage = 20;

           // const wrapper = document.getElementById('instant-search-facets-container');

           /* const widgetConfig = {
                container: wrapper.appendChild(createISWidgetContainer('in_stock')),
                attribute: 'in_stock',
                on: 1,
                templates: {
                    label: 'In Stock'
                }
            };*/

            /*if (typeof allWidgetConfiguration['toggleRefinement'] === 'undefined') {
                allWidgetConfiguration['toggleRefinement'] = [widgetConfig];
            } else {
                allWidgetConfiguration['toggleRefinement'].push(widgetConfig);
            }*/
            return allWidgetConfiguration;
        }
    );

    algolia.registerHook(
        "beforeInstantsearchStart",
        function (search, algoliaBundle) {
            console.log(
                "In hook method to modify instant search instance before search started"
            );

            // Modify instant search instance before search started

            return search;
        }
    );

    algolia.registerHook(
        "afterInstantsearchStart",
        function (search, algoliaBundle) {
            console.log(
                "In hook method to modify instant search instance after search started"
            );

            // Modify instant search instance after search started

            return search;
        }
    );
});
