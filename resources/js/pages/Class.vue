<template>
    <div>
        <h6>調查生物綱別&nbsp;<small class="text-muted">共 {{ total }} 筆</small></h6>
        <table class="table table-sm table-striped table-fixed table-bordered">
            <caption>調查生物綱別&nbsp;<small class="text-muted">共 {{ total }} 筆</small></caption>
            <thead>
            <tr>
                <th>
                    界中文<br/>
                    <input class="form-control form-control-sm" v-on:change="search" v-model="params.kingdomCName"/>
                </th>
                <th>
                    界名<br/>
                    <input class="form-control form-control-sm" v-on:change="search" v-model="params.kingdom"/>
                </th>
                <th>
                    門中文<br/>
                    <input class="form-control form-control-sm" v-on:change="search" v-model="params.phylumCName"/>
                </th>
                <th>
                    門名<br/>
                    <input class="form-control form-control-sm" v-on:change="search" v-model="params.phylum"/>
                </th>
                <th>
                    綱中文<br/>
                    <input class="form-control form-control-sm" v-on:change="search" v-model="params.classCName"/>
                </th>
                <th>
                    綱名<br/>
                    <input class="form-control form-control-sm" v-on:change="search" v-model="params.class"/>
                </th>
                <th>數量</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="c in classes">
                <td v-text="c.kingdom_c"></td>
                <td v-text="c.kingdom"></td>
                <td v-text="c.phylum_c"></td>
                <td v-text="c.phylum"></td>
                <td v-text="c.class_c"></td>
                <td v-text="c.class"></td>
                <td v-text="c.total"></td>
                <td>
                    <router-link :to="`/occurrences?class=${c.class}`">查詢</router-link>
                    <router-link :to="`/maps`">地圖</router-link>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
    export default {
        name: "Class",
        data() {
            return {
                classes: [],
                isLoading: false,
                isEnd: false,
                params: {
                    kingdomCName: '',
                    kingdom: '',
                    classCName: '',
                    class: '',
                    phylumCName: '',
                    phylum: '',
                },
                total: 0,
            }
        },
        created() {
            const paramsString = location.search.substring(1);
            this.$http.get(`/api/classes?${paramsString}`)
                .then(({ data }) => {
                    this.classes = data.data;
                    this.total = data.total;
                });
        },
        methods: {
            search() {
                const page = 1;
                this.isLoading = true;
                this.isEnd = false;
                this.$http.get(
                    `/api/classes?page=${page}&kingdom_c_name=${this.params.kingdomCName}&kingdom=${this.params.kingdom}&phylum_c_name=${this.params.phylumCName}&phylum=${this.params.phylum}&class_c_name=${this.params.classCName}&class=${this.params.class}`
                )
                    .then(({ data: { data, total, currentPage, perPage } }) => {
                        if (perPage > data.length || 0 === data.length) {
                            this.isEnd = true;
                        }
                        this.classes = data;
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
