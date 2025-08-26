<script setup>

import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue'
import { ChevronDownIcon } from '@heroicons/vue/24/solid'
import { useForm } from '@inertiajs/vue3'

import { formatDate } from '@/helpers'

import Comments from './Comments.vue'

import { computed, ref } from 'vue'

import axiosClient from "@/lib/axiosClient";
import NewComment from './NewComment.vue'
import emitter from '@/lib/eventBus'


const props = defineProps({
    comment: Object
})

const modified = computed(() => {
    return props.comment.updated_at !== props.comment.created_at
})

const defaultAvatar = 'https://ui-avatars.com/api/?name=User&background=random'



const emit = defineEmits(['update', 'delete']);

const onEditing = ref(false);

function editClick() {
    onEditing.value = true;
}
const editedComment = ref({
    body: props.comment.body
})
const submitting = ref(false)

const updateComment = async () => {
    if (!editedComment.value.body.trim()) return
    submitting.value = true
    try {
        const response = await axios.put(route('comments.update', props.comment.id), {
            body: editedComment.value.body
        });
        emit('update', response.data
        );

        emitter.emit('show-toast', {
            message: 'Comment updated successfully.',
            type: 'success'
        });

        onEditing.value = false
    } catch (error) {
        emitter.emit('show-toast', {
            message: error.response.data.message,
            type: 'error'
        });
    } finally {
        submitting.value = false
    }
}

function deleteClick() {
    if (confirm('Are you sure you want to delete this comment?')) {
        axios.delete(route('comments.delete', props.comment.id))
            .then(() => {
                emit('delete', props.comment.id)
                emitter.emit('show-toast', {
                    message: 'Comment deleted successfully.',
                    type: 'success'
                });
            })
            .catch(error => {
                emitter.emit('show-toast', {
                    message: error.response.data.message,
                    type: 'error'
                });
            })
    }
}

const reacted = ref(props.comment.reacted_by_user || false)
const reactionsCount = ref(props.comment.reactions_count || 0)

async function react() {
    try {
        const response = await axiosClient.post(route('comments.react', props.comment.id))
        reacted.value = response.data.reacted
        reactionsCount.value = response.data.reactions_count
    } catch (error) {
        console.error('Error toggling like:', error)
    }
}

const showReply = ref(false)
function toggleReply() {
    showReply.value = !showReply.value
}

const repliesCount = ref(props.comment.comments_count || 0)
const replies = ref(props.comment.comments || [])
const showReplies = ref(false)
const repliesLoaded = ref(false)

async function toggleReplies() {
    if (repliesLoaded.value) {
        showReplies.value = !showReplies.value
    } else {
        await loadReplies();
        showReplies.value = !showReplies.value
    }
}

const loadReplies = async () => {
    const response = await axios.get(route('comments.index'), {
        params: {
            commentable_id: props.comment.id,
            commentable_type: 'App\\Models\\Comment'
        }
    });
    if (response.status !== 200) {
        return;
    }
    replies.value = response.data
    repliesLoaded.value = true
}
const addReply = (reply) => {
    replies.value.unshift(reply)
    repliesCount.value++
    showReply.value = !showReply.value

}

const canDelete = ref(props.comment.can.delete || false)
const canUpdate = ref(props.comment.can.update || false)
const canHide = ref(props.comment.can.hide || false)

</script>

