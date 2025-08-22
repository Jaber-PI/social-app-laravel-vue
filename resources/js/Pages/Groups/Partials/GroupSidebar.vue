<script setup>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { capitalize, computed, ref } from 'vue';

import InviteMemberModal from '@/Pages/Groups/Partials/InviteMemberModal.vue';

import { router } from '@inertiajs/vue3';


const props = defineProps(['group', 'members']);


const pendingRequests = computed(() => {
    return props.group.pending_requests;
});

const searchKey = ref('');

const showInviteModal = ref(false);

function openInviteModal() {
    showInviteModal.value = true;
}

function approve(requestId) {
    router.post(route('groups.approve', [props.group.id, requestId]), {}, {
        preserveScroll: true,
    })
}

function reject(requestId) {
    console.log(requestId);
    router.post(route('groups.reject', [props.group.id, requestId]), {}, {
        preserveScroll: true,
    })
}

</script>


<template>
    <!-- members list  -->
    <div class="p-4 bg-white rounded-lg shadow mb-4">
        <div class="flex justify-between items-center mb-2">
            <h2 class="text-xl font-semibold mb-2 hidden sm:block">Members <span>({{ members.length }})</span>
            </h2>
            <PrimaryButton class="px-4 py-2 bg-blue-600 text-white rounded" v-if="group.can.invite"
                @click="openInviteModal">
                Invite Member
            </PrimaryButton>
            <InviteMemberModal v-if="showInviteModal" v-model="showInviteModal" :groupId="group.id" />
        </div>

        <div v-if="group.can.view">
            <div v-if="members.length" class="flex flex-col gap-2 max-h-[200px] overflow-auto p-2 border ">
                <!-- <div v-for="group in []" :key="group.id" -->
                <div class="search mb-2 ">
                    <TextInput :model-value="searchKey" class="w-full" placeholder="Search for a Member" />
                </div>
                <template v-for="member in members" :key="member.id">
                    <div
                        class="flex items-center gap-3 px-2 py-1 bg-gray-100 hover:bg-gray-50 cursor-pointer rounded-md">
                        <div class="flex">
                            <img :src="member.avatar_url" alt="" class="w-7 h-7 sm:w-9 sm:h-9 rounded-full">
                        </div>
                        <div class="p-2 flex flex-1 justify-between items-center">
                            <div class="font-bold text-l sm:text-xl">
                                {{ member.name }}
                            </div>
                            <div class="text-gray-500">{{ capitalize(member.role) }}</div>
                        </div>
                    </div>
                </template>
            </div>
            <div v-else class="text-gray-400 flex text-center py-4">
                No Friend available
            </div>
        </div>
        <div class="text-gray-400 text-sm" v-else>
            the group is private
        </div>



    </div>

    <!-- invited members list to show if user is admin  -->
    <div class="p-4 bg-white rounded-lg shadow mb-4" v-if="group.can.approve && pendingRequests">
        <h2 class="text-xl font-semibold mb-2">Pending Requests</h2>
        <div v-if="pendingRequests.length" class="flex flex-col gap-2 max-h-[200px] overflow-auto p-2 border ">
            <template v-for="req in pendingRequests" :key="req.id">
                <div class="flex items-center gap-3 px-2 py-1 bg-gray-100 hover:bg-gray-50 cursor-pointer rounded-md">
                    <div class="flex">
                        <img :src="req.user.avatar_url" alt="" class="w-7 h-7 sm:w-9 sm:h-9 rounded-full">
                    </div>
                    <div class="p-2 flex flex-1 justify-between items-center">
                        <div class="font-bold text-l sm:text-xl">
                            <p>{{ req.user.name }}</p>
                        </div>
                        <div class="flex space-x-2">
                            <!-- Approve -->
                            <button @click="approve(req.id)"
                                class="w-10 h-10 flex items-center justify-center rounded-full bg-green-500 hover:bg-green-600 text-white shadow transition"
                                title="Approve">
                                ✓
                            </button>

                            <!-- Reject -->
                            <button @click="reject(req.id)"
                                class="w-10 h-10 flex items-center justify-center rounded-full bg-red-500 hover:bg-red-600 text-white shadow transition"
                                title="Reject">
                                ✕
                            </button>
                        </div>
                    </div>
                </div>
            </template>
        </div>
        <div v-else class="text-gray-400 flex text-center py-4">
            No Pending Requests
        </div>
    </div>
</template>
