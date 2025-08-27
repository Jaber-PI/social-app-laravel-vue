<script setup>

import MainLayout from '@/Layouts/MainLayout.vue';
import PostItem from '@/Components/app/PostItem.vue';
import AttachmentPreviewModal from '@/Components/app/AttachmentPreviewModal.vue';
import { useAttachmentsPreviewModal } from '@/composables/useAttachmentsPreviewModal';

const props = defineProps({
    post: Object
})

const { attachmentIndex,attachments, showPreviewModal ,openPreviewModal } = useAttachmentsPreviewModal();


</script>

<template>
    <MainLayout>
        <PostItem :post="post" @previewAttachment="openPreviewModal"/>

        <AttachmentPreviewModal v-if="showPreviewModal" :attachmentIndex="attachmentIndex" :attachments="attachments"
        @closed="showPreviewModal = false" />

    </MainLayout>
</template>
