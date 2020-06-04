//store/index.js
export const state = () => ({
  region: null,
  user: null,
  role: null,
  token: null
});

export const mutations = {
  setRegion(state, region) {
    state.region = region;
  },
  setUser(state, user) {
    state.user = user;
  },
  setRole(state, role) {
    state.role = role;
  },
  setToken(state, token) {
    state.token = token;
  }
};

export const getters = {
  getRegion(state) {
    return state.region;
  },
  getUser(state) {
    return state.user;
  },
  getRole(state) {
    return state.role;
  },
  getToken(state) {
    return state.token;
  }
};
