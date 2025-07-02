<script setup>
import { ref } from 'vue';
import PostItem from './PostItem.vue';
import EditorModal from './EditorModal.vue';
import AttachmentPreviewModal from './AttachmentPreviewModal.vue';

defineProps({
    posts: Array,
})

const editPost = ref({});
const showEditModal = ref(false);

const showPreviewModal = ref(false);

const attachments = ref([]);
const attachmentIndex = ref(0)

const openPreviewModal = (postAttachments, index) => {
    showPreviewModal.value = true;
    attachments.value = postAttachments;
    attachmentIndex.value = index;
    return;
}

const openEditModal = (post) => {
    editPost.value = post;
    showEditModal.value = true;
    return;
}

</script>

<template>

    <EditorModal v-if="openEditModal" :post="editPost" v-model="showEditModal" />
    <AttachmentPreviewModal v-model="showPreviewModal" :attachmentIndex="attachmentIndex"
        :attachments="attachments" />

    <div class="posts flex flex-col gap-2 px-1">
        <PostItem v-for="post in posts" @previewAttachment="openPreviewModal" @editClick="showEditModal" :key="post.id" :post="post" />
    </div>
</template>
