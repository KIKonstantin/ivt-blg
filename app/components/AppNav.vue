<script setup>
import { onMounted, onUnmounted, ref, computed, watch, nextTick } from 'vue'
import gsap from 'gsap'

const { auth, refresh } = useAuth()
const isLoggedIn = computed(() => Boolean(auth.value?.user))
const route = useRoute()

// Hero mode is specifically for Index and Article pages where we have full-screen heros
const isHeroMode = computed(() => route.path === '/' || route.path.startsWith('/posts/'))
// Article mode has the 'Back' link
const isArticle = computed(() => route.path.startsWith('/posts/') && route.path !== '/posts')

const isScrolled = ref(false)
const isMenuOpen = ref(false)

const handleScroll = () => {
  if (window.scrollY > 100) {
    isScrolled.value = true
  } else {
    isScrolled.value = false
  }
}

const toggleMenu = () => {
  isMenuOpen.value = !isMenuOpen.value
}

// Watch for route changes to close menu
watch(() => route.fullPath, () => {
  if (isMenuOpen.value) isMenuOpen.value = false
})

// GSAP Animations for the forest menu
watch(isMenuOpen, (isOpen) => {
  if (isOpen) {
    document.body.style.overflow = 'hidden'
    nextTick(() => {
      const tl = gsap.timeline()

      tl.set(".mobile-menu-overlay", { display: 'flex' })
        .to(".mobile-menu-overlay", {
          autoAlpha: 1,
          duration: 0.4,
          ease: "power2.out"
        })
        .fromTo(".tree-line",
          { scaleY: 0, opacity: 0 },
          {
            scaleY: 1,
            opacity: () => 0.05 + (Math.random() * 0.1),
            duration: 1.5,
            stagger: {
              each: 0.05,
              from: "random"
            },
            ease: "power4.out",
            transformOrigin: "bottom"
          },
          "-=0.2"
        )
        .fromTo(".nav-link-mobile",
          { y: 30, opacity: 0 },
          { y: 0, opacity: 1, duration: 0.8, stagger: 0.1, ease: "power3.out" },
          "-=1.2"
        )
        .fromTo(".menu-footer",
          { opacity: 0 },
          { opacity: 1, duration: 1 },
          "-=0.5"
        )
    })
  } else {
    document.body.style.overflow = ''
    const tl = gsap.timeline({
      onComplete: () => {
        gsap.set(".mobile-menu-overlay", { display: 'none' })
      }
    })
    tl.to(".mobile-menu-overlay", {
      autoAlpha: 0,
      duration: 0.4,
      ease: "power2.in"
    })
  }
})

onMounted(() => {
  window.addEventListener('scroll', handleScroll)
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
  document.body.style.overflow = ''
})

const logout = async () => {
  await $fetch('/api/auth/logout', { method: 'POST' })
  await refresh()
}
</script>

<template>
  <div>
    <nav id="navbar" :class="{
      'scrolled': isScrolled || !isHeroMode,
      'hero-mode': isHeroMode && !isScrolled,
      'article-nav': isArticle,
      'menu-active': isMenuOpen
    }">
      <div class="nav-container-premium">
        <div class="nav-left">
          <NuxtLink v-if="isArticle" to="/posts" class="back-link">Назад към историите</NuxtLink>
          <NuxtLink v-else to="/" class="logo">КОРЕНИ</NuxtLink>
        </div>

        <div class="nav-links">
          <NuxtLink to="/posts" class="nav-link-premium">Истории</NuxtLink>
          <button v-if="isLoggedIn" class="logout-btn-minimal" @click="logout">Изход</button>
        </div>

        <div class="menu-icon" @click="toggleMenu" :class="{ 'active': isMenuOpen }">
          <span class="icon-span"></span>
          <span class="icon-span"></span>
        </div>
      </div>
    </nav>

    <!-- Forest Mobile Menu Overlay -->
    <div class="mobile-menu-overlay">
      <div class="forest-background">
        <div v-for="n in 15" :key="n" class="tree-line" :style="{
          left: `${(n - 1) * 7}%`,
          height: `${30 + Math.random() * 65}%`,
        }"></div>
        <div class="sky-gradient"></div>
      </div>

      <div class="menu-content-premium">
        <nav class="mobile-nav-links">
          <NuxtLink to="/" class="nav-link-mobile" @click="isMenuOpen = false">Начало</NuxtLink>
          <NuxtLink to="/posts" class="nav-link-mobile" @click="isMenuOpen = false">Истории</NuxtLink>
          <NuxtLink to="/author" class="nav-link-mobile" @click="isMenuOpen = false">Автор</NuxtLink>
          <button v-if="isLoggedIn" class="nav-link-mobile logout-btn-mobile" @click="logout">Изход</button>
        </nav>

        <div class="menu-footer">
          <div class="menu-socials">
            <a href="#">Instagram</a>
            <a href="#">Pinterest</a>
          </div>
          <div class="menu-brand">КОРЕНИ</div>
        </div>
      </div>
    </div>
  </div>
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

