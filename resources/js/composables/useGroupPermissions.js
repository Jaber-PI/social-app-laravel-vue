import { computed } from "vue";

/**
 * Composable for managing group permissions
 * @param {Ref<Object>} group - Reactive reference to group object
 * @returns {Object} Permission computed properties
 */
export function useGroupPermissions(group) {
    const isAdmin = computed(() => {
        return group.value?.current_user?.isAdmin ?? false;
    });

    const isOwner = computed(() => {
        return group.value?.current_user?.isOwner ?? false;
    });

    const isMember = computed(() => {
        return !!group.value?.current_user;
    });

    const canInvite = computed(() => {
        return group.value?.can?.invite ?? false;
    });

    const canView = computed(() => {
        return group.value?.can?.view ?? false;
    });

    const canPost = computed(() => {
        return group.value?.can?.post ?? false;
    });

    const isPublicGroup = computed(() => {
        return group.value?.is_public ?? false;
    });

    // Derived permissions
    const canManageMembers = computed(() => {
        return isAdmin.value || isOwner.value;
    });

    const canSeeContent = computed(() => {
        return isMember.value || isPublicGroup.value;
    });

    const canSeePendingRequests = computed(() => {
        return isAdmin.value && group.value?.pending_requests?.length > 0;
    });

    return {
        // Basic permissions
        isAdmin,
        isOwner,
        isMember,
        isPublicGroup,

        // Action permissions
        canInvite,
        canView,
        canPost,

        // Derived permissions
        canManageMembers,
        canSeeContent,
        canSeePendingRequests,
    };
}
