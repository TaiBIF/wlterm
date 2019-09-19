<template>
    <div>
        <h6>測站資料&nbsp;<small class="text-muted">共 {{ total }} 筆</small></h6>
        <table class="table table-sm table-striped table-fixed table-bordered table-hover">
            <caption>測站資料&nbsp;<small class="text-muted">共 {{ total }} 筆</small></caption>
            <thead>
                <tr>
                    <th>
                        測站<br/>
                        <input class="form-control form-control-sm input" v-on:change="search" v-model="params.stationId" style="max-width: 30px"/>
                    </th>
                    <th>緯度</th>
                    <th>精度</th>
                    <th>誤差</th>
                    <th>
                        地名<br/>
                        <input class="form-control form-control-sm input" v-on:change="search" v-model="params.localityName"/>
                    </th>
                    <th>最高海拔</th>
                    <th>最低海拔</th>
                    <th>地點描述</th>
                    <th>x</th>
                    <th>y</th>
                    <th>備註</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="station in stations">
                    <td v-text="station.auto_id"></td>
                    <td v-text="station.latitude"></td>
                    <td v-text="station.longitude"></td>
                    <td v-text="station.coordinate_precision"></td>
                    <td v-text="station.locality_chinese"></td>
                    <td v-text="station.minimum_elevation"></td>
                    <td v-text="station.maximum_elevation"></td>
                    <td v-text="station.locality_describe"></td>
                    <td v-text="station.x"></td>
                    <td v-text="station.y"></td>
                    <td v-text="station.note"></td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
    export default {
        name: "Stations",
        data() {
            return {
                page: 0,
                isEnd: false,
                isLoading: false,
                params: {
                    stationId: '',
                    localityName: '',
                },
                stations: [],
                total: 0,
            }
        },
        created() {
            this.search();

            window.onscroll = function() {
                var d = document.documentElement;
                var offset = d.scrollTop + window.innerHeight;
                var height = d.offsetHeight;

                if (offset >= height && this.isEnd === false && this.isLoading == false) {
                    this.loadMore();
                }
            }.bind(this);
        },
        methods: {
            search() {
                const page = 1;
                this.isLoading = true;
                this.isEnd = false;
                this.$http.get(`/api/stations?page=${page}&station_id=${this.params.stationId}&locality_name=${this.params.localityName}`)
                    .then(({ data: { data, total, currentPage, perPage } }) => {
                        if (perPage > data.length || 0 === data.length) {
                            this.isEnd = true;
                        }
                        this.stations = data;
                        this.total = total;
                        this.page = page;
                        this.isLoading = false;
                    });
            },
            loadMore() {
                const page = this.page + 1;
                this.isLoading = true;
                this.$http.get(`/api/stations?page=${page}&station_id=${this.params.stationId}&locality_name=${this.params.localityName}`)
                    .then(({ data: { data, total, currentPage, perPage } }) => {
                        if (perPage > data.length || 0 === data.length) {
                            this.isEnd = true;
                        }
                        this.stations = this.stations.concat(data);
                        this.total = total;
                        this.page = page;
                        this.isLoading = false;
                    });
            }
        }
    }
</script>

<style lang="scss" scoped>
    @import '@/sass/fix-table';
</style>
