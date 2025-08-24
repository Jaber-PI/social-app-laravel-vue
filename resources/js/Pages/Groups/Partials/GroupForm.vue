<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';

import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Checkbox from '@/Components/Checkbox.vue';


const props = defineProps({
    group: {
        type: Object,
        required: false
    },
});

const form = useForm({
    name: props.group?.name || '',
    description: props.group?.description || '',
    auto_approval: props.group?.auto_approval || false,
});

const emit = defineEmits(['submit', 'cancel']);
function submitForm() {
    if (props.group) {
        form.put(route('groups.update', props.group.id), {
            onSuccess: () => {
                form.reset();
                emit('submit');
            }
        });
    }
    else {
        form.post(route('groups.store'), {
            onSuccess: () => {
                form.reset();
                emit('submitted');
            }
        });
    }
}
</script>


<template>
    <form @submit.prevent="submitForm">
        <div class="mb-4">
            <label class="block text-gray-700 mb-1">Group Name</label>
            <TextInput v-model="form.name" class="w-full" />
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 mb-1">Description</label>
            <textarea v-model="form.description" class="w-full border rounded p-2"></textarea>
        </div>
        <!-- Privacy -->
        <div class="mb-4">
            <label class="flex items-center">
                <Checkbox name="remember" v-model:checked="form.auto_approval" />
                <span class="ms-2 text-sm text-gray-600">Automatique approval</span>
            </label>
        </div>
        <div class="flex space-x-2">
            <PrimaryButton type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">
                Save
            </PrimaryButton>
            <button type="button" @click="$emit('cancel')" class="px-4 py-2 rounded bg-gray-300 text-gray-700">
                Cancel
            </button>
        </div>
    </form>
</template>
