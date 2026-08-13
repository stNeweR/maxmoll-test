<script setup lang="ts">
import { RouterLink, RouterView } from 'vue-router';
</script>

<template>
  <div>
    <header class="flex items-center justify-between bg-slate-800 px-6 py-3 text-white">
      <div class="text-lg font-bold">Микро-CRM</div>
      <nav class="flex gap-5">
        <RouterLink
          to="/orders"
          class="rounded-md px-2.5 py-1.5 text-sm hover:bg-slate-700 router-link-active:bg-slate-700"
        >Заказы</RouterLink>
        <RouterLink
          to="/movements"
          class="rounded-md px-2.5 py-1.5 text-sm hover:bg-slate-700 router-link-active:bg-slate-700"
        >Движения товаров</RouterLink>
      </nav>
    </header>
    <main class="mx-auto my-6 max-w-5xl px-4">
      <RouterView />
    </main>
  </div>
</template>
