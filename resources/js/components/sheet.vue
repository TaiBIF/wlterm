<template>
    <div class="sheet-container">
        <div class="helper">
            <p class="text-muted"></p>
            *點選標題兩下可排序
        </div>
        <tr ref="filter">
            <td width="50">
                <i class="fas fa-search"></i>
            </td>
            <td v-for="column in columns" :width="column.width">
                <input type="text"
                       v-if="column.searchable"
                       :placeholder="`搜尋${column.title}`"
                       :value="value.searchParams[column.name]"
                       v-on:focus="focusSearch"
                       v-on:change="(e) => changeQuery(e.target.value, column.name)"
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
                            <slot name="functions" v-bind:datum="datum">&nbsp;<br/></slot>
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
            },
            type: {
                type: String,
                default: '',
            },
            sortable: {
                type: Boolean,
                default: true,
            },
            value: {
                type: Object,
                default() {
                    return {};
                },
            }
        },
        computed: {
            jExcelOptions() {
                return {
                    data: this.data,
                        columnSorting: this.sortable,
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
        },
        mounted() {
            const jExcelObj = jexcel(this.$refs['spreadsheet'], this.jExcelOptions);
            Object.assign(this, { jExcelObj });

            document.querySelector('table[class="jexcel"] thead')
                .append(this.$refs['filter']);
        },
        methods: {
            changeQuery(v, column) {
                let query = this.value;
                query.searchParams[column] = v;
                this.$emit('input', query);
                this.$emit('search');
            },
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

