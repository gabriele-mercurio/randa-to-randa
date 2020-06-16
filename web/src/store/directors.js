//store/index.js
export const state = () => ({
    directors: null,
  });
  
  export const mutations = {
    setDirectors(state, directors) {
      state.directors = directors;
    },
    addDirector(state, director) {
      state.directors.push(director);
    }
  };
  
  export const getters = {
    getDirectors(state) {
      return state.directors;
    }
  };
  