<template>
    <div>
        <h6>測站資料&nbsp;<small class="text-muted">共 {{ total }} 筆</small></h6>
        <sheet
            :data="data"
            :columns="columns"
            :is-loading="isLoading"
            v-model="sheetValues"
            v-on:sort="sort"
            v-on:search="search"
        ></sheet>
        <div class="myexcel text-muted caption">
            測站資料&nbsp;共 {{ total }} 筆
        </div>
    </div>
</template>

<script>
    import sheet from '../components/sheet';
    import queryString from 'queryString';
    export default {
        name: "Stations",
        components: {
            sheet,
        },
        data() {
            return {
                page: 0,
                isEnd: false,
                isLoading: false,
                direction: '',
                sortBy: '',
                data: [],
                columns: [
                    { type: 'text', title: '測站', width: '100', name: 'auto_id', searchable: true },
                    { type: 'text', title: '緯度', width: '100', name: 'latitude' },
                    { type: 'text', title: '經度', width: '120', name: 'longitude' },
                    { type: 'text', title: '誤差(公尺)', width: '80', name: 'coordinate_precision' },
                    { type: 'text', title: '地名', width: '200', name: 'locality_chinese', searchable: true },
                    { type: 'text', title: '最高海拔(公尺)', width: '60', name: 'maximum_elevation' },
                    { type: 'text', title: '最低海拔(公尺)', width: '60', name: 'minimum_elevation' },
                    { type: 'text', title: '地點描述', width: '250', name: 'locality_describe' },
                    { type: 'text', title: '備註', width: '100', name: 'note' },
                ],
                total: 0,
                sheetValues: {
                    searchParams: {}
                },
            }
        },
        mounted() {
            const app = this;
            const intersectionObserver = new IntersectionObserver(function(entries) {
                if (entries[0].intersectionRatio > 0){
                    app.loadMore();
                }
            });
            intersectionObserver.observe(document.querySelector('.caption'));
        },
        methods: {
            sort(column, direction) {
                this.direction = direction ? 'asc' : 'desc';
                this.sortBy = this.columns[column].name
                this.search();
            },
            search() {
                window.scrollTo(0, 0);

                const page = 1;
                this.isLoading = true;
                this.isEnd = false;
                this.data = [];
                this.$http.get(`/api/stations?page=${page}&sort=${this.sortBy}&direction=${this.direction}&${queryString.stringify(this.sheetValues.searchParams)}`)
                    .then(({ data: { data, total, currentPage, perPage } }) => {
                        if (perPage > data.length || 0 === data.length) {
                            this.isEnd = true;
                        }
                        this.data = data;
                        this.total = total;
                        this.page = page;
                        this.isLoading = false;
                    });
            },
            loadMore() {
                if (this.isEnd || this.isLoading) {
                    return;
                }

                this.isLoading = true;
                const page = this.page + 1;

                this.$http.get(`/api/stations?page=${page}&sort=${this.sortBy}&direction=${this.direction}&${this.query}`)
                    .then(({ data: { data, total, currentPage, perPage } }) => {
                        if (perPage > data.length || 0 === data.length) {
                            this.isEnd = true;
                        }
                        this.data = this.data.concat(data);
                        this.total = total;
                        this.page = page;
                        this.isLoading = false;
                    });
            }
        }
    }
</script>

<style lang="scss" scoped>
    @import '@/sass/fix-table';
</style>
