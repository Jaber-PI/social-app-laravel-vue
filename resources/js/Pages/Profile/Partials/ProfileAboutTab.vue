<script setup>
import { ref, toRef } from 'vue';
import { router, useForm } from '@inertiajs/vue3';

import PrimaryButton from '@/Components/PrimaryButton.vue';


const props = defineProps({
    user: Object,
});

</script>

<template>
    <div class="about-tab overflow-auto">
        <!-- Group Description -->
        <div class="bg-white rounded-lg shadow p-6 max-w-xl mx-auto">
            <h2 class="text-2xl font-semibold mb-4 text-gray-800">About {{ user.name }}</h2>
            <ul class="space-y-3">
                <li>
                    <span class="font-medium text-gray-600">Email:</span>
                    <span class="text-gray-900">{{ user.email }}</span>
                </li>
                <li v-if="user.bio">
                    <span class="font-medium text-gray-600">Bio:</span>
                    <span class="text-gray-900">{{ user.bio }}</span>
                </li>
                <li v-if="user.location">
                    <span class="font-medium text-gray-600">Location:</span>
                    <span class="text-gray-900">{{ user.location }}</span>
                </li>
                <li v-if="user.joined_at">
                    <span class="font-medium text-gray-600">Joined:</span>
                    <span class="text-gray-900">{{ new Date(user.joined_at).toLocaleDateString() }}</span>
                </li>
            </ul>
        </div>
    </div>

</template>
