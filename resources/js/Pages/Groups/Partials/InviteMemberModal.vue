<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

import ModalHeadless from '@/Components/ModalHeadless.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';

import axiosClient from '@/lib/axiosClient.js';

const props = defineProps({
    modelValue: Boolean,
    groupId: Number
});

const emit = defineEmits(['update:modelValue']);

function closeModal() {
    emit('update:modelValue', false);
}

const form = useForm({
    email: ''
});


const inviteForm = ref(null)

function inviteMember() {

    if (!inviteForm.value.checkValidity()) {
        inviteForm.value.reportValidity()
        return
    }
    axiosClient.post(route('groups.invite', props.groupId), {
        email: form.email
    }).then((response) => {
        closeModal();
        alert(response.data.message || 'Invitation sent successfully.');
        form.reset();

    }).catch((error) => {
        if (error.response && error.response.status === 422) {
            // Laravel validation error format
            form.errors = error.response.data.errors || {};
        } else {
            // Fallback for unexpected errors
            alert("Something went wrong, please try again.");
        }
    });
}

</script>

<template>
    <teleport to="body">
        <ModalHeadless :isOpen="modelValue" @close="closeModal">
            <div class="p-7 bg-white rounded-lg shadow container mx-auto max-w-md">
                <h2 class="text-lg font-semibold mb-4">Invite a Member</h2>
                <form ref="inviteForm" @submit.prevent="inviteMember" class="space-y-4">
                    <TextInput v-model="form.email" class="w-full" type="email" required
                        placeholder="Enter email address" />
                    <InputError :message="form.errors.email?.[0]" class="mt-2" />

                    <p class="text-sm text-gray-500">Enter the email address of the person you want to invite.</p>

                    <div class="flex">
                        <PrimaryButton class="ml-auto" type="submit">
                            Send Invite
                        </PrimaryButton>
                    </div>
                </form>
            </div>


        </ModalHeadless>
    </teleport>
</template>