<template>
    <div class="flex relative gap-3 items-start bg-white p-2 pr-10 rounded-xl shadow-sm border ">
        <!-- dropdown menu  -->
        <Menu as="div" class="absolute right-1 top-1 text-left z-20">
            <div>
                <MenuButton
                    class="inline-flex w-full justify-center rounded-md  px-4 py-2 text-sm font-medium text-black  focus:outline-none focus-visible:ring-2 focus-visible:ring-white/75">
                    <ChevronDownIcon class="-mr-1 ml-2 h-5 w-5 text-gray-400 hover:text-black" aria-hidden="true" />
                </MenuButton>
            </div>

            <transition enter-active-class="transition duration-100 ease-out"
                enter-from-class="transform scale-95 opacity-0" enter-to-class="transform scale-100 opacity-100"
                leave-active-class="transition duration-75 ease-in" leave-from-class="transform scale-100 opacity-100"
                leave-to-class="transform scale-95 opacity-0">
                <MenuItems
                    class="absolute right-0  w-40 origin-top-right divide-y divide-gray-100 rounded-md bg-white shadow-lg ring-1 ring-black/5 focus:outline-none">
                    <div class="px-1 py-1" v-if="canUpdate">
                        <MenuItem v-slot="{ active }">
                        <button :class="[
                            active ? 'bg-gray-400 text-white' : 'text-gray-900',
                            'group flex w-full items-center rounded-md px-1 py-1 text-xs',
                        ]" @click="editClick">
                            Edit
                        </button>
                        </MenuItem>
                    </div>
                    <div class="px-1 py-1" v-if="canDelete">
                        <MenuItem v-slot="{ active }">
                        <button :class="[
                            active ? 'bg-gray-400 text-white' : 'text-gray-900',
                            'group flex w-full items-center rounded-md px-1 py-1 text-xs',
                        ]" @click="deleteClick">
                            Delete
                        </button>
                        </MenuItem>
                    </div>
                    <div class="px-1 py-1" v-if="canHide">
                        <MenuItem v-slot="{ active }">
                        <button :class="[
                            active ? 'bg-gray-400 text-white' : 'text-gray-900',
                            'group flex w-full items-center rounded-md px-1 py-1 text-xs',
                        ]" @click="hideClick">
                            Hide
                        </button>
                        </MenuItem>
                    </div>
                </MenuItems>
            </transition>
        </Menu>

        <!-- User avatar -->
        <img :src="comment.user.avatar_url || defaultAvatar" alt="avatar" class="w-10 h-10 rounded-full object-cover" />

        <!-- Comment content -->
        <div class="flex-1">
            <div class="flex justify-between items-center mb-1">
                <h4 class="font-semibold text-sm text-gray-800">
                    {{ comment.user.name }}
                </h4>

                <p class="text-xs text-gray-500">
                    {{ formatDate(comment.updated_at) }}
                    <span class="text-xs text-gray-400" v-if="modified">(edited)</span>
                </p>

            </div>
            <p v-if="!onEditing" class="text-sm text-gray-700 leading-snug whitespace-pre-line">
                {{ comment.body }}
            </p>
            <div v-else>
                <textarea :disabled="submitting" class="w-full p-2 border rounded-md" v-model="editedComment.body"
                    rows="1"></textarea>
                <div class="mt-2">
                    <button @click="updateComment" class="text-blue-500 hover:underline text-xs">
                        Save
                    </button>
                    <button @click="onEditing = false" class="text-red-500 hover:underline text-xs ml-2">
                        Cancel
                    </button>
                </div>
            </div>
            <!-- Like and Reply buttons -->
            <div class="mt-2 flex items-center gap-2">
                <button @click="react"
                    :class="['text-xs px-2 py-1 rounded', reacted ? 'bg-blue-100 text-blue-600' : 'bg-gray-100 text-gray-600']">
                    <span v-if="reacted">♥</span>
                    <span v-else>♡</span>
                    Like
                </button>
                <span class="text-xs text-gray-500">{{ reactionsCount }}</span>
                <button @click="toggleReply"
                    class="text-xs px-2 py-1 rounded bg-gray-100 text-gray-600 hover:bg-gray-200 ml-2">
                    Reply
                </button>
                <span class="text-xs text-gray-500 cursor-pointer px-2 py-1 bg-gray-100 hover:bg-gray-200 rounded"
                    @click="toggleReplies" v-if="repliesCount > 0">{{ repliesCount }}</span>

                <!-- button  to show replies  -->

            </div>
            <div v-if="showReply" class="mt-2">
                <NewComment :commentable-id="comment.id" :commentable-type="'App\\Models\\Comment'"
                    @submitted="addReply" />
            </div>

            <!-- comment replies  -->
            <div v-if="showReplies">
                <Comments :comments="replies" :comments-count="repliesCount" class="mt-2" />
            </div>

        </div>
    </div>
</template>
