<template>
  <div class="p-1 rounded-l">
    <form @submit.prevent="submitComment">
      <textarea
        id="comment"
        v-model="newComment"
        rows="2"
        placeholder="Write your comment here..."
        class="w-full p-3 border bg-gray-100 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400"
      ></textarea>

      <button
        type="submit"
        class="mt-3 inline-flex items-center px-4 py-2 bg-blue-600 text-white font-semibold rounded-md hover:bg-blue-700 transition disabled:opacity-50"
        :disabled="submitting"
      >
        {{ submitting ? 'Posting...' : 'Post Comment' }}
      </button>
    </form>
  </div>
</template>


<script setup>
import { ref} from 'vue'
import axios from 'axios'

const props = defineProps({
  postId: Number
})

const newComment = ref('')
const submitting = ref(false)

const emit = defineEmits(['comment-posted'])

const submitComment = async () => {
  if (!newComment.value.trim()) return

  submitting.value = true
  try {
    const response = await axios.post(`/posts/${props.postId}/comments`, {
      body: newComment.value
    })
    emit('comment-posted', response.data)
    newComment.value = ''
  } catch (error) {
    console.error(error)
  } finally {
    submitting.value = false
  }
}


</script>
