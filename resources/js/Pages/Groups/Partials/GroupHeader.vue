<script setup>

import GroupCover from './GroupCover.vue';

import { router } from '@inertiajs/vue3';

const props = defineProps({
    group: Object
});

function joinGroup() {
    router.post(route('groups.request', props.group.id,), {
        preserveScroll: true
    })
}

function leaveGroup() {
    if (!confirm('Are you sure you want to leave this group?')) {
        return;
    }
    router.delete(route('groups.leave', props.group.id), {
        preserveScroll: true
    })
}


function cancelRequest() {
    router.delete(route('groups.cancelRequest', props.group.id), {
        preserveScroll: true
    })
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
                <button class="px-4 py-2 bg-blue-600 text-white rounded" v-if="!group.current_user && group.can.join" @click="joinGroup">
                    Join Group
                </button>

                <!-- leave button  -->
                <button v-else-if="group.current_user.status == 'approved'"
                    class="px-4 py-2 bg-gray-500 text-white rounded"
                    @click="leaveGroup">
                    Leave Group
                </button>
                <button v-else-if="group.current_user.status == 'pending'"
                    class="px-4 py-2 bg-red-500 text-white rounded"
                    @click="cancelRequest">
                    Cancel Request
                </button>
            </div>
        </div>
    </div>

</template>
