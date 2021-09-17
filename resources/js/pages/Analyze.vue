<template>
    <div>
        <div class="chart-container" ref="ch">
            <div class="p-4">
                <highcharts ref="chart" :options="chartOptions"></highcharts>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    data() {
        return {
            chartOptions: {
                chart: {
                    type: 'column',
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
        }
    },
    mounted() {
        // fetch report data
        this.$http.get(`/api/occurrences-report`)
            .then(({ data: { years, data, currentPage, perPage } }) => {
                this.chartOptions.xAxis.categories = years;
                this.chartOptions.series = [{
                    name: '歷年紀錄',
                    data: data.map(d => d.count),
                }];
                this.$refs.chart.chart.reflow();
            });
    },
}
</script>
<style lang="scss">
.highcharts-container{
    width: 100% !important;
}
</style>
