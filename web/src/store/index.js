//store/index.js
export const state = () => ({
  region: null,
  role: null,
  chapters: []
});

export const mutations = {
  setRegion(state, region) {
    state.region = region;
  },
  setRole(state, role) {
    state.role = role;
  },
  setChapters(state, chapters) {
    state.chapters = chapters;
  }
};

export const getters = {
  getRegion(state) {
    return state.region;
  },
  getRole(state) {
    return state.role;
  },
  getChapters(state) {
    return state.chapters;
  }
};
