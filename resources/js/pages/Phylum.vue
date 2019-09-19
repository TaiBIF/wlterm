<template>
    <div>
        <h6>生物門別數量統計&nbsp;<small class="text-muted">共 {{ total }} 門</small></h6>
        <table class="table table-sm table-striped table-fixed table-bordered">
            <caption>生物門別數量統計&nbsp;<small class="text-muted">共 {{ total }} 門</small></caption>
            <thead>
            <tr>
                <th>界中文</th>
                <th>界名</th>
                <th>門中文</th>
                <th>門名</th>
                <th>數量</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="phylum in phyla">
                <td v-text="phylum.kingdom_c"></td>
                <td v-text="phylum.kingdom"></td>
                <td v-text="phylum.phylum_c"></td>
                <td v-text="phylum.phylum"></td>
                <td v-text="phylum.total"></td>
                <td>
                    <router-link :to="`/occurrences?phylum=${phylum.phylum}`">查看</router-link>
                    <router-link :to="`/maps`">地圖</router-link>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
    export default {
        name: "Phylum",
        data() {
            return {
                phyla: [],
                total: 0,
            }
        },
        created() {
            this.$http.get('/api/phylum')
                .then(({ data }) => {
                    this.phyla = data.data;
                    this.total = data.total;
                });
        }
    }
</script>

<style lang="scss" scoped>
    @import '@/sass/fix-table';
</style>
