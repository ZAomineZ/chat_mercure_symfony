<script setup>
import Conversation from "./Conversation";
import { computed, onMounted } from "vue";
import { useStore } from "vuex";

const store = useStore();

let conversations = computed(() => store.getters.CONVERSATIONS);
let hubURL = computed(() => store.getters.HUBURL);
let username = computed(() => store.getters.USERNAME);

const updateConversations = (data) => {
  store.commit("UPDATE_CONVERSATIONS", data);
};

onMounted(() => {
  store.dispatch("GET_CONVERSATIONS").then(() => {
    const hub = new URL(hubURL.value, window.origin);
    hub.searchParams.append("topic", `/conversations/${username.value}`);

    const eventSource = new EventSource(hub, {
      withCredentials: true,
    });
    eventSource.onmessage = (event) => {
      updateConversations(JSON.parse(event.data));
    };
  });
});
</script>

<template>
  <div class="col-5 px-0">
    <div class="bg-white">
      <div class="bg-gray px-4 py-2 bg-light">
        <p class="h5 mb-0 py-1">Recent</p>
      </div>

      <div class="messages-box">
        <div class="list-group rounded-0">
          <Conversation
            v-for="conversation in conversations"
            :conversation="conversation"
          />
        </div>
      </div>
    </div>
  </div>
</template>
