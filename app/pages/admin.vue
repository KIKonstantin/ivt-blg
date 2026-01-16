<script setup>
definePageMeta({
  middleware: 'admin'
})

const { data: posts, pending, error, refresh } = await useFetch('/api/posts')
const { auth } = useAuth()
const router = useRouter()

const formTitle = ref('')
const formContent = ref('')
const editingId = ref(null)

const isEditing = computed(() => editingId.value !== null)
const isAdmin = computed(() => auth.value?.isAdmin === true)

watchEffect(() => {
  if (auth.value && !isAdmin.value) {
    router.push('/login?next=/admin')
  }
})

const resetForm = () => {
  formTitle.value = ''
  formContent.value = ''
  editingId.value = null
}

const submit = async () => {
  if (!formTitle.value.trim() || !formContent.value.trim()) {
    return
  }

  if (isEditing.value) {
    await $fetch(`/api/posts/${editingId.value}`, {
      method: 'PUT',
      body: {
        title: formTitle.value,
        content: formContent.value
      }
    })
  } else {
    await $fetch('/api/posts', {
      method: 'POST',
      body: {
        title: formTitle.value,
        content: formContent.value
      }
    })
  }

  await refresh()
  resetForm()
}

const startEdit = (post) => {
  editingId.value = post.id
  formTitle.value = post.title
  formContent.value = post.content
}

const cancelEdit = () => {
  resetForm()
}

const removePost = async (post) => {
  const confirmDelete = window.confirm(`Сигурни ли сте, че искате да изтриете "${post.title}"?`)
  if (!confirmDelete) {
    return
  }

  await $fetch(`/api/posts/${post.id}`, {
    method: 'DELETE'
  })
  await refresh()
  if (editingId.value === post.id) {
    resetForm()
  }
}

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
        <span class="hero__badge">Admin Studio</span>
        <h1 class="hero__title">Администраторски панел</h1>
        <p class="hero__lead">
          Създавай, редактирай и управлявай публикациите за сайта.
        </p>
      </div>
      <div class="hero__shape">
        <span>Администратор</span>
        <strong>{{ auth?.user?.email || '...' }}</strong>
      </div>
    </header>

    <section class="composer">
      <h2 class="composer__title">
        {{ isEditing ? 'Редакция на статия' : 'Нова статия' }}
      </h2>
      <form v-if="isAdmin" @submit.prevent="submit">
        <div class="form-grid">
          <div class="field">
            <label for="title">Заглавие</label>
            <input id="title" type="text" name="title" placeholder="Въведи заглавие" v-model="formTitle">
          </div>
          <div class="field">
            <label for="content">Съдържание</label>
            <textarea
              id="content"
              name="content"
              placeholder="Сподели идеята си..."
              v-model="formContent"
            ></textarea>
          </div>
        </div>
        <div class="form-actions">
          <button class="btn btn--primary" type="submit">
            {{ isEditing ? 'Запази промените' : 'Публикувай' }}
          </button>
          <button v-if="isEditing" class="btn btn--ghost" type="button" @click="cancelEdit">
            Откажи
          </button>
        </div>
      </form>
      <p v-else class="state">Само администратор може да управлява статиите.</p>
    </section>

    <section class="feed">
      <div class="feed__header">
        <div>
          <h2 class="feed__title">Управление на статии</h2>
          <p class="feed__subtitle">Бързи действия за редакция и изтриване</p>
        </div>
      </div>

      <p v-if="pending" class="state">Зареждаме последните статии...</p>
      <p v-else-if="error" class="state">Грешка при зареждане. Опитай пак след малко.</p>
      <p v-else-if="posts && posts.length === 0" class="state">Няма публикувани статии.</p>

      <div v-else class="posts">
        <article v-for="post in posts" :key="post.id" class="post-card">
          <h3 class="post-card__title">{{ post.title }}</h3>
          <div class="post-card__meta">
            <span>Публикувано: {{ formatDate(post.created_at) }}</span>
            <span>ID: {{ post.id }}</span>
          </div>
          <p class="post-card__content">{{ post.content }}</p>
          <div class="post-card__actions">
            <button class="btn btn--ghost" type="button" @click="startEdit(post)">Редакция</button>
            <button class="btn btn--danger" type="button" @click="removePost(post)">Изтрий</button>
          </div>
        </article>
      </div>
    </section>
  </div>
</template>
