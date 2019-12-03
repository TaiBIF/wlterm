<template>
    <div>
        <h6>調查生物科別&nbsp;<small class="text-muted">共 {{ total }} 科</small></h6>
        <sheet
            :data="families"
            :columns="columns"
            :is-loading="isLoading"
            v-on:sort="sort"
            v-on:search="search"
        ></sheet>
        <div class="myexcel text-muted caption">
            調查生物目別&nbsp;<small class="text-muted">共 {{ total }} 門</small>
        </div>
    </div>
</template>

<script>
    import sheet from '../components/sheet';
    export default {
        name: 'Family',
        components: {
            sheet,
        },
        data() {
            return {
                families: [],
                isLoading: false,
                isEnd: false,
                sortBy: '',
                direction: '',
                page: 0,
                total: 0,
                columns: [
                    { type: 'text', title: '界中文', width: '100', name: 'kingdom_c', searchable: true },
                    { type: 'text', title: '界名', width: '100', name: 'kingdom', searchable: true },
                    { type: 'text', title: '門中文', width: '120', name: 'phylum_c', searchable: true },
                    { type: 'text', title: '門名', width: '80', name: 'phylum', searchable: true },
                    { type: 'text', title: '綱中文', width: '100', name: 'class_c', searchable: true },
                    { type: 'text', title: '綱名', width: '100', name: 'class', searchable: true },
                    { type: 'text', title: '目中文', width: '100', name: 'order_c', searchable: true },
                    { type: 'text', title: '目名', width: '100', name: 'order', searchable: true },
                    { type: 'text', title: '科中文', width: '100', name: 'family_c', searchable: true },
                    { type: 'text', title: '科名', width: '100', name: 'family', searchable: true },
                    { type: 'text', title: '數量', width: '80', name: 'total' },
                ],
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
            const paramsString = location.search.substring(1);
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

                const page = this.page + 1;
                this.$http.get(`/api/family?page=${page}&sort=${this.sortBy}&direction=${this.direction}&${this.query}`)
                    .then(({ data: { data, total, currentPage, perPage } }) => {
                        if (perPage > data.length || 0 === data.length) {
                            this.isEnd = true;
                        }
                        callback(data);
                        this.total = total;
                        this.page = currentPage;
                        this.isLoading = false;
                    });
            },
            sort(column, direction) {
                this.sortBy = this.columns[column].name;
                this.direction = direction ? 'desc' : 'asc';
                this.search();
            },
            search(query) {
                if (query) {
                    this.searchParams = query;
                }

                window.scrollTo(0, 0);

                this.page = 0;
                this.isEnd = false;
                this.currentPage = 0;
                this.fetchData(data => {
                    this.families = data.map(d => ({ ... d, mapUrl: `/maps?family=${d.family}`}));
                });
            },
            loadMore() {
                if (this.isLoading || this.isEnd) {
                    return;
                }

                this.fetchData(data => {
                    this.families = this.families.concat(
                        data.map(d => ({ ... d, mapUrl: `/maps?family=${d.family}`}))
                    );
                })
            },
        }
    }
</script>

<style lang="scss" scoped>
    @import '@/sass/fix-table';
</style>
