<template>
    <div>
        <h6>生物門別數量統計&nbsp;<small class="text-muted">共 {{ total }} 門</small></h6>
        <div class="sheet-container">
            <table class="filter">
                <tr>
                    <td width="50">
                        <i class="fas fa-search"></i>
                    </td>
                    <td v-for="column in jExcelOptions.columns" :width="column.width"></td>
                    <td></td>
                </tr>
            </table>
            <div class="sheet-content">
                <div id="spreadsheet" ref="spreadsheet"></div>
                <div class="jexcel_content">
                    <table class="jexcel action-table" cellpadding="0" cellspacing="0">
                        <thead class="resizable">
                            <tr>
                                <td width="80px">&nbsp;</td>
                            </tr>
                        </thead>
                        <tbody class="draggable">
                            <tr v-for="phylum in phyla">
                                <td class="jexcel_row">
                                    <router-link :to="`/occurrences?phylum=${phylum.phylum}`">查看</router-link>
                                    <router-link :to="`/maps`">地圖</router-link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="myexcel text-muted caption">
            生物門別數量統計&nbsp;<small class="text-muted">共 {{ total }} 門</small>
        </div>
    </div>
</template>

<script>
    import jexcel from "jexcel";

    export default {
        name: "Phylum",
        data() {
            return {
                phyla: [],
                total: 0,
            }
        },
        computed: {
            jExcelOptions() {
                return {
                    data: this.phyla,
                    columnSorting: true,
                    search: true,
                    editable: true,
                    columns: [
                        { type: 'text', title: '界中文', width: '100', name: 'kingdom_c' },
                        { type: 'text', title: '界名', width: '100', name: 'kingdom' },
                        { type: 'text', title: '門中文', width: '120', name: 'phylum_c' },
                        { type: 'text', title: '門名', width: '80', name: 'phylum' },
                        { type: 'text', title: '數量', width: '200', name: 'total' },
                    ],
                    onsort: this.sort,
                    readOnly: true,
                    onload: this.onload,
                };
            }
        },
        created() {
            this.$http.get('/api/phylum')
                .then(({ data }) => {
                    this.phyla = data.data;
                    this.total = data.total;
                    this.jExcelObj.setData(this.jExcelOptions.data);
                });
        },
        mounted() {
            const jExcelObj = jexcel(this.$refs['spreadsheet'], this.jExcelOptions);
            Object.assign(this, { jExcelObj });
        },
        methods: {
            onload() {
                this.jExcelOptions.data.forEach((data, index) => {
                    const html = document.querySelector(`td[data-y="${index}"][data-x="5"]`);
                    // this.appendTo(html.innerHTML = '<router-link to="/test">123</router-link>';
                })
            }
        }
    }
</script>

<style lang="scss" scoped>
    @import '@/sass/fix-table';
</style>
