<template>
    <div class="bg-white rounded-lg shadow mb-4">
        <img :src="group.cover_path ? `/storage/${group.cover_path}` : default_cover"
            class="w-full h-48 object-cover rounded-t-lg" />
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

const default_cover = '/storage/groups/covers/default_cover.jpg';

const props = defineProps({
    group: Object
});
</script>
