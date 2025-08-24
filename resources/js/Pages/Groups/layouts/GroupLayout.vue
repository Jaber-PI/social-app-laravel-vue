<script setup>

</script>

<template>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4  h-full overflow-auto scroll-smooth ">
        <!-- Left Sidebar -->
        <aside class="hidden md:flex flex-col gap-2">
            <!-- Future sidebar content -->
        </aside>

        <main class="col-span-1 md:col-span-2 pb-3 px-2 ">
            <slot />
        </main>
    </div>
</template>
