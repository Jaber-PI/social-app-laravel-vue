<script setup>
import { ref, toRef } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import { TabPanel } from '@headlessui/vue';

import PrimaryButton from '@/Components/PrimaryButton.vue';
import GroupForm from './GroupForm.vue';
import { useGroupPermissions } from '@/composables/useGroupPermissions';


const props = defineProps({
    group: Object,
});

const editing = ref(false);
const groupRef = toRef(props, 'group');
const { isAdmin } = useGroupPermissions(groupRef);
</script>

<template>
    <div class="about-tab overflow-auto">
        <!-- Group Description -->
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-2">
                About this Group
            </h3>
            <div class="prose prose-sm max-w-none">
                <p v-if="group.description" class="text-gray-600 leading-relaxed">
                    {{ group.description }}
                </p>
                <p v-else class="text-gray-400 italic">
                    No description available.
                </p>
            </div>
        </div>

        <!-- Group Details -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <!-- Creation Date -->
            <div class="bg-gray-50 p-3 rounded-lg">
                <h4 class="font-medium text-gray-700 mb-1">Created</h4>
                <p class="text-sm text-gray-600">
                    {{ new Date(group.created_at).toLocaleDateString() }}
                </p>
            </div>

            <!-- Group Type -->
            <div class="bg-gray-50 p-3 rounded-lg">
                <h4 class="font-medium text-gray-700 mb-1">Privacy</h4>
                <p class="text-sm text-gray-600">
                    {{ group.is_public ? 'Public Group' : 'Private Group' }}
                </p>
            </div>
        </div>

        <!-- Admin Actions -->
        <div v-if="isAdmin" class="border-t pt-4">
            <h4 class="font-medium text-gray-700 mb-3">Admin Actions</h4>
            <div class="flex space-x-2">
                <!-- Add edit/manage buttons here -->
                <button @click="editing = true" class="px-3 py-1 text-sm bg-blue-100 text-blue-700 rounded hover:bg-blue-200 transition">
                    Edit Group
                </button>
                <button class="px-3 py-1 text-sm bg-gray-100 text-gray-700 rounded hover:bg-gray-200 transition">
                    Group Settings
                </button>
            </div>
            <div v-if="editing">
                <GroupForm :group="group" @submit="editing = false" @cancel="editing = false" />
            </div>
        </div>
    </div>

</template>
