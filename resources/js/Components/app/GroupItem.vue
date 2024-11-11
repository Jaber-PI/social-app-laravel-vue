<script setup>

defineProps({
    image: String,
    name: String,
    description: String,
})

</script>

<template>
    <div class="flex items-center gap-3 px-2 py-1 bg-gray-100 hover:bg-gray-50 cursor-pointer rounded-md">

        <div class="flex">
            <img :src="image" alt="" class="w-7 h-7 sm:w-9 sm:h-9 rounded-full">
        </div>
        <div class="p-2 flex-1">
            <div class="font-bold text-l sm:text-xl">
                {{ name }}
            </div>
            <div class="text-xs sm:text-xm">
                {{ description }}
            </div>
        </div>
    </div>
</template>
