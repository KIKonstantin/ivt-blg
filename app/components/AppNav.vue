<script setup>
const { auth, refresh } = useAuth()
const isLoggedIn = computed(() => Boolean(auth.value?.user))

const logout = async () => {
  await $fetch('/api/auth/logout', { method: 'POST' })
  await refresh()
}
</script>

<template>
  <header class="nav">
    <div class="nav__inner">
      <div class="nav__brand">
        <NuxtLink to="/" class="nav__logo">IVT Blog</NuxtLink>
        <span class="nav__tag">Учебна лаборатория</span>
      </div>
      <nav class="nav__links">
        <NuxtLink to="/" class="nav__link">Статии</NuxtLink>
        <NuxtLink to="/admin" class="nav__link">Админ</NuxtLink>
        <NuxtLink v-if="!isLoggedIn" to="/login" class="nav__link nav__link--cta">Вход</NuxtLink>
        <NuxtLink v-if="!isLoggedIn" to="/register" class="nav__link">Регистрация</NuxtLink>
        <button v-else class="btn btn--ghost" type="button" @click="logout">Изход</button>
      </nav>
    </div>
  </header>
</template>
