<script setup>

import SecondaryButton from '@/Components/SecondaryButton.vue';
import { PhotoIcon } from '@heroicons/vue/20/solid';
import axios from 'axios';
import { ref } from 'vue';


const props = defineProps({
    profile: {
        type: Object,
    },
    can: {
        type: Object,
    },
})

const defaultCoverImage = '/images/cover.jpeg';

const coverImageSrc = ref(props.profile.cover_url || defaultCoverImage);
const coverIsChanging = ref(false);
const coverIsUpdated = ref(false);
const selectedImage = ref(null);



const onCoverChange = (event) => {
    coverIsUpdated.value = false;

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
    coverImageSrc.value = props.profile.cover_url || defaultCoverImage;
}

const onCoverSave = async () => {
    if (!selectedImage.value) {
        alert('Please select an image file first.');
        return;
    }

    const formData = new FormData();
    formData.append('cover', selectedImage.value);

    try {
        const response = await axios.post(route('profile.image.store', props.profile.id), formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        });

        coverIsChanging.value = false
        coverIsUpdated.value = true;

        props.profile.cover_url = coverImageSrc.value;

    } catch (error) {
        console.error('Error uploading file', error);
    }
}

</script>

<template>
    <div class="group absolute top-0 w-full h-full bg-center bg-cover" :style="'background-image: url(' + coverImageSrc + ');'
        ">
        <span id="blackOverlay" class="w-full h-full absolute opacity-60 bg-black"></span>
        <template v-if="can.edit">

            <div v-if="coverIsChanging" class="flex items-center gap-2 absolute z-20 top-4 right-2">
                <SecondaryButton @click="onCoverCancel">
                    Cancel
                </SecondaryButton>
                <SecondaryButton @click="onCoverSave">
                    Save
                </SecondaryButton>
            </div>
            <span v-else
                class="absolute transition-opacity opacity-0 group-hover:opacity-100 p-2 font-bold flex items-center bg-gray-50 rounded-md text-xs z-20 top-4 right-2">
                <PhotoIcon class="w-5 mr-2" />
                Edit Cover
                <input type="file" class="absolute inset-0 cursor-pointer opacity-0" @change="onCoverChange">
            </span>

        </template>

    </div>
</template>
