<template>
    <div>
        <h6>溫度監測&nbsp;<small class="text-muted">共 {{ total }} 筆</small></h6>
        <div class="chart-container">
            <highcharts :options="chartOptions"></highcharts>
        </div>
        <sheet
            :data="records"
            :columns="columns"
            :is-loading="isLoading"
            v-model="sheetValues"
            v-on:sort="sort"
            v-on:search="search"
        ></sheet>
        <div class="myexcel text-muted caption">
            溫度監測&nbsp;共 {{ total }} 筆
        </div>
    </div>
</template>

<script>
    import sheet from '../components/sheet';
    import queryString from 'querystring';
    import Highcharts from 'highcharts';

    export default {
        name: 'Temperature',
        data() {
            return {
                isLoading: false,
                records: [],
                sortBy: '',
                direction: '',
                sheetValues: {
                    searchParams: {},
                },
                columns: [
                    { type: 'text', title: '測站', width: '50', name: 'station_id', searchable: true },
                    { type: 'text', title: '測站站名', width: '100', name: 'locality_chinese', searchable: true },
                    { type: 'text', title: '調查日期', width: '100', name: 'date', searchable: true },
                    { type: 'text', title: '氣溫(℃)', width: '60', name: 'air' },
                    { type: 'text', title: '水溫(℃)', width: '60', name: 'water' },
                    { type: 'text', title: '土表溫(℃)', width: '80', name: 'soil_0cm' },
                    { type: 'text', title: '土下 25cm', width: '80', name: 'soil_25cm' },
                    { type: 'text', title: '土下 50cm', width: '80', name: 'soil_50cm' },
                    { type: 'text', title: '土下 65cm', width: '80', name: 'soil_65cm' },
                    { type: 'text', title: '土下 90cm', width: '80', name: 'soil_90cm' },
                ],

                chartOptions: {
                    chart: {
                        type: 'spline',
                        width: 860,
                        height: 300,
                    },
                    title: {
                        text: '溫度監測歷年紀錄'
                    },
                    xAxis: {
                        type: 'datetime',
                        labels: {
                            formatter: function () {
                                return Highcharts.dateFormat('%Y %b', this.value);
                            }
                        },
                    },
                    yAxis: {
                        title: {
                            text: '溫度(℃)'
                        }
                    },
                    tooltip: {
                        formatter: function () {
                            return '<b>' + this.series.name + '</b><br/>' + Highcharts.dateFormat('%Y-%b-%e', this.x, true) + '<br/>' + this.y + ' ℃';
                        },
                        backgroundColor: 'rgba(0, 0, 0, .75)',
                        borderWidth: 2,
                        style: {
                            color: '#CCCCCC'
                        }
                    },
                    legend: {
                        layout: 'vertical',
                        align: 'right',
                        verticalAlign: 'middle'
                    },
                    series: [],
                },
                total: 0,
            }
        },
        components: {
            sheet,
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

            // fetch report data
            this.$http.get(`/api/temperature-report`)
                .then(({ data: { dates, data } }) => {
                    this.chartOptions.xAxis.categories = dates;
                    this.chartOptions.series = data;
                });
        },
        methods: {
            fetchData(callback) {
                if (this.isLoading || this.isEnd) {
                    return;
                }

                this.isLoading = true;

                const page = this.currentPage + 1;
                this.$http.get(`/api/temperature?page=${page}&sort=${this.sortBy}&direction=${this.direction}&${queryString.stringify(this.sheetValues.searchParams)}`)
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
                this.currentPage = 0;
                this.records = [];
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
