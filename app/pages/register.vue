<script setup>
const { auth, refresh } = useAuth()
const router = useRouter()
const route = useRoute()

const email = ref('')
const password = ref('')
const confirmPassword = ref('')
const authError = ref('')
const authSuccess = ref('')
const isLoggedIn = computed(() => Boolean(auth.value?.user))

const register = async () => {
  authError.value = ''
  authSuccess.value = ''

  if (!email.value.trim()) {
    authError.value = 'Моля, въведи имейл.'
    return
  }
  if (!password.value.trim()) {
    authError.value = 'Моля, въведи парола.'
    return
  }
  if (password.value.length < 6) {
    authError.value = 'Паролата трябва да бъде поне 6 символа.'
    return
  }
  if (password.value !== confirmPassword.value) {
    authError.value = 'Паролите не съвпадат.'
    return
  }

  try {
    await $fetch('/api/auth/register', {
      method: 'POST',
      body: {
        email: email.value,
        password: password.value
      }
    })
    await $fetch('/api/auth/login', {
      method: 'POST',
      body: {
        email: email.value,
        password: password.value
      }
    })
    await refresh()
    authSuccess.value = 'Регистрацията е успешна.'
    const nextPath = typeof route.query.next === 'string' ? route.query.next : '/'
    await router.push(nextPath)
  } catch (err) {
    authError.value = 'Неуспешна регистрация. Опитай с друг имейл.'
  }
}
</script>

<template>
  <div class="page">
    <section class="auth-page">
      <div class="auth-page__card">
        <h1 class="auth-page__title">Регистрация</h1>
        <p class="auth-page__lead">
          Създай профил, за да участваш в дискусиите.
        </p>
        <p v-if="isLoggedIn" class="auth-page__state">
          Вече сте влезли в профила си.
        </p>
        <form v-else class="auth-page__form" @submit.prevent="register">
          <div class="field">
            <label for="register-email">Имейл</label>
            <input id="register-email" type="email" name="register-email" placeholder="you@example.com" v-model="email">
          </div>
          <div class="field">
            <label for="register-password">Парола</label>
            <input id="register-password" type="password" name="register-password" placeholder="Поне 6 символа" v-model="password">
          </div>
          <div class="field">
            <label for="register-password-confirm">Повтори паролата</label>
            <input
              id="register-password-confirm"
              type="password"
              name="register-password-confirm"
              placeholder="Повтори паролата"
              v-model="confirmPassword"
            >
          </div>
          <button class="btn btn--primary" type="submit">Създай профил</button>
          <p v-if="authError" class="auth-panel__error">{{ authError }}</p>
          <p v-if="authSuccess" class="auth-panel__status">{{ authSuccess }}</p>
          <NuxtLink to="/login" class="btn btn--ghost">Имам профил</NuxtLink>
        </form>
      </div>
    </section>
  </div>
</template>
