<script setup>
import { ChatBubbleOvalLeftEllipsisIcon, HandThumbUpIcon, ShareIcon } from "@heroicons/vue/24/solid";
import { ref } from "vue";

defineProps({
    post: Object
})

const readMore = ref(false);

const toggleReadMore = () => {
    readMore.value = !readMore.value;
}

const isImage = (attachment) => {
    const mime = attachment.mime.split('/');
    return mime[0].toLowerCase() === "image";
}

</script>

<template>
    <div class="flex flex-col bg-white p-2 rounded-2xl shadow-sm">
        <!-- post header  -->
        <div class="p-3 bg-white ">
            <div class="flex items-center space-x-2">
                <img class="rounded-full icon border-2 hover:border-blue-500 transition-all" :src="post.author.image"
                    width="40" height="40" />
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
                    <p v-if="post.createdAt" class="text-xs text-gray-400">
                        {{ new Date(post.createdAt).toLocaleString() }}
                    </p>
                    <p v-else class="text-xs text-gray-400">Loading</p>
                </div>
            </div>

            <div class="wrapper-description">
                <div v-if="readMore" :class="{ readLess: readMore == true }" class="description-info">
                    <p class="pt-4">{{ post.message }}</p>
                </div>
                <div v-else class="description-info">
                    <p class="pt-4">{{ post.message.slice(0, 60) + "..." }}</p>
                </div>

                <div v-if="post.message.length > 60" class="readMore text-blue-500 flex justify-end">
                    <button @click="toggleReadMore">{{ readMore ? "Read Less" : "Read More" }}</button>
                </div>
            </div>
        </div>

        <!-- attachments section  -->
        <div v-if="post.attachments"
            class="relative w-full rounded-t-2xl grid grid-cols-[repeat(auto-fit,minmax(150px,1fr))] gap-1">
            <div v-for="attachment of post.attachments" :key="attachment.id" class="group overflow-hidden relative">
                <button
                    class=" absolute right-1 cursor-pointer bottom-1 z-10 text-white p-1 bg-blue-400 rounded-full transition-all opacity-0 group-hover:opacity-100">

                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-4">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                    </svg>
                </button>

                <img v-if="isImage(attachment)" :src="attachment.url" class="object-cover w-full" loading="lazy" />

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
