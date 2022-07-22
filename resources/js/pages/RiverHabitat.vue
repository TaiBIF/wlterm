<template>
    <div class="px-4">
        <h6>河道棲地&nbsp;<small class="text-muted">共 {{ total }} 筆</small></h6>
        <div class="chart-container max-w-[1240px]">
            <div class="flex justify-center p-4">
                <div>
                    <highcharts :options="substrateChartOptions"></highcharts>
                </div>
                <div>
                    <highcharts :options="morphologyChartOptions"></highcharts>
                </div>
            </div>
            <div class="text-center py-2">
                <button v-for="station in stations"
                        :class="{'bgg200': activeStationKey === station.id}"
                        class="btn rounded-none ml-1"
                        v-on:click="toggleStation(station.id)">{{ station.locality_chinese }} (#{{ station.id }})
                </button>
            </div>
            <hr class="my-3"/>
        </div>
        <sheet
            v-model="sheetValues"
            :columns="columns"
            :data="records"
            :is-loading="isLoading"
            v-on:search="search"
            v-on:sort="sort"
        ></sheet>
        <div class="myexcel text-muted caption">
            河道棲地&nbsp;共 {{ total }} 筆
        </div>
    </div>
</template>

<script>
import sheet from '../components/sheet';
import queryString from 'querystring';
import Highcharts from 'highcharts';

const chartOption = {
    chart: {
        type: 'column',
        height: 300,
    },
    title: {
        text: '底質百分比'
    },
    xAxis: {
        categories: [],
    },
    yAxis: {
        min: 0,
        title: {
            text: '%'
        }
    },
    colors: ['#023e8a', '#0077b6', '#00b4d8', '#ade8f4', '#2a9d8f', '#63af90'],
    plotOptions: {
        column: {
            stacking: 'percent'
        }
    },
    tooltip: {
        pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y}</b> ({point.percentage:.0f}%)<br/>',
        shared: true,
    },
    legend: {
        layout: 'vertical',
        align: 'right',
        verticalAlign: 'middle'
    },
    series: [],
}

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
                {type: 'text', title: '日期', width: '100', name: 'date', searchable: true},
                {type: 'text', title: '溪名', width: '80', name: 'river', searchable: true},
                {type: 'text', title: '測站', width: '60', name: 'station_id', searchable: true},
                {type: 'text', title: '測站站名', width: '100', name: 'locality_chinese', searchable: true},
                {type: 'text', title: '斷面', width: '80', name: 'section', searchable: false},
                {type: 'text', title: '河寬(公尺)', width: '80', name: 'width'},
                {type: 'text', title: '測量位置', width: '80', name: 'measure_point'},
                {type: 'text', title: '流速(m/s)', width: '80', name: 'flow_velocity'},
                {type: 'text', title: '底質', width: '80', name: 'substrate_name'},
                {type: 'text', title: '水深(公尺)', width: '80', name: 'depth'},
                {type: 'text', title: '福祿數', width: '150', name: 'fr'},
                {type: 'text', title: '棲地環境類型', width: '100', name: 'morphology_name'},
            ],
            total: 0,
            activeStationKey: '',
            stations: [],
            substrateChartOptions: {
                ...chartOption,
                title: {
                    text: '底質百分比'
                },
            },
            morphologyChartOptions: {
                ...chartOption,
                title: {
                    text: '棲地百分比'
                },
            },
        }
    },
    components: {
        sheet,
    },
    mounted() {
        this.search();

        const app = this;
        const intersectionObserver = new IntersectionObserver(function (entries) {
            if (entries[0].intersectionRatio > 0) {
                app.loadMore();
            }
        });
        intersectionObserver.observe(document.querySelector('.caption'));
    },
    methods: {
        fetchReportData() {
            const params = this.sheetValues.searchParams;

            this.$http.get(`
                /api/river-habitat-report?${ queryString.stringify(params) }&active_station=${this.activeStationKey}`
            )
                .then(({data: {data: {categories, morphology, substrate, stations}}}) => {
                    this.activeStationKey = this.activeStationKey || stations[0].id;
                    this.morphologyChartOptions.xAxis.categories = categories;
                    this.morphologyChartOptions.series = morphology;
                    this.substrateChartOptions.xAxis.categories = categories;
                    this.substrateChartOptions.series = substrate;
                    this.stations = stations;
                });
        },
        fetchData(callback) {
            if (this.isLoading || this.isEnd) {
                return;
            }

            this.isLoading = true;

            const page = this.currentPage + 1;
            this.$http.get(`/api/river-habitat?page=${ page }&${ queryString.stringify(this.sheetValues.searchParams) }&sort=${ this.sortBy }&direction=${ this.direction }`)
                .then(({data: {data, total, currentPage, perPage}}) => {
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
            window.scrollTo(0, 0);

            this.page = 0;
            this.isEnd = false;
            this.currentPage = 0;
            this.fetchData(data => {
                this.records = data;
            })

            if (this.sheetValues.searchParams.station_id) this.activeStationKey = parseInt(this.sheetValues.searchParams.station_id, 10);
            else this.activeStationKey = '';
            this.fetchReportData();
        },
        toggleStation(target) {
            this.activeStationKey = target;
            this.fetchReportData();
        }
    }
}
</script>

<style lang="scss" scoped>
@import './../../sass/fix-table';

.bgg200 {
    background-color: #eee;
}

</style>
