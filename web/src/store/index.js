//store/index.js
export const state = () => ({
  region: null,
  role: null
});

export const mutations = {
  setRegion(state, region) {
    state.region = region;
  },
  setRole(state, role) {
    state.role = role;
  }
};

export const getters = {
  getRegion(state) {
    return state.region;
  },
  getRole(state) {
    return state.role;
  }
};
