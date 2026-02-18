<script setup>
import { onMounted, onUnmounted, ref, computed } from 'vue'
const { auth, refresh } = useAuth()
const isLoggedIn = computed(() => Boolean(auth.value?.user))
const route = useRoute()

// Hero mode is specifically for Index and Article pages where we have full-screen heros
const isHeroMode = computed(() => route.path === '/' || route.path.startsWith('/posts/'))
// Article mode has the 'Back' link
const isArticle = computed(() => route.path.startsWith('/posts/') && route.path !== '/posts')

const isScrolled = ref(false)

const handleScroll = () => {
  if (window.scrollY > 100) {
    isScrolled.value = true
  } else {
    isScrolled.value = false
  }
}

onMounted(() => {
  window.addEventListener('scroll', handleScroll)
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
})

const logout = async () => {
  await $fetch('/api/auth/logout', { method: 'POST' })
  await refresh()
}
</script>

<template>
  <nav id="navbar" :class="{
    'scrolled': isScrolled || !isHeroMode,
    'hero-mode': isHeroMode && !isScrolled,
    'article-nav': isArticle
  }">
    <div class="nav-container-premium">
      <div class="nav-left">
        <NuxtLink v-if="isArticle" to="/posts" class="back-link">Back to Stories</NuxtLink>
        <NuxtLink to="/" class="logo">Nat're</NuxtLink>
      </div>

      <div class="nav-links">
        <NuxtLink to="/posts" class="nav-link-premium">Stories</NuxtLink>
        <NuxtLink to="/admin" class="nav-link-premium">Admin</NuxtLink>
        <NuxtLink v-if="!isLoggedIn" to="/login" class="nav-link-premium">Login</NuxtLink>
        <button v-else class="logout-btn-minimal" @click="logout">Logout</button>
      </div>

      <div class="menu-icon">
        <span></span>
        <span></span>
      </div>
    </div>
  </nav>
</template>

<style scoped>
#navbar {
  position: fixed;
  top: 0;
  width: 100%;
  padding: 24px 0;
  z-index: 1000;
  transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
  background-color: var(--color-cream);
  box-shadow: 0 2px 20px rgba(15, 26, 15, 0.05);
}

.nav-container-premium {
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 48px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

#navbar.hero-mode {
  background-color: transparent;
  box-shadow: none;
  padding: 32px 0;
  mix-blend-mode: difference;
}

.nav-left {
  display: flex;
  align-items: center;
  gap: 32px;
}

.back-link {
  color: #0F1A0F;
  text-decoration: none;
  font-size: 11px;
  letter-spacing: 2px;
  text-transform: uppercase;
  display: flex;
  align-items: center;
  gap: 8px;
  font-weight: 700;
}

.hero-mode .back-link {
  color: #FFFFFF;
}

.back-link::before {
  content: '←';
  transition: transform 0.3s ease;
}

.back-link:hover::before {
  transform: translateX(-4px);
}

.logo {
  font-size: 16px;
  font-weight: 800;
  letter-spacing: 4px;
  text-transform: uppercase;
  color: #0F1A0F;
  text-decoration: none;
}

.hero-mode .logo {
  color: #FFFFFF;
}

.nav-links {
  display: flex;
  gap: 40px;
  align-items: center;
}

.nav-link-premium,
.logout-btn-minimal {
  color: #0F1A0F;
  text-decoration: none;
  font-size: 11px;
  letter-spacing: 2px;
  text-transform: uppercase;
  font-weight: 700;
  position: relative;
  background: transparent;
  border: none;
  cursor: pointer;
  padding: 0;
  font-family: inherit;
}

.hero-mode .nav-link-premium,
.hero-mode .logout-btn-minimal {
  color: #FFFFFF;
}

.nav-link-premium::after {
  content: '';
  position: absolute;
  bottom: -4px;
  left: 0;
  width: 0;
  height: 1px;
  background-color: var(--color-gold);
  transition: width 0.3s ease;
}

.nav-link-premium:hover::after {
  width: 100%;
}

.menu-icon {
  display: none;
  flex-direction: column;
  gap: 6px;
  cursor: pointer;
}

.menu-icon span {
  width: 24px;
  height: 2px;
  background-color: var(--color-midnight);
}

.hero-mode .menu-icon span {
  background-color: var(--color-white);
}

@media (max-width: 968px) {
  .nav-links {
    display: none;
  }

  .menu-icon {
    display: flex;
  }
}

@media (max-width: 640px) {
  .nav-container-premium {
    padding: 0 24px;
  }
}
</style>
