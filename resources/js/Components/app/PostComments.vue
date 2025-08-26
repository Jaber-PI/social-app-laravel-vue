<script setup>

import { computed, ref } from 'vue';
import CommentItem from './CommentItem.vue'

import NewComment from './NewComment.vue';
import axios from 'axios';

import axiosClient from '@/lib/axiosClient';

const props = defineProps({
    postId: Number,
    commentsCount: Number,
    canComment: Boolean
})

const showComments = ref(false)
const newCommentsCount = ref(props.commentsCount)
const commentsLoaded = ref(false)

const comments = ref([])
const toggleCommentsText = computed(() => {
    return showComments.value ? 'Hide Comments' : 'Show Comments'
});

const reversedComments = computed(() => [...comments.value].reverse())


function toggleComments() {
    if (commentsLoaded.value) {
        showComments.value = !showComments.value;
    } else {
        showComments.value = !showComments.value;
        loadComments();
    }
}

const addComment = (comment) => {
    comments.value.unshift(comment)
    newCommentsCount.value++
}

const updateComment = (comment) => {
    const index = comments.value.findIndex(c => c.id === comment.id);
    if (index !== -1) {
        comments.value[index].body = comment.body;
    }
}

const deleteComment = (id) => {
    comments.value = comments.value.filter(comment => comment.id !== id)
    newCommentsCount.value--
}

const loadComments = async () => {
    const response = await axios.get(route('comments.index'), {
        params: {
            commentable_id: props.postId,
            commentable_type: 'App\\Models\\Post'
        }
    });
    if (response.status !== 200) {
        console.error('Failed to load comments:', response.statusText);
        return;
    }
    comments.value = response.data
    commentsLoaded.value = true
}

</script>

<template>
    <div class="space-y-2">
        <NewComment v-if="canComment" :commentable-id="postId" :commentable-type="'App\\Models\\Post'" @submitted="addComment"
            class="mt-2" />
        <button @click="toggleComments" class="text-sm text-gray-700 mt-1 underline text-right">{{ toggleCommentsText }}
            ({{ newCommentsCount
            }})</button>

        <div v-show="showComments" class="px-2 space-y-2">
            <div class="" v-for="comment in reversedComments" :key="comment.id">
                <CommentItem :comment="comment" @delete="deleteComment" @update="updateComment" />
            </div>
        </div>
    </div>
</template>
