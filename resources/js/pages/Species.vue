<template>
    <div>
        <h6>調查物種&nbsp;<small class="text-muted">共 {{ total }} 筆</small></h6>
        <table class="table table-sm table-striped table-fixed table-bordered">
            <caption>調查物種&nbsp;<small class="text-muted">共 {{ total }} 筆</small></caption>
            <thead>
            <tr>
                <th>
                    門
                    <sort-icon target="phylum"
                               :sort="params.sort"
                               :direction="params.direction"
                               v-on:change-sort="changeSort"
                    ></sort-icon>
                    <br/>
                    <input type="text" class="form-control form-control-sm input"
                           v-on:change="search"
                           v-model="params.phylum"
                           placeholder="關鍵字"
                    />
                </th>
                <th>
                    綱
                    <sort-icon target="class"
                               :sort="params.sort"
                               :direction="params.direction" v-on:change-sort="changeSort"></sort-icon>
                    <br/>
                    <input type="text" class="form-control form-control-sm input" v-on:change="search" v-model="params.class"/>
                </th>
                <th>
                    目
                    <sort-icon target="order"
                               :sort="params.sort"
                               :direction="params.direction"
                               v-on:change-sort="changeSort"></sort-icon>
                    <br/>
                    <input type="text" class="form-control form-control-sm input"
                           v-on:change="search" v-model="params.order"/>
                </th>
                <th>
                    科
                    <sort-icon target="family"
                               :sort="params.sort"
                               :direction="params.direction"
                               v-on:change-sort="changeSort"></sort-icon>
                    <br/>
                    <input type="text" class="form-control form-control-sm input" v-on:change="search" v-model="params.family"/>
                </th>
                <th>
                    學名
                    <sort-icon target="scientific_name"
                               :sort="params.sort"
                               :direction="params.direction"
                               v-on:change-sort="changeSort"></sort-icon>
                    <br/>
                    <input type="text" class="form-control form-control-sm input" v-on:change="search" v-model="params.scientificName"/>
                </th>
                <th class="ellipsis">
                    中文名
                    <sort-icon target="chinese"
                               :sort="params.sort"
                               :direction="params.direction"
                               v-on:change-sort="changeSort"></sort-icon>
                    <br/>
                    <input type="text" class="form-control form-control-sm input" v-on:change="search" v-model="params.chineseName"/>
                </th>
                <th>
                    命名者
                    <sort-icon target="author"
                               :sort="params.sort"
                               :direction="params.direction"
                               v-on:change-sort="changeSort"></sort-icon>
                    <br/>
                    <input type="text" class="form-control form-control-sm input" v-on:change="search" v-model="params.author"/>
                </th>
                <th>
                    數量
                    <br/>
                    <sort-icon target="total"
                               :sort="params.sort"
                               :direction="params.direction"
                               v-on:change-sort="changeSort"></sort-icon>
                </th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="s in species">
                <td class="ellipsis">
                    {{ s.phylum_c }}<br/>
                    {{ s.phylum }}
                </td>
                <td class="ellipsis">
                    {{ s.class_c }}<br/>
                    {{ s.class }}
                </td>
                <td class="ellipsis">
                    {{ s.order_c }}<br/>
                    {{ s.order }}
                </td>
                <td class="ellipsis">
                    {{ s.family_c }}<br/>
                    {{ s.family }}
                </td>
                <td v-text="s.scientific_name"></td>
                <td class="ellipsis" v-text="s.chinese"></td>
                <td v-text="s.author" class="ellipsis"></td>
                <td v-text="s.total"></td>
                <td class="ellipsis">
                    <router-link :to="`/occurrences?scientific_name=${s.scientific_name}`">查詢</router-link>
                    <router-link :to="`/maps`">地圖</router-link>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
    import queryString from 'querystring';
    import SortIcon from "../components/table/sort-icon";
    export default {
        name: 'Species',
        components: {SortIcon},
        data() {
            return {
                species: [],
                isLoading: false,
                isEnd: false,
                params: {
                    page: 0,
                    sort: '',
                    direction: 'asc',
                    class: '',
                    phylum: '',
                    order: '',
                    family: '',
                    chineseName: '',
                    author: '',
                },
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
            fetchData(callback) {
                const page = this.params.page + 1;
                const query = queryString.stringify(this.params);
                this.$http.get(
                    `/api/species?page=${page}&${query}`
                )
                    .then(({ data: { data, total, currentPage, perPage } }) => {
                        if (perPage > data.length || 0 === data.length) {
                            this.isEnd = true;
                        }
                        callback(data);
                        this.total = total;
                        this.page = page;
                        this.isLoading = false;

                    });
            },
            changeSort(column) {
                this.params.sort = column;
                this.params.direction = this.params.direction == 'asc' ? 'desc' : 'asc';
                this.search();
            },
            loadMore() {

                this.isLoading = true;
                this.fetchData(data => {
                    this.species = this.species.concat(data);
                })
            },
            search() {
                this.isLoading = true;
                this.isEnd = false;
                this.fetchData(data => {
                    this.species = data;
                    window.scrollTo(0, 0);
                })
            }
        }
    }
</script>

<style lang="scss" scoped>
    @import '@/sass/fix-table';
</style>
