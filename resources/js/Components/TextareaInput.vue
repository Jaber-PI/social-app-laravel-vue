<script setup>
import { onMounted, onUpdated, ref } from 'vue';

const model = defineModel({
    type: String,
    required: true,
});

const input = ref(null);

onUpdated(() => {
    autoResize();

})

onMounted(() => {
    autoResize();
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });

const autoResize = () => {
    input.value.style.height = 'auto';
    input.value.style.height = input.value.scrollHeight + 'px';
}

</script>

<template>
    <textarea class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" v-model="model"
        ref="input" :style="{ heigth: 'auto' }" @input="autoResize">
    </textarea>
</template>
