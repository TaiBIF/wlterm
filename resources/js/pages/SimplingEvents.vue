<template>
    <div class="px-4">
        <h6>調查活動&nbsp;<small class="text-muted">共 {{ total }} 筆</small></h6>
        <sheet
            :data="data"
            :columns="columns"
            :is-loading="isLoading"
            v-model="sheetValues"
            v-on:sort="sort"
            v-on:search="search"
        >
            <template v-slot:functions="props">
                <router-link :to="`/projects/${props.datum.project_id}/stations/${props.datum.station_id}/simpling-events?examineWay=${props.datum.examine_way ? props.datum.examine_way : ''}&date=${props.datum.date}`">
                    查看
                </router-link>
            </template>
        </sheet>
        <div class="myexcel text-muted caption">
            調查活動&nbsp;共 {{ total }} 筆
        </div>
    </div>
</template>

<script>
    import sheet from '../components/sheet';
    import queryString from 'querystring';
    export default {
        name: "SimplingEvents",
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
                    { type: 'text', title: '調查日', width: '100', name: 'date', searchable: true },
                    { type: 'text', title: '調查法', width: '100', name: 'examine_way', searchable: true },
                    { type: 'text', title: '測站', width: '60', name: 'id', searchable: true },
                    { type: 'text', title: '地點', width: '90', name: 'locality_chinese', searchable: true },
                    { type: 'text', title: '緯度', width: '95', name: 'latitude' },
                    { type: 'text', title: '經度', width: '95', name: 'longitude' },
                    { type: 'text', title: '計畫名稱', width: '450', name: 'project_name', searchable: true },
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

                this.page = 1;
                this.isEnd = false;
                this.data = [];
                this.loadMore();
            },
            loadMore() {
                if (this.isEnd || this.isLoading) {
                    return;
                }

                this.isLoading = true;
                const page = this.page + 1;

                this.$http.get(`/api/simpling-events?${
                    queryString.stringify({
                        ... this.sheetValues.searchParams,
                        page,
                        sort: this.sortBy,
                        direction: this.direction,
                    })
                }`)
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
    @import './../../sass/fix-table';
</style>
