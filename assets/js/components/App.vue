<script setup>
import Left from "./Left/Left";
</script>

<template>
  <div class="container py-5 px-4">
    <div class="row rounded-lg overflow-hidden shadow">
      <!-- Users box-->
      <Left />
      <!-- Chat Box-->
      <router-view :key="this.$route.fullPath"></router-view>
    </div>
  </div>
</template>
