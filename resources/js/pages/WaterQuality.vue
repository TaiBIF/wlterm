<template>
    <div class="px-4">
        <h6 class="sticky left180">水質監測&nbsp;<small class="text-muted">共 {{ total }} 筆</small></h6>
        <div class="chart-container">
            <highcharts ref="chart" :options="chartOptions"></highcharts>
            <div class="text-center py-2">
                <button class="px-2 py-1 rounded-none ml-1" v-on:click="toggleTab('PH')" :class="{'bgg200': activeTabKey === 'PH'}">氫離子濃度</button>
                <button class="px-2 py-1 rounded-none ml-1" v-on:click="toggleTab('Conductivity')" :class="{'bgg200': activeTabKey === 'Conductivity'}">導電度</button>
                <button class="px-2 py-1 rounded-none ml-1" v-on:click="toggleTab('DO')" :class="{'bgg200': activeTabKey === 'DO'}">溶氧</button>
                <button class="px-2 py-1 rounded-none ml-1" v-on:click="toggleTab('Clarity')" :class="{'bgg200': activeTabKey === 'Clarity'}">濁度</button>
                <button class="px-2 py-1 rounded-none ml-1" v-on:click="toggleTab('Alkali_Silicate')" :class="{'bgg200': activeTabKey === 'Alkali_Silicate'}">矽酸鹽</button>
                <button class="px-2 py-1 rounded-none ml-1" v-on:click="toggleTab('NO3_N')" :class="{'bgg200': activeTabKey === 'NO3_N'}">硝酸鹽氮</button>
                <button class="px-2 py-1 rounded-none ml-1" v-on:click="toggleTab('NO2_N')" :class="{'bgg200': activeTabKey === 'NO2_N'}">亞硝酸鹽氮</button>
                <button class="px-2 py-1 rounded-none ml-1" v-on:click="toggleTab('SO4')" :class="{'bgg200': activeTabKey === 'SO4'}">硫酸鹽</button>
                <button class="px-2 py-1 rounded-none ml-1" v-on:click="toggleTab('Chloride')" :class="{'bgg200': activeTabKey === 'Chloride'}">氯鹽</button>
                <button class="px-2 py-1 rounded-none ml-1" v-on:click="toggleTab('Phosphate')" :class="{'bgg200': activeTabKey === 'Phosphate'}">磷酸鹽</button>
                <button class="px-2 py-1 rounded-none ml-1" v-on:click="toggleTab('AN')" :class="{'bgg200': activeTabKey === 'AN'}">氨氮</button>
                <button class="px-2 py-1 rounded-none ml-1" v-on:click="toggleTab('TOC')" :class="{'bgg200': activeTabKey === 'TOC'}">總有機碳</button>
                <button class="px-2 py-1 rounded-none ml-1" v-on:click="toggleTab('Temperature')" :class="{'bgg200': activeTabKey === 'Temperature'}">水溫</button>
            </div>
            <hr class="my-3"/>
        </div>

        <sheet
            :data="records"
            :columns="columns"
            :is-loading="isLoading"
            v-model="sheetValues"
            v-on:sort="sort"
            v-on:search="search"
        >
            <template v-slot:functions="props">
                <router-link :to="`/records/${props.datum.record_id}?type=water-quality`" target="_blank">
                    內容
                </router-link>
            </template>
        </sheet>
        <div class="myexcel text-muted caption">
            水質監測&nbsp;共 {{ total }} 筆
        </div>
    </div>
</template>

