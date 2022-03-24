<script setup>
import { computed } from "vue";

let { conversation } = defineProps({
  conversation: Object,
});

const date = computed(() =>
  conversation.createdAt ? conversation.createdAt.toLocaleString() : ""
);
</script>

<template>
  <router-link
    :to="{ name: 'conversation', params: { id: conversation.conversationID } }"
    class="list-group-item list-group-item-action"
  >
    <div class="media">
      <img
        src="https://bootstrapious.com/i/snippets/sn-chat/avatar.svg"
        alt="user"
        width="50"
        class="rounded-circle"
      />
      <div class="media-body ml-4">
        <div class="d-flex align-items-center justify-content-between mb-1">
          <h6 class="mb-0">{{ conversation.username }}</h6>
          <small class="small font-weight-bold">{{ date }}</small>
        </div>
        <p class="font-italic mb-0 text-small">
          {{ conversation.content }}
        </p>
      </div>
    </div>
  </router-link>
</template>
