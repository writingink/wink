<script type="text/ecmascript">
    import loadsEntries from '../loadsEntries';
    import FiltersDropdown from '../../partials/FilterDropdown.vue';

    export default {
        mixins: [loadsEntries],


        components: {
            'filters': FiltersDropdown
        },


        /**
         * The component's data.
         */
        data() {
            return {
                baseURL: '/api/categories',
                entries: [],
                hasMoreEntries: false,
                nextPageUrl: null,
                loadingMoreEntries: false,
                ready: false,
                searchQuery: ''
            };
        },


        /**
         * Prepare the component.
         */
        mounted() {
            document.title = "Categories — Wink.";

            this.loadEntries();
        },
    }
</script>

<template>
    <div>
        <page-header>
            <div slot="right-side">
                <router-link :to="{name:'category-new'}" class="py-1 px-2 btn-primary text-sm">
                    New Category
                </router-link>
            </div>
        </page-header>

        <div class="container">
            <div class="mb-10 flex items-center">
                <h1 class="inline font-semibold text-3xl mr-auto">Categories</h1>

                <filters @showing="focusSearchInput" :is-filtered="isFiltered">
                    <input type="text" class="input mt-0 w-full"
                           placeholder="Search..."
                           v-model="searchQuery"
                           ref="searchInput">
                </filters>
            </div>

            <preloader v-if="!ready"></preloader>

            <div v-if="ready && entries.length == 0 && !isFiltered">
                <p>No categories were found, start by
                    <router-link :to="{name:'category-new'}" class="no-underline text-primary hover:text-primary-dark">adding some categories</router-link>
                    .
                </p>
            </div>

            <div v-if="ready && entries.length == 0 && isFiltered">
                No categories matched the given search.
            </div>

            <div v-if="ready && entries.length > 0">
                <div v-for="entry in entries" :key="entry.id" class="border-t border-very-light flex items-center">
                    <div class="py-4" :title="entry.title">
                        <h2 class="text-xl font-semibold">
                            <router-link :to="{name:'category-edit', params:{id: entry.id}}" class="no-underline text-text-color">
                                {{truncate(entry.name, 80)}}
                            </router-link>
                        </h2>
                    </div>
                </div>


                <tr v-if="hasMoreEntries">
                    <td colspan="100" class="text-center py-3">
                        <small><a href="#" v-on:click.prevent="loadOlderEntries" v-if="!loadingMoreEntries">Load Older Categories</a></small>

                        <small v-if="loadingMoreEntries">Loading...</small>
                    </td>
                </tr>
            </div>
        </div>
    </div>
</template>
