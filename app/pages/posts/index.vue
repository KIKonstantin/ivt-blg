<script setup>
import { onMounted } from 'vue'
import gsap from 'gsap'

const { data: posts, pending, error } = await useFetch('/api/posts')

const formatDate = (value) => {
    if (!value) return ''
    return new Date(value).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric'
    })
}

onMounted(() => {
    gsap.from(".list-header", {
        opacity: 0,
        y: 30,
        duration: 1,
        ease: "power3.out"
    })

    gsap.from(".post-list-item", {
        opacity: 0,
        y: 40,
        duration: 0.8,
        stagger: 0.1,
        ease: "power3.out",
        delay: 0.2
    })
})
</script>

<template>
    <div class="posts-archive">
        <div class="container-premium">
            <header class="list-header">
                <span class="label-premium">The Archive</span>
                <h1 class="title-premium">All Stories</h1>
                <p class="subtitle-premium">A collection of our journeys through the wilderness.</p>
            </header>

            <div v-if="pending" class="state-loading">Gathering stories...</div>
            <div v-else-if="error" class="state-error">The trail is blocked. (Error loading posts)</div>

            <div v-else class="post-grid-luxury">
                <article v-for="post in posts" :key="post.id" class="post-list-item"
                    @click="navigateTo(`/posts/${post.id}`)">
                    <div class="post-image-wrapper">
                        <img :src="`https://picsum.photos/seed/${post.id}/800/600`" :alt="post.title">
                        <div class="post-overlay-luxury">
                            <span>Read Story</span>
                        </div>
                    </div>
                    <div class="post-info-luxury">
                        <span class="post-cat">Nature Journal</span>
                        <h3 class="post-title">{{ post.title }}</h3>
                        <div class="post-footer-luxury">
                            <span>{{ formatDate(post.created_at) }}</span>
                            <span class="dot"></span>
                            <span>8 min read</span>
                        </div>
                    </div>
                </article>
            </div>

            <div v-if="!pending && posts && posts.length === 0" class="state-empty">
                The journal is currently empty.
            </div>
        </div>
    </div>
</template>

<style scoped>
.posts-archive {
    padding-top: 160px;
    padding-bottom: 120px;
    min-height: 100vh;
}

.list-header {
    margin-bottom: 80px;
    text-align: center;
}

.label-premium {
    display: block;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 4px;
    color: #D4A574;
    font-weight: 700;
    margin-bottom: 16px;
}

.title-premium {
    font-size: 64px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: -2px;
    margin: 0;
    line-height: 1;
}

.subtitle-premium {
    color: #7D8B7A;
    margin-top: 16px;
    font-size: 18px;
}

.post-grid-luxury {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
    gap: 64px 48px;
}

.post-list-item {
    cursor: pointer;
}

.post-image-wrapper {
    position: relative;
    aspect-ratio: 4/3;
    overflow: hidden;
    margin-bottom: 24px;
}

.post-image-wrapper img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.8s cubic-bezier(0.16, 1, 0.3, 1);
}

.post-overlay-luxury {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(15, 26, 15, 0.4);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.4s ease;
}

.post-overlay-luxury span {
    color: #FFFFFF;
    text-transform: uppercase;
    letter-spacing: 2px;
    font-size: 13px;
    font-weight: 600;
    border-bottom: 1px solid #D4A574;
    padding-bottom: 4px;
}

.post-list-item:hover .post-image-wrapper img {
    transform: scale(1.05);
}

.post-list-item:hover .post-overlay-luxury {
    opacity: 1;
}

.post-cat {
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 2px;
    color: #D4A574;
    font-weight: 700;
    margin-bottom: 8px;
    display: block;
}

.post-title {
    font-size: 24px;
    font-weight: 800;
    margin: 0 0 16px;
    line-height: 1.25;
}

.post-footer-luxury {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 13px;
    color: #7D8B7A;
}

.dot {
    width: 4px;
    height: 4px;
    background-color: #D4A574;
    border-radius: 50%;
}

.state-loading,
.state-error,
.state-empty {
    padding: 100px;
    text-align: center;
    color: #7D8B7A;
    font-size: 18px;
    text-transform: uppercase;
    letter-spacing: 1px;
}

@media (max-width: 900px) {
    .post-grid-luxury {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 640px) {
    .title-premium {
        font-size: 40px;
    }

    .posts-archive {
        padding-top: 120px;
    }
}
</style>
