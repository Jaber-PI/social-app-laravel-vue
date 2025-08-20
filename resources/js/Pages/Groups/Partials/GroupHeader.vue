<template>
    <div class="bg-white rounded-lg shadow mb-4">

        <GroupCover :groupId="group.id" :cover_path="group.cover_path" :canUpdate="group.can.update" />

        <div class="p-4 flex">
            <div class="flex-1">
                <h1 class="text-xl font-bold">{{ group.name }}</h1>
                <span class="text-sm text-gray-500">Created by: {{ group.creator.name }}</span>
                <p class="text-gray-600">{{ group.description }}</p>
            </div>
            <div class="">
                <button class="px-4 py-2 bg-blue-600 text-white rounded" v-if="!group.current_user">
                    Join Group
                </button>
                <template v-else>
                    <button v-if="group.current_user.status == 'approved'"
                        class="px-4 py-2 bg-red-500 text-white rounded">
                        Leave Group
                    </button>
                    <button v-if="group.current_user.status == 'pending'"
                        class="px-4 py-2 bg-red-500 text-white rounded">
                        Cancel Request
                    </button>
                </template>

            </div>
        </div>
    </div>
</template>

<script setup>

import GroupCover from './GroupCover.vue';

const props = defineProps({
    group: Object
});
</script>
