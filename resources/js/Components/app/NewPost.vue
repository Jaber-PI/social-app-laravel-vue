<script setup>
import { ref } from "vue";

import { VideoCameraIcon, CameraIcon,FaceSmileIcon } from "@heroicons/vue/24/solid";
import { usePage } from "@inertiajs/vue3";
// import { SmileFace } from "@heroicons/vue/24/solid";

const page = usePage();

const user = page.props.auth.user;

const filepickerRef = ref(null);
const imageToPost = ref(null);
const message = ref("");

const onSubmit = () => {
    return;
};
const addImageToPost = (e) => {
    return;
};
const removeImage = () => {
    return;
};

</script>

<template>
    <div class="bg-white p-2 rounded-2xl shadow-md text-gray-500 font-medium ">

        <h2 class="text-xl font-semibold mb-2">Add New Post</h2>

        <div class="flex items-center space-x-4 p-2">
            <img :src="user?.photo" :alt="user?.name" class="h-10 w-10 rounded-full object-cover icon"
                loading="lazy" />
            <form @submit.prevent="onSubmit" class="flex flex-1">
                <input type="text" :placeholder="`what's on your mind, ${user?.name}?`" class="
              rounded-full
              h-12
              bg-gray-100
              flex-grow
              px-5
              focus:outline-none
            " v-model.trim="message" />
            </form>
        </div>
        <div class="flex justify-evenly p-3 border-t">
            <div class="inputIcon">
                <VideoCameraIcon class="h-7 text-red-500" />
                <p class="text-xs sm:text-sm xl:text-base">Live Video</p>
            </div>
            <div @click="$refs.filepickerRef.click()" class="inputIcon">
                <CameraIcon class="h-7 text-green-400" />
                <p class="text-xs sm:text-sm xl:text-base">Photo/Video</p>
                <input ref="filepickerRef" type="file" accept="image/png, image/gif, image/jpeg" class="hidden"
                    @change="addImageToPost($event)" />
            </div>
            <div class="inputIcon">
                <FaceSmileIcon class="h-7 text-yellow-300" />
                <p class="text-xs sm:text-sm xl:text-base">Feeling/Activity</p>
            </div>
            <div
            class="
            flex flex-col
            filter
            hover:brightness-110
            transition
            duration-150
            transform
            hover:scale-105
            cursor-pointer
          "
          v-if="imageToPost" @click="removeImage">
                <img :src="imageToPost" class="h-10 object-contain" />
                <p class="text-xs text-red-500 text-center">Remove</p>
            </div>
        </div>
    </div>
</template>

<style lang="scss" scoped></style>
