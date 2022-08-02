<template>
    <div class="px-4">
        <h6>伍、相關文獻&nbsp;<small class="text-muted">共 {{ total }} 筆</small></h6>
        <sheet
            :data="references"
            :columns="columns"
            :is-loading="isLoading"
            :action="false"
            v-model="sheetValues"
            v-on:sort="sort"
            v-on:search="search"
        >
            <template v-slot:search-tags>
                <select v-on:change="(e) => toggleTag(e.target.value, 'tags')">
                    <option value="">全部</option>
                    <option value="資料整合">資料整合</option>
                    <option value="水工模型試驗">水工模型試驗</option>
                    <option value="櫻花鉤吻鮭">櫻花鉤吻鮭</option>
                    <option value="水棲昆蟲">水棲昆蟲</option>
                    <option value="水文物理棲地">水文物理棲地</option>
                    <option value="水質">水質</option>
                    <option value="藻類">藻類</option>
                    <option value="生態資料庫">生態資料庫</option>
                    <option value="鳥類">鳥類</option>
                </select>
            </template>
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
                    { type: 'text', title: '年份', width: '100', name: 'year', searchable: true},
                    { type: 'text', title: '標籤', width: '160', name: 'tags', searchable: true, searchType: 'custom' },
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

    select {
        border: 1px solid gray;
        line-height: 1.2rem;
        font-size: 0.6rem;
        padding: 1px 2px;
        font-weight: normal;
        height: 23px;
    }
</style>
