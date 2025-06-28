<script setup>
import { ref } from "vue";

import {  usePage } from "@inertiajs/vue3";
import TextareaInput from "../TextareaInput.vue";
import EditorModal from "./EditorModal.vue";
// import { SmileFace } from "@heroicons/vue/24/solid";

const page = usePage();

const user = page.props.auth.user;

const newPost = ref({
    body:'',
    author: {
        name: user.name,
        avatar_url: user.avatar_url
    }
})
const showEditor = ref(false)

function openEditorModal() {
    showEditor.value = true
}

</script>

<template>
    <div class="bg-white p-3 rounded-2xl shadow-md text-gray-500 font-medium ">
        <EditorModal :post="newPost" v-model="showEditor" />

        <h2 class="text-xl font-semibold mb-2">Add New Post</h2>

        <div class="flex items-center space-x-4 p-2">
            <img :src="user?.avatar_url" :alt="user?.name" class="h-9 w-9 rounded-full object-cover icon"
                loading="lazy" />
            <form @submit.prevent="onSubmit" class="flex flex-1">
                <TextareaInput @click="openEditorModal" class="w-full" v-model="newPost.body" rows="1"/>
            </form>
        </div>
    </div>
</template>

<style lang="scss" scoped></style>
