<template>
    <div class="p-1 rounded-l">
        <form @submit.prevent="submitComment" class="flex">
            <textarea id="comment" v-model="newComment" rows="1" placeholder="Write your comment here..."
                class="flex-1 p-2 border bg-gray-100 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 resize-none"
                @input="autoResize" ref="textareaRef"></textarea>
            <button type="submit"
                class="ml-2 inline-flex items-center px-4 py-2 bg-blue-600 text-white font-semibold rounded-md hover:bg-blue-700 transition disabled:opacity-50"
                :disabled="submitting || !newComment.trim()">
                {{ submitting ? 'Posting...' : 'Comment' }}
            </button>
        </form>
    </div>
</template>


<script setup>
import { nextTick, ref } from 'vue'
import axios from 'axios'

const props = defineProps({
    commentableId: Number,
    commentableType: String,
})

const newComment = ref('')
const submitting = ref(false)
const textareaRef = ref(null)

const emit = defineEmits(['submitted'])

const submitComment = async () => {
    if (!newComment.value.trim()) return

    submitting.value = true
    try {
        const response = await axios.post(route('comments.store'), {
            body: newComment.value,
            commentable_id: props.commentableId,
            commentable_type: props.commentableType,
        })
        emit('submitted', response.data)
        newComment.value = ''
        await nextTick()
        autoResize()
    } catch (error) {
        console.error(error)
    } finally {
        submitting.value = false
    }
}


const autoResize = () => {
    const el = textareaRef.value
    if (!el) return
    el.style.height = 'auto'
    el.style.height = el.scrollHeight + 'px'
}

</script>
