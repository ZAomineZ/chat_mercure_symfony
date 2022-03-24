import { createApp } from "vue";
import App from "./components/App";
import Blank from "./components/Right/Blank";
import Right from "./components/Right/Right";
import store from "./store/store";
import { createRouter, createWebHistory } from "vue-router";

let app = createApp(App);

const routes = [
  { path: "/", component: Blank, name: "blank" },
  { path: "/conversations/:id", component: Right, name: "conversation" },
];
const router = createRouter({
  mode: "abstract",
  history: createWebHistory(),
  routes,
});

store.commit("SET_USERNAME", document.querySelector("#app").dataset.username);

app.use(router);
app.use(store);

app.mount("#app");

router.replace("/").then((r) => r);
