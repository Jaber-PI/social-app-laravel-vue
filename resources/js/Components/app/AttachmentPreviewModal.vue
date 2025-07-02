<script setup>
import ModalHeadless from '../ModalHeadless.vue';

// import TextareaInput from "../TextareaInput.vue";
import { computed, ref } from 'vue';

import { PaperClipIcon, XMarkIcon } from '@heroicons/vue/24/solid';
import { isImage } from '@/helpers';

const model = defineModel();

const props = defineProps({
    attachmentIndex: {
        type: Number,
        required: true
    },
    attachments: {
        type: Array,
        required: true
    }
})

const selectedAttachmentIndex = ref(props.attachmentIndex);

const selectedAttachment = computed(() => props.attachments[selectedAttachmentIndex.value]);

function prevAttachment() {
    if (selectedAttachmentIndex.value == 0) {
        selectedAttachmentIndex.value = props.attachments.length;
        return;
    }
    selectedAttachmentIndex.value--;
    return
}
function nextAttachment() {
    if (selectedAttachmentIndex.value == props.attachments.length - 1) {
        selectedAttachmentIndex.value = 0;
        return;
    }
    selectedAttachmentIndex.value++;
}

function closeModal() {
    model.value = false;
}

</script>

<template>
    <teleport to="body">
        <ModalHeadless :isOpen="model">
            <div class="w-full relative">
                <button @click="closeModal"
                    class="absolute top-2 z-10 right-2 px-4 py-2 bg-gray-700 text-white rounded">
                    <XMarkIcon class="w-8" />
                </button>

                <!-- ➡️ Right Button -->
                <button @click="prevAttachment" :disabled="selectedAttachmentIndex == 0"
                    class="absolute z-10 left-4 top-1/2 -translate-y-1/2 bg-gray-800 text-white p-2 rounded-full shadow disabled:bg-gray-400 hover:bg-gray-600">
                    ‹
                </button>
                <!-- preview  -->
                <div class="p-4 bg-white relative rounded-lg shadow w-full">
                    <div v-if="selectedAttachment">
                        <img v-if="isImage(selectedAttachment)" :src="selectedAttachment.url" alt="Preview"
                            class="max-h-[80vh] w-full mx-auto" />
                        <div v-else class="text-center">
                            <PaperClipIcon class="w-9 mx-auto" />
                            <p class="mb-2">Cannot preview this file type.
                            </p>
                            <a :href="selectedAttachment.url" target="_blank" class="text-blue-600 underline">
                                Download {{ selectedAttachment.filename }}
                            </a>
                        </div>
                    </div>
                </div>
                <!-- ➡️ Right Button -->
                <button @click="nextAttachment" :disabled="selectedAttachmentIndex == attachments.length - 1"
                    class="absolute z-10 right-4 top-1/2 -translate-y-1/2 bg-gray-800 text-white p-2 rounded-full shadow hover:bg-gray-600 disabled:bg-gray-400">
                    ›
                </button>

            </div>
        </ModalHeadless>
    </teleport>

</template>