<script>
    import sheet from '../components/sheet';
    import queryString from 'querystring';
    import Highcharts from 'highcharts';

    const colors = ['#f5b7b1','#d2b4de','#a2d9ce','#fad7a0','#edbb99','#aeb6bf','#ffdddd','#1bbbd9','#f1948a','#bb8fce','#abebc6','#73c6b6','#85c1e9','#12e0aa','#f8c471','#e59866','#85929e','#ec7063','#a569bd','#5dade2','#45b39d','#d8d68d','#f5b041','#dc7633','#5d6d7e','#e74c3c','#8e44ad','#3498db','#16a085','#2efc71','#f39c12','#d35400','#34495e','#cb4335','#7d3c98','#2e86c1','#138d75','#28b463','#d68910','#ba4a00','#2e4053','#b03a2e','#6c3483','#2874a6','#117a65','#29b5e6','#b9770e','#a04000','#283747','#943126','#5b2c6f','#21618c','#0e6655','#1d8348','#9c640c','#873600','#212f3c','#78281f','#4a235a','#1b4f72','#0b5345','#186a3b','#7e5109','#6e2c00','#1b2631'];

    export default {
        name: 'WaterQuality',
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
                    { type: 'text', title: '測站', width: '60', name: 'id', searchable: true },
                    { type: 'text', title: '測站站名', width: '100', name: 'locality_chinese', searchable: true },
                    { type: 'text', title: '調查日期', width: '100', name: 'date', searchable: true },
                    { type: 'text', title: '調查者', width: '100', name: 'collector_chinese', searchable: true },
                    { type: 'text', title: '氫離子濃度', width: '80', name: 'PH', searchable: false },
                    { type: 'text', title: '導電度(μS/cm)', width: '100', name: 'Conductivity', searchable: false },
                    { type: 'text', title: '溶氧(mg/L)', width: '80', name: 'DO', searchable: false },
                    { type: 'text', title: '濁度(NTU)', width: '80', name: 'Clarity', searchable: false },
                    { type: 'text', title: '矽酸鹽(mg/L)', width: '80', name: 'Alkali_Silicate', searchable: false },
                    { type: 'text', title: '硝酸鹽氮(mg/L)', width: '100', name: 'NO3_N', searchable: false },
                    { type: 'text', title: '亞硝酸鹽氮(μg/L)', width: '110', name: 'NO2_N', searchable: false },
                    { type: 'text', title: '硫酸鹽(mg/L)', width: '100', name: 'SO4', searchable: false },
                    { type: 'text', title: '氯鹽(mg/L)', width: '80', name: 'Chloride', searchable: false },
                    { type: 'text', title: '磷酸鹽(mg/L)', width: '100', name: 'Phosphate', searchable: false },
                    { type: 'text', title: '氨氮(mg/L)', width: '80', name: 'AN', searchable: false },
                    { type: 'text', title: '總有機碳(mg/L)', width: '100', name: 'TOC', searchable: false },
                    { type: 'text', title: '水溫(℃)', width: '80', name: 'Temperature', searchable: false },
                ],
                total: 0,
                activeTabKey: 'PH',
                chartOptions: {
                    chart: {
                        height: 300,
                    },
                    title: {
                        text: '氫離子濃度監測紀錄'
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
                            text: '氫離子濃度'
                        }
                    },
                    tooltip: {
                        formatter: function () {
                            return '<b>' + this.series.name + '</b><br/>' + Highcharts.dateFormat('%Y-%b-%e', this.x, true) + '<br/>' + this.y;
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
            }
        },
        components: {
            sheet,
        },
        computed: {
            currentTab() {
                const map = {
                    'PH': {
                        'name': '氫離子濃度',
                        'unit': '',
                    },
                    'Conductivity': {
                        'name': '導電度',
                        'unit': 'μS/cm',
                    },
                    'DO': {
                        'name': '溶氧',
                        'unit': 'mg/L',
                    },
                    'Clarity': {
                        'name': '濁度',
                        'unit': 'NTU',
                    },
                    'Alkali_Silicate': {
                        'name': '矽酸鹽',
                        'unit': 'mg/L',
                    },
                    'NO3_N': {
                        'name': '硝酸鹽氮',
                        'unit': 'mg/L',
                    },
                    'NO2_N': {
                        'name': '亞硝酸鹽氮',
                        'unit': 'μg/L',
                    },
                    'SO4': {
                        'name': '硫酸鹽',
                        'unit': 'mg/L',
                    },
                    'Chloride': {
                        'name': '氯鹽',
                        'unit': 'mg/L',
                    },
                    'Phosphate': {
                        'name': '磷酸鹽',
                        'unit': 'mg/L',
                    },
                    'AN': {
                        'name': '氨氮',
                        'unit': 'mg/L',
                    },
                    'TOC': {
                        'name': '總有機碳',
                        'unit': 'mg/L',
                    },
                    'Temperature': {
                        'name': '水溫',
                        'unit': '℃',
                    },
                }

                return map[this.activeTabKey];
            },
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
            fetchReportData() {
                this.$http.get(`/api/water-quality-report?${queryString.stringify(this.sheetValues.searchParams)}&target=${this.activeTabKey}`)
                    .then(({ data: { dates, data } }) => {
                        this.chartOptions.series = data.map((d) => {
                            return {
                                ...d,
                                color: colors[d.station_id%66],
                            }
                        });
                        this.chartOptions.title.text = this.currentTab.name + '監測紀錄';
                        this.chartOptions.yAxis.title.text = this.currentTab.unit;
                    });
            },
            fetchData(callback) {
                if (this.isLoading || this.isEnd) {
                    return;
                }

                this.isLoading = true;

                const page = this.currentPage + 1;

                this.$http.get(`/api/water-quality?page=${page}&sort=${this.sortBy}&direction=${this.direction}&${queryString.stringify(this.sheetValues.searchParams)}`)
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
            toggleTab(target) {
                this.activeTabKey = target;
                this.fetchReportData();
            },
            search() {
                window.scrollTo(0, 0);
                this.fetchReportData();

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
