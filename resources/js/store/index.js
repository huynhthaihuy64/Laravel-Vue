import en from './localization/en';
import vi from './localization/vi';
import Vue from 'vue';
import Vuex from 'vuex';

window.Vue = require("vue");
Vue.use(Vuex);
const store = new Vuex.Store({
    state: {
        currentLocale: "en",
        localization: {
            en,
            vi,
        },
        showConfirmModal: false,
    },
    mutations: {
        toggleConfirmModal(state) {
            state.showConfirmModal = !state.showConfirmModal;
        },
    },
    getters: {
        localizedStrings(state) {
            return state.localization[state.currentLocale];
        },
    },
    actions: {
        toggleConfirmModal(context) {
            context.commit("toggleConfirmModal");
        },
    },
    // ...
});

export default store;