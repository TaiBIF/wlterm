<template>
    <div>
        <h5>日期：{{ $route.params.date }}</h5>
        <hr />
        <h5 class="subtitle is-5 text-info">後視點與轉點</h5>
        <p>
            <strong>後視點與轉點編號</strong>： {{ bsTp.bs_and_tp }}<br/>
            <strong>儀器高度</strong>： {{ bsTp.elevation_of_instrument }} 公尺
        </p>
        <table class="table table-sm">
            <thead>
                <tr>
                    <th></th>
                    <th>方向角(度)</th>
                    <th>弧度角(度)</th>
                    <th>水平距(公尺)</th>
                    <th>高程差(公尺)</th>
                    <th>經度</th>
                    <th>緯度</th>
                    <th>海拔高(公尺)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th>後視點</th>
                    <td v-text="bsTp.bs_angle"></td>
                    <td v-text="bsTp.bs_radian"></td>
                    <td v-text="bsTp.bs_horizontal_distance"></td>
                    <td v-text="bsTp.bs_elevation_difference"></td>
                    <td v-text="bsTp.bs_longitude"></td>
                    <td v-text="bsTp.bs_latitude"></td>
                    <td v-text="bsTp.bs_altitude"></td>

                </tr>
                <tr>
                    <th>轉點</th>
                    <td v-text="bsTp.tp_angle"></td>
                    <td v-text="bsTp.tp_radian"></td>
                    <td v-text="bsTp.tp_horizontal_distance"></td>
                    <td v-text="bsTp.tp_elevation_difference"></td>
                    <td v-text="bsTp.tp_longitude"></td>
                    <td v-text="bsTp.tp_latitude"></td>
                    <td v-text="bsTp.tp_altitude"></td>
                </tr>
            </tbody>
        </table>
        <br />
        <h5 class="subtitle is-5 text-info">斷面測量點</h5>
        <table class="table table-sm" v-for="(section, sectionName) in sections">
            <thead>
                <tr>
                    <th>斷面</th>
                    <th>點位</th>
                    <th>測量角(度)</th>
                    <th>弧度角(度)</th>
                    <th>水平距(公尺)</th>
                    <th>高程差(公尺)</th>
                    <th>經度</th>
                    <th>緯度</th>
                    <th>海拔高度(公尺)</th>
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
                    <td v-text="point.altitude"></td>
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
