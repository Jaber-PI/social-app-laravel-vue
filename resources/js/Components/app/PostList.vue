<script setup>
import { ref } from 'vue';
import PostItem from './PostItem.vue';
import EditPostModal from './EditPostModal.vue';

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

    <EditPostModal v-if="isShowEditModal" :post="editPost" v-model="isShowEditModal" />
    <div class="posts flex flex-col gap-2 px-1">
        <PostItem v-for="post in posts" @editClick="showEditModal" :key="post.id" :post="post" />
    </div>
</template>
