<script setup>
import { onMounted, computed } from 'vue'

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

const featuredPost = computed(() => posts.value?.[0])
const recentPosts = computed(() => posts.value?.slice(1))

useHead({
  title: "NAT'RE — Forest Travel Blog",
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

      // Hero entrance animation
      const heroTl = gsap.timeline();

      heroTl.from(".hero-title span", {
        y: 100,
        opacity: 0,
        duration: 1.2,
        stagger: 0.1,
        ease: "power4.out",
        delay: 0.3
      })
        .to(".hero-subtitle", {
          opacity: 0.9,
          duration: 1,
          ease: "power2.out"
        }, "-=0.5")
        .to(".scroll-indicator", {
          opacity: 0.7,
          duration: 1,
          ease: "power2.out"
        }, "-=0.5");

      // Hero parallax
      gsap.to(".hero-image", {
        yPercent: 30,
        ease: "none",
        scrollTrigger: {
          trigger: ".hero",
          start: "top top",
          end: "bottom top",
          scrub: true
        }
      });

      // Hero fade out on scroll
      gsap.to(".hero-content", {
        opacity: 0,
        y: -50,
        ease: "none",
        scrollTrigger: {
          trigger: ".hero",
          start: "top top",
          end: "50% top",
          scrub: true
        }
      });

      // Featured section animations
      const featuredTl = gsap.timeline({
        scrollTrigger: {
          trigger: ".featured",
          start: "top 80%",
          end: "top 30%",
          toggleActions: "play none none reverse"
        }
      });

      featuredTl.to("#featuredImage", {
        clipPath: "inset(0 0% 0 0)",
        duration: 1.2,
        ease: "power3.inOut"
      })
        .to("#featuredContent", {
          opacity: 1,
          x: 0,
          duration: 1,
          ease: "power3.out"
        }, "-=0.8")
        .to(".section-label span", {
          y: 0,
          duration: 0.8,
          ease: "power3.out"
        }, "-=0.6")
        .to(".featured-title span", {
          y: 0,
          duration: 0.8,
          stagger: 0.1,
          ease: "power3.out"
        }, "-=0.6")
        .to("#featuredExcerpt", {
          opacity: 1,
          y: 0,
          duration: 0.8,
          ease: "power2.out"
        }, "-=0.4")
        .to("#featuredLink", {
          opacity: 1,
          duration: 0.8,
          ease: "power2.out"
        }, "-=0.6");

      // Stories section animations
      gsap.to(".stories-title", {
        y: 0,
        duration: 0.8,
        ease: "power3.out",
        scrollTrigger: {
          trigger: ".stories-header",
          start: "top 85%"
        }
      });

      gsap.to("#viewAll", {
        opacity: 1,
        duration: 0.8,
        delay: 0.2,
        ease: "power2.out",
        scrollTrigger: {
          trigger: ".stories-header",
          start: "top 85%"
        }
      });

      // Story cards staggered reveal
      gsap.utils.toArray(".story-card").forEach((card, i) => {
        gsap.to(card, {
          opacity: 1,
          y: 0,
          duration: 0.8,
          delay: i * 0.15,
          ease: "power3.out",
          scrollTrigger: {
            trigger: card,
            start: "top 85%"
          }
        });
      });

      // Newsletter animations
      const newsletterTl = gsap.timeline({
        scrollTrigger: {
          trigger: "#newsletter",
          start: "top 80%"
        }
      });

      newsletterTl.to("#newsletter", {
        opacity: 1,
        y: 0,
        duration: 1,
        ease: "power3.out"
      })
        .to(".newsletter-title span", {
          y: 0,
          duration: 0.8,
          ease: "power3.out"
        }, "-=0.6")
        .to("#newsletterText", {
          opacity: 1,
          duration: 0.8,
          ease: "power2.out"
        }, "-=0.4")
        .to("#newsletterForm", {
          opacity: 1,
          y: 0,
          duration: 0.8,
          ease: "power2.out"
        }, "-=0.4");

      // Footer fade in
      gsap.to("#footer", {
        opacity: 1,
        duration: 1,
        scrollTrigger: {
          trigger: "#footer",
          start: "top 90%"
        }
      });
    } else {
      setTimeout(initGSAP, 100);
    }
  };

  initGSAP();
});

