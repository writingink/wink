module.exports = {
    computed: {
        isFiltered(){
            return !!this.searchQuery.length;
        }
    },


    methods: {
        loadEntries(){
            this.http().get(this.baseURL).then(response => {
                this.entries = response.data.data;

                this.hasMoreEntries = !!response.data.next_page_url;

                this.nextPageUrl = response.data.next_page_url;

                this.ready = true;
            });
        },


        /**
         * Load the older entries.
         */
        loadOlderEntries(){
            this.loadingMoreEntries = true;

            this.http().get(this.nextPageUrl).then(response => {
                this.entries.push(...response.data.data);

                this.hasMoreEntries = !!response.data.next_page_url;

                this.nextPageUrl = response.data.next_page_url;

                this.loadingMoreEntries = false;
            });
        },


        /**
         * Filter the entries by the search query.
         */
        searchEntries(){
            if (!this.searchQuery) {
                this.ready = false;
            }

            this.debouncer(() => {
                this.ready = false;

                this.http().get(this.baseURL + '?search=' + this.searchQuery).then(response => {
                    this.entries = response.data.data;

                    this.hasMoreEntries = !!response.data.next_page_url;

                    this.nextPageUrl = response.data.next_page_url;

                    this.ready = true;
                });
            });
        },


        /**
         * Focus the search input when the filter dropdown opens.
         */
        focusSearchInput(){
            this.$nextTick(() => {
                this.$refs.searchInput.focus();
            });
        }
    }
};