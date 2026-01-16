<script setup>
const { data: posts, pending, error } = await useFetch('/api/posts')

const formatDate = (value) => {
  if (!value) {
    return 'Без дата'
  }
  return new Date(value).toLocaleDateString('bg-BG', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}
</script>

<template>
  <div class="page">
    <header class="hero">
      <div class="hero__text">
        <span class="hero__badge">Nuxt + PostgreSQL</span>
        <h1 class="hero__title">Всички статии</h1>
        <p class="hero__lead">
          Прегледай последните публикации или отвори отделна статия за пълен изглед.
        </p>
      </div>
      <div class="hero__shape">
        <span>Налични</span>
        <strong>{{ posts?.length ?? 0 }} статии</strong>
      </div>
    </header>

    <section class="feed">
      <div class="feed__header">
        <div>
          <h2 class="feed__title">Списък с публикации</h2>
          <p class="feed__subtitle">Подредени по дата на публикуване</p>
        </div>
      </div>

      <p v-if="pending" class="state">Зареждаме последните статии...</p>
      <p v-else-if="error" class="state">Грешка при зареждане. Опитай пак след малко.</p>
      <p v-else-if="posts && posts.length === 0" class="state">Няма публикувани статии.</p>

      <div v-else class="posts">
        <article v-for="post in posts" :key="post.id" class="post-card">
          <h3 class="post-card__title">
            <NuxtLink :to="`/posts/${post.id}`">{{ post.title }}</NuxtLink>
          </h3>
          <div class="post-card__meta">
            <span>Публикувано: {{ formatDate(post.created_at) }}</span>
          </div>
          <p class="post-card__content">{{ post.content }}</p>
          <div class="post-card__actions">
            <NuxtLink class="btn btn--ghost" :to="`/posts/${post.id}`">Прочети</NuxtLink>
          </div>
        </article>
      </div>
    </section>
  </div>
</template>
