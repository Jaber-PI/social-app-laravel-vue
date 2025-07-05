<script setup>
import { ChatBubbleOvalLeftEllipsisIcon, HandThumbUpIcon, ShareIcon } from "@heroicons/vue/24/solid";

import { Menu, MenuButton, MenuItems, MenuItem } from '@headlessui/vue'
import { ChevronDownIcon } from '@heroicons/vue/20/solid'

import { ref } from "vue";
import { router } from "@inertiajs/vue3";

import { isImage } from "@/helpers";

const props = defineProps({
    post: Object
})

const readMore = ref(false);

const toggleReadMore = () => {
    readMore.value = !readMore.value;
}


const emit = defineEmits(['editClick', 'previewAttachment']);


const editClick = () => {
    emit('editClick', props.post);
}


function deletePost() {
    if (window.confirm("Are You sure to Delete this Post?")) {
        router.delete(route('post.delete', props.post.id), {
            preserveScroll: true
        });
    }
}
function downloadFile(id) {
    window.open(route('attachments.download', id), '_blank');
}

function previewAttachment(ind) {
    emit('previewAttachment', props.post.attachments, ind)
}
</script>

<template>
    <div class="flex relative flex-col bg-white p-2 rounded-2xl shadow-sm">

        <!-- dropdown menu  -->
        <Menu as="div" class="absolute top-4 right-4 inline-block text-left z-10">
            <div>
                <MenuButton
                    class="inline-flex w-full justify-center rounded-md  px-4 py-2 text-sm font-medium text-black  focus:outline-none focus-visible:ring-2 focus-visible:ring-white/75">
                    <ChevronDownIcon class="-mr-1 ml-2 h-5 w-5 text-gray-400 hover:text-black" aria-hidden="true" />
                </MenuButton>
            </div>

            <transition enter-active-class="transition duration-100 ease-out"
                enter-from-class="transform scale-95 opacity-0" enter-to-class="transform scale-100 opacity-100"
                leave-active-class="transition duration-75 ease-in" leave-from-class="transform scale-100 opacity-100"
                leave-to-class="transform scale-95 opacity-0">
                <MenuItems
                    class="absolute right-0  w-40 origin-top-right divide-y divide-gray-100 rounded-md bg-white shadow-lg ring-1 ring-black/5 focus:outline-none">
                    <div class="px-1 py-1">
                        <MenuItem>
                        <button class='group cursor-pointer flex w-full items-center rounded-md px-1 py-1 text-xs' @click="editClick">
                            Edit
                        </button>
                        </MenuItem>
                    </div>
                    <div class="px-1 py-1">
                        <MenuItem v-slot="{ active }">
                        <button :class="[
                            active ? 'bg-gray-400 text-white' : 'text-gray-900',
                            'group flex w-full items-center rounded-md px-1 py-1 text-xs',
                        ]" @click="deletePost">
                            Delete
                        </button>
                        </MenuItem>
                    </div>
                </MenuItems>
            </transition>
        </Menu>


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
                <div v-if="readMore" :class="{ readLess: readMore == true }" class="description-info">
                    <div class="pt-2 ck-content" v-html="post.body"></div>
                </div>
                <div v-else class="description-info">
                    <div class="pt-2 ck-content" v-html="post.body.slice(0, 150)"></div>
                </div>
                <div v-if="post.body.length > 60" class="readMore text-blue-500 flex justify-end">
                    <button @click="toggleReadMore">{{ readMore ? "Read Less" : "Read More" }}</button>
                </div>
            </div>
        </div>

        <!-- attachments section  -->
        <div v-if="post.attachments"
            class="relative w-full rounded-t-2xl grid grid-cols-[repeat(auto-fit,minmax(150px,1fr))] gap-1">
            <div v-for="(attachment, ind) of post.attachments.slice(0, 4)" :key="attachment.id"
                @click="previewAttachment(ind)" class="group overflow-hidden relative aspect-square">
                <!-- download button  -->
                <button @click="downloadFile(attachment.id)"
                    class=" absolute right-1 cursor-pointer bottom-1 z-10 text-white p-1 bg-blue-400 rounded-full transition-all opacity-0 group-hover:opacity-100">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-4">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                    </svg>
                </button>

                <div v-if="ind == 3 && post.attachments.length > 4"
                    class="absolute inset-0 z-10 bg-black opacity-60 text-center flex justify-center items-center text-xl text-white font-bold">
                    <div class="text-white">
                        And {{ post.attachments.length - 3 }} Files More.
                    </div>
                </div>

                <img v-if="isImage(attachment)" :src="attachment.url" class="object-cover h-full w-full"
                    loading="lazy" />

                <div v-else class="grid place-content-center bg-blue-100 aspect-square">
                    <p class="text-xl text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-9">
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

        <!-- Post Footer -->
        <div class="flex justify-evenly items-center rounded-b-2xl  shadow-md text-gray-400  border-t">
            <button
                class="inputIcon flex justify-center items-center px-5 py-3 rounded-none rounded-bl-2xl hover:text-gray-800 cursor-pointer">
                <HandThumbUpIcon class="h-5" />
                <p class="ms-4 text-xs sm:text-base">Like</p>
            </button>

            <button
                class="inputIcon flex justify-center items-center px-5 py-3 rounded-none rounded-bl-2xl hover:text-gray-800 cursor-pointer">
                <ChatBubbleOvalLeftEllipsisIcon class="h-5" />
                <p class="ms-4  text-xs sm:text-base">Comment</p>
            </button>

            <button
                class="inputIcon flex justify-center items-center px-5 py-3 rounded-none rounded-bl-2xl hover:text-gray-800 cursor-pointer">
                <ShareIcon class="h-5" />
                <p class="ms-4  text-xs sm:text-base">Share</p>
            </button>
        </div>
    </div>
</template>
