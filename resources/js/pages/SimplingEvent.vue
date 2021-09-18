<template>
    <div class="px-4">
        <h6>{{ project.projectname }} {{ station.locality_chinese }} 調查活動&nbsp;<small class="text-muted">共 {{ total }} 筆</small></h6>
        <sheet
            :data="data"
            :columns="columns"
            :is-loading="isLoading"
            v-model="sheetValues"
            v-on:sort="sort"
            v-on:search="search"
        >
            <template v-slot:functions="props">
                <router-link :to="`/occurrences?scientific_name=${ props.datum.scientific_name}`">
                    查看物種紀錄
                </router-link>
            </template>
        </sheet>
        <div class="myexcel text-muted caption">
            調查活動&nbsp;共 {{ total }} 筆
        </div>
    </div>
</template>

<script>
    import sheet from '../components/sheet';
    import queryString from 'querystring';
    export default {
        name: "SimplingEvent",
        components: {
            sheet,
        },
        data() {
            return {
                page: 0,
                isEnd: false,
                isLoading: false,
                direction: '',
                sortBy: '',
                project: {},
                station: {},
                data: [],
                columns: [
                    { type: 'text', title: '目', width: '100', name: 'order', searchable: true },
                    { type: 'text', title: '科', width: '100', name: 'family', searchable: true },
                    { type: 'text', title: '原紀錄學名', width: '100', name: 'record_use_name', searchable: true },
                    { type: 'text', title: '接受學名', width: '180', name: 'scientific_name', searchable: true },
                    { type: 'text', title: '中文名', width: '120', name: 'chinese' },
                    { type: 'text', title: '個體數', width: '80', name: 'individual_count' },
                    { type: 'text', title: '覆蓋率', width: '80', name: 'cover_rate', searchable: true },
                    { type: 'text', title: '體長', width: '80', name: 'body_length', searchable: true },
                    { type: 'text', title: '體長單位', width: '80', name: 'body_length_unit', searchable: true },
                    { type: 'text', title: '生物量', width: '80', name: 'biomass', searchable: true },
                    { type: 'text', title: '生物量單位', width: '80', name: 'biomass_unit', searchable: true },
                    { type: 'text', title: '備註', width: '150', name: 'notes', searchable: true },

                ],
                total: 0,
                sheetValues: {
                    searchParams: {}
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
            sort(column, direction) {
                this.direction = direction ? 'asc' : 'desc';
                this.sortBy = this.columns[column].name
                this.search();
            },
            search() {
                window.scrollTo(0, 0);

                this.page = 1;
                this.isEnd = false;

                this.loadMore()
            },
            loadMore() {
                if (this.isEnd || this.isLoading) {
                    return;
                }

                this.isLoading = true;
                const page = this.page + 1;


                this.$http.get(`/api/projects/${this.$route.params.projectId}/stations/${this.$route.params.stationId}/simpling-events?${
                    queryString.stringify({
                        ... this.sheetValues.searchParams,
                        ... this.$route.query,
                        page,
                        sort: this.sortBy,
                        direction: this.direction,
                    })
                }`)
                    .then(({ data: { data, total, currentPage, perPage, project, station } }) => {
                        if (perPage > data.length || 0 === data.length) {
                            this.isEnd = true;
                        }
                        this.data = this.data.concat(data);
                        this.project = project;
                        this.station = station;
                        this.total = total;
                        this.page = page;
                        this.isLoading = false;
                    });
            }
        }
    }
</script>

<style lang="scss" scoped>
    @import './../../sass/fix-table';
</style>
