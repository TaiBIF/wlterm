import Vue from 'vue';
import VueRouter from 'vue-router';

import Index from '@/js/pages/Index';
import Photos from '@/js/pages/Photos';
import Stations from '@/js/pages/Stations';
import Maps from '@/js/pages/Maps';
import Phylum from '@/js/pages/Phylum';
import Class from '@/js/pages/Class';
import Order from '@/js/pages/Order';
import Family from '@/js/pages/Family';
import Species from '@/js/pages/Species';
import Occurrences from '@/js/pages/Occurrences';
import WaterQuality from '@/js/pages/WaterQuality';
import ElementFlux from '@/js/pages/ElementFlux';
import Temperature from '@/js/pages/Temperature';
import AlgaeDebris from '@/js/pages/AlgaeDebris';
import RiverDischargeEstimation from '@/js/pages/RiverDischargeEstimation';
import Record from '@/js/pages/Record';

Vue.use(VueRouter);

const router = new VueRouter ({
    mode: 'history',
    scrollBehavior() {
        return { x: 0, y: 0 };
    },
    routes: [
        {
            path: '/',
            name: 'home',
            component: Index,
        },
        {
            path: '/photos',
            name: 'photos',
            component: Photos,
        },
        {
            path: '/stations',
            name: 'stations',
            component: Stations,
        },
        {
            path: '/maps',
            name: 'maps',
            component: Maps,
        },
        {
            path: '/phylum',
            name: 'phylum',
            component: Phylum,
        },
        {
            path: '/class',
            name: 'class',
            component: Class,
        },
        {
            path: '/order',
            name: 'order',
            component: Order,
        },
        {
            path: '/family',
            name: 'family',
            component: Family,
        },
        {
            path: '/species',
            name: 'species',
            component: Species,
        },
        {
            path: '/occurrences',
            name: 'occurrences',
            component: Occurrences,
        },
        {
            path: '/water-quality',
            name: 'water-quality',
            component: WaterQuality,
        },
        {
            path: '/records/:recordId',
            name: 'record',
            component: Record,
        },
        {
            path: '/element-flux',
            name: 'element-flux',
            component: ElementFlux,
        },
        {
            path: '/temperature',
            name: 'temperature',
            component: Temperature,
        },
        {
            path: '/algae-debris',
            name: 'algae-debris',
            component: AlgaeDebris,
        },
        {
            path: '/river-discharge-estimation',
            name: 'river-discharge-estimation',
            component: RiverDischargeEstimation,
        },
    ]
});

export default router;
