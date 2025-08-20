<template>
    <div class="bg-white rounded-lg shadow mb-4">

        <GroupCover :groupId="group.id" :cover_path="group.cover_path" />

        <div class="p-4 flex">
            <div class="flex-1">
                <h1 class="text-xl font-bold">{{ group.name }}</h1>
            <p class="text-gray-600">{{ group.description }}</p>
            </div>
            <div class="">
                <button class="px-4 py-2 bg-blue-600 text-white rounded" v-if="!group.is_member">
                    Join Group
                </button>
                <button class="px-4 py-2 bg-gray-300 rounded" v-else>
                    Leave Group
                </button>
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
