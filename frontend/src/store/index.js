import { createStore } from 'vuex';
import api from '../services/api';

export default createStore({
  state: {
    token: localStorage.getItem('token') || null,
    user: null,
  },
  mutations: {
    SET_TOKEN(state, token) {
      state.token = token;
      if (token) localStorage.setItem('token', token);
      else localStorage.removeItem('token');
    },
    SET_USER(state, user) {
      state.user = user;
    },
  },
  actions: {
    async login({ commit }, credentials) {
      const res = await api.post('/login', credentials);
      commit('SET_TOKEN', res.data.token);
    },
    async logout({ commit }) {
      commit('SET_TOKEN', null);
      commit('SET_USER', null);
    },
  },
  getters: {
    isAuthenticated: state => !!state.token,
  },
});