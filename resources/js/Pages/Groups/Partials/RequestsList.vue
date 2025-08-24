<script setup>
import { ref } from "vue";
import TextInput from "@/Components/TextInput.vue";

import { router } from "@inertiajs/vue3";

const props = defineProps(['requests','groupId']);

const searchKey = ref('');


function approve(requestId) {
    router.post(route('groups.approve', [props.groupId, requestId]), {}, {
        preserveScroll: true,
    })
}

function reject(requestId) {
    router.post(route('groups.reject', [props.groupId, requestId]), {}, {
        preserveScroll: true,
    })
}

</script>

<template>

    <div>
        <div v-if="requests.length" class="flex flex-col gap-2 max-h-[200px] overflow-auto p-2 border ">
            <!-- <div v-for="group in []" :key="group.id" -->
            <div class="search mb-2 ">
                <TextInput :model-value="searchKey" class="w-full" placeholder="Search for a Member" />
            </div>
            <div>
                <div v-for="req in requests" :key="req.id">
                    <div
                        class="flex items-center gap-3 px-2 py-1 bg-gray-100 hover:bg-gray-50 cursor-pointer rounded-md">
                        <div class="flex">
                            <img :src="req.user.avatar_url" alt="" class="w-7 h-7 sm:w-9 sm:h-9 rounded-full">
                        </div>
                        <div class="p-2 flex flex-1 justify-between items-center">
                            <div class="font-bold text-l sm:text-xl">
                                <p>{{ req.user.name }}</p>
                            </div>
                            <div class="flex space-x-2">
                                <!-- Approve -->
                                <button @click="approve(req.id)"
                                    class="w-10 h-10 flex items-center justify-center rounded-full bg-green-500 hover:bg-green-600 text-white shadow transition"
                                    title="Approve">
                                    ✓
                                </button>

                                <!-- Reject -->
                                <button @click="reject(req.id)"
                                    class="w-10 h-10 flex items-center justify-center rounded-full bg-red-500 hover:bg-red-600 text-white shadow transition"
                                    title="Reject">
                                    ✕
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div v-else class="text-gray-400 flex text-center py-4">
            No Pending Requests
        </div>
    </div>
</template>
