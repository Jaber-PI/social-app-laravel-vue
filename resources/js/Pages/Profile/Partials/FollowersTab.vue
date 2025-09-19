<script setup>
import { Link } from '@inertiajs/vue3';


const props = defineProps({
    user: Object,
})

</script>

<template>
    <div class="text-sm">
        <p class="text-gray-500">Followers</p>
        <div v-if="user.followers.length > 0" class="mt-2 space-y-2">
            <div v-for="follower in user.followers" :key="follower.id" class="flex items-center space-x-2">
                <Link :href="route('profile.show', follower.username)" class="flex items-center space-x-2" >
                    <img :src="follower.avatar_url" alt="Avatar" class="w-8 h-8 rounded-full">
                    <span class="font-medium text-gray-800">{{ follower.name }}</span>
                </Link>
            </div>
        </div>
        <div class="mt-2" v-else>
            <p class="text-gray-700">No followers yet.</p>
        </div>
    </div>
</template>
