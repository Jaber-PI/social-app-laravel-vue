<script setup>

import GroupCover from './GroupCover.vue';

import { router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

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

    router.post(route('groups.leave', props.group.id), {
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
    <div>
        <GroupCover :groupId="group.id" :cover_path="group.cover_path" :canUpdate="group.can.update" />
        <div class="p-4 flex bg-white ">
            <div class="flex-1">
                <h1 class="text-xl font-bold">{{ group.name }}</h1>
                <span class="text-sm text-gray-500">Created by: {{ group.creator.name }}</span>
                <p class="text-gray-600">{{ group.description }}</p>
            </div>
            <div class="">
                <button class="px-4 py-2 bg-blue-600 text-white rounded" v-if="canJoin" @click="joinGroup">
                    Join Group
                </button>

                <!-- leave button  -->
                <button v-else-if="canLeave" class="px-4 py-2 bg-gray-500 text-white rounded" @click="leaveGroup">
                    Leave Group
                </button>
                <button v-else-if="canCancel" class="px-4 py-2 bg-red-500 text-white rounded" @click="cancelRequest">
                    Cancel Request
                </button>
            </div>
        </div>
    </div>

</template>
