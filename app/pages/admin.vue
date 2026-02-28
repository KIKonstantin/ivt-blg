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

const { data: posts, pending, error, refresh: refreshPosts } = await useFetch('/api/posts')
const { data: comments, refresh: refreshComments } = await useFetch('/api/admin/comments')
const { data: author, refresh: refreshAuthor } = await useFetch('/api/author')
const { auth } = useAuth()
const router = useRouter()

const activeTab = ref('stories') // stories, comments, author, categories

const approveComment = async (id) => {
  await $fetch(`/api/admin/comments/${id}`, {
    method: 'PUT',
    body: { action: 'approve' }
  })
  await refreshComments()
}

const deleteComment = async (id) => {
  if (!window.confirm('Are you sure you want to delete this comment?')) return
  await $fetch(`/api/admin/comments/${id}`, {
    method: 'PUT',
    body: { action: 'delete' }
  })
  await refreshComments()
}

const refreshAll = async () => {
  await Promise.all([refreshPosts(), refreshComments(), refreshAuthor()])
}

const formTitle = ref('')
const formExcerpt = ref('')
const formContent = ref('')
const formImageUrl = ref('')
const formGallery = ref([]) // Array of image URLs
const isUploading = ref(false)
const isUploadingGallery = ref(false)
const editingId = ref(null)

// Author form state
const authorName = ref('')
const authorDescription = ref('')
const authorContent = ref('')
const authorImageUrl = ref('')
const isUploadingAuthor = ref(false)

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
  formExcerpt.value = ''
  formContent.value = ''
  formImageUrl.value = ''
  formGallery.value = []
  isUploading.value = false
  isUploadingGallery.value = false
  editingId.value = null
  editor.value.commands.setContent('')
}

const resetAuthorForm = () => {
  if (author.value) {
    authorName.value = author.value.name || ''
    authorDescription.value = author.value.description || ''
    authorContent.value = author.value.content || ''
    authorImageUrl.value = author.value.image_url || ''
    authorEditor.value.commands.setContent(author.value.content || '')
  }
}

const submit = async () => {
  const content = editor.value.getHTML()
  if (!formTitle.value.trim() || !content.trim() || content === '<p></p>') {
    return
  }

  const payload = {
    title: formTitle.value,
    excerpt: formExcerpt.value,
    content: content,
    image_url: formImageUrl.value,
    gallery: formGallery.value
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

  await refreshAll()
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

const handleGalleryUpload = async (event) => {
  const files = event.target.files
  if (!files.length) return

  isUploadingGallery.value = true

  try {
    for (const file of files) {
      const formData = new FormData()
      formData.append('file', file)
      const res = await $fetch('/api/upload', {
        method: 'POST',
        body: formData
      })
      formGallery.value.push(res.url)
    }
  } catch (err) {
    console.error('Gallery upload error:', err)
    alert('One or more images failed to upload.')
  } finally {
    isUploadingGallery.value = false
    event.target.value = ''
  }
}

const handleAuthorImageUpload = async (event) => {
  const file = event.target.files[0]
  if (!file) return

  isUploadingAuthor.value = true
  const formData = new FormData()
  formData.append('file', file)

  try {
    const res = await $fetch('/api/upload', {
      method: 'POST',
      body: formData
    })
    authorImageUrl.value = res.url
  } catch (err) {
    alert('Грешка при качване на изображението.')
  } finally {
    isUploadingAuthor.value = false
  }
}

const submitAuthor = async () => {
  const content = authorEditor.value.getHTML()
  await $fetch('/api/author', {
    method: 'PUT',
    body: {
      name: authorName.value,
      description: authorDescription.value,
      content: content,
      image_url: authorImageUrl.value
    }
  })
  await refreshAuthor()
  alert('Профилът е обновен успешно.')
}

const removeGalleryImage = (index) => {
  formGallery.value.splice(index, 1)
}

const startEdit = async (post) => {
  // Fetch full post detail for editing (to get gallery etc)
  const fullPost = await $fetch(`/api/posts/${post.id}`)

  editingId.value = fullPost.id
  formTitle.value = fullPost.title
  formExcerpt.value = fullPost.excerpt || ''
  formContent.value = fullPost.content
  formImageUrl.value = fullPost.image_url || ''
  formGallery.value = fullPost.gallery || []
  editor.value.commands.setContent(fullPost.content)
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
  return new Date(value).toLocaleDateString('bg-BG', {
    month: 'short',
    day: 'numeric',
    year: 'numeric'
  })
}

const authorEditor = ref(null)

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

  authorEditor.value = new Editor({
    content: author.value?.content || '',
    extensions: [
      StarterKit,
      Image.configure({ allowBase64: true }),
      Link
    ],
    onUpdate: ({ editor }) => {
      authorContent.value = editor.getHTML()
    }
  })

  // Initialize author form
  resetAuthorForm()

  gsap.from(".admin-sidebar", {
    x: -100,
    opacity: 0,
    duration: 1,
    ease: "power3.out"
  })

  gsap.from(".studio-content", {
    opacity: 0,
    y: 20,
    duration: 1,
    delay: 0.2,
    ease: "power3.out"
  })
})

