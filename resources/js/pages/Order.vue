<template>
    <div>
        <h6>調查生物目別&nbsp;<small class="text-muted">共 {{ total }} 筆</small></h6>
        <table class="table table-sm table-striped table-fixed table-bordered">
            <caption>調查生物目別&nbsp;<small class="text-muted">共 {{ total }} 筆</small></caption>
            <thead>
            <tr>
                <th>
                    界中文
                    <sort-icon target="station_id" :sort="params.sort" :direction="params.direction" v-on:change-sort="changeSort"></sort-icon>
                    <br/>
                    <input type="text" class="form-control form-control-sm" v-on:change="search" v-model="params.kingdomCName"/>
                </th>
                <th>
                    界名
                    <sort-icon target="station_id" :sort="params.sort" :direction="params.direction" v-on:change-sort="changeSort"></sort-icon>
                    <br/>
                    <input type="text" class="form-control form-control-sm" v-on:change="search" v-model="params.kingdom"/>
                </th>
                <th>
                    門中文
                    <sort-icon target="station_id" :sort="params.sort" :direction="params.direction" v-on:change-sort="changeSort"></sort-icon>
                    <br/>
                    <input type="text" class="form-control form-control-sm" v-on:change="search" v-model="params.phylumCName"/>
                </th>
                <th>
                    門名
                    <sort-icon target="station_id" :sort="params.sort" :direction="params.direction" v-on:change-sort="changeSort"></sort-icon>
                    <br/>
                    <input type="text" class="form-control form-control-sm" v-on:change="search" v-model="params.phylum"/>
                </th>
                <th>
                    綱中文
                    <sort-icon target="station_id" :sort="params.sort" :direction="params.direction" v-on:change-sort="changeSort"></sort-icon>
                    <br/>
                    <input type="text" class="form-control form-control-sm" v-on:change="search" v-model="params.classCName"/>
                </th>
                <th>
                    綱名
                    <sort-icon target="station_id" :sort="params.sort" :direction="params.direction" v-on:change-sort="changeSort"></sort-icon>
                    <br/>
                    <input type="text" class="form-control form-control-sm" v-on:change="search" v-model="params.class"/>
                </th>
                <th>
                    目中文
                    <sort-icon target="station_id" :sort="params.sort" :direction="params.direction" v-on:change-sort="changeSort"></sort-icon>
                    <br/>
                    <input type="text" class="form-control form-control-sm" v-on:change="search" v-model="params.orderCName"/>
                </th>
                <th>
                    目名
                    <sort-icon target="station_id" :sort="params.sort" :direction="params.direction" v-on:change-sort="changeSort"></sort-icon>
                    <br/>
                    <input type="text" class="form-control form-control-sm" v-on:change="search" v-model="params.order"/>
                </th>
                <th>數量</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="order in orders">
                <td v-text="order.kingdom_c"></td>
                <td v-text="order.kingdom"></td>
                <td v-text="order.phylum_c"></td>
                <td v-text="order.phylum"></td>
                <td v-text="order.class_c"></td>
                <td v-text="order.class"></td>
                <td v-text="order.order_c"></td>
                <td v-text="order.order"></td>
                <td v-text="order.total"></td>
                <td>
                    <router-link :to="`/occurrences?order=${order.order}`">查詢</router-link>
                    <router-link :to="`/maps`">地圖</router-link>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
    import SortIcon from '@/js/components/table/sort-icon';
    export default {
        name: 'Order',
        data() {
            return {
                orders: [],
                currentPage: 0,
                isLoading: false,
                isEnd: false,
                params: {
                    kingdomCName: '',
                    kingdom: '',
                    classCName: '',
                    class: '',
                    phylumCName: '',
                    phylum: '',
                    orderCName: '',
                    order: '',
                },
                total: 0,
            }
        },
        components: {
            SortIcon,
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
                this.$http.get(`/api/orders?page=${page}&kingdom_c_name=${this.params.kingdomCName}&kingdom=${this.params.kingdom}&phylum_c_name=${this.params.phylumCName}&phylum=${this.params.phylum}&class_c_name=${this.params.classCName}&class=${this.params.class}`)
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
            search() {
                this.isEnd = false;
                this.currentPage = 1;
                this.fetchData(data => {
                    this.orders = data;
                })
            },
            loadMore() {
                this.fetchData(data => {
                    this.orders = this.orders.concat(data);
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
