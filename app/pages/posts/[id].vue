<script setup>
const route = useRoute()
const { auth } = useAuth()
const { data: post, pending, error } = await useFetch(`/api/posts/${route.params.id}`)
const {
  data: comments,
  pending: commentsPending,
  error: commentsError,
  refresh: refreshComments
} = await useFetch(`/api/posts/${route.params.id}/comments`)

const commentContent = ref('')
const commentError = ref('')
const commentPending = ref(false)
const isLoggedIn = computed(() => Boolean(auth.value?.user))

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

const formatDateTime = (value) => {
  if (!value) {
    return 'Без дата'
  }
  return new Date(value).toLocaleString('bg-BG', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const submitComment = async () => {
  commentError.value = ''
  const content = commentContent.value.trim()
  if (!content) {
    commentError.value = 'Моля, въведи коментар.'
    return
  }

  commentPending.value = true
  try {
    await $fetch(`/api/posts/${route.params.id}/comments`, {
      method: 'POST',
      body: { content }
    })
    commentContent.value = ''
    await refreshComments()
  } catch (err) {
    commentError.value = 'Неуспешно изпращане на коментар.'
  } finally {
    commentPending.value = false
  }
}
</script>

<template>
  <div class="page">
    <section class="post-view">
      <p v-if="pending" class="state">Зареждаме статията...</p>
      <p v-else-if="error" class="state">Статията не беше намерена.</p>
      <div v-else class="post-view__card">
        <article>
          <p class="post-view__meta">Публикувано: {{ formatDate(post?.created_at) }}</p>
          <h1 class="post-view__title">{{ post?.title }}</h1>
          <p class="post-view__content">{{ post?.content }}</p>
          <NuxtLink to="/" class="btn btn--ghost">Назад към списъка</NuxtLink>
        </article>

        <section class="comments">
          <div class="comments__header">
            <h2 class="comments__title">Коментари</h2>
            <span class="comments__count">{{ comments?.length ?? 0 }} общо</span>
          </div>

          <p v-if="commentsPending" class="state">Зареждаме коментарите...</p>
          <p v-else-if="commentsError" class="state">Неуспешно зареждане на коментарите.</p>
          <p v-else-if="comments && comments.length === 0" class="state">Все още няма коментари.</p>

          <div v-else class="comments__list">
            <article v-for="comment in comments" :key="comment.id" class="comment-card">
              <div class="comment-card__meta">
                <span>{{ comment.email }}</span>
                <span>•</span>
                <span>{{ formatDateTime(comment.created_at) }}</span>
              </div>
              <p class="comment-card__content">{{ comment.content }}</p>
            </article>
          </div>

          <div v-if="!isLoggedIn" class="comments__notice">
            <p>За да оставиш коментар, трябва да си влязъл в профила си.</p>
            <div class="comments__actions">
              <NuxtLink to="/login" class="btn btn--ghost">Вход</NuxtLink>
              <NuxtLink to="/register" class="btn btn--primary">Регистрация</NuxtLink>
            </div>
          </div>

          <form v-else class="comment-form" @submit.prevent="submitComment">
            <div class="field">
              <label for="comment-content">Нов коментар</label>
              <textarea
                id="comment-content"
                name="comment-content"
                placeholder="Сподели мисълта си..."
                v-model="commentContent"
              ></textarea>
            </div>
            <div class="form-actions">
              <button class="btn btn--primary" type="submit" :disabled="commentPending">
                {{ commentPending ? 'Изпращаме...' : 'Публикувай коментар' }}
              </button>
            </div>
            <p v-if="commentError" class="auth-panel__error">{{ commentError }}</p>
          </form>
        </section>
      </div>
    </section>
  </div>
</template>
