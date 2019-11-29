<template>
    <div>
        <h6>測站資料&nbsp;<small class="text-muted">共 {{ total }} 筆</small></h6>

        <div class="sheet-container">
            <table class="filter">
                <tr>
                    <td width="50">
                        <i class="fas fa-search"></i>
                    </td>
                    <td v-for="column in jExcelOptions.columns" :width="column.width">
                        <input type="text"
                               v-if="column.searchable"
                               :style="{ width: `${column.width - 6}px`}"
                               :placeholder="`搜尋${column.title}`"
                               v-model="searchParams[column.name]"
                        />
                    </td>
                </tr>
            </table>
            <div class="sheet-content">
                <div id="spreadsheet" ref="spreadsheet"></div>
                <div class="myexcel spinner-container d-flex align-items-center" v-if="isLoading">
                    <strong>載入中...</strong>
                    <div class="spinner-border text-secondary ml-auto" role="status">
                    </div>
                </div>
            </div>
        </div>
        <div class="myexcel text-muted caption">
            測站資料&nbsp;共 {{ total }} 筆
        </div>
    </div>
</template>

<script>
    import jexcel from 'jexcel';
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
                    direction: '',
                    sort: '',
                },
                stations: [],
                total: 0,
                searchParams: {}
            }
        },
        watch: {
            searchParams: {
                deep: true,
                handler() {
                    this.search();
                }
            }
        },
        computed: {
            query() {
                const query = Object.keys(this.params).map((key) => {
                    return encodeURIComponent(key) + '=' + encodeURIComponent(this.params[key])
                }).join('&');

                const searchQuery = Object.keys(this.searchParams).map((key) => {
                    return encodeURIComponent(key) + '=' + encodeURIComponent(this.searchParams[key])
                }).join('&');
                return query + '&' + searchQuery;
            },
            jExcelOptions() {
                return {
                    data: this.stations,
                    columnSorting: true,
                    search: true,
                    editable: true,
                    columns: [
                        { type: 'text', title: '測站', width: '100', name: 'auto_id', searchable: true },
                        { type: 'text', title: '緯度', width: '100', name: 'latitude' },
                        { type: 'text', title: '精度', width: '120', name: 'longitude' },
                        { type: 'text', title: '誤差', width: '80', name: 'coordinate_precision' },
                        { type: 'text', title: '地名', width: '200', name: 'locality_chinese', searchable: true },
                        { type: 'text', title: '最高海拔', width: '60', name: 'maximum_elevation' },
                        { type: 'text', title: '最低海拔', width: '60', name: 'minimum_elevation' },
                        { type: 'text', title: '地點描述', width: '250', name: 'locality_describe' },
                        { type: 'text', title: 'x', width: '35', name: 'x' },
                        { type: 'text', title: 'y', width: '35', name: 'y' },
                        { type: 'text', title: '備註', width: '100', name: 'note', style: '123' },
                    ],
                    onsort: this.sort,
                    readOnly: true,
                };
            }
        },
        created() {
            this.search();
        },
        mounted() {
            const jExcelObj = jexcel(this.$refs['spreadsheet'], this.jExcelOptions);
            Object.assign(this, { jExcelObj });

            const app = this;
            const intersectionObserver = new IntersectionObserver(function(entries) {
                if (entries[0].intersectionRatio > 0){
                    app.loadMore();
                }
            });
            intersectionObserver.observe(document.querySelector('.caption'));
        },
        methods: {
            sort(instance, cellNum, order) {
                this.params.direction = order ? 'asc' : 'desc';
                this.params.sort = this.jExcelOptions.columns[cellNum].name;
                this.search();
            },
            search() {
                const page = 1;
                window.scrollTo(0, 0);
                this.isLoading = true;
                this.isEnd = false;
                this.$http.get(`/api/stations?page=${page}&${this.query}`)
                    .then(({ data: { data, total, currentPage, perPage } }) => {
                        if (perPage > data.length || 0 === data.length) {
                            this.isEnd = true;
                        }
                        this.stations = data;
                        this.total = total;
                        this.page = page;
                        this.isLoading = false;
                        this.jExcelObj.setData(this.jExcelOptions.data);
                    });
            },
            loadMore() {
                const page = this.page + 1;
                this.isLoading = true;
                this.$http.get(`/api/stations?page=${page}&${this.query}`)
                    .then(({ data: { data, total, currentPage, perPage } }) => {
                        if (perPage > data.length || 0 === data.length) {
                            this.isEnd = true;
                        }
                        this.stations = this.stations.concat(data);
                        this.total = total;
                        this.page = page;
                        this.isLoading = false;
                        this.jExcelObj.setData(this.jExcelOptions.data);
                    });
            }
        }
    }
</script>

<style lang="scss" scoped>
    @import '@/sass/fix-table';
</style>
