<script setup>
import { onMounted, computed, ref } from 'vue'

const route = useRoute()
const { auth } = useAuth()

// Fetch primary post data
const { data: post, pending, error } = await useFetch(`/api/posts/${route.params.id}`)

// Fetch comments
const {
  data: comments,
  pending: commentsPending,
  error: commentsError,
  refresh: refreshComments
} = await useFetch(`/api/posts/${route.params.id}/comments`)

// Fetch all posts for "Related Stories"
const { data: allPosts } = await useFetch('/api/posts')
const { data: author } = await useFetch('/api/author')
const relatedPosts = computed(() => {
  if (!allPosts.value) return []
  return allPosts.value
    .filter(p => p.id !== post.value?.id)
    .slice(0, 3)
})

// Gallery Lightbox State
const lightboxActive = ref(false)
const currentImageIdx = ref(0)
const isZoomed = ref(false)
const lightboxImg = ref(null)

const openLightbox = (idx) => {
  currentImageIdx.value = idx
  lightboxActive.value = true
  isZoomed.value = false
  document.body.style.overflow = 'hidden'

  // GSAP animation for entry
  nextTick(() => {
    gsap.fromTo(".premium-lightbox",
      { opacity: 0 },
      { opacity: 1, duration: 0.5, ease: "power2.out" }
    )
    gsap.fromTo(".lightbox-main-image",
      { scale: 0.9, opacity: 0 },
      { scale: 1, opacity: 1, duration: 0.6, delay: 0.1, ease: "back.out(1.2)" }
    )
  })
}

const closeLightbox = () => {
  gsap.to(".premium-lightbox", {
    opacity: 0,
    duration: 0.4,
    ease: "power2.inOut",
    onComplete: () => {
      lightboxActive.value = false
      document.body.style.overflow = ''
    }
  })
}

const nextImage = () => {
  if (post.value?.gallery) {
    gsap.to(".lightbox-main-image", {
      x: -50,
      opacity: 0,
      duration: 0.3,
      ease: "power2.in",
      onComplete: () => {
        currentImageIdx.value = (currentImageIdx.value + 1) % post.value.gallery.length
        isZoomed.value = false
        gsap.fromTo(".lightbox-main-image",
          { x: 50, opacity: 0 },
          { x: 0, opacity: 1, duration: 0.4, ease: "power2.out" }
        )
      }
    })
  }
}

const prevImage = () => {
  if (post.value?.gallery) {
    gsap.to(".lightbox-main-image", {
      x: 50,
      opacity: 0,
      duration: 0.3,
      ease: "power2.in",
      onComplete: () => {
        currentImageIdx.value = (currentImageIdx.value - 1 + post.value.gallery.length) % post.value.gallery.length
        isZoomed.value = false
        gsap.fromTo(".lightbox-main-image",
          { x: -50, opacity: 0 },
          { x: 0, opacity: 1, duration: 0.4, ease: "power2.out" }
        )
      }
    })
  }
}

const toggleZoom = () => {
  isZoomed.value = !isZoomed.value
}

// Handle keys
onMounted(() => {
  const handleKeydown = (e) => {
    if (!lightboxActive.value) return
    if (e.key === 'Escape') closeLightbox()
    if (e.key === 'ArrowRight') nextImage()
    if (e.key === 'ArrowLeft') prevImage()
  }
  window.addEventListener('keydown', handleKeydown)
  onUnmounted(() => window.removeEventListener('keydown', handleKeydown))
})

const authorName = ref('')
const commentContent = ref('')
const commentError = ref('')
const commentPending = ref(false)
const commentSuccess = ref(false)
const isLoggedIn = computed(() => Boolean(auth.value?.user))

