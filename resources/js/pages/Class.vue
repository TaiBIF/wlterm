<template>
    <div>
        <h6>調查生物綱別&nbsp;<small class="text-muted">共 {{ total }} 筆</small></h6>
        <sheet
            :data="classes"
            :columns="columns"
            :is-loading="isLoading"
            v-on:search="search"
        ></sheet>
        <div class="myexcel text-muted caption">
            調查生物綱別&nbsp;<small class="text-muted">共 {{ total }} 門</small>
        </div>
    </div>
</template>

<script>
    import sheet from '../components/sheet';
    export default {
        name: "Class",
        components: {
            sheet,
        },
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
                columns: [
                    { type: 'text', title: '界中文', width: '100', name: 'kingdom_c', searchable: true },
                    { type: 'text', title: '界名', width: '100', name: 'kingdom', searchable: true },
                    { type: 'text', title: '門中文', width: '120', name: 'phylum_c', searchable: true },
                    { type: 'text', title: '門名', width: '80', name: 'phylum', searchable: true },
                    { type: 'text', title: '綱中文', width: '100', name: 'class_c', searchable: true },
                    { type: 'text', title: '綱名', width: '100', name: 'class', searchable: true },
                    { type: 'text', title: '數量', width: '200', name: 'total' },
                ],
                total: 0,
                searchParams: {}
            }
        },
        computed: {
            query() {
                const searchQuery = Object.keys(this.searchParams).map((key) => {
                    return encodeURIComponent(key) + '=' + encodeURIComponent(this.searchParams[key])
                }).join('&');
                return searchQuery;
            },
        },
        mounted() {
            this.search();
        },
        methods: {
            search(query) {
                if (query) {
                    this.searchParams = query;
                }

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
                    });
            }
        }
    }
</script>

<style lang="scss" scoped>
    @import '@/sass/fix-table';
</style>
