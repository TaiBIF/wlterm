<template>
    <div class="px-4">
        <h6>伍、相關文獻&nbsp;<small class="text-muted">共 {{ total }} 筆</small></h6>
        <div class="w-full mb-2">
            以標籤篩選:
            <button class="px-2 py-1 rounded-none ml-1" v-on:click="toggleTag('資料整合')" :class="{'bgg200': sheetValues.searchParams.tag === '資料整合'}">資料整合</button>
            <button class="px-2 py-1 rounded-none ml-1" v-on:click="toggleTag('水工模型試驗')" :class="{'bgg200': sheetValues.searchParams.tag === '水工模型試驗'}">水工模型試驗</button>
            <button class="px-2 py-1 rounded-none ml-1" v-on:click="toggleTag('櫻花鉤吻鮭')" :class="{'bgg200': sheetValues.searchParams.tag === '櫻花鉤吻鮭'}">櫻花鉤吻鮭</button>
            <button class="px-2 py-1 rounded-none ml-1" v-on:click="toggleTag('水棲昆蟲')" :class="{'bgg200': sheetValues.searchParams.tag === '水棲昆蟲'}">水棲昆蟲</button>
            <button class="px-2 py-1 rounded-none ml-1" v-on:click="toggleTag('水文物理棲地')" :class="{'bgg200': sheetValues.searchParams.tag === '水文物理棲地'}">水文物理棲地</button>
            <button class="px-2 py-1 rounded-none ml-1" v-on:click="toggleTag('水質')" :class="{'bgg200': sheetValues.searchParams.tag === '水質'}">水質</button>
            <button class="px-2 py-1 rounded-none ml-1" v-on:click="toggleTag('藻類')" :class="{'bgg200': sheetValues.searchParams.tag === '藻類'}">藻類</button>
            <button class="px-2 py-1 rounded-none ml-1" v-on:click="toggleTag('生態資料庫')" :class="{'bgg200': sheetValues.searchParams.tag === '生態資料庫'}">生態資料庫</button>
            <button class="px-2 py-1 rounded-none ml-1" v-on:click="toggleTag('鳥類')" :class="{'bgg200': sheetValues.searchParams.tag === '鳥類'}">鳥類</button>
        </div>
        <sheet
            :data="references"
            :columns="columns"
            :is-loading="isLoading"
            :action="false"
            v-model="sheetValues"
            v-on:sort="sort"
            v-on:search="search"
        >
        </sheet>
        <div class="myexcel text-muted caption">
            相關文獻&nbsp;共 {{ total }} 筆
        </div>
    </div>
</template>

<script>
    import sheet from '../components/sheet';
    import queryString from 'querystring';
    export default {
        name: 'References',
        components: {
            sheet,
        },
        data() {
            return {
                references: [],
                isLoading: false,
                isEnd: false,
                currentPage: 0,
                sortBy: '',
                direction: '',
                sheetValues: {
                    searchParams: {
                        tag: '',
                    },
                    defaultColAlign: 'left',
                },
                columns: [
                    { type: 'html', title: '引用文獻', width: '700', name: 'citation', searchable: true },
                    { type: 'text', title: '年份', width: '100', name: 'year', searchable: true },
                    { type: 'text', title: '標籤', width: '160', name: 'tags' },
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
                    `/api/references?page=${page}&sort=${this.sortBy}&direction=${this.direction}&${queryString.stringify(this.sheetValues.searchParams)}`
                )
                    .then(({ data: { data, total, currentPage, perPage } }) => {
                        if (perPage > data.length || 0 === data.length) {
                            this.isEnd = true;
                        }

                        callback(data.map((d) => {
                            return {
                                ...d,
                                citation: `<div style="width: 100%;white-space: break-spaces;text-align: left;line-height: 16px">${d.citation}</div>`,
                            }
                        }));
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
                this.references = [];
                this.currentPage = 0;
                this.fetchData(data => {
                    this.references = data;
                })
            },
            toggleTag(tag) {
                this.sheetValues.searchParams.tag = tag;
                this.search();
            },
            loadMore() {
                this.fetchData(data => {
                    this.references = this.references.concat(data);
                })
            },
        }
    }
</script>

<style lang="scss" scoped>
    @import './../../sass/fix-table';

    .bgg200 {
        background-color: #eee;
    }
</style>
