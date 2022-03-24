import { createStore } from "vuex";
import conversation from "./modules/conversation";
import user from "./modules/user";

const store = createStore({
  modules: { conversation, user },
});

export default store;
