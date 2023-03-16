import en from './localization/en';
import vi from './localization/vi';
import Vue from 'vue';
import Vuex from 'vuex';

window.Vue = require("vue");
Vue.use(Vuex);
const store = new Vuex.Store({
    state: {
        currentLocale: 'en',
        localization: {
            en,
            vi
        }
    },
    getters: {
        localizedStrings(state) {
            return state.localization[state.currentLocale];
        }
    },
    // ...
});

export default store;