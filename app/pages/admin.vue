<script setup>
import { onMounted, onBeforeUnmount } from 'vue'
import gsap from 'gsap'
import { Editor, EditorContent } from '@tiptap/vue-3'
import StarterKit from '@tiptap/starter-kit'
import Image from '@tiptap/extension-image'
import Link from '@tiptap/extension-link'

definePageMeta({
  middleware: 'admin'
})

const { data: posts, pending, error, refresh } = await useFetch('/api/posts')
const { auth } = useAuth()
const router = useRouter()

const formTitle = ref('')
const formContent = ref('')
const formImageUrl = ref('')
const isUploading = ref(false)
const editingId = ref(null)

const editor = ref(null)

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
  formImageUrl.value = ''
  isUploading.value = false
  editingId.value = null
  editor.value.commands.setContent('')
}

const submit = async () => {
  const content = editor.value.getHTML()
  if (!formTitle.value.trim() || !content.trim() || content === '<p></p>') {
    return
  }

  const payload = {
    title: formTitle.value,
    content: content,
    image_url: formImageUrl.value
  }

  if (isEditing.value) {
    await $fetch(`/api/posts/${editingId.value}`, {
      method: 'PUT',
      body: payload
    })
  } else {
    await $fetch('/api/posts', {
      method: 'POST',
      body: payload
    })
  }

  await refresh()
  resetForm()
}

const handleFileUpload = async (event) => {
  const file = event.target.files[0]
  if (!file) return

  isUploading.value = true
  const formData = new FormData()
  formData.append('file', file)

  try {
    const res = await $fetch('/api/upload', {
      method: 'POST',
      body: formData
    })
    formImageUrl.value = res.url
  } catch (err) {
    console.error('Upload error:', err)
    alert('Failed to upload image. Please try again.')
  } finally {
    isUploading.value = false
  }
}

const startEdit = (post) => {
  editingId.value = post.id
  formTitle.value = post.title
  formContent.value = post.content
  formImageUrl.value = post.image_url || ''
  editor.value.commands.setContent(post.content)
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
  editor.value = new Editor({
    content: formContent.value,
    extensions: [
      StarterKit,
      Image.configure({
        allowBase64: true,
      }),
      Link,
    ],
    onUpdate: ({ editor }) => {
      formContent.value = editor.getHTML()
    },
  })

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

onBeforeUnmount(() => {
  editor.value.destroy()
})

const addEditorImage = () => {
  const input = document.createElement('input')
  input.type = 'file'
  input.accept = 'image/*'
  input.onchange = async (e) => {
    const file = e.target.files[0]
    if (!file) return

    const formData = new FormData()
    formData.append('file', file)

    try {
      const res = await $fetch('/api/upload', {
        method: 'POST',
        body: formData
      })
      editor.value.chain().focus().setImage({ src: res.url }).run()
    } catch (err) {
      alert('Failed to upload image')
    }
  }
  input.click()
}
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
              <label>Body Content (Rich Text)</label>
              <div class="tiptap-container">
                <div v-if="editor" class="editor-toolbar">
                  <button @click.prevent="editor.chain().focus().toggleBold().run()"
                    :class="{ 'is-active': editor.isActive('bold') }">B</button>
                  <button @click.prevent="editor.chain().focus().toggleItalic().run()"
                    :class="{ 'is-active': editor.isActive('italic') }">I</button>
                  <button @click.prevent="editor.chain().focus().toggleHeading({ level: 2 }).run()"
                    :class="{ 'is-active': editor.isActive('heading', { level: 2 }) }">H2</button>
                  <button @click.prevent="editor.chain().focus().toggleBulletList().run()"
                    :class="{ 'is-active': editor.isActive('bulletList') }">List</button>
                  <button @click.prevent="addEditorImage">Img</button>
                </div>
                <editor-content :editor="editor" class="tiptap-editor" />
              </div>
            </div>

            <div class="field-premium">
              <label>Hero Image</label>
              <div class="upload-area">
                <input type="file" @change="handleFileUpload" accept="image/*" id="fileUpload" class="hidden-input">
                <label for="fileUpload" class="upload-trigger">
                  <span v-if="isUploading">Uploading...</span>
                  <span v-else-if="formImageUrl">Change Image</span>
                  <span v-else>Select Hero Image</span>
                </label>
                <div v-if="formImageUrl" class="image-preview">
                  <img :src="formImageUrl" alt="Preview">
                  <button type="button" @click="formImageUrl = ''" class="remove-preview">×</button>
                </div>
              </div>
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

.upload-area {
  display: grid;
  gap: 16px;
  padding: 24px;
  border: 1px dashed rgba(15, 26, 15, 0.1);
  background: rgba(15, 26, 15, 0.02);
  text-align: center;
}

.hidden-input {
  display: none;
}

.upload-trigger {
  cursor: pointer;
  font-size: 13px;
  text-transform: uppercase;
  letter-spacing: 2px;
  font-weight: 700;
  color: var(--color-gold);
  padding: 12px;
  transition: opacity 0.3s;
}

.upload-trigger:hover {
  opacity: 0.7;
}

.image-preview {
  position: relative;
  width: 100%;
  aspect-ratio: 16/9;
  overflow: hidden;
}

.image-preview img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.remove-preview {
  position: absolute;
  top: 12px;
  right: 12px;
  background: var(--color-midnight);
  color: white;
  border: none;
  width: 24px;
  height: 24px;
  border-radius: 50%;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 18px;
}

.tiptap-container {
  border: 1px solid rgba(15, 26, 15, 0.1);
  background: var(--color-white);
  min-height: 400px;
}

.editor-toolbar {
  padding: 8px;
  border-bottom: 1px solid rgba(15, 26, 15, 0.1);
  display: flex;
  gap: 8px;
  background: rgba(15, 26, 15, 0.02);
}

.editor-toolbar button {
  background: white;
  border: 1px solid rgba(15, 26, 15, 0.1);
  padding: 4px 12px;
  font-size: 12px;
  cursor: pointer;
  text-transform: uppercase;
  font-weight: 700;
  color: var(--color-midnight);
}

.editor-toolbar button.is-active {
  background: var(--color-gold);
  color: white;
  border-color: var(--color-gold);
}

.tiptap-editor {
  padding: 24px;
}

:deep(.tiptap) {
  outline: none;
  min-height: 300px;
  line-height: 1.6;
}

:deep(.tiptap p) {
  margin-bottom: 1em;
}

:deep(.tiptap img) {
  max-width: 100%;
  height: auto;
  margin: 24px 0;
  display: block;
}

:deep(.tiptap h2) {
  font-size: 24px;
  font-weight: 800;
  margin: 1.5em 0 0.5em;
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