const formatDate = (value) => {
  if (!value) return ''
  return new Date(value).toLocaleDateString('bg-BG', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const formatDateTime = (value) => {
  if (!value) return ''
  return new Date(value).toLocaleString('bg-BG', {
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const submitComment = async () => {
  commentError.value = ''
  commentSuccess.value = false
  const content = commentContent.value.trim()
  const name = authorName.value.trim()

  if (!content || !name) {
    commentError.value = 'Моля, въведи име и коментар.'
    return
  }

  commentPending.value = true
  try {
    await $fetch(`/api/posts/${route.params.id}/comments`, {
      method: 'POST',
      body: {
        content,
        author_name: name
      }
    })
    commentContent.value = ''
    authorName.value = ''
    commentSuccess.value = true
    // refreshComments() // Don't refresh yet as it's not approved
  } catch (err) {
    commentError.value = 'Грешка при изпращане на коментара.'
  } finally {
    commentPending.value = false
  }
}

// Split title into lines for animation
const titleChunks = computed(() => {
  if (!post.value?.title) return []
  const words = post.value.title.split(' ')
  const chunks = []
  for (let i = 0; i < words.length; i += 3) {
    chunks.push(words.slice(i, i + 3).join(' '))
  }
  return chunks
})

useHead({
  title: post.value ? `${post.value.title} — КОРЕНИ` : "КОРЕНИ",
  link: [
    { rel: 'preconnect', href: 'https://fonts.googleapis.com' },
    { rel: 'preconnect', href: 'https://fonts.gstatic.com', crossorigin: '' },
    { rel: 'stylesheet', href: 'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800;900&display=swap' }
  ],
  script: [
    { src: 'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js' },
    { src: 'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js' }
  ]
})

onMounted(() => {
  const initGSAP = () => {
    if (typeof gsap !== 'undefined' && typeof ScrollTrigger !== 'undefined') {
      gsap.registerPlugin(ScrollTrigger);

      // Reading progress bar
      window.addEventListener('scroll', () => {
        const winScroll = document.body.scrollTop || document.documentElement.scrollTop;
        const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
        const scrolled = (winScroll / height) * 100;
        const pb = document.getElementById('progressBar');
        if (pb) pb.style.width = scrolled + '%';
      });

      // Hero entrance animation
      const heroTl = gsap.timeline();

      heroTl.from(".article-category span", {
        y: 30,
        opacity: 0,
        duration: 1,
        ease: "power3.out",
        delay: 0.3
      })
        .from(".article-title .line span", {
          y: 100,
          opacity: 0,
          duration: 1.2,
          stagger: 0.15,
          ease: "power4.out"
        }, "-=0.5")
        .from(".article-meta span", {
          opacity: 0,
          y: 20,
          duration: 0.8,
          stagger: 0.1,
          ease: "power2.out"
        }, "-=0.8");

      // Hero parallax
      gsap.to("#heroImage", {
        yPercent: 30,
        ease: "none",
        scrollTrigger: {
          trigger: ".article-header",
          start: "top top",
          end: "bottom top",
          scrub: true
        }
      });

      // Hero content fade out
      gsap.to(".article-hero-content", {
        opacity: 0,
        y: -50,
        ease: "none",
        scrollTrigger: {
          trigger: ".article-header",
          start: "top top",
          end: "50% top",
          scrub: true
        }
      });

      // Staggered reveal for article elements
      gsap.utils.toArray(".article-body p, .article-body h2, .article-body h3, .pull-quote, .inline-image, .comment-card, .gallery-item-luxury").forEach((element) => {
        gsap.to(element, {
          opacity: 1,
          y: 0,
          duration: 0.8,
          ease: "power3.out",
          scrollTrigger: {
            trigger: element,
            start: "top 90%"
          }
        });
      });

      // Share section reveal
      gsap.to("#shareSection", {
        opacity: 1,
        duration: 1,
        ease: "power2.out",
        scrollTrigger: {
          trigger: "#shareSection",
          start: "top 90%"
        }
      });

      // Author box reveal
      gsap.to("#authorBox", {
        opacity: 1,
        y: 0,
        duration: 1,
        ease: "power3.out",
        scrollTrigger: {
          trigger: "#authorBox",
          start: "top 90%"
        }
      });

      // Related cards staggered reveal
      gsap.utils.toArray(".related-card").forEach((card, i) => {
        gsap.to(card, {
          opacity: 1,
          y: 0,
          duration: 0.8,
          delay: i * 0.15,
          ease: "power3.out",
          scrollTrigger: {
            trigger: card,
            start: "top 90%"
          }
        });
      });

    } else {
      setTimeout(initGSAP, 100);
    }
  };

  initGSAP();
});
</script>

<template>
  <div class="article-page-wrapper">
    <!-- Reading Progress -->
    <div class="progress-bar" id="progressBar"></div>

    <p v-if="pending" class="state-loading">Зареждаме историята...</p>
    <p v-else-if="error" class="state-error">Историята не е намерена.</p>

    <div v-else>
      <!-- Article Header -->
      <header class="article-header">
        <div class="article-hero-image" id="heroImage"
          :style="{ backgroundImage: `url(${post.image_url || `https://picsum.photos/seed/${post.id}/1920/1080`})` }">
        </div>
        <div class="article-hero-overlay"></div>
        <div class="article-hero-content">
          <div class="article-category">
            <span>Природа</span>
          </div>
          <h1 class="article-title">
            <div v-for="(line, idx) in titleChunks" :key="idx" class="line">
              <span>{{ line }}</span>
            </div>
          </h1>
          <div class="article-meta">
            <span>От екипа</span>
            <span>{{ formatDate(post.created_at) }}</span>
            <span>8 мин. четене</span>
          </div>
        </div>
      </header>

      <!-- Article Content -->
      <article class="article-content">
        <div class="article-body rich-content" v-html="post.content"></div>

        <!-- Article Gallery -->
        <section v-if="post.gallery && post.gallery.length" class="article-gallery-section">
          <header class="section-header-minimal">
            <span class="label-gold">Визуално пътешествие</span>
            <h2 class="title-minimal">Галерия</h2>
          </header>
          <div class="gallery-grid">
            <div v-for="(img, idx) in post.gallery" :key="idx" class="gallery-item-luxury" @click="openLightbox(idx)">
              <img :src="img" :alt="`Gallery image ${idx + 1}`" class="reveal-on-scroll">
            </div>
          </div>
        </section>

        <!-- Premium Lightbox -->
        <Transition name="lightbox">
          <div v-if="lightboxActive" class="premium-lightbox" @click.self="closeLightbox">
            <div class="lightbox-overlay"></div>

            <div class="lightbox-content" :class="{ 'zoomed': isZoomed }">
              <img :src="post.gallery[currentImageIdx]" class="lightbox-main-image" @click="toggleZoom"
                ref="lightboxImg">
            </div>

            <div class="lightbox-controls">
              <button class="control-btn close" @click="closeLightbox">
                <span class="icon">×</span>
                <span class="label">Затвори</span>
              </button>

              <div class="nav-controls">
                <button class="control-btn prev" @click="prevImage">←</button>
                <div class="image-counter">{{ currentImageIdx + 1 }} / {{ post.gallery.length }}</div>
                <button class="control-btn next" @click="nextImage">→</button>
              </div>

              <button class="control-btn zoom" @click="toggleZoom">
                <span class="icon">{{ isZoomed ? '⊖' : '⊕' }}</span>
                <span class="label">{{ isZoomed ? 'Намали' : 'Увеличи' }}</span>
              </button>
            </div>
          </div>
        </Transition>

        <div class="share-section" id="shareSection">
          <span class="share-label">Сподели тази история</span>
          <div class="share-links">
            <a href="#">Twitter</a>
            <a href="#">Facebook</a>
            <a href="#">Копирай линк</a>
          </div>
        </div>

        <div class="author-box" id="authorBox">
          <div class="author-avatar">
            <img :src="author?.image_url || 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=200&q=80'"
              :alt="author?.name">
          </div>
          <div class="author-info">
            <h4>{{ author?.name || 'Ивета Костадинова' }}</h4>
            <p>{{ author?.description || 'Пътешественик и разказвач на истории, посветена на изследването на скритите кътчета на природата.' }}</p>
          </div>
        </div>

        <!-- Comments Section (Themed) -->
        <section class="comments-section">
          <div class="comments-header">
            <h3>Мисли</h3>
            <span class="comments-count">{{ comments?.length ?? 0 }} споделени мисли</span>
          </div>

          <div class="comments-list">
            <article v-for="comment in comments" :key="comment.id" class="comment-card">
              <div class="comment-meta">
                <strong>{{ comment.author_name }}</strong>
                <span>{{ formatDateTime(comment.created_at) }}</span>
              </div>
              <p>{{ comment.content }}</p>
            </article>
          </div>

          <form class="comment-form-luxury" @submit.prevent="submitComment">
            <div v-if="commentSuccess" class="success-msg-luxury">
              Твоята мисъл беше изпратена за модерация. Тя ще се появи веднага след одобрение.
            </div>
            <div class="form-row-luxury">
              <input v-model="authorName" type="text" placeholder="Твоето име" required>
            </div>
            <textarea v-model="commentContent" placeholder="Какво събуди в теб тази история?" required></textarea>
            <button type="submit" :disabled="commentPending">
              {{ commentPending ? 'Изпращане...' : 'Сподели мисъл' }}
            </button>
            <p v-if="commentError" class="error-msg">{{ commentError }}</p>
          </form>
        </section>
      </article>

      <!-- Related Stories -->
      <section v-if="relatedPosts.length" class="related">
        <div class="related-header">
          <h3 class="related-title">Още истории</h3>
        </div>
        <div class="related-grid">
          <article v-for="p in relatedPosts" :key="p.id" class="related-card" @click="navigateTo(`/posts/${p.id}`)">
            <div class="related-image">
              <img :src="p.image_url || `https://picsum.photos/seed/${p.id}/600/400`" :alt="p.title">
            </div>
            <h4 class="related-card-title">{{ p.title }}</h4>
            <div class="related-card-meta">Природа · {{ formatDate(p.created_at) }}</div>
          </article>
        </div>
      </section>

      <!-- Footer -->
      <footer>
        <div class="footer-content">
          <div>© 2024 Nat're</div>
          <div class="footer-links">
            <a href="#">Instagram</a>
            <a href="#">Pinterest</a>
            <a href="#">RSS</a>
          </div>
        </div>
      </footer>
    </div>
  </div>
</template>

<style scoped>
.article-page-wrapper {
  background-color: var(--color-cream);
  color: var(--color-midnight);
  font-family: 'Inter', sans-serif;
  line-height: 1.8;
}

/* Progress Bar */
.progress-bar {
  position: fixed;
  top: 0;
  left: 0;
  height: 3px;
  background-color: var(--color-gold);
  z-index: 1001;
  width: 0%;
}

/* Article Header */
.article-header {
  height: 100vh;
  position: relative;
  display: flex;
  align-items: flex-end;
  justify-content: center;
  overflow: hidden;
}

.article-hero-image {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 120%;
  background-size: cover;
  background-position: center;
}

.article-hero-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(to bottom,
      rgba(15, 26, 15, 0.3) 0%,
      rgba(15, 26, 15, 0.5) 60%,
      rgba(15, 26, 15, 0.8) 100%);
}

.article-hero-content {
  position: relative;
  z-index: 2;
  text-align: center;
  color: #FFFFFF;
  padding: 80px 48px;
  max-width: 900px;
}

.article-category {
  font-size: 12px;
  letter-spacing: 3px;
  text-transform: uppercase;
  margin-bottom: 24px;
  color: var(--color-gold);
  overflow: hidden;
}

.article-category span {
  display: inline-block;
}

.article-title {
  font-size: clamp(40px, 8vw, 80px);
  font-weight: 800;
  line-height: 1.1;
  margin-bottom: 32px;
  text-transform: uppercase;
  letter-spacing: -2px;
}

.article-title .line {
  display: block;
  overflow: hidden;
}

.article-title .line span {
  display: block;
}

.article-meta {
  font-size: 14px;
  letter-spacing: 2px;
  text-transform: uppercase;
  opacity: 0.8;
  display: flex;
  justify-content: center;
  gap: 32px;
}

.article-meta span {
  /* Initial opacity for GSAP */
  opacity: 0.8;
}

/* Content */
.article-content {
  max-width: 720px;
  margin: 0 auto;
  padding: 120px 48px;
}

.article-body p {
  font-size: 18px;
  margin-bottom: 32px;
  opacity: 0;
  transform: translateY(20px);
}

.pull-quote {
  margin: 80px -120px;
  padding: 48px 120px;
  border-left: 4px solid var(--color-gold);
  font-size: 32px;
  font-weight: 300;
  line-height: 1.4;
  color: var(--color-midnight);
  font-style: italic;
  opacity: 0;
  transform: translateX(-30px);
}

.pull-quote cite {
  display: block;
  margin-top: 24px;
  font-size: 14px;
  font-style: normal;
  text-transform: uppercase;
  letter-spacing: 2px;
  color: var(--color-sage);
  font-weight: 600;
}

.inline-image {
  margin: 64px -48px;
  opacity: 0;
  transform: translateY(40px);
}

.inline-image img {
  width: 100%;
  height: auto;
  display: block;
}

.inline-image-caption {
  font-size: 13px;
  color: var(--color-sage);
  margin-top: 16px;
  text-align: center;
  letter-spacing: 1px;
}

/* Gallery Section */
.article-gallery-section {
  margin-top: 120px;
  padding-top: 80px;
  border-top: 1px solid rgba(15, 26, 15, 0.05);
}

.section-header-minimal {
  margin-bottom: 48px;
  text-align: center;
}

.label-gold {
  display: block;
  font-size: 11px;
  text-transform: uppercase;
  letter-spacing: 3px;
  color: var(--color-gold);
  margin-bottom: 12px;
  font-weight: 700;
}

.title-minimal {
  font-size: 32px;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: -1px;
}

.gallery-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 32px;
}

.gallery-item-luxury {
  aspect-ratio: 4/5;
  overflow: hidden;
  opacity: 0;
  transform: translateY(30px);
}

.gallery-item-luxury img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.8s cubic-bezier(0.16, 1, 0.3, 1);
}

.gallery-item-luxury:hover img {
  transform: scale(1.05);
}

@media (max-width: 768px) {
  .gallery-grid {
    grid-template-columns: 1fr;
  }
}

/* Share */
.share-section {
  margin-top: 80px;
  padding-top: 48px;
  border-top: 1px solid rgba(15, 26, 15, 0.1);
  display: flex;
  justify-content: space-between;
  align-items: center;
  opacity: 0;
}

.share-label {
  font-size: 12px;
  letter-spacing: 2px;
  text-transform: uppercase;
  color: var(--color-sage);
}

/* Premium Lightbox */
.premium-lightbox {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  z-index: 2000;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: default;
}

.lightbox-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(15, 26, 15, 0.95);
  backdrop-filter: blur(10px);
}

.lightbox-content {
  position: relative;
  z-index: 2001;
  width: 90%;
  height: 85%;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.6s cubic-bezier(0.16, 1, 0.3, 1);
}

.lightbox-content.zoomed {
  width: 100%;
  height: 100%;
}

.lightbox-main-image {
  max-width: 100%;
  max-height: 100%;
  object-fit: contain;
  cursor: zoom-in;
  box-shadow: 0 30px 60px rgba(0, 0, 0, 0.5);
  transition: transform 0.6s cubic-bezier(0.16, 1, 0.3, 1);
}

.zoomed .lightbox-main-image {
  transform: scale(1.5);
  cursor: zoom-out;
}

.lightbox-controls {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  pointer-events: none;
  z-index: 2002;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  padding: 40px;
}

.lightbox-controls button {
  pointer-events: auto;
}

.control-btn {
  background: none;
  border: none;
  color: white;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 12px;
  font-family: 'Inter', sans-serif;
  font-size: 11px;
  text-transform: uppercase;
  letter-spacing: 2px;
  font-weight: 700;
  opacity: 0.6;
  transition: all 0.3s;
}

.control-btn:hover {
  opacity: 1;
  color: var(--color-gold);
}

.control-btn .icon {
  font-size: 24px;
  line-height: 1;
}

.nav-controls {
  position: absolute;
  top: 50%;
  left: 0;
  width: 100%;
  transform: translateY(-50%);
  display: flex;
  justify-content: space-between;
  padding: 0 40px;
  align-items: center;
  pointer-events: none;
}

.nav-controls button {
  pointer-events: auto;
  font-size: 32px;
}

.image-counter {
  color: rgba(255, 255, 255, 0.4);
  font-size: 12px;
  letter-spacing: 4px;
  font-weight: 300;
  pointer-events: auto;
}

.lightbox-controls .close {
  align-self: flex-end;
}

.lightbox-controls .zoom {
  align-self: flex-end;
}

/* Transitions */
.lightbox-enter-active,
.lightbox-leave-active {
  transition: opacity 0.5s ease;
}

.lightbox-enter-from,
.lightbox-leave-to {
  opacity: 0;
}

.success-msg-luxury {
  background: rgba(184, 158, 101, 0.1);
  color: var(--color-gold);
  padding: 16px 24px;
  margin-bottom: 32px;
  font-size: 13px;
  letter-spacing: 1px;
  border-left: 2px solid var(--color-gold);
}

.form-row-luxury {
  margin-bottom: 24px;
}

.comment-form-luxury input[type="text"] {
  width: 100%;
  background: transparent;
  border: none;
  border-bottom: 1px solid rgba(15, 26, 15, 0.1);
  padding: 12px 0;
  font-family: 'Inter', sans-serif;
  font-size: 15px;
  color: var(--color-midnight);
  transition: border-color 0.3s;
}

.comment-form-luxury input[type="text"]:focus {
  outline: none;
  border-bottom-color: var(--color-gold);
}

.comment-form-luxury textarea {
  width: 100%;
  height: 120px;
  background: transparent;
  border: none;
  border-bottom: 1px solid rgba(15, 26, 15, 0.1);
  padding: 12px 0;
  font-family: 'Inter', sans-serif;
  font-size: 15px;
  color: var(--color-midnight);
  resize: none;
  margin-bottom: 32px;
  transition: border-color 0.3s;
}

.comment-form-luxury textarea:focus {
  outline: none;
  border-bottom-color: var(--color-gold);
}

.share-links {
  display: flex;
  gap: 24px;
}

.share-links a {
  color: var(--color-midnight);
  text-decoration: none;
  font-size: 13px;
  letter-spacing: 1px;
  text-transform: uppercase;
  transition: color 0.3s ease;
}

.share-links a:hover {
  color: var(--color-gold);
}

/* Author */
.author-box {
  margin-top: 80px;
  padding: 48px;
  background-color: rgba(15, 26, 15, 0.03);
  display: grid;
  grid-template-columns: 100px 1fr;
  gap: 32px;
  align-items: start;
  opacity: 0;
  transform: translateY(30px);
}

.author-avatar {
  width: 100px;
  height: 100px;
  border-radius: 50%;
  overflow: hidden;
}

.author-avatar img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.author-info h4 {
  font-size: 18px;
  font-weight: 700;
  margin-bottom: 8px;
  text-transform: uppercase;
  letter-spacing: 1px;
}

.author-info p {
  font-size: 16px;
  color: var(--color-sage);
  line-height: 1.6;
}

/* Comments (Luxury Theme) */
.comments-section {
  margin-top: 120px;
}

.comments-header {
  display: flex;
  justify-content: space-between;
  align-items: baseline;
  margin-bottom: 48px;
  border-bottom: 1px solid rgba(15, 26, 15, 0.1);
  padding-bottom: 16px;
}

.comments-header h3 {
  font-size: 24px;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: -1px;
}

.comments-count {
  font-size: 13px;
  color: var(--color-sage);
  text-transform: uppercase;
  letter-spacing: 1px;
}

.comment-card {
  margin-bottom: 40px;
  opacity: 0;
  transform: translateY(20px);
}

.comment-meta {
  display: flex;
  justify-content: space-between;
  margin-bottom: 12px;
  font-size: 14px;
}

.comment-meta strong {
  text-transform: uppercase;
  letter-spacing: 1px;
  color: var(--color-midnight);
}

.comment-meta span {
  color: var(--color-sage);
}

.comment-card p {
  font-size: 16px;
  color: var(--color-midnight);
}

.auth-box-comments {
  text-align: center;
  padding: 48px;
  background-color: var(--color-midnight);
  color: var(--color-cream);
  margin-top: 64px;
}

.auth-buttons {
  display: flex;
  gap: 24px;
  justify-content: center;
  margin-top: 24px;
}

.link-btn {
  color: var(--color-cream);
  text-decoration: none;
  text-transform: uppercase;
  letter-spacing: 2px;
  font-size: 13px;
  padding: 8px 16px;
  border: 1px solid rgba(250, 249, 246, 0.3);
  transition: all 0.3s ease;
}

.link-btn:hover {
  background-color: var(--color-cream);
  color: var(--color-midnight);
}

.link-btn.primary {
  background-color: var(--color-gold);
  border-color: var(--color-gold);
  color: var(--color-midnight);
}

.comment-form-luxury textarea {
  width: 100%;
  min-height: 120px;
  background: transparent;
  border: 1px solid rgba(15, 26, 15, 0.2);
  padding: 16px;
  font-family: inherit;
  font-size: 16px;
  margin-bottom: 16px;
  outline: none;
  transition: border-color 0.3s;
}

.comment-form-luxury textarea:focus {
  border-color: var(--color-midnight);
}

.comment-form-luxury button {
  background: #0F1A0F;
  color: #FAF9F6;
  border: none;
  padding: 12px 32px;
  text-transform: uppercase;
  letter-spacing: 2px;
  font-size: 13px;
  cursor: pointer;
  transition: background 0.3s;
}

.comment-form-luxury button:hover {
  background: var(--color-gold);
}

/* Related */
.related {
  padding: 120px 48px;
  background-color: var(--color-midnight);
  color: var(--color-white);
}

.related-header {
  max-width: 1400px;
  margin: 0 auto 64px;
}

.related-title {
  font-size: 36px;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: -1px;
}

.related-grid {
  max-width: 1400px;
  margin: 0 auto;
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 32px;
}

.related-card {
  cursor: pointer;
  opacity: 0;
  transform: translateY(40px);
}

.related-image {
  width: 100%;
  height: 280px;
  overflow: hidden;
  margin-bottom: 20px;
}

.related-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.6s ease;
}

