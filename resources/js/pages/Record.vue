<template>
    <div class="page">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <td colspan="6">
                        藻類與有機碎屑調查記錄編號：urn:lsid:wlterm.biodiv.sinica.edu.tw:observation:AO791
                    </td>
                </tr>
            </thead>
            <tbody>
            <tr>
                <td nowrap="">調查日期</td>
                <td v-text="record.date"></td>
                <td>高度上限</td>
                <td></td>
                <td>深度上限</td>
                <td ></td>
            </tr>
            <tr>
                <td>測站代號</td>
                <td v-text="record.station.id"></td>
                <td>高度下限</td>
                <td v-text="record.station.maximum_elevation">1891&nbsp;</td>
                <td>深度下限</td>
                <td v-text="record.station.maximum_depth">0.52&nbsp;</td>
            </tr>
            <tr>
                <td>調查地點</td>
                <td v-text="record.station.locality_chinese">桃山北溪</td>
                <td>全天光空域</td>
                <td>51.56&nbsp;</td>
                <td>調查項目</td>
                <td>藻類生物量&nbsp;</td>
            </tr>
            <tr v-if="type !== 'water-quality'">
                <td>緯度</td>
                <td v-text="record.station.longitude">24.39816</td>
                <td>直射光空域</td>
                <td>59.35&nbsp;</td>
                <td>調查值</td>
                <td>2.869</td>
            </tr>
            <tr v-if="type !== 'water-quality'">
                <td>經度</td>
                <td v-text="record.station.latitude"></td>
                <td>調查者</td>
                <td v-text="record.collector"></td>
                <td>單位</td>
                <td v-text="record.unit">mg Chla m-2&nbsp;</td>
            </tr>
            <tr>
                <td>經緯度誤差</td>
                <td>15&nbsp;</td>
                <td>調查者中文名</td>
                <td v-text="record.collector_chinese"></td>
                <td>　</td>
                <td>　</td>
            </tr>

            <tr v-if="type === 'element-flux' || type === 'water-quality'">
                <td>緯度</td>
                <td v-text="record.station.latitude">24.39816</td>
                <td>水溫&nbsp; ℃</td>
                <td v-text="record.temperature"></td>
                <td>鈉 mg/L</td>
                <td v-text="record.Na"></td>
            </tr>
            <tr v-if="type === 'element-flux' || type === 'water-quality'">
                <td>經度</td>
                <td v-text="record.station.longitude">121.30928</td>
                <td>氫離子濃度</td>
                <td v-text="record.PH"></td>
                <td>鎂 mg/L</td>
                <td v-text="record.Mg"></td>
            </tr>
            <tr v-if="type === 'element-flux' || type === 'water-quality'">
                <td>經緯度誤差</td>
                <td v-text="record.coordinate_precision">15&nbsp;</td>
                <td>導電度 μS/cm</td>
                <td v-text="record.Conductivity"></td>
                <td>鉀 mg/L</td>
                <td v-text="record.K"></td>
            </tr>
            <tr v-if="type === 'element-flux' || type === 'water-quality'">
                <td>深度上限</td>
                <td v-text="record.minimum_depth">0.07&nbsp;</td>
                <td>硝酸鹽 mg/L</td>
                <td v-text="record.nitrate"></td>
                <td>鈣 mg/L</td>
                <td v-text="record.Ca"></td>
            </tr>
            <tr v-if="type === 'element-flux' || type === 'water-quality'">
                <td>深度下限</td>
                <td v-text="record.maximum_depth"></td>
                <td>硫酸鹽 mg/L</td>
                <td v-text="record.SO4"></td>
                <td>鍶 mg/L</td>
                <td v-text="record.Sr"></td>
            </tr>
            <tr v-if="type === 'element-flux' || type === 'water-quality'">
                <td>高度上限</td>
                <td v-text="record.maximum_elevation"></td>
                <td>磷酸鹽 mg/L</td>
                <td v-text="record.Phosphate"></td>
                <td>鋇 mg/L</td>
                <td v-text="record.Ba"></td>
            </tr>
            <tr v-if="type === 'element-flux' || type === 'water-quality'">
                <td>高度下限</td>
                <td v-text="record.minimum_elevation">1891&nbsp;</td>
                <td>氯鹽 mg/L</td>
                <td v-text="record.Chloride"></td>
                <td>矽 mg/L</td>
                <td v-text="record.Si">&nbsp;</td>
            </tr>
            <tr>
                <td>調查點描述</td>
                <td colspan="5">與桃山西溪匯流後進入七家灣溪，河川上多礫石&nbsp;</td>
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
        name: 'WaterQualityPage',
        data() {
            return {
                type: 'algae-debris',
                record: {
                    station: {},
                    project: {},
                },
                total: 0,
            }
        },
        mounted() {
            const { recordId } = this.$route.params;
            this.type = this.$route.query.type;

            this.$http.get(`/api/records/${recordId}?type=${this.type}`)
                .then(({ data }) => {
                    this.record = data.record;
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

