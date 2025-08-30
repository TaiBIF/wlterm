<template>
    <div class="px-4">
        <h6 class="sticky left180">環境塑膠微粒&nbsp;<small class="text-muted">共 {{ total }} 筆</small></h6>

        <sheet
            :data="records"
            :columns="columns"
            :is-loading="isLoading"
            v-model="sheetValues"
            v-on:sort="sort"
            v-on:search="search"
        >
            <template v-slot:functions="props">
                <router-link :to="`/env-microplastics/${props.datum.record_id}?type=env-microplastics`" target="_blank">
                    內容
                </router-link>
            </template>
        </sheet>
        <div class="myexcel text-muted caption">
            環境塑膠微粒&nbsp;共 {{ total }} 筆
        </div>
    </div>
</template>

<script>
    import sheet from '../components/sheet';
    import queryString from 'querystring';
    import Highcharts from 'highcharts';

    export default {
        name: 'EnvMicroplastics',
        data() {
            return {
                isLoading: false,
                records: [],
                sortBy: '',
                direction: '',
                sheetValues: {
                    searchParams: {}
                },
                columns: [
                    { type: 'text', title: '測站', width: '60', name: 'station_id', searchable: true },
                    { type: 'text', title: '測站站名', width: '100', name: 'locality_chinese', searchable: true },
                    { type: 'text', title: '調查日期', width: '100', name: 'date', searchable: true },
                    { type: 'text', title: '調查者', width: '100', name: 'collector_chinese', searchable: true },
                    { type: 'text', title: '調查項目', width: '100', name: 'item_chinese', searchable: true },

                    { type: 'text', title: 'PET', width: '80', name: 'pet', searchable: false },
                    { type: 'text', title: 'PE', width: '80', name: 'pe', searchable: false },
                    { type: 'text', title: 'PVC', width: '80', name: 'pvc', searchable: false },
                    { type: 'text', title: 'PP', width: '80', name: 'pp', searchable: false },
                    { type: 'text', title: 'PS', width: '80', name: 'ps', searchable: false },
                    { type: 'text', title: 'PC', width: '80', name: 'pc', searchable: false },
                    { type: 'text', title: 'PA', width: '80', name: 'pa', searchable: false },
                    { type: 'text', title: 'PE+PP', width: '80', name: 'pe_pp', searchable: false },
                    { type: 'text', title: 'Rayon', width: '80', name: 'rayon', searchable: false },
                    { type: 'text', title: 'EEA', width: '80', name: 'eea', searchable: false },
                    { type: 'text', title: '聚酯纖維', width: '80', name: 'polyester', searchable: false },
                    { type: 'text', title: 'PMMA', width: '80', name: 'pmma', searchable: false },
                    { type: 'text', title: 'PU', width: '80', name: 'pu', searchable: false },
                    { type: 'text', title: '採樣量', width: '80', name: 'sample_size', searchable: false },
                    { type: 'text', title: '總量', width: '80', name: 'mps', searchable: false },
                    { type: 'text', title: '單位', width: '80', name: 'unit', searchable: false },
                ],
                total: 0,
            }
        },
        components: {
            sheet,
        },
        mounted() {
            this.sheetValues.searchParams = this.$route.query;
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

                this.$http.get(`/api/env-microplastics?page=${page}&sort=${this.sortBy}&direction=${this.direction}&${queryString.stringify(this.sheetValues.searchParams)}`)
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
            search() {
                window.scrollTo(0, 0);

                this.page = 0;
                this.isEnd = false;
                this.records = [];
                this.currentPage = 0;
                this.fetchData(data => {
                    this.records = data;
                })
            }
        }
    }
</script>

<style lang="scss" scoped>
    @import './../../sass/fix-table';
    .chart-container {
        position: sticky;
        left: 180px;
    }

    .bgg200 {
        background-color: #eee;
    }

    .left180 {
        left: 180px;
        width: calc(100vw - 180px);
    }
</style>
