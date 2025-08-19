<script setup>
import { ref, toRaw } from 'vue';
import ModalHeadless from '../ModalHeadless.vue';
import { XMarkIcon } from '@heroicons/vue/24/solid';
import PrimaryButton from '../PrimaryButton.vue';

import TextInput from '../TextInput.vue';
import InputError from '../InputError.vue';
import TextareaInput from '../TextareaInput.vue';
import Checkbox from '../Checkbox.vue';

import axiosClient from '@/lib/axiosClient';
import { useForm } from '@inertiajs/vue3';

const emit = defineEmits(['closed', 'saved']);

const props = defineProps({
    group: {
        type: Object,
        default: () => ({}),
        required: false
    }
});

const form = useForm({
    name: props.group.name || '',
    description: props.group.description || '',
    auto_approval: props.group.auto_approval || true,
    processing: false,
})

// Handle file upload
const thumbnailPreview = ref(props.group.thumbnail_path || null);
const coverPreview = ref(props.group.cover_path || null);

function handleThumbnailChange(event) {
    const file = event.target.files[0];
    if (file) {
        // form.thumbnail = file;
        thumbnailPreview.value = URL.createObjectURL(file);
    }
}
// Handle file upload
function handleCoverChange(event) {
    const file = event.target.files[0];
    if (file) {
        // form.cover = file;
        coverPreview.value = URL.createObjectURL(file);
    }
}

function closeModal() {
    emit('closed');
}

async function saveGroup() {
    if (form.processing) return; // Prevent multiple submissions

    form.processing = true;

    axiosClient.post(route('groups.store'), form)
        .then(response => {
            emit('saved', response.data);
        }).catch(error => {
            console.error("Failed to save group:", error);
        }).finally(() => {
            form.processing = false;
        });
}

</script>

<template>
    <teleport to="body">
        <ModalHeadless :isOpen="true" @close="closeModal">
            <div class="w-[500px] mx-auto rounded-2xl  p-6  shadow-xl bg-white relative min-h-[300px] px-10 mt-10 ">
                <!-- Close button -->
                <button @click="closeModal" class="absolute top-2 right-2 px-4 py-2 bg-gray-700 text-white rounded">
                    <XMarkIcon class="w-8" />
                </button>

                <h3 class="text-lg font-semibold mb-4">Create Group</h3>

                <!-- Form -->
                <form @submit.prevent="saveGroup" class="space-y-4 px-1">
                    <!-- Group Name -->
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <label class="block text-sm font-medium text-gray-700 mr-3">Group Name * :</label>
                            <TextInput class="flex-1" v-model="form.name" required />
                        </div>
                        <!-- <InputError class="mt-2" :message="form.errors.name" /> -->
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <TextareaInput v-model="form.description" rows="3"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-300 focus:border-blue-500" />

                        <!-- <InputError class="mt-2" :message="form.errors.description" /> -->
                    </div>

                    <!-- Privacy -->
                    <div>
                        <label class="flex items-center">
                            <Checkbox name="remember" v-model:checked="form.auto_approval" />
                            <span class="ms-2 text-sm text-gray-600">Automatique approval</span>
                        </label>
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-end space-x-3 pt-4">
                        <button type="button" @click="closeModal"
                            class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg">
                            Cancel
                        </button>
                        <button type="submit" :disabled="form.processing"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md shadow-sm disabled:opacity-50">
                            {{ form.processing ? 'Creating...' : 'Create Group' }}
                        </button>
                    </div>


                    <!-- Thumbnail Upload -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Thumbnail</label>
                        <input type="file" accept="image/*" @change="handleThumbnailChange" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4
                               file:rounded-full file:border-0 file:text-sm file:font-semibold
                               file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                        <div v-if="thumbnailPreview" class="mt-3">
                            <img :src="thumbnailPreview" alt="Thumbnail Preview"
                                class="w-32 h-32 object-cover rounded-lg border" />
                        </div>
                    </div>

                    <!-- Cover Image -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cover Image</label>
                        <input type="file" accept="image/*" @change="handleCoverChange" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4
                               file:rounded-full file:border-0 file:text-sm file:font-semibold
                               file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                        <div v-if="coverPreview" class="mt-3">
                            <img :src="coverPreview" alt="Cover Preview"
                                class="w-full h-40 object-cover rounded-lg border" />
                        </div>
                    </div>

                </form>
            </div>
        </ModalHeadless>
    </teleport>
</template>
