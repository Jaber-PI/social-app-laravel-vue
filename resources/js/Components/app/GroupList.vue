<script setup>

import GroupItem from './GroupItem.vue';

import TextInput from '../TextInput.vue';
import { ref } from 'vue';

const serachKey = ref('');

</script>

<template>

    <div class="p-4">
        <h2 class="text-xl font-semibold mb-2 hidden sm:block ">My Groups</h2>
        <div class="search mb-2 ">
            <TextInput :model-value="serachKey" class="w-full" placeholder="Search for a Group" />
        </div>
        <div v-if="true" class="flex flex-col gap-2 max-h-[200px] overflow-auto p-2 border">
            <!-- <div v-for="group in []" :key="group.id" -->
            <GroupItem name="VueJS developers" image="https://placehold.jp/3d4070/ffffff/300x300.png"
                description="Lorem ipsomLorem ipsomLorem ipsomLorem ipsomLorem ipsomLorem ipsomLorem ipsom" />
            <GroupItem name="VueJS developers" image="https://placehold.jp/3d4070/ffffff/300x300.png"
                description="Lorem ipsomLorem ipsomLorem ipsomLorem ipsomLorem ipsomLorem ipsomLorem ipsom" />
            <GroupItem name="VueJS developers" image="https://placehold.jp/3d4070/ffffff/300x300.png"
                description="Lorem ipsomLorem ipsomLorem ipsomLorem ipsomLorem ipsomLorem ipsomLorem ipsom" />
            <GroupItem name="VueJS developers" image="https://placehold.jp/3d4070/ffffff/300x300.png"
                description="Lorem ipsomLorem ipsomLorem ipsomLorem ipsomLorem ipsomLorem ipsomLorem ipsom" />
            <GroupItem name="VueJS developers" image="https://placehold.jp/3d4070/ffffff/300x300.png"
                description="Lorem ipsomLorem ipsomLorem ipsomLorem ipsomLorem ipsomLorem ipsomLorem ipsom" />
            <GroupItem name="VueJS developers" image="https://placehold.jp/3d4070/ffffff/300x300.png"
                description="Lorem ipsomLorem ipsomLorem ipsomLorem ipsomLorem ipsomLorem ipsomLorem ipsom" />
        </div>

        <div v-else class="text-gray-400 flex text-center py-8">
            no groups for you
        </div>

    </div>
</template>
