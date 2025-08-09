<script setup>

import { computed, ref } from 'vue';
import CommentItem from './CommentItem.vue'

import axios from 'axios';

import axiosClient from '@/lib/axiosClient';

const props = defineProps({
    comments: Array,
    commentsCount: Number
})

const newCommentsCount = ref(props.commentsCount)

const localComments = ref(props.comments || [])

const updateComment = (comment) => {
    const index = localComments.value.findIndex(c => c.id === comment.id);
    if (index !== -1) {
        localComments.value[index].body = comment.body;
    }
}

const deleteComment = (id) => {
    localComments.value = localComments.value.filter(comment => comment.id !== id)
    newCommentsCount.value--
}

</script>

<template>
    <div class="space-y-2">
        <div class="" v-for="comment in localComments" :key="comment.id">
            <CommentItem :comment="comment" @delete="deleteComment" @update="updateComment" />
        </div>
    </div>
</template>
