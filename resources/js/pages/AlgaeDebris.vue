<template>
    <div>
        <h6>藻類與碎屑&nbsp;<small class="text-muted">共 {{ total }} 筆</small></h6>
        <sheet
            :data="records"
            :columns="columns"
            :is-loading="isLoading"
            :record-url="true"
            :type="'algae-debris'"
            v-on:sort="sort"
            v-on:search="search"
        ></sheet>
        <div class="myexcel text-muted caption">
            藻類與碎屑&nbsp;共 {{ total }} 筆
        </div>
    </div>
</template>

<script>
    import sheet from '../components/sheet';
    import queryString from 'querystring';
    export default {
        name: 'WaterQuality',
        data() {
            return {
                isLoading: false,
                records: [],
                sortBy: '',
                direction: '',
                searchParams: {},
                columns: [
                    { type: 'text', title: '測站', width: '50', name: 'station_id', searchable: true },
                    { type: 'text', title: '測站站名', width: '100', name: 'locality_chinese', searchable: true },
                    { type: 'text', title: '緯度', width: '100', name: 'latitude' },
                    { type: 'text', title: '經度', width: '120', name: 'longitude' },
                    { type: 'text', title: '高度', width: '80', name: 'maximum_elevation' },
                    { type: 'text', title: '深度', width: '80', name: 'maximum_depth' },
                    { type: 'text', title: '調查日期', width: '170', name: 'date', searchable: true },
                ],
                total: 0,
            }
        },
        components: {
            sheet,
        },
        computed: {
            query() {
                return queryString.stringify(this.searchParams);
            },
        },
        mounted() {
            this.search();

            const app = this;
            const intersectionObserver = new IntersectionObserver(function(entries) {
                if (entries[0].intersectionRatio > 0){
                    app.loadMore();
                }
            });
            intersectionObserver.observe(document.querySelector('.caption'));
        },
        methods: {
            fetchData(callback) {
                if (this.isLoading || this.isEnd) {
                    return;
                }

                this.isLoading = true;

                const page = this.currentPage + 1;
                this.$http.get(`/api/algae-debris?page=${page}&${this.query}`)
                    .then(({ data: { data, total, currentPage, perPage } }) => {
                        if (perPage > data.length || 0 === data.length) {
                            this.isEnd = true;
                        }
                        callback(data);
                        this.total = total;
                        this.currentPage = currentPage;
                        this.isLoading = false;
                    });
            },
            sort(column, direction) {
                this.direction = direction ? 'asc' : 'desc';
                this.sortBy = this.columns[column].name
                this.search();
            },
            loadMore() {
                this.fetchData(data => {
                    this.records = this.records.concat(data);
                })
            },
            search(query) {
                if (query) {
                    this.searchParams = query;
                }

                window.scrollTo(0, 0);

                this.page = 0;
                this.isEnd = false;
                this.currentPage = 0;
                this.fetchData(data => {
                    this.records = data;
                })
            }
        }
    }
</script>

<style lang="scss" scoped>
    @import '@/sass/fix-table';
</style>
