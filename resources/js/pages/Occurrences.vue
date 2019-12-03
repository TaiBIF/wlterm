<template>
    <div>
        <h6>生物物種調查紀錄<small class="text-muted">共 {{ total }} 筆</small></h6>
        <sheet
            :data="occurrences"
            :columns="columns"
            :is-loading="isLoading"
            :occurence-content="true"
            v-on:sort="sort"
            v-on:search="search"
        ></sheet>
        <div class="myexcel text-muted caption">
            生物物種調查紀錄&nbsp;共 {{ total }} 筆
        </div>
    </div>
</template>

<script>
    import sheet from "../components/sheet";
    import queryString from 'querystring';
    export default {
        name: 'Occurrences',
        components: {
            sheet,
        },
        data() {
            const app = this;
            return {
                occurrences: [],
                isLoading: false,
                currentPage: 0,
                isEnd: false,
                sortBy: '',
                direction: '',
                columns: [
                    { type: 'text', title: '測站', width: '50', name: 'sid', searchable: true },
                    { type: 'text', title: '門名', width: '80', name: 'phylum', searchable: true },
                    { type: 'text', title: '綱名', width: '100', name: 'class', searchable: true },
                    { type: 'text', title: '目名', width: '100', name: 'order', searchable: true },
                    { type: 'text', title: '科名', width: '100', name: 'family', searchable: true },
                    { type: 'text', title: '學名', width: '100', name: 'scientific_name', searchable: true },
                    { type: 'text', title: '中文名', width: '100', name: 'chinese', searchable: true },
                    { type: 'text', title: '調查日 ', width: '100', name: 'date', searchable: true },
                    { type: 'text', title: '地點 ', width: '100', name: 'locality_chinese', searchable: true },
                    { type: 'text', title: '緯度 ', width: '80', name: 'latitude', searchable: true },
                    { type: 'text', title: '經度 ', width: '80', name: 'longitude', searchable: true },
                    { type: 'text', title: '調查者', width: '80', name: 'collector_chinese', searchable: true },
                    { type: 'text', title: '調查法', width: '100', name: 'examine_way', searchable: true },
                    { type: 'text', title: '鑒定者', width: '80', name: 'identified_by_chinese', searchable: true },
                ],
                total: 0,
                searchParams: {}
            }
        },
        mounted() {
            const app = this;
            const intersectionObserver = new IntersectionObserver(function(entries) {
                if (entries[0].intersectionRatio > 0){
                    app.loadMore();
                }
            });
            // Start observing
            intersectionObserver.observe(document.querySelector('.caption'));
        },
        computed: {
            query() {
                return queryString.stringify(this.searchParams);
            },
        },
        methods: {
            fetchData(callback) {
                if (this.isLoading) {
                    return;
                }

                if (this.isEnd) {
                    return;
                }

                 this.isLoading = true;

                const page = this.currentPage + 1;
                this.$http.get(`/api/occurrences?page=${page}&sort=${this.sortBy}&direction=${this.direction}&${this.query}`)
                    .then(({ data: { data, total, currentPage, perPage } }) => {
                        if (perPage > data.length || 0 === data.length) {
                            this.isEnd = true;
                            return;
                        }
                        callback(data);
                        this.total = total;
                        this.currentPage = currentPage;
                        this.isLoading = false;
                    });
            },
            sort(column, direction) {
                this.sortBy = this.columns[column].name;
                this.direction = direction ? 'desc' : 'asc';
                this.search();
            },
            loadMore() {
                this.fetchData(data => {
                    this.occurrences = this.occurrences.concat(data);
                })
            },
            search(query) {
                if (query) {
                    this.searchParams = query;
                }

                window.scrollTo(0, 0);

                this.isEnd = false;
                this.currentPage = 0;
                this.fetchData(data => {
                    this.occurrences = data;
                })
            }
        }
    }
</script>


<style lang="scss" scoped>
   @import '@/sass/fix-table';
</style>
