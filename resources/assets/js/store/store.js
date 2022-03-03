import Vue from 'vue';
import Vuex from "vuex";

Vue.use(Vuex);

export const store = new Vuex.Store({
    state: {
        documents: {},
        summaryFromTo: {}
    },
    getters: {},
    mutations: {
        setFromTo(state, payload) {
            state.summaryFromTo = payload
        },
    }
})