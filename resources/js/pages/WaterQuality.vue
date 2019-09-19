<template>
    <div>
        <h6>水質監測&nbsp;<small class="text-muted">共 {{ total }} 筆</small></h6>
        <table class="table table-sm table-striped table-fixed table-bordered table-hover">
            <caption>水質監測&nbsp;<small class="text-muted">共 {{ total }} 筆</small></caption>
            <thead>
            <tr>
                <th>
                    測站
                    <sort-icon target="station_id"
                               :sort="params.sort"
                               :direction="params.direction"
                               v-on:change-sort="changeSort"
                    ></sort-icon>
                    <br/>
                    <input type="text" class="form-control form-control-sm input"
                           v-on:change="search"
                           v-model="params.station_id"
                           placeholder="關鍵字"
                    />
                </th>
                <th>
                    測站站名
                    <sort-icon target="locality"
                               :sort="params.sort"
                               :direction="params.direction"
                               v-on:change-sort="changeSort"
                    ></sort-icon>
                    <br/>
                    <input type="text" class="form-control form-control-sm input"
                           v-on:change="search"
                           v-model="params.locality"
                    />
                </th>
                <th>緯度</th>
                <th>經度</th>
                <th>高度</th>
                <th>深度</th>
                <th>
                    調查日期
                    <sort-icon target="date"
                               :sort="params.sort"
                               :direction="params.direction"
                               v-on:change-sort="changeSort"
                    ></sort-icon>
                    <br/>
                    <input type="text" class="form-control form-control-sm input"
                           v-on:change="search"
                           v-model="params.date"
                    />
                </th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="record in records">
                <td v-text="record.station.id"></td>
                <td v-text="record.station.locality_chinese"></td>
                <td v-text="record.station.latitude"></td>
                <td v-text="record.station.longitude"></td>
                <td v-text="record.station.maximum_elevation"></td>
                <td v-text="record.station.maximum_depth"></td>
                <td v-text="record.date"></td>
                <td>
                    <router-link :to="`/records/${record.record_id}?type=water-quality`">內容</router-link>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
    import queryString from 'querystring';
    import SortIcon from '@/js/components/table/sort-icon';
    export default {
        name: 'WaterQuality',
        data() {
            return {
                records: [],
                params: {

                },
                total: 0,
            }
        },
        components: {
            SortIcon,
        },
        created() {
            const paramsString = location.search.substring(1);
            let paramsObject = {};
            if (paramsString) {
                paramsObject = JSON.parse('{"' + decodeURI(paramsString).replace(/"/g, '\\"').replace(/&/g, '","').replace(/=/g,'":"') + '"}')
            }
            this.params = { ... this.params, ... paramsObject};
        },
        mounted() {
            this.search();

            const app = this;
            const intersectionObserver = new IntersectionObserver(function(entries) {
                if (entries[0].intersectionRatio > 0){
                    app.loadMore();
                }
            });
            intersectionObserver.observe(document.querySelector('caption'));
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
                const paramsString = queryString.stringify({... this.params, ... { page: this.currentPage }});
                this.$http.get(`/api/water-quality?${paramsString}`)
                    .then(({ data: { data, total, currentPage, perPage } }) => {
                        if (perPage > data.length || 0 === data.length) {
                            this.isEnd = true;
                            return;
                        }
                        callback(data);
                        this.total = total;
                        this.currentPage = currentPage;
                        this.isLoading = false;
                    });
            },
            changeSort(column) {
                this.params.sort = column;
                this.params.direction = this.params.direction == 'asc' ? 'desc' : 'asc';
                this.search();
            },
            loadMore() {
                this.fetchData(data => {
                    this.records = this.records.concat(data);
                })
            },
            search() {
                this.isEnd = false;
                this.currentPage = 1;
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
