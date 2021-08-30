import './bootstrap';
import Vue from 'vue';
import Axios from 'axios';
import Highcharts from 'highcharts';
import HighchartsVue from 'highcharts-vue';
import HeatMapInit from 'highcharts/modules/heatmap';

HeatMapInit(Highcharts);

import Routes from './routes';
import App from './pages/App';
import SortIcon from './components/table/sort-icon';

Vue.prototype.$http = Axios;

Vue.use(HighchartsVue);

Vue.component('sort-icon', SortIcon);

new Vue({
    el: '#app',
    router: Routes,
    render: h => h(App)
})

