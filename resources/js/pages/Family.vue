<template>
    <div>
        <h6>調查生物科別&nbsp;<small class="text-muted">共 {{ total }} 科</small></h6>
        <table class="table table-sm table-striped table-fixed table-bordered table-hover">
            <caption>調查生物科別&nbsp;<small class="text-muted">共 {{ total }} 科</small></caption>
            <thead>
            <tr>
                <th>
                    界中文<br/>
                    <input type="text" class="form-control form-control-sm" v-on:change="search" v-model="params.kingdomCName"/>
                </th>
                <th>
                    界名<br/>
                    <input type="text" class="form-control form-control-sm" v-on:change="search" v-model="params.kingdom"/>
                </th>
                <th>
                    門中文<br/>
                    <input type="text" class="form-control form-control-sm" v-on:change="search" v-model="params.phylumCName"/>
                </th>
                <th>
                    門名<br/>
                    <input type="text" class="form-control form-control-sm" v-on:change="search" v-model="params.phylum"/>
                </th>
                <th>
                    綱中文<br/>
                    <input type="text" class="form-control form-control-sm" v-on:change="search" v-model="params.classCName"/>
                </th>
                <th>
                    綱名<br/>
                    <input type="text" class="form-control form-control-sm" v-on:change="search" v-model="params.class"/>
                </th>
                <th>
                    目中文<br/>
                    <input type="text" class="form-control form-control-sm" v-on:change="search" v-model="params.orderCName"/>
                </th>
                <th>
                    目名<br/>
                    <input type="text" class="form-control form-control-sm" v-on:change="search" v-model="params.order"/>
                </th>
                <th>
                    科中文<br/>
                    <input type="text" class="form-control form-control-sm" v-on:change="search" v-model="params.familyCName"/>
                </th>
                <th>
                    科名<br/>
                    <input type="text" class="form-control form-control-sm" v-on:change="search" v-model="params.family"/>
                </th>
                <th>數量</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="family in families">
                <td v-text="family.kingdom_c"></td>
                <td v-text="family.kingdom"></td>
                <td v-text="family.phylum_c"></td>
                <td v-text="family.phylum"></td>
                <td v-text="family.class_c"></td>
                <td v-text="family.class"></td>
                <td v-text="family.order_c"></td>
                <td v-text="family.order"></td>
                <td v-text="family.family_c"></td>
                <td v-text="family.family"></td>
                <td v-text="family.total"></td>
                <td>
                    <router-link :to="`/occurrences?family=${family.family}`">查詢</router-link>
                    <router-link :to="`/maps`">地圖</router-link>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
    export default {
        name: 'Family',
        data() {
            return {
                families: [],
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
        created() {
            const paramsString = location.search.substring(1);
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
            loadMore() {
                const page = this.page + 1;
                this.isLoading = true;
                this.$http.get(`/api/family?page=${page}&station_id=${this.params.stationId}&locality_name=${this.params.localityName}`)
                    .then(({ data: { data, total, currentPage, perPage } }) => {
                        if (perPage > data.length || 0 === data.length) {
                            this.isEnd = true;
                        }
                        this.families = this.families.concat(data);
                        this.total = total;
                        this.page = page;
                        this.isLoading = false;
                    });
            },
            search() {
                const page = 1;
                this.isLoading = true;
                this.isEnd = false;
                this.$http.get(
                    `/api/family?page=${page}&kingdom_c_name=${this.params.kingdomCName}&kingdom=${this.params.kingdom}&phylum_c_name=${this.params.phylumCName}&phylum=${this.params.phylum}&class_c_name=${this.params.classCName}&class=${this.params.class}`
                )
                    .then(({ data: { data, total, currentPage, perPage } }) => {
                        if (perPage > data.length || 0 === data.length) {
                            this.isEnd = true;
                        }
                        this.families = data;
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
