<template>
    <div class="relative group w-24 h-24 rounded-full overflow-hidden border-4 border-white shadow-lg bg-gray-200">
        <img alt="..." :src="imageSrc || group.avatar_path || defaultImage"
            class="border-none min-h-full min-w-full object-cover">
        <template v-if="true">
            <div v-if="isChanging"
                class="absolute z-20  bottom-0 pb-1 text-white w-full bg-gray-500/60 flex items-center  justify-center gap-2">
                <SecondaryButton @click="onCancel" class=" cursor-pointer opacity-40 hover:opacity-100 p-1">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </SecondaryButton>
                <SecondaryButton @click="onSave" class="cursor-pointer opacity-40 hover:opacity-100">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </SecondaryButton>
            </div>
            <div v-else
                class="absolute transition-opacity  opacity-0 group-hover:opacity-60 p-1 w-full font-bold flex items-center justify-center bg-gray-500 rounded-md text-xs z-20 bottom-0">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-6 text-white">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                </svg>
                <input type="file" class="absolute inset-0 cursor-pointer opacity-0" @change="onChange">
            </div>
        </template>
    </div>
</template>


<script>
import { CheckCircleIcon, CheckIcon, XMarkIcon } from '@heroicons/vue/20/solid';

import { PhotoIcon } from '@heroicons/vue/20/solid';

import { ref } from 'vue';

import SecondaryButton from '@/Components/SecondaryButton.vue';
import emitter from '@/lib/eventBus';
import axiosClient from '@/lib/axiosClient'

export default {
    props: ['group', 'can'],
    components: {
        SecondaryButton
    },
    setup(props) {
        const defaultImage = '/images/monir.jpeg';
        const imageSrc = ref(null);
        const isChanging = ref(false);
        const selectedImage = ref(null);

        const onChange = (event) => {
            const file = event.target.files[0];
            if (file) {
                if (!file.type.startsWith('image/')) {
                    alert('Please select an image file.');
                    return;
                }

                if (file.size > 2 * 1024 * 1024) { // 2MB limit
                    alert('File size exceeds 2MB.');
                    return;
                }

                selectedImage.value = file;

                const reader = new FileReader();
                reader.onload = (e) => {
                    imageSrc.value = e.target.result;
                    isChanging.value = true;
                }
                reader.readAsDataURL(file);
            }
        };

        const onCancel = () => {
            isChanging.value = false;
            imageSrc.value = props.group.avatar_url || defaultImage;
        }

        const onSave = async () => {
            if (!selectedImage.value) {
                alert('Please select an image file first.');
                return;
            }
            const formData = new FormData();
            formData.append('avatar', selectedImage.value);
            axiosClient.post(route('groups.image.store', props.group.id), formData).then(response => {
                isChanging.value = false;
                emitter.emit('show-toast', {
                    message: 'Avatar updated successfully',
                    type: 'success'
                });
            }).catch(error => {
                emitter.emit('show-toast', {
                    message: 'Error uploading file',
                    type: 'error'
                });
            });
        }
        return {
            defaultImage,
            imageSrc,
            isChanging,
            selectedImage,
            onChange,
            onCancel,
            onSave,
        }

    },

}


</script>
