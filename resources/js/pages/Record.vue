<template>
    <div class="page">

        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <td colspan="4" v-text="title" />
                </tr>
            </thead>
            <tbody>
            <tr>
                <td nowrap="">調查日期</td>
                <td colspan="3" v-text="record.date"/>
            </tr>
            <tr>
                <td>測站代號</td>
                <td v-text="record.station.id"></td>
                <td>調查地點</td>
                <td v-text="record.station.locality_chinese">桃山北溪</td>
            </tr>
            <tr>
                <td>高度上限</td>
                <td v-text="`${record.station.maximum_elevation} M`"></td>
                <td>深度上限</td>
                <td v-text="`${record.station.minimum_depth} M`"></td>
            </tr>
            <tr>
                <td>高度下限</td>
                <td v-text="`${record.station.minimum_elevation} M`">1891&nbsp;</td>
                <td>深度下限</td>
                <td v-text="`${record.station.maximum_depth} M`"/>
            </tr>
            <tr>
                <td>緯度</td>
                <td v-text="record.station.latitude"/>
                <td>經度</td>
                <td v-text="record.station.longitude"/>
            </tr>
            <tr>
                <td>全天光空域</td>
                <td v-text="record.station['全天光空域']"/>

                <td>直射光空域</td>
                <td v-text="record.station['直射光空域']"/>
            </tr>
            <tr>
                <td>經緯度誤差</td>
                <td colspan="3" v-text="record.station.coordinate_precision">15&nbsp;</td>
            </tr>
            <template v-if="type === 'occurrence'">
                <tr>
                    <td colspan="1">學名</td>
                    <td colspan="3" v-text="`${record.scientific_name} ${record.author} ${record.chinese}`" />
                </tr>
                <tr>
                    <td colspan="1">科名</td>
                    <td colspan="3" v-text="`${record.family} ${record.family_c}`" />
                </tr>
                <tr>
                    <td>個體數</td>
                    <td v-text="record.individual_count" />
                    <td>覆蓋率</td>
                    <td v-text="record.cover_rate"/>
                </tr>
                <tr>
                    <td>體長</td>
                    <td v-text="`${record.body_length} ${record.body_length_unit}`" />
                    <td>量測值</td>
                    <td v-text="`${record.biomass} ${record.biomass_unit}`"/>
                </tr>
            </template>
            <template v-if="type === 'algae-debris'">
                <tr>
                    <td>調查項目</td>
                    <td v-text="record.item_chinese" />
                    <td>調查值</td>
                    <td v-text="record.biomass" />
                </tr>
                <tr>
                    <td>單位</td>
                    <td colspan="3" v-text="record.unit" />
                </tr>
            </template>
            <tr>
                <td>調查點描述</td>
                <td colspan="3" v-text="record.station.locality_describe"/>
            </tr>
            <tr>
                <td>調查者</td>
                <td v-text="record.collector"></td>
                <td>調查者中文名</td>
                <td v-text="record.collector_chinese"></td>
            </tr>

            <template v-if="type === 'element-flux' || type === 'water-quality'">
                <tr>
                    <td>氫離子濃度</td>
                    <td v-text="record.PH"></td>
                    <td>導電度 μS/cm</td>
                    <td v-text="record.Conductivity"></td>
                </tr>
            </template>
            <template v-if="type === 'element-flux'">
                <tr>
                    <td>水溫&nbsp;℃</td>
                    <td v-text="record.temperature"></td>
                    <td></td>
                    <td></td>
                </tr>
            </template>
            <template v-if="type === 'water-quality'">
                <tr>
                    <td>溶氧 mg/L</td>
                    <td v-text="record.DO"/>
                    <td>濁度 mg/L</td>
                    <td v-text="record.Clarity" />
                </tr>
                <tr>
                    <td>矽酸鹽 mg/L</td>
                    <td v-text="record.Alkali_Silicate" />
                    <td>生化需氧量 mg/L</td>
                    <td v-text="record.BOD" />
                </tr>
                <tr>
                    <td style="white-space: nowrap;">硝酸鹽氮 mg/L</td>
                    <td v-text="record.NO3_N" />
                    <td>亞硝酸鹽氮 mg/L</td>
                    <td v-text="record.NO2_N" />
                </tr>
                <tr>
                    <td>硫酸鹽 mg/L</td>
                    <td v-text="record.SO4" />
                    <td>氯鹽 mg/L</td>
                    <td v-text="record.Chloride" />
                </tr>
                <tr>
                    <td>磷酸鹽 mg/L</td>
                    <td v-text="record.Phosphate" />
                    <td>總磷 mg/L</td>
                    <td v-text="record.TP" />
                </tr>
                <tr>
                    <td>氨氮 mg/L</td>
                    <td v-text="record.AN" />
                    <td>總有機碳 mg/L</td>
                    <td v-text="record.TOC" />
                </tr>
            </template>
            <template v-else-if="type === 'element-flux'">
                <tr>
                    <td>磷酸鹽 mg/L</td>
                    <td v-text="record.Phosphate"></td>
                    <td>鈉 mg/L</td>
                    <td v-text="record.Na"/>
                </tr>
                <tr>
                    <td>硝酸鹽 mg/L</td>
                    <td v-text="record.nitrate"></td>
                    <td>鎂 mg/L</td>
                    <td v-text="record.Mg"></td>
                </tr>
                <tr>
                    <td>硫酸鹽 mg/L</td>
                    <td v-text="record.SO4"></td>
                    <td>鉀 mg/L</td>
                    <td v-text="record.K"></td>

                </tr>
                <tr>
                    <td>氯鹽 mg/L</td>
                    <td v-text="record.Chloride"></td>
                    <td>鈣 mg/L</td>
                    <td v-text="record.Ca"/>
                </tr>
                <tr>
                    <td>氟 mg/L</td>
                    <td v-text="record.Fl"/>
                    <td>鍶 mg/L</td>
                    <td v-text="record.Sr"/>
                </tr>
                <tr>
                    <td>氨</td>
                    <td v-text="record.AN"/>
                    <td>鋇 mg/L</td>
                    <td v-text="record.Ba"></td>
                </tr>
                <tr>
                    <td>矽 mg/L</td>
                    <td v-text="record.Si">&nbsp;</td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td style="white-space: nowrap">亞硝酸鹽 mg/L</td>
                    <td v-text="record.NO2"></td>
                    <td>溶解有機碳 mg/L</td>
                    <td v-text="record.DOC"/>
                </tr>
            </template>
            <tr>
                <td colspan="4"></td>
            </tr>
            <tr>
                <td>計畫名稱</td>
                <td colspan="3" v-text="record.project.projectname"></td>
            </tr>
            <tr>
                <td>執行期間</td>
                <td colspan="3" v-text="record.project.executiveperiod"></td>
            </tr>
            <tr>
                <td>委託單位</td>
                <td colspan="3" v-text="record.project.associatedagency"></td>
            </tr>
            <tr>
                <td>執行單位</td>
                <td colspan="3" v-text="record.project.executiveagency"></td>
            </tr>
            <tr>
                <td>主持人</td>
                <td colspan="3" v-text="`${record.project.hostname_chinese}, ${record.project.hostname}`"></td>
            </tr>
            <tr>
                <td>地址</td>
                <td colspan="3" v-text="record.project.hostaddress"></td>
            </tr>
            <tr>
                <td>E-Mail</td>
                <td colspan="3" v-text="record.project.hoste_mail">hjlin@dragon.nchu.edu.tw&nbsp;</td>
            </tr>
            <tr>
                <td nowrap="">協同主持人</td>
                <td colspan="3" v-text="record.project.coordination"></td>
            </tr>
            <tr>
                <td>執行方法</td>
                <td colspan="3" v-text="record.project.executiveway"></td>
            </tr>
            <tr>
                <td>計畫摘要</td>
                <td colspan="3" v-text="record.project.projectsummary"/>
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
        computed: {
            title() {
                let word = '';
                let code = '';
                switch (this.type) {
                    case 'water-quality':
                        word = '水質調查記錄編號';
                        code = 'WQ';
                        break;
                    case 'element-flux':
                        word = '主要元素通量調查記錄編號';
                        code = 'WQ';
                        break;
                    case 'algae-debris':
                        word = '藻類與有機碎屑調查記錄編號';
                        code = 'AO';
                        break;
                    case 'occurrence':
                        word = '記錄編號';
                        break;
                }

                return `${word}:urn:lsid:wlterm.biodiv.sinica.edu.tw:observation:${code}${this.record.record_id}`;
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

