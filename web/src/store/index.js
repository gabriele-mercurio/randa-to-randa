//store/index.js
export const state = () => ({
  user: null,
  role: null,
  token: null,
  region: null
});

export const mutations = {
  setUser(state, user) {
    state.user = user;
  },
  setRole(state, role) {
    state.role = role;
  },
  setToken(state, token) {
    state.token = token;
  },
  setRegion(state, region) {
    state.region = region;
  }
};

export const getters = {
  getUser(state) {
    return state.user;
  },
  getRole(state) {
    return state.role;
  },
  getToken(state) {
    return state.token;
  },
  getRegion(state) {
    return state.region;
  }
};
