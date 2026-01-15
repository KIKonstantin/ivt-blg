<script setup>
  const { data: posts, pending, error, refresh } = await useFetch('/api/posts');
  const title = ref('');
  const content = ref('');

  const submit = async () => {
    await $fetch('/api/posts', {
      method: 'POST',
      body: {
        title: title.value,
        content: content.value
      }
    });
    title.value = '';
    content.value = '';
  };
</script>
<template>
  <div>
    <form @submit.prevent="submit">
      <input type="text" name="title" placeholder="Title" v-model="title">
      <textarea name="content" id="content" placeholder="Post Content" v-model="content"></textarea>
      <button>Add post</button>
    </form>
    <h1>Всички статии</h1>
    <p v-if="pending">Зареждаме...</p>
    <p v-if="error">Грешка при зареждане ...</p>
    <div v-if="posts && posts.length === 0">
      Няма публикувани статии
    </div>
    <article
    v-for="post in posts"
    :key="post.id"
    >
    <h2>{{ post.title }}</h2>
    <p>{{ post.content }}</p>
    </article>
  </div>
</template>
