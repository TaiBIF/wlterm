<template>
    <div class="popup">
        <bold>{{ station.locality_chinese }}</bold>
        <br/><br/>
        <div class="section" v-if="occurrences">
            物種紀錄:
            <router-link :to="`/occurrences?locality_chinese=${station.locality_chinese}&date=${date}`" target="_blank">
                <i class="fas fa-external-link-alt"></i>
            </router-link>
        </div>

        <div class="section" v-if="water">
            水質調查:
            <router-link :to="`/water-quality?id=${station.id}&locality_chinese=${station.locality_chinese}&date=${date}`" target="_blank">
                <i class="fas fa-external-link-alt"></i>
            </router-link>
        </div>

        <div class="section" v-if="elementFlux">
            元素通量:
            <router-link :to="`/element-flux?station_id=${station.id}&locality_chinese=${station.locality_chinese}&date=${date}`" target="_blank">
                <i class="fas fa-external-link-alt"></i>
            </router-link>
        </div>

        <div  class="section" v-if="elementFlux">
            溫度監測:
            <router-link :to="`/temperature?station_id=${station.id}&locality_chinese=${station.locality_chinese}&date=${date}`" target="_blank">
                <i class="fas fa-external-link-alt"></i>
            </router-link>
        </div>

        <div class="section" v-if="algaeDebris">
            藻類與碎屑:
            <router-link :to="`/algae-debris?station_id=${station.id}&locality_chinese=${station.locality_chinese}&date=${date}`" target="_blank">
                <i class="fas fa-external-link-alt"></i>
            </router-link>
        </div>
    </div>
</template>
<script>
    export default  {
        props: {
            marker: {
                type: Object,
                required: true,
            },
            date: {
                type: String,
                required: true,
            }
        },
        mounted() {
            this.fetchStation();
        },
        methods: {
            fetchStation() {
                const stationId = this.marker.stationId;
                this.$http.get('/api/stations/' + stationId + `?date=${this.date}`)
                    .then(({ data: { station, occurrences, water, temperature, elementFlux, algaeDebris }
                    }) => {
                        this.station = station;
                        this.occurrences = occurrences;
                        this.water = water;
                        this.temperature = temperature;
                        this.elementFlux = elementFlux;
                        this.algaeDebris = algaeDebris;
                    });
            }
        },
        data() {
            return {
                station: {},
                occurrences: false,
                water: false,
                temperature:false,
                elementFlux: false,
                algaeDebris: false,
            }
        }
    }
</script>