onBeforeUnmount(() => {
  editor.value?.destroy()
  authorEditor.value?.destroy()
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
    <aside class="admin-sidebar">
      <div class="sidebar-logo">
        <span>КОРЕНИ</span>
      </div>
      <nav class="sidebar-nav">
        <button :class="{ active: activeTab === 'stories' }" @click="activeTab = 'stories'">
          <span class="nav-icon">📖</span> Истории
        </button>
        <button :class="{ active: activeTab === 'comments' }" @click="activeTab = 'comments'">
          <span class="nav-icon">💬</span> Коментари
          <span v-if="comments?.filter(c => !c.is_approved).length" class="badge">
            {{comments.filter(c => !c.is_approved).length}}
          </span>
        </button>
        <button :class="{ active: activeTab === 'author' }" @click="activeTab = 'author'">
          <span class="nav-icon">👤</span> Автор
        </button>
        <button :class="{ active: activeTab === 'categories' }" @click="activeTab = 'categories'">
          <span class="nav-icon">🏷️</span> Категории
        </button>
      </nav>
      <div class="sidebar-footer">
        <button @click="router.push('/')">Към сайта</button>
      </div>
    </aside>

    <div class="studio-content">
      <header class="admin-header">
        <div class="header-content">
          <span class="label-premium">Админ панел</span>
          <h1 class="title-premium">
            {{
              activeTab === 'stories' ? 'Управление на историите' :
                activeTab === 'comments' ? 'Модерация на коментари' :
                  activeTab === 'author' ? 'Профил на автора' :
                    'Категории'
            }}
          </h1>
          <p class="subtitle-premium">Добре дошли, {{ auth?.user?.email }}</p>
        </div>
      </header>

      <main v-if="isAdmin" class="studio-grid">
        <!-- Stories Section -->
        <template v-if="activeTab === 'stories'">
          <section class="studio-card editor-section">
            <div class="card-header">
              <h2 class="card-title">{{ isEditing ? 'Обнови история' : 'Нова история' }}</h2>
            </div>
            <form @submit.prevent="submit" class="luxury-form">
              <div class="field-premium">
                <label>Заглавие</label>
                <input type="text" v-model="formTitle" placeholder="Заглавие на вашата история..." required>
              </div>
              <div class="field-premium">
                <label>Кратко описание</label>
                <textarea v-model="formExcerpt" placeholder="Кратко резюме..." rows="3"></textarea>
              </div>
              <div class="field-premium">
                <label>Съдържание</label>
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
                <label>Основна снимка</label>
                <div class="upload-area">
                  <input type="file" @change="handleFileUpload" accept="image/*" id="fileUpload" class="hidden-input">
                  <label for="fileUpload" class="upload-trigger">
                    <span v-if="isUploading">Качване...</span>
                    <span v-else-if="formImageUrl">Промени снимката</span>
                    <span v-else>Избери снимка</span>
                  </label>
                  <div v-if="formImageUrl" class="image-preview">
                    <img :src="formImageUrl" alt="Preview">
                    <button type="button" @click="formImageUrl = ''" class="remove-preview">×</button>
                  </div>
                </div>
              </div>
              <div class="field-premium">
                <label>Галерия</label>
                <div class="gallery-management">
                  <div class="gallery-grid-admin">
                    <div v-for="(img, idx) in formGallery" :key="idx" class="gallery-item-admin">
                      <img :src="img" alt="Gallery item">
                      <button type="button" @click="removeGalleryImage(idx)" class="remove-gallery-item">×</button>
                    </div>
                    <div class="add-gallery-item">
                      <input type="file" @change="handleGalleryUpload" accept="image/*" id="galleryUpload"
                        class="hidden-input" multiple>
                      <label for="galleryUpload" class="gallery-upload-trigger">
                        <span>+</span>
                      </label>
                    </div>
                  </div>
                </div>
              </div>
              <div class="form-actions-premium">
                <button class="btn-luxury primary" type="submit">
                  {{ isEditing ? 'Запази промените' : 'Публикувай' }}
                </button>
                <button v-if="isEditing" class="btn-luxury ghost" type="button" @click="cancelEdit">
                  Отказ
                </button>
              </div>
            </form>
          </section>

          <section class="studio-card list-section">
            <div class="card-header">
              <h2 class="card-title">Публикувани истории</h2>
            </div>
            <div v-if="pending" class="state-loading">Зареждане...</div>
            <div v-else-if="posts && posts.length" class="posts-admin-list">
              <article v-for="post in posts" :key="post.id" class="post-admin-card">
                <div class="post-info">
                  <h3>{{ post.title }}</h3>
                  <span class="post-date">{{ formatDate(post.created_at) }}</span>
                </div>
                <div class="post-actions">
                  <button @click="startEdit(post)" class="action-btn edit">Редактирай</button>
                  <button @click="removePost(post)" class="action-btn delete">Изтрий</button>
                </div>
              </article>
            </div>
            <div v-else class="state-empty">Няма публикувани истории.</div>
          </section>
        </template>

        <!-- Comments Section -->
        <template v-if="activeTab === 'comments'">
          <section class="studio-card">
            <div class="card-header">
              <h2 class="card-title">Читателски мисли</h2>
            </div>
            <div v-if="!comments || !comments.length" class="state-empty">Няма намерени коментари.</div>
            <div v-else class="comments-admin-list">
              <article v-for="c in comments" :key="c.id" class="comment-admin-card"
                :class="{ 'pending': !c.is_approved }">
                <div class="comment-admin-header">
                  <div>
                    <span class="comment-author">{{ c.author_name }}</span>
                    <span class="comment-post-title">към {{ c.post_title }}</span>
                  </div>
                  <span class="status-badge" :class="c.is_approved ? 'approved' : 'pending'">
                    {{ c.is_approved ? 'Одобрен' : 'Чакащ' }}
                  </span>
                </div>
                <p class="comment-admin-content">{{ c.content }}</p>
                <div class="comment-admin-actions">
                  <button v-if="!c.is_approved" @click="approveComment(c.id)" class="action-btn edit">Одобри</button>
                  <button @click="deleteComment(c.id)" class="action-btn delete">Изтрий</button>
                </div>
              </article>
            </div>
          </section>
        </template>

        <!-- Author Section -->
        <template v-if="activeTab === 'author'">
          <section class="studio-card">
            <div class="card-header">
              <h2 class="card-title">Профил на автора</h2>
            </div>
            <form @submit.prevent="submitAuthor" class="luxury-form">
              <div class="field-premium">
                <label>Име</label>
                <input type="text" v-model="authorName" placeholder="Име на автора..." required>
              </div>
              <div class="field-premium">
                <label>Кратко представяне</label>
                <textarea v-model="authorDescription" placeholder="Кратка биография..." rows="3"></textarea>
              </div>
              <div class="field-premium">
                <label>История/Биография (WYSIWYG)</label>
                <div class="tiptap-container">
                  <div v-if="authorEditor" class="editor-toolbar">
                    <button @click.prevent="authorEditor.chain().focus().toggleBold().run()"
                      :class="{ 'is-active': authorEditor.isActive('bold') }">B</button>
                    <button @click.prevent="authorEditor.chain().focus().toggleItalic().run()"
                      :class="{ 'is-active': authorEditor.isActive('italic') }">I</button>
                    <button @click.prevent="authorEditor.chain().focus().toggleHeading({ level: 2 }).run()"
                      :class="{ 'is-active': authorEditor.isActive('heading', { level: 2 }) }">H2</button>
                    <button @click.prevent="authorEditor.chain().focus().toggleBulletList().run()"
                      :class="{ 'is-active': authorEditor.isActive('bulletList') }">List</button>
                  </div>
                  <editor-content :editor="authorEditor" class="tiptap-editor" />
                </div>
              </div>
              <div class="field-premium">
                <label>Профилна снимка</label>
                <div class="upload-area">
                  <input type="file" @change="handleAuthorImageUpload" accept="image/*" id="authorUpload"
                    class="hidden-input">
                  <label for="authorUpload" class="upload-trigger">
                    <span v-if="isUploadingAuthor">Качване...</span>
                    <span v-else-if="authorImageUrl">Промени снимката</span>
                    <span v-else>Избери снимка</span>
                  </label>
                  <div v-if="authorImageUrl" class="image-preview">
                    <img :src="authorImageUrl" alt="Author">
                  </div>
                </div>
              </div>
              <div class="form-actions-premium">
                <button class="btn-luxury primary" type="submit">Запази профила</button>
              </div>
            </form>
          </section>
        </template>

        <!-- Categories Section Placeholder -->
        <template v-if="activeTab === 'categories'">
          <section class="studio-card">
            <div class="card-header">
              <h2 class="card-title">Категории</h2>
            </div>
            <p class="state-notice">Управлението на категории ще бъде добавено в следващата версия.</p>
          </section>
        </template>
      </main>
      <div v-else class="state-notice">
        Само администратори имат достъп до това съдържание.
      </div>
    </div>
  </div>
</template>

<style scoped>
.admin-studio {
  display: flex;
  min-height: 100vh;
  padding-top: 0;
  background-color: #fcfcfc;
}

.admin-sidebar {
  width: 280px;
  background-color: var(--color-midnight);
  color: white;
  display: flex;
  flex-direction: column;
  position: fixed;
  height: 100vh;
  z-index: 100;
}

.sidebar-logo {
  padding: 40px;
  font-size: 20px;
  font-weight: 800;
  letter-spacing: 4px;
}

.sidebar-nav {
  flex: 1;
  padding: 0 20px;
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.sidebar-nav button {
  background: transparent;
  border: none;
  color: rgba(255, 255, 255, 0.6);
  padding: 16px 20px;
  text-align: left;
  font-family: inherit;
  font-size: 13px;
  text-transform: uppercase;
  letter-spacing: 2px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
  display: flex;
  align-items: center;
  gap: 12px;
  border-radius: 4px;
}

.sidebar-nav button:hover,
.sidebar-nav button.active {
  color: white;
  background: rgba(255, 255, 255, 0.05);
}

.sidebar-nav button.active {
  color: var(--color-gold);
}

.nav-icon {
  font-size: 16px;
  width: 24px;
}

.badge {
  background: var(--color-gold);
  color: white;
  font-size: 10px;
  padding: 2px 8px;
  border-radius: 10px;
  margin-left: auto;
}

.sidebar-footer {
  padding: 40px 20px;
  border-top: 1px solid rgba(255, 255, 255, 0.05);
}

.sidebar-footer button {
  width: 100%;
  padding: 12px;
  background: transparent;
  border: 1px solid rgba(255, 255, 255, 0.2);
  color: white;
  font-size: 11px;
  text-transform: uppercase;
  letter-spacing: 2px;
  cursor: pointer;
}

.studio-content {
  flex: 1;
  margin-left: 280px;
  padding: 64px 80px;
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
  grid-template-columns: 1fr;
  gap: 48px;
  max-width: 1000px;
}

/* Moderation Styles */
.comments-admin-list {
  display: grid;
  gap: 24px;
}

.comment-admin-card {
  padding: 24px;
  border: 1px solid rgba(15, 26, 15, 0.05);
  background: rgba(15, 26, 15, 0.01);
  transition: all 0.3s;
}

.comment-admin-card.pending {
  border-left: 3px solid var(--color-gold);
  background: rgba(184, 158, 101, 0.02);
}

.comment-admin-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 12px;
}

.comment-author {
  font-weight: 800;
  font-size: 14px;
  text-transform: uppercase;
  letter-spacing: 1px;
}

.comment-post-title {
  display: block;
  font-size: 11px;
  color: var(--color-sage);
  margin-top: 2px;
}

.status-badge {
  font-size: 9px;
  text-transform: uppercase;
  letter-spacing: 1px;
  font-weight: 800;
  padding: 4px 8px;
  border-radius: 2px;
}

.status-badge.approved {
  background: rgba(15, 26, 15, 0.05);
  color: var(--color-sage);
}

.status-badge.pending {
  background: var(--color-gold);
  color: white;
}

.comment-admin-content {
  font-size: 14px;
  color: var(--color-midnight);
  line-height: 1.6;
  margin-bottom: 20px;
}

.comment-admin-actions {
  display: flex;
  gap: 20px;
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

.gallery-management {
  margin-top: 8px;
}

.gallery-grid-admin {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
  gap: 16px;
  margin-bottom: 12px;
}

.gallery-item-admin {
  position: relative;
  aspect-ratio: 1;
  border: 1px solid rgba(15, 26, 15, 0.1);
  overflow: hidden;
}

.gallery-item-admin img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.remove-gallery-item {
  position: absolute;
  top: 4px;
  right: 4px;
  background: var(--color-midnight);
  color: white;
  border: none;
  width: 20px;
  height: 20px;
  border-radius: 50%;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 14px;
  opacity: 0.8;
  transition: opacity 0.3s;
}

.remove-gallery-item:hover {
  opacity: 1;
}

.add-gallery-item {
  aspect-ratio: 1;
  border: 1.5px dashed rgba(15, 26, 15, 0.2);
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.3s;
}

.add-gallery-item:hover {
  border-color: var(--color-gold);
  background: rgba(15, 26, 15, 0.02);
}

.gallery-upload-trigger {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  font-size: 12px;
  text-transform: uppercase;
  letter-spacing: 1px;
  font-weight: 700;
  color: var(--color-sage);
}

.field-hint {
  font-size: 11px;
  color: var(--color-sage);
  font-style: italic;
  margin-top: 4px;
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
