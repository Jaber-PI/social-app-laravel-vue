<script setup>
import ModalHeadless from '../ModalHeadless.vue';
import { useForm } from '@inertiajs/vue3';

// import TextareaInput from "../TextareaInput.vue";
import { computed, ref } from 'vue';
import PrimaryButton from '../PrimaryButton.vue';

import Editor from '../Editor.vue';

const props = defineProps({
    post: {
        type: Object,
        required: true,
    },
    modelValue: Boolean,
})

const form = useForm({
    body: props.post.body,
    id: props.post.id,
})


const emit = defineEmits('update:modelValue');

const show = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value)
}
)

const onSubmit = () => {
    if (form.id) {
        form.put(route('post.update', props.post), {
            preserveScroll: true,
        });
        emit('update:modelValue', false);
    } else {
        form.post(route('post.store'), {
            preserveScroll: true,
            onSuccess: () => {
                form.reset();
            }
        });
    }
    show.value = false;
    return;
};

</script>

<template>
    <teleport to="body">
        <ModalHeadless :isOpen="show">
            <div class="mt-2">
                <!-- post header  -->
                <div class="p-3 bg-white ">
                    <div class="flex items-center space-x-2">
                        <img class="rounded-full icon border-2 hover:border-blue-500 transition-all w-9 h-9"
                            :src="post.author.avatar_url" />
                        <div>
                            <div class="flex">
                                <p class="font-medium hover:underline cursor-pointer">{{ post.author.name }}
                                </p>
                                <template v-if="post.group">
                                    <span class=" mx-2 font-medium"> > </span>
                                    <p class="font-medium hover:underline cursor-pointer">
                                        {{ post.group.name }}</p>
                                </template>
                            </div>
                            <p v-if="post.created_at" class="text-xs text-gray-400">
                                {{ new Date(post.created_at).toLocaleString() }}
                            </p>
                            <p v-else class="text-xs text-gray-400">Loading</p>
                        </div>
                    </div>

                    <div class="wrapper-description cursor-pointer">

                        <div class="description-info">
                            <Editor v-model="form.body" />
                            <!-- <Ckeditor :editor="editor" :model-value="data" :config="editorConfig" /> -->
                            <!-- <TextareaInput v-model.trim="form.body" class="w-full mt-2 p-3" rows="1" /> -->
                        </div>

                    </div>
                </div>

                <!-- attachments section  -->
                <div v-if="post.attachments"
                    class="relative w-full rounded-t-2xl grid grid-cols-[repeat(auto-fit,minmax(150px,1fr))] gap-1">
                    <div v-for="attachment of post.attachments" :key="attachment.id"
                        class="group overflow-hidden relative">
                        <button
                            class=" absolute right-1 cursor-pointer bottom-1 z-10 text-white p-1 bg-blue-400 rounded-full transition-all opacity-0 group-hover:opacity-100">

                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                            </svg>
                        </button>

                        <img v-if="isImage(attachment)" :src="attachment.url" class="object-cover w-full"
                            loading="lazy" />

                        <div v-else class="grid place-content-center bg-blue-100 aspect-square">
                            <p class="text-xl text-center">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-9">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                </svg>
                                <small>
                                    {{ attachment.name }}
                                </small>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="mt-2 px-3 flex justify-between">
                    <PrimaryButton @click="onSubmit">{{ post.id ? 'Update' : 'Create'}} </PrimaryButton>
                    <button type="button"
                        class="inline-flex justify-center rounded-md border border-transparent bg-blue-100 px-4 py-2 text-sm font-medium text-blue-900 hover:bg-blue-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2"
                        @click="show = false">
                        Cancel
                    </button>
                </div>
            </div>
        </ModalHeadless>
    </teleport>

</template>
