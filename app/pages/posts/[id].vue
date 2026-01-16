<script setup>
const route = useRoute()
const { data: post, pending, error } = await useFetch(`/api/posts/${route.params.id}`)

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
    <section class="post-view">
      <p v-if="pending" class="state">Зареждаме статията...</p>
      <p v-else-if="error" class="state">Статията не беше намерена.</p>
      <article v-else class="post-view__card">
        <p class="post-view__meta">Публикувано: {{ formatDate(post?.created_at) }}</p>
        <h1 class="post-view__title">{{ post?.title }}</h1>
        <p class="post-view__content">{{ post?.content }}</p>
        <NuxtLink to="/" class="btn btn--ghost">Назад към списъка</NuxtLink>
      </article>
    </section>
  </div>
</template>
