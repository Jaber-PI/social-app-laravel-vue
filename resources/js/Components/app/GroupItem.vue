<script setup>
import { Link } from '@inertiajs/vue3';
import { capitalize } from 'vue';


defineProps({
    group: Object
})

</script>

<template>
    <div class="flex items-center gap-3 px-2 py-1 bg-gray-100 hover:bg-gray-50 cursor-pointer rounded-md">

        <div class="flex">
            <img :src="group.thumbnail_path" alt="" class="w-7 h-7 sm:w-9 sm:h-9 rounded-full">
        </div>
        <div class="p-2 flex-1">
            <Link :href="route('groups.show', group.slug)" class="text-gray-800 hover:text-blue-600">
            <div class="font-bold text-l sm:text-xl">
                {{ group.name }}
            </div>
            <div class="text-xs sm:text-sm">
                <span class="font-bold">Status:</span> {{ group.current_user?.status == 'approved' ? capitalize(group.current_user?.role) : 'Pending' }}
            </div>
            </Link>

        </div>
    </div>
</template>
