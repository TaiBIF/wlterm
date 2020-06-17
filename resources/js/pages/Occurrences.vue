<template>
    <div>
        <h6 class="page-title">生物物種調查紀錄 <small class="text-muted">共 {{ total }} 筆</small></h6>
        <div class="chart-container">
            <highcharts :options="chartOptions"></highcharts>
        </div>
        <sheet
            :data="occurrences"
            :columns="columns"
            :is-loading="isLoading"
            v-model="sheetValues"
            v-on:sort="sort"
            v-on:search="search"
        >
            <template v-slot:functions="props">
                <router-link :to="`/records/${props.datum.record_id}?type=occurrence`">
                    內容
                </router-link>
            </template>
        </sheet>
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
            return {
                occurrences: [],
                isLoading: false,
                currentPage: 0,
                isEnd: false,
                sortBy: '',
                direction: '',
                columns: [
                    { type: 'text', title: '測站', width: '50', name: 'sid', searchable: true },
                    { type: 'text', title: '門名', width: '120', name: 'phylum', searchable: true },
                    { type: 'text', title: '綱名', width: '120', name: 'class', searchable: true },
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
                chartOptions: {
                    chart: {
                        type: 'column',
                        width: 800,
                        height: 300,
                    },
                    title: {
                        text: '生物物種調查歷年紀錄'
                    },
                    xAxis: {
                        categories: [],
                        title: {
                            text: '年份'
                        }
                    },
                    yAxis: {
                        title: {
                            text: '調查紀錄數'
                        }
                    },
                    legend: {
                        enabled: false
                    },
                    series: [],
                },
                sheetValues: {
                    searchParams: {}
                }
            }
        },
        created() {
            this.sheetValues.searchParams = this.$route.query;
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

            // fetch report data
            this.$http.get(`/api/occurrences-report`)
                .then(({ data: { years, data, currentPage, perPage } }) => {
                    this.chartOptions.xAxis.categories = years;
                    this.chartOptions.series = [{
                        name: '歷年紀錄',
                        data: data.map(d => d.count),
                    }];
                });
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

                this.$http.get(`/api/occurrences?page=${page}&sort=${this.sortBy}&direction=${this.direction}&${queryString.stringify(this.sheetValues.searchParams)}`)
                    .then(({ data: { data, total, currentPage, perPage } }) => {
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
                    if (this.perPage > data.length || 0 === data.length) {
                        this.isEnd = true;
                        return;
                    }
                    this.occurrences = this.occurrences.concat(data);
                })
            },
            search() {
                window.scrollTo(0, 0);

                this.isEnd = false;
                this.currentPage = 0;
                this.fetchData(data => {
                    this.isEnd = true;
                    this.occurrences = data;
                })
            }
        }
    }
</script>


<style lang="scss" scoped>
   @import '@/sass/fix-table';
</style>
