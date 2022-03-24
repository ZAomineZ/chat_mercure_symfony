<script setup>
import { useStore } from "vuex";
import { useRouter } from "vue-router";
import { ref } from "vue";

const store = useStore();
const router = useRouter();

let content = ref("");

const sendMessage = () => {
  store
    .dispatch("POST_MESSAGE", {
      conversationID: router.currentRoute.value.params.id,
      content: content.value,
    })
    .then((r) => {
      content.value = "";
    });
};
</script>

<template>
  <form action="#" class="bg-light" @submit.prevent="sendMessage">
    <div class="input-group">
      <input
        type="text"
        placeholder="Type a message"
        aria-describedby="button-addon2"
        class="form-control rounded-0 border-0 py-4 bg-light"
        v-model="content"
      />
      <div class="input-group-append">
        <button id="button-addon2" type="submit" class="btn btn-link">
          <i class="fa fa-paper-plane"></i>
        </button>
      </div>
    </div>
  </form>
</template>
