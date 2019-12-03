<template>
    <div class="sheet-container">
        <tr ref="filter">
            <td width="50">
                <i class="fas fa-search"></i>
            </td>
            <td v-for="column in columns" :width="column.width">
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
                    <tr v-for="datum in data">
                        <td class="jexcel_row">
                            <router-link
                                v-if="occurenceContent"
                                :to="`/occurrences/${datum.record_id}`">
                                內容
                            </router-link>

                            <router-link
                                v-if="datum.checkUrl"
                                :to="datum.checkUrl"
                            >
                                查看
                            </router-link>
                            &nbsp;
                            <router-link
                                v-if="datum.mapUrl"
                                :to="datum.mapUrl"
                            >
                                地圖
                            </router-link>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="myexcel spinner-container d-flex align-items-center" v-if="isLoading">
            <strong>載入中...</strong>
            <div class="spinner-border text-secondary ml-auto" role="status">
            </div>
        </div>
    </div>

</template>
<script>
    import jexcel from "jexcel";
    export default {
        props: {
            data: {
                type: Array,
                required: true,
            },
            checkUrl: {
                type: String,
            },
            mapUrl: {
                type: String,
            },
            columns: {
                type: Array,
                required: true,
            },
            isLoading: {
                type: Boolean,
                default: true,
            },
            occurenceContent: {
                type: Boolean,
                default: false,
            }
        },
        data() {
            return {
                searchParams: {},
            }
        },
        computed: {
            jExcelOptions() {
                return {
                    data: this.data,
                        columnSorting: true,
                        editable: true,
                        columns: this.columns,
                        onsort: this.onsort,
                        readOnly: true,
                }
            }
        },
        watch: {
            data: {
                handler() {
                    this.jExcelObj.setData(this.data);
                },
                deep: true,
            },
            searchParams: {
                handler(values) {
                    this.$emit('search', values);
                },
                deep: true,
            }
        },
        mounted() {
            const jExcelObj = jexcel(this.$refs['spreadsheet'], this.jExcelOptions);
            Object.assign(this, { jExcelObj });

            document.querySelector('table[class="jexcel"] thead')
                .append(this.$refs['filter']);
        },
        methods: {
            focusSearch() {
                this.jExcelObj.resetSelection(true);
            },
            onLoad() {
                this.$emit('onLoad');
            },
            onsort(instance, column, direction) {
                this.$emit('sort', column, direction);
            }
        }
    }

</script>

