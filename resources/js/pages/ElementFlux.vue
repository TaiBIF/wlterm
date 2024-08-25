<template>
    <div class="px-4">
        <h6>元素通量&nbsp;<small class="text-muted">共 {{ total }} 筆</small></h6>
        <sheet
            :data="records"
            :columns="columns"
            :is-loading="isLoading"
            :type="'element-flux'"
            v-model="sheetValues"
            v-on:sort="sort"
            v-on:search="search"
        >
            <template v-slot:functions="props">
                <router-link :to="`/records/${props.datum.record_id}?type=element-flux`" target="_blank">
                    內容
                </router-link>
            </template>
        </sheet>
        <div class="myexcel text-muted caption">
            元素通量&nbsp;共 {{ total }} 筆
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
                    searchParams: {},
                },
                columns: [
                    { type: 'text', title: '測站', width: '50', name: 'station_id', searchable: true },
                    { type: 'text', title: '測站站名', width: '100', name: 'locality_chinese', searchable: true },
                    { type: 'text', title: '調查日期', width: '170', name: 'date', searchable: true },
                    { type: 'text', title: '硝酸鹽 (mg/L)', width: '90', name: 'Nitrate'},
                    { type: 'text', title: '亞硝酸鹽 (mg/L)', width: '90', name: 'NO2'},
                    { type: 'text', title: '氟 (mg/L)', width: '75', name: 'Fl'},
                    { type: 'text', title: '氨 (mg/L)', width: '75', name: 'NH4'},
                    { type: 'text', title: '鈉 (mg/L)', width: '75', name: 'Na'},
                    { type: 'text', title: '鎂 (mg/L)', width: '75', name: 'Mg'},
                    { type: 'text', title: '鉀 (mg/L)', width: '75', name: 'K'},
                    { type: 'text', title: '鈣 (mg/L)', width: '75', name: 'Ca'},
                    { type: 'text', title: '鍶 (mg/L)', width: '75', name: 'Sr'},
                    { type: 'text', title: '鋇 (mg/L)', width: '75', name: 'Ba'},
                    { type: 'text', title: '矽 (mg/L)', width: '75', name: 'Si'},
                    { type: 'text', title: '溶解有機碳 (mg/L)', width: '100', name: 'DOC'},
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
                this.$http.get(`/api/element-flux?page=${page}&sort=${this.sortBy}&direction=${this.direction}&${queryString.stringify(this.sheetValues.searchParams)}`)
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
    @import './../../sass/fix-table';
</style>
