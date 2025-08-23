<script setup>

import GroupCover from './GroupCover.vue';

import { router } from '@inertiajs/vue3';
import { capitalize, computed, ref } from 'vue';
import Avatar from './Avatar.vue';

const props = defineProps({
    group: Object
});

const canJoin = computed(() => props.group.can.join);
const canLeave = computed(() => props.group.can.leave);
const canCancel = computed(() => props.group.can.cancel);

function joinGroup() {
    router.post(route('groups.request', props.group.id), {
        preserveScroll: true,
    })
}

function leaveGroup() {
    if (!confirm('Are you sure you want to leave this group?')) {
        return;
    }

    router.delete(route('groups.leave', props.group.id), {
        preserveScroll: true,
    });
}
function cancelRequest() {
    router.delete(route('groups.cancelRequest', props.group.id), {
        preserveScroll: true,
    });
}

</script>


<template>
    <div class="relative w-full h-48 bg-gradient-to-r from-indigo-500 to-purple-600">
        <GroupCover :groupId="group.id" :cover_path="group.cover_path" :canUpdate="group.can.update" />
        <div class="absolute -bottom-12 left-6 flex items-end space-x-4">
            <!-- avatar  -->
            <Avatar :group="group" :can="group.can" />
        </div>
        <div class="absolute right-6 bottom-6 flex space-x-2">
            <button class="px-4 py-2 bg-indigo-600 text-white rounded-xl shadow" v-if="canJoin"
                @click="joinGroup">Join</button>
            <button class="px-4 py-2 bg-white border rounded-xl shadow" v-else-if="canLeave" @click="leaveGroup">
                Leave Group</button>
            <button class="px-4 py-2 bg-white border rounded-xl shadow" v-else-if="canCancel" @click="cancelRequest">
                Cancel Request</button>
        </div>
    </div>

    <div class="flex items-center ps-20 ms-20 py-4 space-x-3">
        <h1 class="text-2xl font-bold text-gray-900">{{ capitalize(group.name) }}</h1>
        <div class="ms-auto flex  space-x-3">
            <p class="text-gray-600 text-sm">{{ group.is_public ? 'Public' : 'Private' }} • {{ group.members_count
                }} members</p>
            <p class="text-sm text-gray-600">Created by: {{ group.creator.name }}</p>
        </div>

        <!-- <p class="text-gray-600">{{ group.description }}</p> -->
    </div>

</template>