const scrollToFeatured = () => {
  document.getElementById('featured')?.scrollIntoView({ behavior: 'smooth' });
}

const handleNewsletterSubmit = () => {
  alert('Welcome to the forest!');
}
</script>

<template>
  <div class="blog-landing">
    <!-- Hero Section -->
    <section class="hero">
      <div class="hero-image" id="heroImage"></div>
      <div class="hero-overlay"></div>
      <div class="hero-content">
        <h1 class="hero-title">
          <span>N</span><span>a</span><span>t</span><span>'</span><span>r</span><span>e</span>
        </h1>
        <p class="hero-subtitle">Forest Travel Journal</p>
      </div>
      <div class="scroll-indicator" @click="scrollToFeatured">
        Scroll
      </div>
    </section>

    <!-- Featured Story -->
    <section class="featured" id="featured">
      <div class="featured-image" id="featuredImage">
        <img src="https://images.unsplash.com/photo-1511497584788-876760111969?w=800&q=80" alt="Misty forest trail">
      </div>
      <div v-if="featuredPost" class="featured-content" id="featuredContent">
        <div class="section-label"><span>Featured Story</span></div>
        <h2 class="featured-title">
          <span>{{ featuredPost.title.split(' ').slice(0, 3).join(' ') }}</span><br>
          <span>{{ featuredPost.title.split(' ').slice(3, 6).join(' ') }}</span><br>
          <span>{{ featuredPost.title.split(' ').slice(6).join(' ') }}</span>
        </h2>
        <p class="featured-excerpt" id="featuredExcerpt">
          {{ featuredPost.content.substring(0, 180) }}...
        </p>
        <NuxtLink :to="`/posts/${featuredPost.id}`" class="read-more" id="featuredLink">Read Article</NuxtLink>
      </div>
      <div v-else class="featured-content" id="featuredContent">
        <div class="section-label"><span>Featured Story</span></div>
        <h2 class="featured-title"><span>The Silent Trails</span><br><span>of the
            Pacific</span><br><span>Northwest</span></h2>
        <p class="featured-excerpt" id="featuredExcerpt">
          A week spent wandering through old-growth forests where sunlight filters through ancient canopies...
        </p>
        <a href="#" class="read-more" id="featuredLink">Read Article</a>
      </div>
    </section>

    <!-- Recent Stories -->
    <section class="stories" id="stories">
      <div class="stories-header">
        <h3 class="stories-title">Recent Stories</h3>
        <NuxtLink to="/posts" class="view-all" id="viewAll">View All →</NuxtLink>
      </div>

      <div v-if="pending" class="state">Зареждаме...</div>
      <div v-else-if="error" class="state">Грешка при зареждане.</div>
      <div v-else-if="recentPosts && recentPosts.length" class="stories-grid">
        <article v-for="post in recentPosts" :key="post.id" class="story-card" @click="navigateTo(`/posts/${post.id}`)">
          <div class="story-image">
            <img :src="`https://picsum.photos/seed/${post.id}/800/600`" :alt="post.title">
          </div>
          <div class="story-category">Forest Travel</div>
          <h4 class="story-title">{{ post.title }}</h4>
          <div class="story-meta">{{ formatDate(post.created_at) }} · 8 min read</div>
        </article>
      </div>
      <div v-else class="stories-grid">
        <!-- Fallback items if no posts -->
        <article class="story-card">
          <div class="story-image">
            <img src="https://images.unsplash.com/photo-1478131143081-80f7f84ca84d?w=800&q=80" alt="Camping">
          </div>
          <div class="story-category">Camping</div>
          <h4 class="story-title">Wild Camping: A Guide to Responsible Nights in the Woods</h4>
          <div class="story-meta">Dec 12 · 8 min read</div>
        </article>
        <article class="story-card">
          <div class="story-image">
            <img src="https://images.unsplash.com/photo-1507041957456-9c397ce39c97?w=800&q=80" alt="Forest path">
          </div>
          <div class="story-category">Foraging</div>
          <h4 class="story-title">The Hidden Harvest: Finding Wild Mushrooms in Autumn</h4>
          <div class="story-meta">Nov 28 · 6 min read</div>
        </article>
      </div>
    </section>

    <!-- Newsletter -->
    <section class="newsletter" id="newsletter">
      <h3 class="newsletter-title"><span>Join the Journey</span></h3>
      <p class="newsletter-text" id="newsletterText">Weekly stories from the forest, delivered to your inbox.</p>
      <form class="newsletter-form" id="newsletterForm" @submit.prevent="handleNewsletterSubmit">
        <input type="email" class="newsletter-input" placeholder="Your email address" required>
        <button type="submit" class="newsletter-button">→</button>
      </form>
    </section>

    <!-- Footer -->
    <footer id="footer">
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
</template>

