<template>
    <div>
        <h6>調查物種&nbsp;<small class="text-muted">共 {{ total }} 筆</small></h6>
        <sheet
            :data="species"
            :columns="columns"
            :is-loading="isLoading"
            v-model="sheetValues"
            v-on:sort="sort"
            v-on:search="search"
        >
            <template v-slot:functions="props">
                <router-link :to="`/occurrences?scientific_name=${props.datum.scientific_name}`">
                    查看
                </router-link>
                &nbsp;
                <router-link :to="`/maps?scientific_name=${props.datum.scientific_name}`">
                    地圖
                </router-link>
            </template>
        </sheet>
        <div class="myexcel text-muted caption">
            調查物種&nbsp;共 {{ total }} 筆
        </div>
    </div>
</template>

<script>
    import sheet from '../components/sheet';
    import queryString from 'querystring';
    export default {
        name: 'Species',
        components: {
            sheet,
        },
        data() {
            return {
                species: [],
                isLoading: false,
                isEnd: false,
                currentPage: 0,
                sortBy: '',
                direction: '',
                sheetValues: {
                    searchParams: {},
                },
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
                    { type: 'text', title: '學名', width: '100', name: 'scientific_name', searchable: true },
                    { type: 'text', title: '中文名', width: '100', name: 'chinese', searchable: true },
                    { type: 'text', title: '命名者', width: '100', name: 'author', searchable: true },
                    { type: 'text', title: '數量', width: '80', name: 'total' },
                ],
                total: 0,
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
                if (this.isEnd || this.isLoading) {
                    return;
                }

                this.isLoading = true;
                const page = this.currentPage + 1;
                this.$http.get(
                    `/api/species?page=${page}&sort=${this.sortBy}&direction=${this.direction}&${queryString.stringify(this.sheetValues.searchParams)}`
                )
                    .then(({ data: { data, total, currentPage, perPage } }) => {
                        if (perPage > data.length || 0 === data.length) {
                            this.isEnd = true;
                        }
                        callback(data);
                        this.total = total;
                        this.currentPage = currentPage;
                        this.isLoading = false;

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
                this.species = [];
                this.currentPage = 0;
                this.fetchData(data => {
                    this.species = data;
                })
            },
            loadMore() {
                this.fetchData(data => {
                    this.species = this.species.concat(data);
                })
            },
        }
    }
</script>

<style lang="scss" scoped>
    @import '@/sass/fix-table';
</style>
