<script setup>
import { computed, toRef } from 'vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import InviteMemberModal from '../Partials/InviteMemberModal.vue';
import MembersList from '../Partials/MembersList.vue';
import RequestsList from '../Partials/RequestsList.vue';
import { useGroupPermissions } from '@/composables/useGroupPermissions';
import { useModals } from '@/composables/useModals';

// Props
const props = defineProps({
    group: {
        type: Object,
        required: true
    },
    members: {
        type: Array,
        default: () => []
    }
});

const groupRef = toRef(props, 'group');

const {
    isAdmin,
    canInvite,
    canView,
    canSeePendingRequests
} = useGroupPermissions(groupRef);

const {
    showInviteModal,
    showPendingRequests,
    openInviteModal,
    togglePendingRequests
} = useModals();

// Local computed properties
const pendingRequestsCount = computed(() => {
    return props.group.pending_requests?.length || 0;
});

const hasPendingRequests = computed(() => {
    return pendingRequestsCount.value > 0;
});

</script>

<template>
    <div class="members-tab">
        <!-- Action Buttons Section -->
        <div class="flex justify-end w-full items-center mb-4 space-x-3">
            <!-- Invite Member Button -->
            <PrimaryButton v-if="canInvite" @click="openInviteModal" class="invite-btn">
                Invite Member
            </PrimaryButton>

            <!-- Pending Requests Button -->
            <SecondaryButton v-if="group.can.viewPendingRequests && hasPendingRequests" @click="togglePendingRequests"
                class="requests-btn">
                Pending Requests ({{ pendingRequestsCount }})
            </SecondaryButton>
        </div>

        <!-- Modals and Lists Section -->
        <div class="content-section">
            <!-- Invite Member Modal -->
            <InviteMemberModal v-if="showInviteModal" v-model="showInviteModal" :groupId="group.id" />

            <!-- Pending Requests List -->
            <RequestsList v-if="showPendingRequests && hasPendingRequests" :groupId="group.id" :isAdmin="isAdmin"
                :requests="group.pending_requests || []" class="mb-4" />

            <!-- Members List -->
            <MembersList v-if="group.can.viewMembers" :isAdmin="group.can.promoteMembers" :members="members" />

            <!-- Private Group Message -->
            <div v-else class="text-gray-400 text-sm text-center p-4">
                This group is private
            </div>
        </div>
    </div>
</template>

<style scoped>
.members-tab {
    min-height: 200px;
}

.content-section {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.invite-btn:hover {
    transform: translateY(-1px);
    transition: transform 0.2s ease;
}
</style>
