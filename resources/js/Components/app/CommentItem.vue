<script setup>

import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue'
import { ChevronDownIcon } from '@heroicons/vue/24/solid'
import { useForm } from '@inertiajs/vue3'

import { formatDistanceToNow } from 'date-fns'
import { computed, ref } from 'vue'

const props = defineProps({
    comment: Object
})

const modified = computed(() => {
    return props.comment.updated_at !== props.comment.created_at
})

const defaultAvatar = 'https://ui-avatars.com/api/?name=User&background=random'

const formatDate = (iso) => {
    return formatDistanceToNow(new Date(iso), { addSuffix: true })
}

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
        })
        emit('update', response.data)
        onEditing.value = false
    } catch (error) {
        console.error(error)
    } finally {
        submitting.value = false
    }
}

function deleteClick() {
    if (confirm('Are you sure you want to delete this comment?')) {
        axios.delete(route('comments.delete', props.comment.id))
            .then(() => {
                emit('delete', props.comment.id)
            })
            .catch(error => {
                console.error('Error deleting comment:', error)
            })
    }
}

const comment = props.comment   // The comment object passed from the parent component
const canUpdate = comment.can.update || false
const canDelete = comment.can.delete || false
const canHide = comment.can.hide || true   // Default values in case the properties are not provided

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
            <p v-if="!onEditing" class="text-sm text-gray-700 leading-snug">
                {{ comment.body }}
            </p>
            <div v-else>
                <textarea :disabled="submitting" class="w-full p-2 border rounded-md" v-model="editedComment.body"
                    rows="3"></textarea>
                <div class="mt-2">
                    <button @click="updateComment" class="text-blue-500 hover:underline text-xs">
                        Save
                    </button>
                    <button @click="onEditing = false" class="text-red-500 hover:underline text-xs ml-2">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
