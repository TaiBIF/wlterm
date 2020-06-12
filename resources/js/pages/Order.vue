<template>
    <div>
        <h6>調查生物目別&nbsp;<small class="text-muted">共 {{ total }} 筆</small></h6>
        <sheet
            :data="data"
            :columns="columns"
            :is-loading="isLoading"
            v-model="sheetValues"
            v-on:sort="sort"
            v-on:search="search"
        ></sheet>
        <div class="myexcel text-muted caption">
            調查生物目別&nbsp;<small class="text-muted">共 {{ total }} 門</small>
        </div>
    </div>
</template>

<script>
    import queryString from 'querystring';
    import sheet from '../components/sheet';
    export default {
        name: 'Order',
        components: {
            sheet,
        },
        data() {
            return {
                data: [],
                currentPage: 0,
                isLoading: false,
                isEnd: false,
                sortBy: '',
                direction: '',
                columns: [
                    { type: 'text', title: '界中文', width: '100', name: 'kingdom_c', searchable: true },
                    { type: 'text', title: '界名', width: '100', name: 'kingdom', searchable: true },
                    { type: 'text', title: '門中文', width: '120', name: 'phylum_c', searchable: true },
                    { type: 'text', title: '門名', width: '80', name: 'phylum', searchable: true },
                    { type: 'text', title: '綱中文', width: '100', name: 'class_c', searchable: true },
                    { type: 'text', title: '綱名', width: '100', name: 'class', searchable: true },
                    { type: 'text', title: '目中文', width: '100', name: 'order_c', searchable: true },
                    { type: 'text', title: '目名', width: '100', name: 'order', searchable: true },
                    { type: 'text', title: '數量', width: '80', name: 'total' },
                ],
                total: 0,
                sheetValues: {
                    searchParams: {},
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
            fetchData(callback) {
                if (this.isLoading || this.isEnd) {
                    return;
                }

                this.isLoading = true;

                const page = this.currentPage + 1;
                this.$http.get(`/api/orders?page=${page}&sort=${this.sortBy}&direction=${this.direction}&${queryString.stringify(this.sheetValues.searchParams)}`)
                    .then(({ data: { data, total, currentPage, perPage } }) => {
                        callback(data);
                        this.total = total;
                        this.currentPage = currentPage;
                        this.isLoading = false;
                        if (perPage > data.length || 0 === data.length) {
                            this.isEnd = true;
                        }
                    });
            },
            sort(column, direction) {
                this.sortBy = this.columns[column].name;
                this.direction = direction ? 'desc' : 'asc';
                this.search();
            },
            search() {
                window.scrollTo(0, 0);

                this.isEnd = false;
                this.currentPage = 0;
                this.total = 0;
                this.data = [];

                this.fetchData(data => {
                    this.data = data;
                })
            },
            loadMore() {
                this.fetchData(data => {
                    this.data = this.data.concat(data);
                })
            },
        }
    }
</script>

<style lang="scss" scoped>
    @import '@/sass/fix-table';
    .input {
        max-width: 90px;
    }
</style>
