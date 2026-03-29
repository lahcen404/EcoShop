<script setup>
import { ref } from 'vue'

const msg = ref('Welcome to EcoShop Frontend!')
</script>

<template>
  <div class="container">
    <h1>{{ msg }}</h1>
    <p>Hello Hello Hello</p>
  </div>
</template>

<style scoped>
.container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  height: 100vh;
  text-align: center;
}
h1 {
  color: #42b883;
}
p {
  color: #888;
}
</style>
