<script setup>
import { ref } from "vue";
import TextInput from "@/Components/TextInput.vue";
import { router } from "@inertiajs/vue3";

defineProps(['members', 'isAdmin']);

const searchKey = ref('');


function changeRole(userId, role, groupId) {
    router.put(route('groups.changeRole', groupId), { user_id: userId, role }, { preserveScroll: true })
}
function removeMember(userId, groupId) {
    if (!confirm('are you sure')) {
        return;
    }
    router.put(route('groups.remove-member', groupId), { user_id: userId }, { preserveScroll: true })
}
</script>

<template>

    <div>
        <div v-if="members.length" class="flex flex-col gap-2 max-h-[400px] overflow-auto p-2 border ">
            <!-- <div v-for="group in []" :key="group.id" -->
            <div class="search mb-2 ">
                <TextInput :model-value="searchKey" class="w-full" placeholder="Search for a Member" />
            </div>
            <template v-for="member in members" :key="member.id">
                <div class="flex items-center gap-3 px-2 py-1 bg-gray-100 hover:bg-gray-50 cursor-pointer rounded-md">
                    <div class="flex">
                        <img :src="member.avatar_url" alt="" class="w-7 h-7 sm:w-9 sm:h-9 rounded-full">
                    </div>
                    <div class="p-2 flex flex-1 justify-between items-center">
                        <div class="font-bold text-l sm:text-xl">
                            {{ member.isCurrentUser ? 'You' : member.name }}
                        </div>
                        <div class="flex items-center">
                            <button v-if="isAdmin && !member.isCurrentUser"
                                @click="removeMember(member.id, member.group_id)"
                                class="text-red-500 mr-4 hover:text-red-700 transition">
                                Delete
                            </button>
                            <select :disabled="!isAdmin || member.isCurrentUser" v-model="member.role"
                                @change="changeRole(member.id, member.role, member.group_id)"
                                class="border rounded px-2 pr-8  py-1 bg-white text-gray-700 focus:outline-none disabled:border-none disabled:bg-transparent transition">
                                <option disabled value="">Select Role</option>
                                <option value="member">Member</option>
                                <option value="admin">Admin</option>
                            </select>

                        </div>

                    </div>
                </div>
            </template>
        </div>
        <div v-else class="text-gray-400 flex text-center py-4">
            No Friend available
        </div>
    </div>
</template>
