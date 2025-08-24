<script setup>
import { toRef } from 'vue';
import PostList from '@/Components/app/PostList.vue';
import { useGroupPermissions } from '@/composables/useGroupPermissions';

const props = defineProps({
    group: {
        type: Object,
        required: true
    }
});

const groupRef = toRef(props, 'group');
const { canSeeContent, canPost } = useGroupPermissions(groupRef);

</script>

<template>
    <div class="posts-tab">
        <!-- Posts List for accessible groups -->
        <PostList
            v-if="canSeeContent"
            :canCreate="canPost"
            :groupId="group.id"
        />

        <!-- Private group message -->
        <div
            v-else
            class="text-center p-5 bg-red-500 text-white rounded-md"
        >
            This group is private
        </div>
    </div>
</template>

