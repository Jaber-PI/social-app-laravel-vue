<script setup>
import { onMounted, ref } from 'vue';

const props = defineProps({
    type: {
        type: String,
        default: 'text',
    },
    required: {
        type: Boolean,
        default: false,
    },
    pattern: {
        type: String,
        default: null,
    },
    minlength: {
        type: Number,
        default: null,
    },
    maxlength: {
        type: Number,
        default: null,
    }
});

const model = defineModel({
    type: String,
    required: true,
});

const input = ref(null);

onMounted(() => {
    if (input.value?.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <input :type="type" v-model="model" ref="input"
        class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" :required="required"
        :pattern="pattern" :minlength="minlength" :maxlength="maxlength" />
</template>
