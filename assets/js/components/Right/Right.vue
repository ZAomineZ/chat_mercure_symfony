<script setup>
import Message from "./Message";
import FormMessage from "./FormMessage";
import {
  computed,
  nextTick,
  onBeforeUnmount,
  onMounted,
  ref,
  watch,
  watchEffect,
} from "vue";
import { useRouter } from "vue-router";
import { useStore } from "vuex";

const router = useRouter();
const store = useStore();
const messageBody = ref(null);
let eventSource = ref(null);

let messages = computed(() => {
  return store.getters.MESSAGES(router.currentRoute.value.params.id);
});
let hubURL = computed(() => store.getters.HUBURL);
let username = computed(() => store.getters.USERNAME);

const scrollDown = () => {
  if (messageBody.value)
    messageBody.value.scrollTop = messageBody.value.scrollHeight;
};

store.watch(
  () => store.getters.MESSAGES(router.currentRoute.value.params.id),
  () => {
    nextTick(() => scrollDown());
  },
  { deep: true }
);

onMounted(() => {
  let conversationID = router.currentRoute.value.params.id;
  store.dispatch("GET_MESSAGES", conversationID).then(() => {
    scrollDown();

    if (eventSource === null) {
      const hub = new URL(hubURL.value, window.origin);
      hub.searchParams.append(
        "topic",
        `/conversations/${conversationID}/${username.value}`
      );

      eventSource.value = new EventSource(hub, {
        withCredentials: true,
      });
      eventSource.value.onmessage = (event) => {
        store.commit("ADD_MESSAGE", {
          payload: JSON.parse(event.data),
          conversationID: conversationID,
        });
      };
    }
  });
});

onBeforeUnmount(() => {
  if (eventSource.value instanceof EventSource) {
    eventSource.close();
  }
});
</script>

<template>
  <div class="col-7 px-0">
    <div class="px-4 py-5 chat-box bg-white" ref="messageBody">
      <!-- Message conversation -->
      <Message v-for="message in messages" :message="message" />
    </div>
    <!-- Form Message -->
    <FormMessage />
  </div>
</template>
