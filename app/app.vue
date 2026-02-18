<script setup>
const route = useRoute()
const isHome = computed(() => route.path === '/')
const isArticle = computed(() => route.path.startsWith('/posts/'))
const isPremiumMode = computed(() => isHome.value || isArticle.value)

useHead({
  meta: [
    { name: 'viewport', content: 'width=device-width, initial-scale=1' }
  ],
  link: [
    { rel: 'icon', href: '/favicon.ico' },
    { rel: 'preconnect', href: 'https://fonts.googleapis.com' },
    { rel: 'preconnect', href: 'https://fonts.gstatic.com', crossorigin: '' },
    { rel: 'stylesheet', href: 'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800;900&display=swap' }
  ],
  htmlAttrs: {
    lang: 'en'
  }
})

const title = 'NAT\'RE — Forest Travel Blog'
const description = 'A journey through the ancient woods.'

useSeoMeta({
  title,
  description,
  ogTitle: title,
  ogDescription: description,
  ogImage: 'https://images.unsplash.com/photo-1448375240586-882707db888b?w=1200&q=80',
  twitterImage: 'https://images.unsplash.com/photo-1448375240586-882707db888b?w=1200&q=80',
  twitterCard: 'summary_large_image'
})
</script>

<template>
  <UApp>
    <AppNav />

    <UMain>
      <NuxtPage />
    </UMain>
  </UApp>
</template>

<style>
/* Global resets and typography for the new design */
:root {
  --color-midnight: #0F1A0F;
  --color-sage: #7D8B7A;
  --color-cream: #FAF9F6;
  --color-gold: #D4A574;
  --color-white: #FFFFFF;
}

body {
  margin: 0;
  padding: 0;
  font-family: 'Inter', sans-serif;
  background-color: #FAF9F6;
  color: #0F1A0F;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}

* {
  box-sizing: border-box;
}

/* Override any default Nuxt UI or global styles that conflict with the premium look */
::selection {
  background: #D4A574;
  color: #0F1A0F;
}

/* Responsive container */
.container-premium {
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 48px;
}

@media (max-width: 640px) {
  .container-premium {
    padding: 0 24px;
  }
}
</style>
