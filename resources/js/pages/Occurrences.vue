<template>
    <div>
        <h6>生物物種調查紀錄<small class="text-muted">共 {{ total }} 筆</small></h6>
        <table class="table table-sm table-striped table-fixed table-bordered table-hover">
            <caption>生物物種調查紀錄&nbsp;<small class="text-muted">共 {{ total }} 筆</small></caption>
            <thead>
            <tr>
                <th>測站</th>
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
                               v-on:change-sort="changeSort"
                    ></sort-icon>
                    <br/>
                    <input type="text" class="form-control form-control-sm input"
                           v-on:change="search"
                           v-model="params.scientific_name"
                    />
                </th>
                <th>
                    中文名
                    <sort-icon target="chinese"
                               :sort="params.chinese"
                               :direction="params.direction"
                               v-on:change-sort="changeSort"
                    ></sort-icon>
                    <br/>
                    <input type="text" class="form-control form-control-sm input"
                           v-on:change="search"
                           v-model="params.chinese"
                    />
                </th>
                <th>
                    調查日
                    <sort-icon target="phylum"
                               :sort="params.date"
                               :direction="params.direction"
                               v-on:change-sort="changeSort"
                    ></sort-icon>
                    <br/>
                    <input type="text" class="form-control form-control-sm input"
                           v-on:change="search"
                           v-model="params.date"
                    />
                </th>
                <th>地點
                    <sort-icon target="phylum"
                               :sort="params.locality_chinese"
                               :direction="params.direction"
                               v-on:change-sort="changeSort"
                    ></sort-icon>
                    <br/>
                    <input type="text" class="form-control form-control-sm input"
                           v-on:change="search"
                           v-model="params.locality_chinese"
                    />
                </th>
                <th>緯度
                    <sort-icon target="latitude"
                               :sort="params.latitude"
                               :direction="params.direction"
                               v-on:change-sort="changeSort"
                    ></sort-icon>
                    <br/>
                    <input type="text" class="form-control form-control-sm input"
                           v-on:change="search"
                           v-model="params.latitude"
                    />
                </th>
                <th>
                    經度
                    <sort-icon target="longitude"
                               :sort="params.longitude"
                               :direction="params.direction"
                               v-on:change-sort="changeSort"
                    ></sort-icon>
                    <br/>
                    <input type="text" class="form-control form-control-sm input"
                           v-on:change="search"
                           v-model="params.longitude"
                    />
                </th>
                <th>
                    調查者
                    <sort-icon target="phylum"
                               :sort="params.collector_chinese"
                               :direction="params.direction"
                               v-on:change-sort="changeSort"
                    ></sort-icon>
                    <br/>
                    <input type="text" class="form-control form-control-sm input"
                           v-on:change="search"
                           v-model="params.collector_chinese"
                    />
                </th>
                <th class="ellipsis">
                    調查法
                </th>
                <th>
                    鑒定者
                    <sort-icon target="phylum"
                               :sort="params.identified_by_chinese"
                               :direction="params.direction"
                               v-on:change-sort="changeSort"
                    ></sort-icon>
                    <br/>
                    <input type="text" class="form-control form-control-sm input"
                           v-on:change="search"
                           v-model="params.identified_by_chinese"
                    />
                </th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="occurrence in occurrences">
                <td v-text="occurrence.sid"></td>
                <td v-text="occurrence.phylum"></td>
                <td v-text="occurrence.class"></td>
                <td v-text="occurrence.order"></td>
                <td v-text="occurrence.family"></td>
                <td v-text="occurrence.scientific_name"></td>
                <td v-text="occurrence.chinese"></td>
                <td v-text="occurrence.date"></td>
                <td v-text="occurrence.locality_chinese"></td>
                <td v-text="occurrence.latitude"></td>
                <td v-text="occurrence.longitude"></td>
                <td v-text="occurrence.collector_chinese"></td>
                <td></td>
                <td v-text="occurrence.identified_by_chinese"></td>
                <td>
                    <router-link :to="`/occurrences`">內容</router-link>
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
        name: 'Occurrences',
        components: {
            SortIcon,
        },
        data() {
            const app = this;
            return {
                occurrences: [],
                isLoading: false,
                currentPage: 0,
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
            const paramsString = location.search.substring(1);
            // const paramsObject = JSON.parse('{"' + decodeURI(paramsString).replace(/"/g, '\\"').replace(/&/g, '","').replace(/=/g,'":"') + '"}');
            this.params = { ... this.params};
        },
        mounted() {
            this.search();
            const app = this;
            const intersectionObserver = new IntersectionObserver(function(entries) {
                if (entries[0].intersectionRatio > 0){
                    app.loadMore();
                }
            });
            // Start observing
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
                const query = queryString.stringify({... this.params, ... { page }});
                this.$http.get(`/api/occurrences?${query}`)
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
                    this.occurrences = this.occurrences.concat(data);
                })
            },
            search() {
                this.isEnd = false;
                this.fetchData(data => {
                    this.occurrences = data;
                })
            }
        }
    }
</script>


<style lang="scss" scoped>
   @import '@/sass/fix-table';
</style>
