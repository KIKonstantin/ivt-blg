<script setup>
const { auth, refresh } = useAuth()
const router = useRouter()
const route = useRoute()

const email = ref('')
const password = ref('')
const authError = ref('')
const isLoggedIn = computed(() => Boolean(auth.value?.user))

const login = async () => {
  authError.value = ''
  if (!email.value.trim()) {
    authError.value = 'Моля, въведи имейл.'
    return
  }
  if (!password.value.trim()) {
    authError.value = 'Моля, въведи парола.'
    return
  }

  try {
    await $fetch('/api/auth/login', {
      method: 'POST',
      body: {
        email: email.value,
        password: password.value
      }
    })
    await refresh()
    const nextPath = typeof route.query.next === 'string' ? route.query.next : '/'
    await router.push(nextPath)
  } catch (err) {
    authError.value = 'Невалидни данни за вход.'
  }
}
</script>

<template>
  <div class="page">
    <section class="auth-page">
      <div class="auth-page__card">
        <h1 class="auth-page__title">Вход</h1>
        <p class="auth-page__lead">
          Влез в профила си, за да коментираш или да управляваш съдържание.
        </p>
        <p v-if="isLoggedIn" class="auth-page__state">
          Вече сте влезли в профила си.
        </p>
        <form v-else class="auth-page__form" @submit.prevent="login">
          <div class="field">
            <label for="login-email">Имейл</label>
            <input id="login-email" type="email" name="login-email" placeholder="admin@example.com" v-model="email">
          </div>
          <div class="field">
            <label for="login-password">Парола</label>
            <input id="login-password" type="password" name="login-password" placeholder="Вашата парола" v-model="password">
          </div>
          <button class="btn btn--primary" type="submit">Влез</button>
          <p v-if="authError" class="auth-panel__error">{{ authError }}</p>
          <NuxtLink to="/register" class="btn btn--ghost">Създай профил</NuxtLink>
        </form>
      </div>
    </section>
  </div>
</template>
