// composables/useModals.js
import { ref } from "vue";

/**
 * Composable for managing modal states
 * @returns {Object} Modal state and control functions
 */
export function useModals() {
    // Modal state
    const showInviteModal = ref(false);
    const showPendingRequests = ref(false);

    // Invite Modal controls
    const openInviteModal = () => {
        showInviteModal.value = true;
    };

    const closeInviteModal = () => {
        showInviteModal.value = false;
    };

    // Pending Requests controls
    const togglePendingRequests = () => {
        showPendingRequests.value = !showPendingRequests.value;
    };

    const showPendingRequestsModal = () => {
        showPendingRequests.value = true;
    };

    const hidePendingRequestsModal = () => {
        showPendingRequests.value = false;
    };

    // Close all modals (useful for cleanup)
    const closeAllModals = () => {
        showInviteModal.value = false;
        showPendingRequests.value = false;
    };

    return {
        // State
        showInviteModal,
        showPendingRequests,

        // Invite Modal methods
        openInviteModal,
        closeInviteModal,

        // Pending Requests methods
        togglePendingRequests,
        showPendingRequestsModal,
        hidePendingRequestsModal,

        // Utility methods
        closeAllModals,
    };
}
