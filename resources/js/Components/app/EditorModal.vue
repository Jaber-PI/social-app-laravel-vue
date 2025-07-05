<script setup>
import ModalHeadless from '../ModalHeadless.vue';
import { router, useForm } from '@inertiajs/vue3';

// import TextareaInput from "../TextareaInput.vue";
import { computed, ref } from 'vue';
import PrimaryButton from '../PrimaryButton.vue';

import Editor from '../Editor.vue';
import { isImage } from '@/helpers';
import { PaperClipIcon, XMarkIcon } from '@heroicons/vue/24/solid';
import InputError from '../InputError.vue';

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
    deleted_attachments: [],
    _method: ''
})

const emit = defineEmits('update:modelValue');

const show = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value)
}
)

const newAttachments = ref([]);
const oldAttachments = ref(props.post.attachments || [])

const attachmentsErrors = ref([]);

const postAttachments = computed(
    () => [...oldAttachments.value, ...newAttachments.value]
);

function removeAttachment(attachment) {
    attachmentsErrors.value = []
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
        form._method = 'PUT'
        form.post(route('post.update', props.post), {
            preserveScroll: true,
            onSuccess: () => {
                form.reset();
            },
            onError: (errors) => {
                for (const key in errors) {
                    if (key.includes('.')) {
                        const [name, index] = key.split('.');
                        if (name == 'attachments') {
                            attachmentsErrors.value[index + oldAttachments.value.length - 0] = errors[key];
                        }
                    }
                }
            }
        })
    } else {
        form.post(route('post.store'), {
            preserveScroll: true,
            onSuccess: () => {
                form.reset();
            },
            onError: (errors) => {
                for (const key in errors) {
                    if (key.includes('.')) {
                        const [name, index] = key.split('.');
                        if (name == 'attachments') {
                            attachmentsErrors.value[index] = errors[key];
                        }
                    }
                }
            }
        });
        // emit('update:modelValue', false);
    }
    // closeModal();
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
    attachmentsErrors.value = []
    postAttachments.value = [];
    show.value = false;
}
</script>

<template>
    <teleport to="body">
        <ModalHeadless :isOpen="show">
            <div class="pt-6 z-40 ">
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
                    class="px-3 w-full rounded-t-2xl grid grid-cols-[repeat(auto-fit,minmax(150px,1fr))] gap-1">
                    <div v-for="(attachment, ind) of postAttachments" :key="ind" :class="[
                        attachmentsErrors[ind] ? 'border-2 p-1 pb-5 border-red-500' : ''
                    ]" class="group h-full w-full max-h-64 aspect-square relative">
                        <div class="relative max-h-full overflow-hidden">
                            <!-- delete attachment icon  -->
                            <button @click="removeAttachment(attachment)"
                                class="absolute right-1 cursor-pointer top-1 z-10 text-white font-bold p-1 bg-red-400 rounded-full transition-all opacity-0 group-hover:opacity-100">
                                <XMarkIcon class="w-9 h-9" />
                            </button>
                            <img v-if="isImage(attachment.file || attachment)" :src="attachment.src || attachment.url"
                                class="object-cover mx-auto max-h-full" loading="lazy" />
                            <div v-else
                                class="p-2  bg-blue-100 aspect-square border-2 border-dashed border-blue-300 hover:bg-blue-200 cursor-pointer">
                                <div class="flex p-2 flex-col items-center text-center">
                                    <PaperClipIcon class="w-12 h-12 text-blue-600" />
                                    <small class="max-w-[90%] break-words text-center text-blue-900">
                                        {{ attachment?.file?.name || attachment.filename }}
                                    </small>
                                </div>
                            </div>
                        </div>
                        <InputError :message="attachmentsErrors[ind] ?? ''" />
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
                        Close
                    </button>
                </div>
            </div>
        </ModalHeadless>
    </teleport>

</template>
