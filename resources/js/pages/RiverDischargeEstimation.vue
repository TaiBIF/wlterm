<template>
    <div>
        <h6>河川流量推估&nbsp;<small class="text-muted">共 {{ total }} 筆</small></h6>
        <highcharts :options="chartOptions"></highcharts>
        <sheet
            :data="records"
            :columns="columns"
            :is-loading="isLoading"
            v-model="sheetValues"
            v-on:sort="sort"
            v-on:search="search"
        >
        </sheet>
        <div class="myexcel text-muted caption">
            河川流量推估&nbsp;共 {{ total }} 筆
        </div>
    </div>
</template>

<script>
    import sheet from '../components/sheet';
    import queryString from 'querystring';
    import Highcharts from 'highcharts';
    export default {
        name: 'WaterQuality',
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
                    { type: 'text', title: '河川', width: '100', name: 'station_name', searchable: true },
                    { type: 'text', title: '日期', width: '100', name: 'date', searchable: true },
                    { type: 'text', title: '公告流量(cms)', width: '120', name: 'public' },
                    { type: 'text', title: '模擬流量(cms)', width: '140', name: 'simu' },
                    { type: 'text', title: '模擬上界(cms)', width: '100', name: 'max' },
                    { type: 'text', title: '模擬下界(cms)', width: '100', name: 'min' },
                    { type: 'text', title: '思源雨量(mm)', width: '100', name: 'rain_st1' },
                    { type: 'text', title: '桃山雨量(mm)', width: '100', name: 'rain_st2' },
                    { type: 'text', title: '環山雨量(mm)', width: '100', name: 'rain_st3' },
                    { type: 'text', title: '松茂雨量(mm)', width: '100', name: 'rain_st4' },
                ],
                chartOptions: {
                    chart: {
                        type: 'spline',
                        width: 980,
                        height: 300,
                    },
                    title: {
                        text: '河川公告流量歷年紀錄'
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
                            text: '流量(cms)'
                        }
                    },
                    tooltip: {
                        formatter: function () {
                            return '<b>' + this.series.name + '</b><br/>' + Highcharts.dateFormat('%Y-%b-%e', this.x, true) + '<br/>' + this.y + ' cms';
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
            this.$http.get(`/api/river-discharge-estimation-report`)
                .then(({ data: { years, data } }) => {
                    // this.chartOptions.xAxis.categories = years;
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
                this.$http.get(`/api/river-discharge-estimation?page=${page}&sort=${this.sortBy}&direction=${this.direction}&${queryString.stringify(this.sheetValues.searchParams)}`)
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
                this.sortBy = this.columns[column].name;
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
