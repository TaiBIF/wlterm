<template>
    <div>
        <h6>調查生物綱別&nbsp;<small class="text-muted">共 {{ total }} 筆</small></h6>
        <div class="sheet-container">
            <tr ref="filter">
                <td width="50">
                    <i class="fas fa-search"></i>
                </td>
                <td v-for="column in jExcelOptions.columns" :width="column.width">
                    <input type="text"
                           v-if="column.searchable"
                           :placeholder="`搜尋${column.title}`"
                           v-model="searchParams[column.name]"
                           v-on:focus="focusSearch"
                    />
                </td>
                <td></td>
            </tr>
            <div class="sheet-content">
                <div id="spreadsheet" ref="spreadsheet"></div>
                <div class="jexcel_content">
                    <table class="jexcel action-table" cellpadding="0" cellspacing="0">
                        <thead class="resizable1">
                        <tr>
                            <td width="80px">&nbsp;</td>
                        </tr>
                        <tr>
                            <td width="80px">&nbsp;</td>
                        </tr>
                        </thead>
                        <tbody class="draggable">
                        <tr v-for="c in classes">
                            <td class="jexcel_row">
                                <router-link :to="`/occurrences?class=${c.class}`">查看</router-link>
                                <router-link :to="`/maps?class=${c.class}`">地圖</router-link>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="myexcel text-muted caption">
            調查生物綱別&nbsp;<small class="text-muted">共 {{ total }} 門</small>
        </div>
    </div>
</template>

<script>
    import jexcel from "jexcel";

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
                const searchQuery = Object.keys(this.searchParams).map((key) => {
                    return encodeURIComponent(key) + '=' + encodeURIComponent(this.searchParams[key])
                }).join('&');
                return searchQuery;
            },
            jExcelOptions() {
                return {
                    data: this.classes,
                    columnSorting: true,
                    search: true,
                    editable: true,
                    columns: [
                        { type: 'text', title: '界中文', width: '100', name: 'kingdom_c', searchable: true },
                        { type: 'text', title: '界名', width: '100', name: 'kingdom', searchable: true },
                        { type: 'text', title: '門中文', width: '120', name: 'phylum_c', searchable: true },
                        { type: 'text', title: '門名', width: '80', name: 'phylum', searchable: true },
                        { type: 'text', title: '綱中文', width: '100', name: 'class_c', searchable: true },
                        { type: 'text', title: '綱名', width: '100', name: 'class', searchable: true },
                        { type: 'text', title: '數量', width: '200', name: 'total', searchable: true },
                    ],
                    onsort: this.sort,
                    readOnly: true,
                    onload: this.onload,
                };
            }
        },
        created() {
            const paramsString = location.search.substring(1);
            this.$http.get(`/api/classes?${paramsString}`)
                .then(({ data }) => {
                    this.classes = data.data;
                    this.total = data.total;
                    this.jExcelObj.setData(this.jExcelOptions.data);
                });
        },
        mounted() {
            const jExcelObj = jexcel(this.$refs['spreadsheet'], this.jExcelOptions);
            Object.assign(this, { jExcelObj });
            document.querySelector('table[class="jexcel"] thead').append(this.$refs['filter']);
        },
        methods: {
            focusSearch() {
                this.jExcelObj.resetSelection(true);
            },
            search() {
                const page = 1;
                this.isLoading = true;
                this.isEnd = false;
                this.$http.get(
                    `/api/classes?page=${page}&${this.query}`
                )
                    .then(({ data: { data, total, currentPage, perPage } }) => {
                        if (perPage > data.length || 0 === data.length) {
                            this.isEnd = true;
                        }
                        this.classes = data;
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
