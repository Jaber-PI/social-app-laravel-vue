<script setup>
import { ref } from 'vue';
import PostItem from './PostItem.vue';
import EditorModal from './EditorModal.vue';

defineProps({
    posts: Array,
})

const editPost = ref({});
const isShowEditModal = ref(false);

const showEditModal = (post) => {
    editPost.value = post;
    isShowEditModal.value = true;
    return;
}

</script>

<template>

    <EditorModal v-if="isShowEditModal" :post="editPost" v-model="isShowEditModal" />
    <div class="posts flex flex-col gap-2 px-1">
        <PostItem v-for="post in posts" @editClick="showEditModal" :key="post.id" :post="post" />
    </div>
</template>
