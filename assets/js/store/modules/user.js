export default {
  state: {
    username: null,
  },
  getters: {
    USERNAME: (state) => state.username,
  },
  mutations: {
    SET_USERNAME: (state, paylaod) => (state.username = paylaod),
  },
  actions: {},
};
