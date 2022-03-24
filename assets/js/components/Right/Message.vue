<script setup>
import { computed } from "vue";

let { message } = defineProps({
  message: Object,
});

let date = computed(() => new Date(message.createdAt).toLocaleString());
</script>

<template>
  <div class="media w-50 mb-3" :class="{ 'ml-auto': message.mine }">
    <img
      v-if="!message.mine"
      src="https://bootstrapious.com/i/snippets/sn-chat/avatar.svg"
      alt="user"
      width="50"
      class="rounded-circle"
    />
    <div class="media-body ml-3">
      <div
        class="rounded py-2 px-3 mb-2"
        :class="[message.mine ? 'bg-primary' : 'bg-light']"
      >
        <p
          class="text-small mb-0"
          :class="[message.mine ? 'text-light' : 'text-muted']"
        >
          {{ message.content }}
        </p>
      </div>
      <p class="small text-muted">{{ date }}</p>
    </div>
  </div>
</template>
