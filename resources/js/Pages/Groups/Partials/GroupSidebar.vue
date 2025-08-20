<script setup>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { capitalize, ref } from 'vue';


const { props } = defineProps(['group', 'members']);


const searchKey = ref('');

</script>



<template>

    <!-- members list  -->
    <div class="p-4 bg-white rounded-lg shadow mb-4">
        <div class="flex justify-between items-center mb-2">
            <h2 class="text-xl font-semibold mb-2 hidden sm:block">Members <span>({{ members.length }})</span>
            </h2>
            <PrimaryButton class="px-4 py-2 bg-blue-600 text-white rounded" v-if="group.current_user.role == 'admin'">
                Invite Member
            </PrimaryButton>
        </div>

        <div class="search mb-2 ">
            <TextInput :model-value="searchKey" class="w-full" placeholder="Search for a Member" />
        </div>

        <div v-if="true" class="flex flex-col gap-2 max-h-[200px] overflow-auto p-2 border ">
            <!-- <div v-for="group in []" :key="group.id" -->
            <template v-for="member in members" :key="member.id">
                <div class="flex items-center gap-3 px-2 py-1 bg-gray-100 hover:bg-gray-50 cursor-pointer rounded-md">
                    <div class="flex">
                        <img :src="member.avatar_url" alt="" class="w-7 h-7 sm:w-9 sm:h-9 rounded-full">
                    </div>
                    <div class="p-2 flex flex-1 justify-between items-center">
                        <div class="font-bold text-l sm:text-xl">
                            {{ member.name }}
                        </div>
                        <div class="text-gray-500">{{ capitalize(member.role) }}</div>
                    </div>
                </div>
            </template>
        </div>

        <div v-else class="text-gray-400 flex text-center py-8">
            No Friend available
        </div>

    </div>
</template>
