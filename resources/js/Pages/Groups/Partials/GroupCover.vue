<script setup>

import SecondaryButton from '@/Components/SecondaryButton.vue';
import { PhotoIcon } from '@heroicons/vue/20/solid';

import { useForm } from '@inertiajs/vue3';
import { defineProps } from 'vue';

import { ref } from 'vue';

const props = defineProps({
    groupId: Number,
    cover_path: String
})

const default_cover = '/storage/groups/covers/default_cover.jpg';

const coverImageSrc = ref(props.cover_path ? `/storage/${props.cover_path}` : default_cover);
const coverIsChanging = ref(false);
const selectedImage = ref(null);


const form = useForm({
    cover: null
})

const onCoverChange = (event) => {

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
            coverImageSrc.value = e.target.result;
            coverIsChanging.value = true;
        }
        reader.readAsDataURL(file);
    }
}
const onCoverCancel = () => {
    coverIsChanging.value = false;
    coverImageSrc.value = props.cover_path || default_cover;
}

const onCoverSave = async () => {
    if (!selectedImage.value) {
        alert('Please select an image file first.');
        return;
    }

    form.cover = selectedImage.value;

    try {
        form.post(route('groups.image.store', props.groupId), {
            preserveScroll: true,
            onSuccess: () => {
                coverIsChanging.value = false;
                form.reset();
            }
        })

    } catch (error) {
        console.error('Error uploading file', error);
    }
}

</script>

<template>
    <div class="relative group w-full h-48 bg-center bg-cover" :style="'background-image: url(' + coverImageSrc + ');'
        ">
        <div v-if="coverIsChanging" class="flex items-center gap-2 absolute z-20 top-4 right-2">
            <SecondaryButton @click="onCoverCancel">
                Cancel
            </SecondaryButton>
            <SecondaryButton @click="onCoverSave">
                Save
            </SecondaryButton>
        </div>
        <span v-else
            class="absolute z-20 transition-opacity opacity-0 group-hover:opacity-100 p-2 font-bold flex items-center bg-gray-50 rounded-md text-xs top-4 right-2">
            <PhotoIcon class="w-5 mr-2" />
            Edit Cover
            <input type="file" class="absolute inset-0 cursor-pointer opacity-0" @change="onCoverChange">
        </span>
    </div>
</template>
