import { ref } from "vue";

/**
 * Composable for managing modal states
 * @returns {Object} Modal state and control functions
 */
export function useAttachmentsPreviewModal() {
    // Modal state
    const showPreviewModal = ref(false);
    const attachments = ref([]);
    const attachmentIndex = ref(0);

    const openPreviewModal = (postAttachments, index) => {
        showPreviewModal.value = true;
        attachments.value = postAttachments;
        attachmentIndex.value = index;
        return;
    };

    return {
        showPreviewModal,
        attachments,
        attachmentIndex,
        openPreviewModal
    };
}
