//store/index.js
export const state = () => ({
    directors: null,
  });
  
  export const mutations = {
    setDirectors(state, directors) {
      state.directors = directors;
    }
  };
  
  export const getters = {
    getDirecctors(state) {
      return state.directors;
    }
  };
  