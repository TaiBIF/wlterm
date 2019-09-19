<template>
    <div>
        <h6>河川流量推估&nbsp;<small class="text-muted">共 {{ total }} 筆</small></h6>
        <table class="table table-sm table-striped table-fixed table-bordered">
            <caption>河川流量推估&nbsp;<small class="text-muted">共 {{ total }} 筆</small></caption>
            <thead>
            <tr>
                <th>
                    河川
                    <sort-icon target="station"
                               :sort="params.sort"
                               :direction="params.direction"
                               v-on:change-sort="changeSort"
                    ></sort-icon>
                    <br/>
                    <input type="text" class="form-control form-control-sm input"
                           v-on:change="search"
                           v-model="params.station"
                           placeholder="關鍵字"
                    />
                </th>
                <th>
                    日期
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
                <th>公告流量</th>
                <th>模擬流量</th>
                <th>模擬上界</th>
                <th>模擬下界</th>
                <th>思源雨量</th>
                <th>桃山雨量</th>
                <th>環山雨量</th>
                <th>松茂雨量</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="record in records">
                <td v-text="record.station.name"></td>
                <td v-text="record.date"></td>
                <td v-text="record.public"></td>
                <td v-text="record.simu"></td>
                <td v-text="record.max"></td>
                <td v-text="record.min"></td>
                <td v-text="record.rain.st1"></td>
                <td v-text="record.rain.st2"></td>
                <td v-text="record.rain.st3"></td>
                <td v-text="record.rain.st4"></td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
    import queryString from 'querystring';
    export default {
        name: 'RiverDischargeEstimation',
        data() {
            return {
                records: [],
                params: {
                    sort: '',
                },
                total: 0,
            }
        },
        created() {

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
                this.$http.get(`/api/river-discharge-estimation?${paramsString}`)
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
