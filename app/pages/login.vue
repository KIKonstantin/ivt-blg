<script setup>
import { onMounted } from 'vue'
import gsap from 'gsap'

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
    authError.value = 'Please enter your email.'
    return
  }
  if (!password.value.trim()) {
    authError.value = 'Please enter your password.'
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
    authError.value = 'Invalid login credentials.'
  }
}

onMounted(() => {
  gsap.from(".auth-luxury-card", {
    opacity: 0,
    y: 30,
    duration: 1.2,
    ease: "power4.out"
  })
})
</script>

<template>
  <div class="auth-page-luxury">
    <div class="auth-container">
      <div class="auth-luxury-card">
        <header class="auth-header">
          <span class="auth-label">Authentication</span>
          <h1 class="auth-title">Welcome</h1>
          <p class="auth-subtitle">Continue your journey through the forest.</p>
        </header>

        <div v-if="isLoggedIn" class="already-logged">
          <p>You are already signed in as <strong>{{ auth.user.email }}</strong></p>
          <NuxtLink to="/" class="btn-luxury primary">Back to Stories</NuxtLink>
        </div>

        <form v-else class="luxury-form-auth" @submit.prevent="login">
          <div class="input-group-luxury">
            <label>Email Address</label>
            <input type="email" v-model="email" placeholder="explorer@forest.com" required>
          </div>

          <div class="input-group-luxury">
            <label>Password</label>
            <input type="password" v-model="password" placeholder="••••••••" required>
          </div>

          <div class="auth-actions-luxury">
            <button type="submit" class="btn-luxury primary">Sign In</button>
            <NuxtLink to="/register" class="link-luxury">Create Account</NuxtLink>
          </div>

          <p v-if="authError" class="auth-error-msg">{{ authError }}</p>
        </form>
      </div>
    </div>
  </div>
</template>

<style scoped>
.auth-page-luxury {
  height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: var(--color-midnight);
  /* Dark background for auth pages */
}

.auth-container {
  width: 100%;
  max-width: 500px;
  padding: 24px;
}

.auth-luxury-card {
  background-color: var(--color-cream);
  padding: 64px;
  box-shadow: 0 40px 100px rgba(0, 0, 0, 0.4);
}

.auth-header {
  margin-bottom: 48px;
  text-align: center;
}

.auth-label {
  display: block;
  font-size: 11px;
  text-transform: uppercase;
  letter-spacing: 4px;
  color: var(--color-gold);
  font-weight: 700;
  margin-bottom: 16px;
}

.auth-title {
  font-size: 36px;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: -1px;
  margin: 0;
}

.auth-subtitle {
  color: var(--color-sage);
  margin-top: 8px;
  font-size: 14px;
}

.luxury-form-auth {
  display: grid;
  gap: 32px;
}

.input-group-luxury {
  display: grid;
  gap: 8px;
}

.input-group-luxury label {
  font-size: 11px;
  text-transform: uppercase;
  letter-spacing: 2px;
  font-weight: 700;
  color: var(--color-sage);
}

.input-group-luxury input {
  width: 100%;
  padding: 12px 0;
  font-family: inherit;
  font-size: 16px;
  background: transparent;
  border: none;
  border-bottom: 1px solid rgba(15, 26, 15, 0.1);
  outline: none;
  transition: border-color 0.3s;
}

.input-group-luxury input:focus {
  border-color: var(--color-midnight);
}

.auth-actions-luxury {
  display: flex;
  flex-direction: column;
  gap: 20px;
  align-items: center;
  margin-top: 16px;
}

.btn-luxury.primary {
  width: 100%;
  padding: 16px;
  background-color: var(--color-midnight);
  color: #FFFFFF;
  border: none;
  text-transform: uppercase;
  letter-spacing: 3px;
  font-weight: 700;
  font-size: 13px;
  cursor: pointer;
  transition: background 0.3s;
}

.btn-luxury.primary:hover {
  background-color: var(--color-gold);
}

.link-luxury {
  font-size: 13px;
  text-transform: uppercase;
  letter-spacing: 1px;
  color: var(--color-sage);
  text-decoration: none;
  border-bottom: 1px solid transparent;
  transition: all 0.3s;
}

.link-luxury:hover {
  color: var(--color-midnight);
  border-bottom-color: var(--color-gold);
}

.auth-error-msg {
  text-align: center;
  color: #FF4444;
  font-size: 14px;
  margin: 0;
}

.already-logged {
  text-align: center;
  color: var(--color-sage);
}

.already-logged strong {
  color: var(--color-midnight);
}

.already-logged .btn-luxury {
  margin-top: 24px;
}

@media (max-width: 640px) {
  .auth-luxury-card {
    padding: 40px 24px;
  }
}
</style>
