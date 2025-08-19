<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import GroupHeader from '@/Components/app/GroupHeader.vue';
import GroupSidebar from '@/Components/app/GroupSidebar.vue';
import PostList from '@/Components/app/PostList.vue';

const props = defineProps({
    group: Object,
});
</script>

<template>

    <Head :title="group.name" />
    <AuthenticatedLayout>
        <div class="container pt-20 mx-auto h-full">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 h-full">
                <!-- Left Sidebar -->
                <aside class="hidden md:flex flex-col gap-2">
                    <GroupSidebar :group="group" />
                </aside>

                <!-- Main Content -->
                <main class="col-span-1 md:col-span-2 overflow-auto scroll-smooth pb-3 px-2">
                    <!-- Group Banner + Info -->
                    <GroupHeader :group="group" />
                    <!-- Group posts -->
                    <PostList :canCreate="group.is_member || false" :groupId="group.id" />
                </main>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
