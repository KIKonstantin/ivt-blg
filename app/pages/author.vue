<script setup>
const { data: author } = await useFetch('/api/author')

useHead({
    title: author.value?.name ? `${author.value.name} — КОРЕНИ` : "КОРЕНИ",
    link: [
        { rel: 'preconnect', href: 'https://fonts.googleapis.com' },
        { rel: 'preconnect', href: 'https://fonts.gstatic.com', crossorigin: '' },
        { rel: 'stylesheet', href: 'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800;900&display=swap' }
    ]
})
</script>

<template>
    <div class="author-page">
        <div class="container-premium">
            <header class="author-header">
                <div class="author-hero-image" v-if="author?.image_url">
                    <img :src="author.image_url" :alt="author.name">
                </div>
                <div class="author-hero-content">
                    <span class="label-premium">Автор</span>
                    <h1 class="title-premium">{{ author?.name || 'Ивета Костадинова' }}</h1>
                    <p class="subtitle-premium">{{ author?.description }}</p>
                </div>
            </header>

            <main class="author-main">
                <div class="author-body rich-content" v-html="author?.content"></div>
            </main>
        </div>
    </div>
</template>

<style scoped>
.author-page {
    padding-top: 160px;
    padding-bottom: 120px;
    min-height: 100vh;
    background-color: var(--color-cream);
}

.author-header {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    margin-bottom: 80px;
}

.author-hero-image {
    width: 200px;
    height: 200px;
    border-radius: 50%;
    overflow: hidden;
    margin-bottom: 32px;
    box-shadow: 0 20px 40px rgba(15, 26, 15, 0.1);
}

.author-hero-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
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
    margin-top: 24px;
    font-size: 20px;
    max-width: 600px;
}

.author-main {
    max-width: 720px;
    margin: 0 auto;
    padding: 0 24px;
}

.author-body {
    font-size: 18px;
    line-height: 1.8;
    color: var(--color-midnight);
}

:deep(.rich-content p) {
    margin-bottom: 32px;
}

@media (max-width: 640px) {
    .title-premium {
        font-size: 40px;
    }
}
</style>
