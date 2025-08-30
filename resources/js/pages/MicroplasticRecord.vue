<template>
    <div class="p-4">
        <table class="table table-striped table-bordered">
            <thead>
            <tr>
                <td colspan="4" v-text="title"/>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td width="120">調查日期</td>
                <td colspan="3" v-text="record.date"/>
            </tr>
            <template v-if="record.station">
                <tr>
                    <td>測站代號</td>
                    <td v-text="record.station.id"></td>
                    <td>調查地點</td>
                    <td v-text="record.station.locality_chinese"/>
                </tr>
                <tr>
                    <td>高度上限</td>
                    <td v-text="`${record.station.maximum_elevation} M`"></td>
                    <td>深度上限</td>
                    <td v-text="`${record.station.minimum_depth} M`"></td>
                </tr>
                <tr>
                    <td>高度下限</td>
                    <td v-text="`${record.station.minimum_elevation} M`"/>
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
                    <td colspan="3" v-text="record.station.coordinate_precision"/>
                </tr>
            </template>
                <tr>
                    <td>調查項目</td>
                    <td v-text="record.item_chinese"/>
                    <td></td>
                    <td></td>
                </tr>
            <tr>
                <td>調查者</td>
                <td v-text="record.collector"></td>
                <td>調查者中文名</td>
                <td v-text="record.collector_chinese"></td>
            </tr>
            <tr>
                <td colspan="4"></td>
            </tr>
            <tr>
                <td>PET</td>
                <td>{{ record.pet }}</td>
                <td>PE</td>
                <td>{{ record.pe }}</td>
            </tr>
            <tr>
                <td>PVC</td>
                <td>{{ record.pvc }}</td>
                <td>PP</td>
                <td>{{ record.pp }}</td>
            </tr>
            <tr>
                <td>PS</td>
                <td>{{ record.ps }}</td>
                <td>PC</td>
                <td>{{ record.pc }}</td>
            </tr>
            <tr>
                <td>PA</td>
                <td>{{ record.pa }}</td>
                <td>PE+PP</td>
                <td>{{ record.pe_pp }}</td>
            </tr>
            <tr>
                <td>Rayon</td>
                <td>{{ record.rayon ? record.rayon : '-' }}</td>
                <td>EEA</td>
                <td>{{ record.eea ? record.rayon : '-' }}</td>
            </tr>
            <tr>
                <td>聚酯纖維</td>
                <td>{{ record.polyester ? record.polyester : '-' }}</td>
                <td>PMMA</td>
                <td>{{ record.pmma ? record.pmma : '-' }}</td>
            </tr>
            <tr>
                <td>PU</td>
                <td>{{ record.pu ? record.pu : '-' }}</td>
                <td>採樣量</td>
                <td>{{ record.sample_size ? record.sample_size : '-' }}</td>
            </tr>
            <tr>
                <td>總量</td>
                <td>{{ record.mps }}</td>
                <td>單位</td>
                <td>{{ record.unit }}</td>
            </tr>
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
                <td colspan="3" v-text="record.project.hostaddress"/>
            </tr>
            <tr>
                <td>E-Mail</td>
                <td colspan="3" v-text="record.project.hoste_mail"/>
            </tr>
            <tr v-if="record.project.coordination">
                <td nowrap="">協同主持人</td>
                <td colspan="3" v-text="record.project.coordination"/>
            </tr>
            <tr>
                <td>執行方法</td>
                <td colspan="3" v-text="record.project.executiveway"/>
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
    name: 'MicroplasticRecord',
    data() {
        return {
            record: {
                station: {},
                project: {},
            },
            total: 0,
        }
    },
    computed: {
        title() {
            let word = '環境塑膠微粒調查記錄編號';
            let code = 'EM';

            return `${ word }:urn:lsid:wlterm.biodiv.sinica.edu.tw:observation:${ code }${ this.record.record_id }`;
        },
    },
    mounted() {
        const {recordId} = this.$route.params;

        this.$http.get(`/api/env-microplastics/${ recordId }`)
            .then(({data}) => {
                this.record = data.record;
            });
    },
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

