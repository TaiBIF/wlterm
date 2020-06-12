<template>
    <div>
        <h6>生物門別數量統計&nbsp;<small class="text-muted">共 {{ total }} 門</small></h6>
        <sheet
            :data="phyla"
            :columns="columns"
            :is-loading="isLoading"
        >
            <template v-slot:functions="props">
                <router-link :to="`/occurrences?phylum=${props.datum.phylum}`">
                    查看
                </router-link>
                &nbsp;
                <router-link :to="`/maps?phylum=${props.datum.phylum}`">
                    地圖
                </router-link>
            </template>
        </sheet>
        <div class="myexcel text-muted caption">
            生物門別數量統計&nbsp;<small class="text-muted">共 {{ total }} 門</small>
        </div>
    </div>
</template>

<script>
    import sheet from '../components/sheet';
    export default {
        name: "Phylum",
        components: {
            sheet,
        },
        data() {
            return {
                phyla: [],
                isLoading: true,
                columns: [
                    { type: 'text', title: '界中文', width: '100', name: 'kingdom_c' },
                    { type: 'text', title: '界名', width: '100', name: 'kingdom' },
                    { type: 'text', title: '門中文', width: '120', name: 'phylum_c' },
                    { type: 'text', title: '門名', width: '80', name: 'phylum' },
                    { type: 'text', title: '數量', width: '200', name: 'total' },
                ],
                total: 0,
            }
        },
        created() {
            this.$http.get('/api/phylum')
                .then(({ data }) => {
                    this.phyla = data.data;
                    this.total = data.total;
                    this.isLoading = false;
                });
        },
    }
</script>

<style lang="scss" scoped>
    @import '@/sass/fix-table';
</style>
