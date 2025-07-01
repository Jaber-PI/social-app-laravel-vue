<script setup>
import ModalHeadless from '../ModalHeadless.vue';
import { router, useForm } from '@inertiajs/vue3';

// import TextareaInput from "../TextareaInput.vue";
import { computed, ref } from 'vue';
import PrimaryButton from '../PrimaryButton.vue';

import Editor from '../Editor.vue';
import { isImage } from '@/helpers';
import { PaperClipIcon, XMarkIcon } from '@heroicons/vue/24/solid';

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
    attachments: [],
    deleted_attachments: []
})

const emit = defineEmits('update:modelValue');

const show = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value)
}
)

const newAttachments = ref([]);
const oldAttachments = ref(props.post.attachments || [])

const postAttachments = computed(
    () => [...oldAttachments.value, ...newAttachments.value]
);

function removeAttachment(attachment) {
    if (attachment.id) {
        oldAttachments.value = oldAttachments.value.filter(file => file != attachment);
        form.deleted_attachments.push(attachment.id);
    } else {
        newAttachments.value = newAttachments.value.filter(file => file != attachment);
    }
}
const onSubmit = () => {
    form.attachments = newAttachments.value.map(f => f.file);

    if (form.id) {
        router.post(route('post.update', props.post), {
            _method: 'put',
            body: form.body,
            attachments: form.attachments,
            deleted_attachments: form.deleted_attachments,
        })
        emit('update:modelValue', false);
    } else {
        form.post(route('post.store'), {
            preserveScroll: true,
            onSuccess: () => {
                form.reset();
            }
        });
        emit('update:modelValue', false);
    }
    closeModal();
    return;
};

async function onFileChosen($event) {
    for (const file of $event.target.files) {
        const myFile = {
            file,
            src: await readFile(file)
        }
        newAttachments.value.push(myFile);
    };
    $event.target.value = null;
}

function readFile(file) {
    return new Promise((res, rej) => {
        if (isImage({ mime: file.type })) {
            const reader = new FileReader();
            reader.onload = () => {
                res(reader.result);
            }
            reader.onerror = rej;
            reader.readAsDataURL(file);
        } else {
            res(null);
        }
    })
}



function closeModal() {
    postAttachments.value = [];
    show.value = false;
}
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

                        </div>

                    </div>
                </div>

                <!-- attachments section  -->
                <div v-if="postAttachments"
                    class="relative w-full rounded-t-2xl grid grid-cols-[repeat(auto-fit,minmax(150px,1fr))] gap-1">
                    <div v-for="(attachment, ind) of postAttachments" :key="ind"
                        class="group overflow-hidden aspect-square relative">
                        <!-- download icon  -->
                        <button
                            class=" absolute right-1 cursor-pointer bottom-1 z-10 font-bold text-white p-1 bg-blue-400 rounded-full transition-all opacity-0 group-hover:opacity-100">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                            </svg>
                        </button>

                        <!-- delete attachment icon  -->
                        <button @click="removeAttachment(attachment)"
                            class="absolute right-1 cursor-pointer top-1 z-10 text-white font-bold p-1 bg-red-400 rounded-full transition-all opacity-0 group-hover:opacity-100">
                            <XMarkIcon class="w-9 h-9" />
                        </button>

                        <!-- <div v-if="ind == 2 && postAttachments.length > 3"
                            class="absolute inset-0 z-10 bg-black opacity-60 text-center flex justify-center items-center text-white text-xl font-bold">
                            <div class="text-white">
                                And {{ postAttachments.length - 3 }} Files More.
                            </div>
                        </div> -->

                        <img v-if="isImage(attachment.file || attachment)" :src="attachment.src || attachment.url"
                            class="object-cover h-full w-full" loading="lazy" />

                        <div v-else
                            class="grid flex-col-reverse p-2 place-content-center bg-blue-100 aspect-square border-2 border-dashed border-blue-300 hover:bg-blue-200 cursor-pointer">
                            <div class="flex p-2 flex-col items-center text-center">
                                <PaperClipIcon class="w-12 h-12 text-blue-600" />
                                <small v-if="attachment?.file"
                                    class="max-w-[90%]  break-words text-center text-blue-900">
                                    {{ attachment.file.name || attachment.filename }}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-2 px-3 w-100">
                    <button type="button"
                        class="inline-flex relative gap-3 justify-center align-center rounded-md border border-transparent bg-blue-100 px-4 py-2 text-sm font-medium text-blue-900 hover:bg-blue-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2">
                        <PaperClipIcon class="w-4 h-4" />
                        Attach files
                        <input type="file" multiple @change="onFileChosen"
                            class="absolute z-30 top-0 left-0 bottom-0 right-0 opacity-0">
                    </button>
                </div>
                <div class="mt-2 px-3 flex justify-between">
                    <PrimaryButton @click="onSubmit">{{ post.id ? 'Update' : 'Create' }} </PrimaryButton>
                    <button type="button"
                        class="inline-flex justify-center rounded-md border border-transparent bg-blue-100 px-4 py-2 text-sm font-medium text-blue-900 hover:bg-blue-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2"
                        @click="closeModal">
                        Cancel
                    </button>
                </div>
            </div>
        </ModalHeadless>
    </teleport>

</template>
