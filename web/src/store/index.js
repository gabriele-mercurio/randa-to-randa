//store/index.js
export const state = () => ({
  user: null,
  role: null,
  token: null,
  region: null,
  actAs: null,
  isNational: null
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
  },
  setActAs(state, actAs) {
    state.actAs = actAs
  },
  setUserRole(state, role) {
    state.region.role = role;
  },
  setIsNational(state, isNational) {
    state.isNational = isNational;
  }
};

export const getters = {
  getUser(state) {
    if(state.actAs) return state.actAs;
    return state.user;
  },
  getOriginalUser(state) {
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
  },
  getActAs(state) {
    return state.actAs
  },
  isFreeAccount(state) {
    if(state.region) {
      return state.region.isFreeAccount;
    }
  },
  getIsNational(state) {
    return state.isNational;
  }
};