#navbar.menu-active {
  mix-blend-mode: normal !important;
  background-color: transparent !important;
  box-shadow: none !important;
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
  position: relative;
  width: 24px;
  height: 14px;
  justify-content: space-between;
  z-index: 1100;
}

.icon-span {
  width: 100%;
  height: 2px;
  background-color: var(--color-midnight);
  transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
  transform-origin: center;
}

.hero-mode .icon-span {
  background-color: var(--color-white);
}

.menu-icon.active .icon-span:nth-child(1) {
  transform: translateY(6px) rotate(45deg);
  background-color: var(--color-midnight);
}

.menu-icon.active .icon-span:nth-child(2) {
  transform: translateY(-6px) rotate(-45deg);
  background-color: var(--color-midnight);
}

/* Mobile Menu Overlay Styles */
.mobile-menu-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100vh;
  background-color: var(--color-cream);
  z-index: 999;
  /* Just below navbar icon but above content */
  visibility: hidden;
  opacity: 0;
  display: none;
  overflow: hidden;
  align-items: center;
  justify-content: center;
}

.forest-background {
  position: absolute;
  bottom: 0;
  left: 0;
  width: 100%;
  height: 100%;
  pointer-events: none;
}

.tree-line {
  position: absolute;
  bottom: 0;
  width: 1px;
  background-color: var(--color-midnight);
  opacity: 0;
  will-change: transform, opacity;
}

.sky-gradient {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 50%;
  background: linear-gradient(to bottom, rgba(212, 165, 116, 0.15), transparent);
}

.menu-content-premium {
  position: relative;
  z-index: 2;
  text-align: center;
  width: 100%;
  max-width: 600px;
  padding: 0 40px;
}

.mobile-nav-links {
  display: flex;
  flex-direction: column;
  gap: 40px;
  margin-bottom: 80px;
}

.nav-link-mobile {
  font-size: 42px;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: -1px;
  color: var(--color-midnight);
  text-decoration: none;
  border: none;
  background: transparent;
  cursor: pointer;
  font-family: inherit;
  transition: all 0.3s ease;
}

.nav-link-mobile:hover {
  color: var(--color-gold);
  transform: scale(1.05);
}

.logout-btn-mobile {
  opacity: 0.5;
  font-size: 20px;
  letter-spacing: 2px;
}

.menu-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-top: 1px solid rgba(15, 26, 15, 0.1);
  padding-top: 32px;
  font-size: 11px;
  letter-spacing: 3px;
  text-transform: uppercase;
  color: var(--color-sage);
}

.menu-socials {
  display: flex;
  gap: 24px;
}

.menu-socials a {
  color: inherit;
  text-decoration: none;
  transition: color 0.3s;
}

.menu-socials a:hover {
  color: var(--color-gold);
}

.menu-brand {
  font-weight: 800;
  color: var(--color-midnight);
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

  .nav-link-mobile {
    font-size: 32px;
  }

  .menu-footer {
    flex-direction: column;
    gap: 20px;
  }
}
</style>
