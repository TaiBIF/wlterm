<template>
    <div>
        <h6>河道斷面&nbsp;<small class="text-muted">共 {{ total }} 筆</small></h6>
        <sheet
            :data="records"
            :columns="columns"
            :is-loading="isLoading"
            :section-url="true"
            :sortable="false"
            v-on:sort="sort"
            v-on:search="search"
        ></sheet>
        <div class="myexcel text-muted caption">
            河道斷面&nbsp;共 {{ total }} 筆
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
                    { type: 'text', title: '日期', width: '100', name: 'date', searchable: true },
                    { type: 'text', title: '溪名', width: '80', name: 'river' },
                    { type: 'text', title: '斷面', width: '100', name: 'section', searchable: true },
                    { type: 'text', title: '點位', width: '80', name: 'measure_point' },
                    { type: 'text', title: '方向角', width: '140', name: 'angle' },
                    { type: 'text', title: '弧度角', width: '140', name: 'radian' },
                    { type: 'text', title: '水平距(公尺)', width: '80', name: 'horizontal_distance' },
                    { type: 'text', title: '經度', width: '80', name: 'longitude' },
                    { type: 'text', title: '緯度', width: '80', name: 'latitude' },
                    { type: 'text', title: '海拔高度(公尺)', width: '100', name: 'altitude' },
                    { type: 'text', title: '後視點與轉點', width: '100', name: 'bs_and_tp' },
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
                this.$http.get(`/api/river-section?page=${page}&${this.query}`)
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