<style scoped>
.blog-landing {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: 'Inter', sans-serif;
  background-color: #FAF9F6;
  color: #0F1A0F;
  line-height: 1.6;
  overflow-x: hidden;
}

:root {
  --color-midnight: #0F1A0F;
  --color-sage: #7D8B7A;
  --color-cream: #FAF9F6;
  --color-gold: #D4A574;
  --color-white: #FFFFFF;
}

/* Hero Section */
.hero {
  height: 100vh;
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
  background-color: #0F1A0F;
}

.hero-image {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 120%;
  background-image: url('https://images.unsplash.com/photo-1448375240586-882707db888b?w=1920&q=80');
  background-size: cover;
  background-position: center;
  will-change: transform;
}

.hero-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(to bottom,
      rgba(15, 26, 15, 0.2) 0%,
      rgba(15, 26, 15, 0.4) 50%,
      rgba(15, 26, 15, 0.6) 100%);
}

.hero-content {
  position: relative;
  z-index: 2;
  text-align: center;
  color: #FFFFFF;
}

.hero-title {
  font-size: clamp(64px, 15vw, 180px);
  font-weight: 900;
  letter-spacing: -4px;
  line-height: 0.9;
  margin-bottom: 24px;
  text-transform: uppercase;
  overflow: hidden;
}

.hero-title span {
  display: inline-block;
}

.hero-subtitle {
  font-size: 14px;
  letter-spacing: 4px;
  text-transform: uppercase;
  opacity: 0;
  font-weight: 300;
}

.scroll-indicator {
  position: absolute;
  bottom: 48px;
  left: 50%;
  transform: translateX(-50%);
  color: #FFFFFF;
  font-size: 12px;
  letter-spacing: 2px;
  text-transform: uppercase;
  opacity: 0;
  cursor: pointer;
  z-index: 5;
}

/* Featured Section */
.featured {
  padding: 120px 48px;
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 80px;
  align-items: center;
  max-width: 1400px;
  margin: 0 auto;
  overflow: hidden;
}

.featured-image {
  position: relative;
  height: 600px;
  overflow: hidden;
  clip-path: inset(0 100% 0 0);
}

.featured-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.6s ease;
}

.featured-image:hover img {
  transform: scale(1.03);
}

.featured-content {
  padding: 40px 0;
  opacity: 0;
  transform: translateX(50px);
}

.section-label {
  font-size: 12px;
  letter-spacing: 3px;
  text-transform: uppercase;
  color: #7D8B7A;
  margin-bottom: 24px;
  font-weight: 600;
  overflow: hidden;
}

.section-label span {
  display: inline-block;
  transform: translateY(100%);
}

.featured-title {
  font-size: 48px;
  font-weight: 800;
  line-height: 1.1;
  margin-bottom: 24px;
  text-transform: uppercase;
  letter-spacing: -1px;
  overflow: hidden;
}

.featured-title span {
  display: inline-block;
  transform: translateY(100%);
}

.featured-excerpt {
  font-size: 18px;
  color: #7D8B7A;
  line-height: 1.8;
  margin-bottom: 32px;
  max-width: 90%;
  opacity: 0;
  transform: translateY(20px);
}

.read-more {
  display: inline-flex;
  align-items: center;
  gap: 12px;
  color: #0F1A0F;
  text-decoration: none;
  font-size: 13px;
  letter-spacing: 2px;
  text-transform: uppercase;
  font-weight: 600;
  position: relative;
  opacity: 0;
}

.read-more::after {
  content: '→';
  transition: transform 0.3s ease;
  margin-left: 8px;
}

.read-more:hover::after {
  transform: translateX(4px);
}

