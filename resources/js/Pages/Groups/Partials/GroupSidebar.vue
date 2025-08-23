<script setup>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { capitalize, computed, ref } from 'vue';

import { TabGroup, TabList, Tab, TabPanels, TabPanel } from '@headlessui/vue'

import InviteMemberModal from '@/Pages/Groups/Partials/InviteMemberModal.vue';

import { router, useForm } from '@inertiajs/vue3';
import MembersList from './MembersList.vue';
import Checkbox from '@/Components/Checkbox.vue';


const props = defineProps(['group', 'members']);

const isAdmin = computed(() => {
    return props.group.current_user?.isAdmin;
});

const isOwner = computed(() => {
    return props.group.current_user?.isOwner;
});

const pendingRequests = computed(() => {
    return props.group.pending_requests;
});


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
    router.post(route('groups.reject', [props.group.id, requestId]), {}, {
        preserveScroll: true,
    })
}

function changeRole(userId, role) {
    router.post(route('groups.changeRole', props.group.id), { user_id: userId, role }, { preserveScroll: true })
}

const editing = ref(false);
const editForm = useForm({
    name: props.group.name,
    description: props.group.description,
    auto_approval: props.group.auto_approval || false,
});

function submitEdit() {
    router.put(route('groups.update', props.group.id), editForm, {
        preserveScroll: true,
        onSuccess: () => {
            editing.value = false;
        }
    });
}

</script>


<template>
    <!-- members list  -->
    <div class=" bg-white rounded-lg shadow mb-4">
        <div class="w-full">
            <TabGroup>
                <TabList class="flex space-x-1 rounded-xl bg-white p-1 border-b">
                    <Tab as="template" v-slot="{ selected }">
                        <button :class="[
                            'w-full rounded-lg py-2.5 text-sm font-medium leading-5 transition',
                            selected
                                ? 'bg-blue-100 text-blue-700 shadow font-semibold'
                                : 'text-gray-500 hover:bg-gray-100 hover:text-blue-700',
                        ]">
                            About
                        </button>
                    </Tab>

                    <Tab as="template" v-slot="{ selected }">
                        <button :class="[
                            'w-full rounded-lg py-2.5 text-sm font-medium leading-5 transition',
                            selected
                                ? 'bg-blue-100 text-blue-700 shadow font-semibold'
                                : 'text-gray-500 hover:bg-gray-100 hover:text-blue-700',
                        ]">
                            Members <span>({{ members.length }})</span>
                        </button>
                    </Tab>
                    <Tab as="template" v-slot="{ selected }">
                        <button :class="[
                            'w-full rounded-lg py-2.5 text-sm font-medium leading-5 transition',
                            selected
                                ? 'bg-blue-100 text-blue-700 shadow font-semibold'
                                : 'text-gray-500 hover:bg-gray-100 hover:text-blue-700',
                        ]">
                            Pending  <span>({{ pendingRequests.length }})</span>
                        </button>
                    </Tab>

                </TabList>

                <TabPanels class="">
                     <!-- about tab  -->
                    <TabPanel :class="[
                        'rounded-xl bg-white p-3',
                        'border-none outline-none focus:outline-none ',
                    ]">
                        <div v-if="editing">
                            <form @submit.prevent="submitEdit">
                                <div class="mb-4">
                                    <label class="block text-gray-700 mb-1">Group Name</label>
                                    <TextInput v-model="editForm.name" class="w-full" />
                                </div>
                                <div class="mb-4">
                                    <label class="block text-gray-700 mb-1">Description</label>
                                    <textarea v-model="editForm.description"
                                        class="w-full border rounded p-2"></textarea>
                                </div>
                                <!-- Privacy -->
                                <div class="mb-4">
                                    <label class="flex items-center">
                                        <Checkbox name="remember" v-model:checked="editForm.auto_approval" />
                                        <span class="ms-2 text-sm text-gray-600">Automatique approval</span>
                                    </label>
                                </div>
                                <div class="flex space-x-2">
                                    <PrimaryButton type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">
                                        Save
                                    </PrimaryButton>
                                    <button type="button" @click="editing = false"
                                        class="px-4 py-2 rounded bg-gray-300 text-gray-700">
                                        Cancel
                                    </button>
                                </div>
                            </form>
                        </div>
                        <div v-else class="p-4 relative">
                            <PrimaryButton v-if="isAdmin"
                                class=" absolute top-2 right-3 px-3 py-1 bg-yellow-500 text-white rounded"
                                @click="editing = true">
                                Edit
                            </PrimaryButton>
                            <h2 class="text-lg font-semibold mb-2">About {{ group.name }}</h2>
                            <p class="text-gray-700">{{ group.description || 'No description provided.' }}</p>
                            <p class="text-gray-500 text-sm mt-2">Created by: {{ group.creator.name }}</p>
                        </div>
                    </TabPanel>

                    <!-- members tab  -->
                    <TabPanel :class="[
                        'rounded-xl bg-white p-3',
                        'ring-white/60 ring-offset-2 ring-offset-blue-400 focus:outline-none ',
                    ]">
                        <div class="flex justify-between items-center mb-2">
                            <PrimaryButton class="px-4 py-2 bg-blue-600 text-white rounded" v-if="group.can.invite"
                                @click="openInviteModal">
                                Invite Member
                            </PrimaryButton>
                            <InviteMemberModal v-if="showInviteModal" v-model="showInviteModal" :groupId="group.id" />
                        </div>
                        <MembersList :isAdmin="isAdmin" v-if="group.can.view" :members="members" />
                        <div class="text-gray-400 text-sm" v-else>
                            the group is private
                        </div>
                    </TabPanel>
                    <TabPanel :class="[
                        'rounded-xl bg-white p-3',
                        'border-none outline-none focus:outline-none ',
                    ]">
                        <div class="flex flex-col gap-2 max-h-[200px] overflow-auto p-2 border ">
                            <template v-if="isAdmin">
                                <div v-if="pendingRequests.length">
                                    <div v-for="req in pendingRequests" :key="req.id">
                                        <div
                                            class="flex items-center gap-3 px-2 py-1 bg-gray-100 hover:bg-gray-50 cursor-pointer rounded-md">
                                            <div class="flex">
                                                <img :src="req.user.avatar_url" alt=""
                                                    class="w-7 h-7 sm:w-9 sm:h-9 rounded-full">
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
                                    </div>
                                </div>
                                <div v-else class="text-gray-400 flex text-center py-4">
                                    No Pending Requests
                                </div>
                            </template>
                        </div>
                    </TabPanel>


                </TabPanels>
            </TabGroup>
        </div>
    </div>

    <!-- invited members list to show if user is admin  -->
    <div class="p-4 bg-white rounded-lg shadow mb-4">
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

    </div>
</template>
