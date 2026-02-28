<script setup>
import { onMounted } from 'vue'
import gsap from 'gsap'

const currentSort = ref('newest')

const { data: posts, pending, error, refresh } = await useFetch('/api/posts', {
    query: { sort: currentSort }
})

const updateSort = (val) => {
    currentSort.value = val
    refresh()
}

const formatDate = (value) => {
    if (!value) return ''
    return new Date(value).toLocaleDateString('bg-BG', {
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
                <span class="label-premium">Архив</span>
                <h1 class="title-premium">Всички истории</h1>
                <div class="sorting-controls">
                    <div class="sort-dropdown">
                        <select v-model="currentSort" @change="updateSort(currentSort)">
                            <option value="newest">Най-нови</option>
                            <option value="oldest">Най-стари</option>
                            <option value="most_commented">Най-коментирани</option>
                            <option value="popular">Популярни</option>
                        </select>
                    </div>
                </div>
                <p class="subtitle-premium">Колекция от нашите пътувания из дивата природа.</p>
            </header>

            <div v-if="pending" class="state-loading">Събираме истории...</div>
            <div v-else-if="error" class="state-error">Пътеката е блокирана. (Грешка при зареждане)</div>

            <div v-else class="post-grid-luxury">
                <article v-for="post in posts" :key="post.id" class="post-list-item"
                    @click="navigateTo(`/posts/${post.id}`)">
                    <div class="post-image-wrapper">
                        <img :src="post.image_url || `https://picsum.photos/seed/${post.id}/800/600`" :alt="post.title">
                        <div class="post-overlay-luxury">
                            <span>Прочети историята</span>
                        </div>
                    </div>
                    <div class="post-info-luxury">
                        <span class="post-cat">Природа</span>
                        <h3 class="post-title">{{ post.title }}</h3>
                        <div class="post-footer-luxury">
                            <span>{{ formatDate(post.created_at) }}</span>
                            <span class="dot"></span>
                            <span>8 мин. четене</span>
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
    color: var(--color-gold);
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
    color: var(--color-sage);
    margin-top: 16px;
    font-size: 18px;
}

.sorting-controls {
    margin-top: 32px;
    display: flex;
    justify-content: center;
}

.sort-dropdown select {
    background: transparent;
    border: 1px solid rgba(15, 26, 15, 0.1);
    padding: 8px 16px;
    font-family: inherit;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 2px;
    font-weight: 700;
    color: var(--color-midnight);
    outline: none;
    cursor: pointer;
    transition: all 0.3s;
}

.sort-dropdown select:hover {
    border-color: var(--color-gold);
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
    color: var(--color-white);
    text-transform: uppercase;
    letter-spacing: 2px;
    font-size: 13px;
    font-weight: 600;
    border-bottom: 1px solid var(--color-gold);
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
    color: var(--color-gold);
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
    color: var(--color-sage);
}

.dot {
    width: 4px;
    height: 4px;
    background-color: var(--color-gold);
    border-radius: 50%;
}

.state-loading,
.state-error,
.state-empty {
    padding: 100px;
    text-align: center;
    color: var(--color-sage);
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