/* Recent Stories Grid */
.stories {
  padding: 80px 48px;
  max-width: 1400px;
  margin: 0 auto;
}

.stories-header {
  display: flex;
  justify-content: space-between;
  align-items: baseline;
  margin-bottom: 64px;
  overflow: hidden;
}

.stories-title {
  font-size: 36px;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: -1px;
  transform: translateY(100%);
}

.view-all {
  color: #7D8B7A;
  text-decoration: none;
  font-size: 13px;
  letter-spacing: 2px;
  text-transform: uppercase;
  transition: color 0.3s ease;
  opacity: 0;
}

.view-all:hover {
  color: #D4A574;
}

.stories-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 48px;
}

.story-card {
  cursor: pointer;
  opacity: 0;
  transform: translateY(60px);
}

.story-image {
  width: 100%;
  height: 400px;
  overflow: hidden;
  margin-bottom: 24px;
  position: relative;
}

.story-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: all 0.6s ease;
}

.story-card:hover .story-image img {
  transform: scale(1.02);
  filter: saturate(0.9);
}

.story-category {
  font-size: 11px;
  letter-spacing: 3px;
  text-transform: uppercase;
  color: var(--color-sage);
  margin-bottom: 12px;
  font-weight: 600;
}

.story-title {
  font-size: 24px;
  font-weight: 700;
  margin-bottom: 12px;
  line-height: 1.3;
  transition: color 0.3s ease;
}

.story-card:hover .story-title {
  color: #D4A574;
}

.story-meta {
  font-size: 14px;
  color: var(--color-sage);
  font-weight: 400;
}

/* Newsletter */
.newsletter {
  padding: 160px 48px;
  text-align: center;
  max-width: 600px;
  margin: 0 auto;
  opacity: 0;
  transform: translateY(40px);
}

.newsletter-title {
  font-size: 36px;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: -1px;
  margin-bottom: 16px;
  overflow: hidden;
}

.newsletter-title span {
  display: inline-block;
  transform: translateY(100%);
}

.newsletter-text {
  color: var(--color-sage);
  margin-bottom: 48px;
  font-size: 18px;
  opacity: 0;
}

.newsletter-form {
  display: flex;
  gap: 16px;
  justify-content: center;
  border-bottom: 2px solid var(--color-midnight);
  padding-bottom: 16px;
  opacity: 0;
  transform: translateY(20px);
}

.newsletter-input {
  flex: 1;
  border: none;
  background: transparent;
  font-size: 18px;
  font-family: inherit;
  color: var(--color-midnight);
  outline: none;
}

.newsletter-input::placeholder {
  color: #7D8B7A;
}

.newsletter-button {
  background: none;
  border: none;
  color: var(--color-midnight);
  font-size: 24px;
  cursor: pointer;
  transition: transform 0.3s ease;
  padding: 0 8px;
}

.newsletter-button:hover {
  transform: translateX(4px);
  color: var(--color-gold);
}

/* Footer */
footer {
  padding: 48px;
  text-align: center;
  border-top: 1px solid rgba(15, 26, 15, 0.1);
  opacity: 0;
}

.footer-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
  max-width: 1400px;
  margin: 0 auto;
  font-size: 13px;
  letter-spacing: 1px;
  color: var(--color-sage);
}

.footer-links {
  display: flex;
  gap: 32px;
}

.footer-links a {
  color: #7D8B7A;
  text-decoration: none;
  text-transform: uppercase;
  transition: color 0.3s ease;
}

.footer-links a:hover {
  color: #D4A574;
}

.state {
  text-align: center;
  padding: 40px;
  color: var(--color-sage);
  font-size: 18px;
}

/* Responsive */
@media (max-width: 968px) {
  .featured {
    grid-template-columns: 1fr;
    gap: 48px;
  }

  .featured-image {
    height: 400px;
    clip-path: inset(0 0 100% 0);
  }

  .stories-grid {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 640px) {
  .hero-title {
    font-size: 64px;
    letter-spacing: -2px;
  }

  .featured {
    padding: 80px 24px;
  }

  .featured-title {
    font-size: 36px;
  }

  .stories {
    padding: 60px 24px;
  }

  .newsletter {
    padding: 100px 24px;
  }

  .footer-content {
    flex-direction: column;
    gap: 24px;
  }
}
</style>
