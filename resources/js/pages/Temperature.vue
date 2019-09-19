<template>
    <div>
        <h6>環境日均溫監測&nbsp;<small class="text-muted">共 {{ total }} 筆</small></h6>
        <table class="table table-sm table-striped table-fixed table-bordered">
            <caption>環境日均溫監測&nbsp;<small class="text-muted">共 {{ total }} 筆</small></caption>
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
                <th>氣溫</th>
                <th>水溫</th>
                <th>土表溫</th>
                <th>土下 25cm</th>
                <th>土下 50cm</th>
                <th>土下 65cm</th>
                <th>土下 90cm</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="record in records">
                <td v-text="record.station.id"></td>
                <td v-text="record.station.locality_chinese"></td>
                <td v-text="record.date"></td>
                <td v-text="record.air"></td>
                <td v-text="record.water"></td>
                <td v-text="record.soil_25cm"></td>
                <td v-text="record.soil_50cm"></td>
                <td v-text="record.soil_65cm"></td>
                <td v-text="record.soil_90cm"></td>
                <td v-text="record.date"></td>
                <td>
                    <router-link :to="`/occurrences?class=${record.class}`">xml</router-link>
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
        name: 'Temperature',
        data() {
            return {
                currentPage: 0,
                params: {
                    date: '',
                    stationId: '',
                    locality: '',
                },
                records: [],
                total: 0,
            }
        },
        components: {
            SortIcon,
        },
        created() {
            const paramsString = location.search.substring(1);
            this.params = { ... this.params, ... paramsString};
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
                this.$http.get(`/api/temperature?${paramsString}`)
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
