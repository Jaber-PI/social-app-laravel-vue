<script setup>
import PostItem from './PostItem.vue';
import NewPost from './NewPost.vue';

defineProps({
    posts: Array,
})

</script>

<template>
    <div class="">
        <div class="mb-3">
            <NewPost />
        </div>

        <div class="posts flex flex-col gap-2 px-1">
            <PostItem v-for="post in posts" :key="post.id" :post="post" />
        </div>
    </div>
</template>
