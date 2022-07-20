<template>
    <div class="px-4">
        <h6>水質監測&nbsp;<small class="text-muted">共 {{ total }} 筆</small></h6>
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
                    { type: 'text', title: '總磷(mg/L)', width: '80', name: 'TP', searchable: false },
                    { type: 'text', title: '氨氮(mg/L)', width: '80', name: 'AN', searchable: false },
                    { type: 'text', title: '總有機碳(mg/L)', width: '100', name: 'TOC', searchable: false },
                    { type: 'text', title: '水溫(℃)', width: '80', name: 'Temperature', searchable: false },
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
</style>
