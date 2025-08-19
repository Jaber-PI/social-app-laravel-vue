<script setup>

import GroupItem from './GroupItem.vue';
import PrimaryButton from '../PrimaryButton.vue';
import GroupInfoModal from './GroupInfoModal.vue';

import TextInput from '../TextInput.vue';
import { ref } from 'vue';

const props = defineProps({
    groups: {
        required: false
    }
});

const searchKey = ref('');

const showGroupModal = ref(false);

function createGroup() {
    showGroupModal.value = true;
}

function handleGroupSaved(group) {
    // Refresh group list or append new group
    showGroupModal.value = false;
    localGroups.value.unshift(group);
}

const localGroups = ref(props.groups || []);

async function loadGroups() {
    // Fetch groups from the API
    axios.get('/groups')
        .then(response => {
            localGroups.value = response.data;
        })
        .catch(error => {
            console.error('Error loading groups:', error);
        });
}

</script>

<template>
    <div class="p-4">
        <div class="flex p-2">
            <h2 class="text-xl font-semibold mb-2 hidden sm:block ">My Groups</h2>
            <primary-button class="ml-auto" @click="createGroup">
                Create Group
            </primary-button>
            <!-- create group modal  -->
            <GroupInfoModal v-if="showGroupModal" @closed="showGroupModal = false" @saved="handleGroupSaved" />
        </div>
        <div class="search mb-2 ">
            <TextInput :model-value="searchKey" class="w-full" placeholder="Search for a Group" />
        </div>
        <div v-if="localGroups.length" class="flex flex-col gap-2 p-2 border">
            <GroupItem v-for="group in localGroups" :key="group.id" :group="group"/>
        </div>

        <div v-else class="text-gray-400 flex text-center py-8">
            no groups for you
        </div>

    </div>
</template>