.related-card:hover .related-image img {
  transform: scale(1.05);
}

.related-card-title {
  font-size: 20px;
  font-weight: 600;
  margin-bottom: 8px;
  transition: color 0.3s ease;
}

.related-card:hover .related-card-title {
  color: var(--color-gold);
}

.related-card-meta {
  font-size: 13px;
  color: var(--color-sage);
  text-transform: uppercase;
  letter-spacing: 1px;
}

/* Footer */
footer {
  padding: 48px;
  text-align: center;
  background-color: var(--color-midnight);
  color: var(--color-sage);
  border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.footer-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
  max-width: 1400px;
  margin: 0 auto;
  font-size: 13px;
  letter-spacing: 1px;
}

.footer-links {
  display: flex;
  gap: 32px;
}

.footer-links a {
  color: var(--color-sage);
  text-decoration: none;
  text-transform: uppercase;
  transition: color 0.3s ease;
}

.footer-links a:hover {
  color: var(--color-gold);
}

.state-loading,
.state-error {
  height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 24px;
  text-transform: uppercase;
  letter-spacing: 2px;
}

/* Responsive */
@media (max-width: 968px) {
  .pull-quote {
    margin: 60px 0;
    padding: 32px 48px;
    font-size: 24px;
  }

  .inline-image {
    margin: 48px 0;
  }

  .related-grid {
    grid-template-columns: 1fr;
  }

  .author-box {
    grid-template-columns: 1fr;
    text-align: center;
  }

  .author-avatar {
    margin: 0 auto;
  }
}

@media (max-width: 640px) {
  .article-hero-content {
    padding: 40px 24px;
  }

  .article-title {
    font-size: 36px;
  }

  .article-content {
    padding: 80px 24px;
  }

  .related {
    padding: 80px 24px;
  }

  .footer-content {
    flex-direction: column;
    gap: 24px;
  }
}
</style>
