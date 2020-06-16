//store/index.js
export const state = () => ({
  data: null
});

export const mutations = {
  setData(state, data) {
    state.data = data;
  }
};

export const getters = {
  getData(state) {
    return state.data;
  }
};
