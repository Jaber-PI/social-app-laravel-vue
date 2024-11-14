<script setup>

defineProps({
    selected: Boolean,
})

</script>

<template>
    <button :class="[
        ' py-2.5 px-4 text-sm font-medium border-b-4 ',
        selected
            ? 'bg-white text-purple-700  border-b-purple-700'
            : 'text-blue-900 hover:bg-white/[0.12] hover:text-black hover:border-b-purple-300',
    ]">
        <slot />
    </button>
</template>
