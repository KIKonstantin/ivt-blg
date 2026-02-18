<script setup>
import { onMounted } from 'vue'
import gsap from 'gsap'

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
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

const cancelEdit = () => {
  resetForm()
}

const removePost = async (post) => {
  const confirmDelete = window.confirm(`Are you sure you want to delete "${post.title}"?`)
  if (!confirmDelete) return

  await $fetch(`/api/posts/${post.id}`, {
    method: 'DELETE'
  })
  await refresh()
  if (editingId.value === post.id) resetForm()
}

const formatDate = (value) => {
  if (!value) return ''
  return new Date(value).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric'
  })
}

onMounted(() => {
  gsap.from(".admin-header", {
    opacity: 0,
    y: -20,
    duration: 1,
    ease: "power3.out"
  })

  gsap.from(".studio-card", {
    opacity: 0,
    y: 30,
    duration: 1,
    delay: 0.2,
    ease: "power3.out"
  })
})
</script>

<template>
  <div class="admin-studio">
    <div class="container-premium">
      <header class="admin-header">
        <div class="header-content">
          <span class="label-premium">Studio Dashboard</span>
          <h1 class="title-premium">Content Management</h1>
          <p class="subtitle-premium">Welcome back, {{ auth?.user?.email }}</p>
        </div>
      </header>

      <main class="studio-grid">
        <!-- Editor Section -->
        <section class="studio-card editor-section">
          <div class="card-header">
            <h2 class="card-title">{{ isEditing ? 'Update Story' : 'New Story' }}</h2>
          </div>

          <form v-if="isAdmin" @submit.prevent="submit" class="luxury-form">
            <div class="field-premium">
              <label>Title</label>
              <input type="text" v-model="formTitle" placeholder="The silent echo of the pines..." required>
            </div>

            <div class="field-premium">
              <label>Body Content</label>
              <textarea v-model="formContent" placeholder="Write your story here..." required></textarea>
            </div>

            <div class="form-actions-premium">
              <button class="btn-luxury primary" type="submit">
                {{ isEditing ? 'Save Changes' : 'Publish Story' }}
              </button>
              <button v-if="isEditing" class="btn-luxury ghost" type="button" @click="cancelEdit">
                Discard
              </button>
            </div>
          </form>
          <div v-else class="state-notice">
            Only administrators can manage content.
          </div>
        </section>

        <!-- List Section -->
        <section class="studio-card list-section">
          <div class="card-header">
            <h2 class="card-title">Recent Publications</h2>
          </div>

          <div v-if="pending" class="state-loading">Syncing...</div>
          <div v-else-if="posts && posts.length" class="posts-admin-list">
            <article v-for="post in posts" :key="post.id" class="post-admin-card">
              <div class="post-info">
                <h3>{{ post.title }}</h3>
                <span class="post-date">{{ formatDate(post.created_at) }}</span>
              </div>
              <div class="post-actions">
                <button @click="startEdit(post)" class="action-btn edit">Edit</button>
                <button @click="removePost(post)" class="action-btn delete">Delete</button>
              </div>
            </article>
          </div>
          <div v-else class="state-empty">No stories published yet.</div>
        </section>
      </main>
    </div>
  </div>
</template>

<style scoped>
.admin-studio {
  padding-top: 140px;
  padding-bottom: 80px;
  min-height: 100vh;
}

.admin-header {
  margin-bottom: 64px;
}

.label-premium {
  display: inline-block;
  font-size: 11px;
  text-transform: uppercase;
  letter-spacing: 3px;
  color: var(--color-gold);
  margin-bottom: 12px;
  font-weight: 600;
}

.title-premium {
  font-size: 48px;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: -1px;
  margin: 0;
  line-height: 1;
}

.subtitle-premium {
  color: var(--color-sage);
  margin-top: 12px;
  font-size: 18px;
}

.studio-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 48px;
  align-items: start;
}

.studio-card {
  background-color: var(--color-white);
  border: 1px solid rgba(15, 26, 15, 0.08);
  padding: 48px;
  transition: box-shadow 0.3s ease;
}

.studio-card:hover {
  box-shadow: 0 20px 40px rgba(15, 26, 15, 0.03);
}

.card-header {
  margin-bottom: 32px;
  border-bottom: 1px solid rgba(15, 26, 15, 0.05);
  padding-bottom: 16px;
}

.card-title {
  font-size: 20px;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 1px;
}

.luxury-form {
  display: grid;
  gap: 32px;
}

.field-premium {
  display: grid;
  gap: 8px;
}

.field-premium label {
  font-size: 11px;
  text-transform: uppercase;
  letter-spacing: 2px;
  font-weight: 700;
  color: var(--color-sage);
}

.field-premium input,
.field-premium textarea {
  width: 100%;
  padding: 16px 0;
  font-family: inherit;
  font-size: 16px;
  background: transparent;
  border: none;
  border-bottom: 1px solid rgba(15, 26, 15, 0.1);
  outline: none;
  transition: border-color 0.3s;
}

.field-premium input:focus,
.field-premium textarea:focus {
  border-color: var(--color-midnight);
}

.field-premium textarea {
  min-height: 300px;
  resize: none;
  line-height: 1.6;
}

.form-actions-premium {
  display: flex;
  gap: 24px;
}

.btn-luxury {
  padding: 14px 28px;
  font-family: inherit;
  font-size: 13px;
  text-transform: uppercase;
  letter-spacing: 2px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
  border: none;
}

.btn-luxury.primary {
  background-color: var(--color-midnight);
  color: var(--color-white);
}

.btn-luxury.primary:hover {
  background-color: var(--color-gold);
}

.btn-luxury.ghost {
  background: transparent;
  color: var(--color-sage);
  border: 1px solid rgba(15, 26, 15, 0.1);
}

.btn-luxury.ghost:hover {
  border-color: var(--color-midnight);
  color: var(--color-midnight);
}

.posts-admin-list {
  display: grid;
  gap: 24px;
}

.post-admin-card {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-bottom: 24px;
  border-bottom: 1px solid rgba(15, 26, 15, 0.05);
}

.post-info h3 {
  font-size: 16px;
  font-weight: 700;
  margin: 0 0 4px;
}

.post-date {
  font-size: 12px;
  color: var(--color-sage);
}

.post-actions {
  display: flex;
  gap: 16px;
}

.action-btn {
  background: transparent;
  border: none;
  font-size: 12px;
  text-transform: uppercase;
  letter-spacing: 1px;
  cursor: pointer;
  transition: color 0.3s;
}

.action-btn.edit {
  color: var(--color-gold);
}

.action-btn.delete {
  color: var(--color-sage);
}

.action-btn.delete:hover {
  color: #FF4444;
}

.state-loading,
.state-empty,
.state-notice {
  padding: 40px;
  text-align: center;
  color: var(--color-sage);
  font-style: italic;
}

@media (max-width: 1024px) {
  .studio-grid {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 640px) {
  .admin-studio {
    padding-top: 100px;
  }

  .title-premium {
    font-size: 32px;
  }

  .studio-card {
    padding: 24px;
  }
}
</style>
