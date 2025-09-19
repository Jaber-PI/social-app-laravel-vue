<script setup>
import { toRef } from 'vue';
import PostList from '@/Components/app/PostList.vue';
import { useGroupPermissions } from '@/composables/useGroupPermissions';
import { ref } from 'vue';

const props = defineProps({
    user: {
        type: Object,
        required: true
    }
});

const canSeeContent = ref(true);

</script>

<template>
    <div class="posts-tab">
        <!-- Posts List for accessible groups -->

        <PostList
            v-if="canSeeContent"
            :canCreate="false"
            :profile-id="user.id"
        />

        <!-- Private group message -->
        <div v-else
            class="text-center p-5 bg-red-500 text-white rounded-md"
        >
            Follow this user to see their posts.
        </div>
    </div>
</template>

