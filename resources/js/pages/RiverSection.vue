<template>
    <div>
        <h5>日期：{{ $route.params.date }}</h5>
        <hr />
        <table class="table table-sm">
            <thead>
                <tr>
                    <th>編號</th>
                    <th>後視點海拔高</th>
                    <th>後視點經度</th>
                    <th>後視點緯度</th>
                    <th>轉點海拔高</th>
                    <th>轉點經度</th>
                    <th>轉點緯度</th>
                    <th>儀器高度</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td v-text="bsTp.bs_and_tp"></td>
                    <td v-text="bsTp.bs_altitude"></td>
                    <td v-text="bsTp.bs_longitude"></td>
                    <td v-text="bsTp.bs_latitude"></td>
                    <td v-text="bsTp.tp_altitude"></td>
                    <td v-text="bsTp.tp_longitude"></td>
                    <td v-text="bsTp.tp_latitude"></td>
                    <td v-text="bsTp.elevation_of_instrument"></td>
                </tr>
            </tbody>
        </table>
        <hr />
        <table class="table table-sm" v-for="(section, sectionName) in sections">
            <thead>
                <tr>
                    <th>斷面</th>
                    <th>點位</th>
                    <th>度</th>
                    <th>弧度角</th>
                    <th>水平距(公尺)</th>
                    <th>高程差(公尺)</th>
                    <th>經度</th>
                    <th>緯度</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="point in section">
                    <td v-text="sectionName" v-if="point.measure_point == 1" rowspan="10"></td>
                    <td v-text="point.measure_point"></td>
                    <td v-text="point.angle"></td>
                    <td v-text="point.radian"></td>
                    <td v-text="point.horizontal_distance"></td>
                    <td v-text="point.elevation_difference"></td>
                    <td v-text="point.longitude"></td>
                    <td v-text="point.latitude"></td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
    export default {
        name: 'RiverSection',
        data() {
            return {
                bsTp: {},
                sections: [],
            }
        },
        mounted() {
            const { params } = this.$route;
            this.$http.get(`/api/river-sections/${params.sectionId}/${params.date}`)
                .then(({ data: { bs_tp, sections }}) => {
                    this.bsTp = bs_tp;
                    this.sections = sections;
                });
        },
        methods: {

        }
    }
</script>

<style lang="scss" scoped>
    @import '@/sass/fix-table';
</style>
