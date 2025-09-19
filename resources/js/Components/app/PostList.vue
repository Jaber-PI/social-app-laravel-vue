<script setup>
import { ref } from 'vue';
import PostItem from './PostItem.vue';
import EditorModal from './EditorModal.vue';
import AttachmentPreviewModal from './AttachmentPreviewModal.vue';
import { useAttachmentsPreviewModal } from '@/composables/useAttachmentsPreviewModal';

import axiosClient from "@/lib/axiosClient";

import { onMounted } from 'vue';
import NewPost from './NewPost.vue';

const props = defineProps({
    groupId: {
        type: Number,
        required: false,
    },
    profileId: {
        type: Number,
        required: false,
    },
    user: {
        type: Object,
        required: false,
    },
    canCreate: {
        type: Boolean,
        required: true
    }
})


const { showPreviewModal, openPreviewModal, attachments, attachmentIndex } = useAttachmentsPreviewModal();


const loading = ref(false);
const noMorePosts = ref(false);
const posts = ref([]);
const cursor = ref(null);


function deletePost(postId) {
    posts.value = posts.value.filter(post => post.id !== postId);
}

function addPost(post) {
    posts.value.unshift(post);
}
function updatePost(post) {

    const index = posts.value.findIndex(p => p.id === post.id);

    if (index !== -1) {
        posts.value[index] = post;
    }

}

const loadPosts = async () => {
    if (loading.value || noMorePosts.value) return;
    loading.value = true;

    let url;

    if (props.groupId) {
        url = route('groups.posts', {
            group: props.groupId,
            cursor: cursor.value || null,
        })
    } else if (props.profileId) {
        url = route('profile.posts', {
            user: props.profileId,
            cursor: cursor.value || null,
        })
    } else {
        url = route('posts.index', {
            cursor: cursor.value || null
        })
    }

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
        <NewPost v-if="canCreate" @postCreated="addPost" :group-id="groupId" />
    </div>

    <AttachmentPreviewModal v-if="showPreviewModal" :attachmentIndex="attachmentIndex" :attachments="attachments"
        @closed="showPreviewModal = false" />

    <div class="posts flex flex-col gap-2 px-1">
        <PostItem v-for="post in posts" @previewAttachment="openPreviewModal" :key="post.id" :post="post"
            @post-deleted="deletePost" @post-upated="updatePost" />
    </div>

    <div v-if="loading" class="text-center py-4">Loading...</div>
    <div v-if="noMorePosts" class="text-center py-4">No more posts</div>

    <!-- Trigger element for IntersectionObserver -->
    <div id="loadMoreTrigger" style="height: 1px;"></div>
</template>
