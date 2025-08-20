<script setup>
import { ref } from 'vue';
import PostItem from './PostItem.vue';
import EditorModal from './EditorModal.vue';
import AttachmentPreviewModal from './AttachmentPreviewModal.vue';

import axiosClient from "@/lib/axiosClient";

import { onMounted } from 'vue';
import NewPost from './NewPost.vue';

const props = defineProps({
    groupId: {
        type: Number,
        required: false,
    },
    canCreate: {
        type: Boolean,
        required: true
    }
})


const editPost = ref({});
const showEditModal = ref(false);

const showPreviewModal = ref(false);

const attachments = ref([]);
const attachmentIndex = ref(0)

const openPreviewModal = (postAttachments, index) => {
    showPreviewModal.value = true;
    attachments.value = postAttachments;
    attachmentIndex.value = index;
    return;
}

const openEditModal = (post) => {
    editPost.value = post;
    showEditModal.value = true;
    return;
}

function updatePost(post) {

    const index = posts.value.findIndex(p => p.id === post.id);
    if (index !== -1) {
        posts.value[index] = post;
    }

}

function deletePost(postId) {
    posts.value = posts.value.filter(post => post.id !== postId);
}

function addPost(post) {
    posts.value.unshift(post);
}

const loading = ref(false);
const noMorePosts = ref(false);
const posts = ref([]);
const cursor = ref(null);


const loadPosts = async () => {
    if (loading.value || noMorePosts.value) return

    loading.value = true
    const url = props.groupId ? route('groups.posts', props.groupId, {
        cursor: cursor.value || null,
    }) : route('posts.index', {
        cursor: cursor.value || null
    })

    try {
        const { data } = await axiosClient.get(url)
        posts.value.push(...data.data)

        if (data.links.next) {
            const urlParams = new URL(data.links.next).searchParams
            cursor.value = urlParams.get('cursor')
        } else {
            noMorePosts.value = true
        }
    } finally {
        loading.value = false
    }
}

onMounted(() => {
    loadPosts()
    const observer = new IntersectionObserver((entries) => {
        if (entries[0].isIntersecting) {
            loadPosts()
        }
    }, { threshold: 1.0 })

    observer.observe(document.querySelector('#loadMoreTrigger'))
})

</script>

<template>

    <div class="mb-3">
        <NewPost v-if="canCreate" @postCreated="addPost" :group-id="groupId"/>
    </div>

    <EditorModal v-if="showEditModal" :post="editPost" v-model="showEditModal" @post-updated="updatePost" />

    <AttachmentPreviewModal v-if="showPreviewModal" :attachmentIndex="attachmentIndex" :attachments="attachments"
        @closed="showPreviewModal = false" />

    <div class="posts flex flex-col gap-2 px-1">
        <PostItem v-for="post in posts" @previewAttachment="openPreviewModal" @editClick="openEditModal" :key="post.id"
            :post="post" @post-deleted="deletePost" />
    </div>
    <div v-if="loading" class="text-center py-4">Loading...</div>
    <div v-if="noMorePosts" class="text-center py-4">No more posts</div>

    <!-- Trigger element for IntersectionObserver -->
    <div id="loadMoreTrigger" style="height: 1px;"></div>
</template>
