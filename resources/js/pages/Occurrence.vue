<template>
    <div class="page">
        <table class="table table-striped table-bordered">
            <thead>
            <tr>
                <td colspan="4">
                    記錄編號：urn:lsid:wlterm.biodiv.sinica.edu.tw:observation:{{ record.record_id }}
                </td>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td nowrap="">學名</td>
                <td v-text="record.scientific_name"></td>
                <td>測站編號</td>
                <td v-text="record.station.id"></td>
            </tr>
            <tr>
                <td>科名</td>
                <td v-text="`${record.species.family} ${record.species.family_c}`"></td>
                <td>調查地點</td>
                <td v-text="record.station.locality_chinese"></td>
            </tr>
            <tr>
                <td>個體數</td>
                <td v-text="record.individual_count"></td>
                <td>緯度</td>
                <td v-text="record.station.latitude"></td>
            </tr>
            <tr>
                <td>覆蓋率</td>
                <td v-text="record.cover_rate"></td>
                <td>經度</td>
                <td v-text="record.station.longitude"></td>
            </tr>
            <tr>
                <td>體長</td>
                <td v-text="`${record.body_length} ${record.body_length_unit}`"></td>
                <td>經緯度誤差</td>
                <td v-text="`${record.station.coordinate_precision}M`"></td>
            </tr>
            <tr>
                <td>量測值</td>
                <td v-text="`${record.biomass} ${record.biomass_unit}`"></td>
                <td>深度上限</td>
                <td v-text="`${record.station.minimum_depth}M`"></td>
            </tr>

            <tr >
                <td>調查者</td>
                <td v-text="record.collector_chinese"></td>
                <td>深度下限</td>
                <td v-text="`${record.station.maximum_depth}M`"></td>
            </tr>
            <tr>
                <td>調查方法</td>
                <td v-text="record.examine_way"></td>
                <td>高度上限</td>
                <td v-text="`${record.station.maximum_elevation}M`"></td>
            </tr>
            <tr>
                <td>調查日期</td>
                <td v-text="record.date"></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>地點描述</td>
                <td colspan="3" v-text="record.station.locality_describe"></td>
            </tr>

            <tr>
                <td colspan="6"></td>
            </tr>
            <tr>
                <td>計畫名稱</td>
                <td colspan="5" v-text="record.project.projectname"></td>
            </tr>
            <tr>
                <td>執行期間</td>
                <td colspan="5" v-text="record.project.executiveperiod"></td>
            </tr>
            <tr>
                <td>委託單位</td>
                <td colspan="5" v-text="record.project.associatedagency"></td>
            </tr>
            <tr>
                <td>執行單位</td>
                <td colspan="5" v-text="record.project.executiveagency"></td>
            </tr>
            <tr>
                <td>主持人</td>
                <td colspan="5" v-text="`${record.project.hostname_chinese}, ${record.project.hostname}`"></td>
            </tr>
            <tr>
                <td>地址</td>
                <td colspan="5" v-text="record.project.hostaddress"></td>
            </tr>
            <tr>
                <td>E-Mail</td>
                <td colspan="5" v-text="record.project.hoste_mail">hjlin@dragon.nchu.edu.tw&nbsp;</td>
            </tr>
            <tr>
                <td nowrap="">協同主持人</td>
                <td colspan="5" v-text="record.project.coordination"></td>
            </tr>
            <tr>
                <td>執行方法</td>
                <td colspan="5" v-text="record.project.executiveway"></td>
            </tr>
            <tr>
                <td>計畫摘要</td>
                <td colspan="5" v-text="record.project.projectsummary"></td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
    export default {
        name: 'Occurrence',
        data() {
            return {
                record: {
                    station: {},
                    project: {},
                    species: {},
                },
                total: 0,
            }
        },
        mounted() {
            const { recordId } = this.$route.params;
            this.type = this.$route.query.type;

            this.$http.get(`/api/occurrences/${recordId}`)
                .then(({ data }) => {
                    this.record = data.occurence;
                });
        }
    }
</script>

<style lang="scss" scoped>
    .page {
        max-width: 800px;
    }

    label {
        font-weight: bold;
    }
</style>

