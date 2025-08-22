<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import GroupHeader from '@/Pages/Groups/Partials/GroupHeader.vue';
import GroupSidebar from '@/Pages/Groups/Partials/GroupSidebar.vue';
import PostList from '@/Components/app/PostList.vue';

const props = defineProps({
    group: Object,
    members: Array,
});

</script>

<template>

    <Head :title="group.name" />
    <AuthenticatedLayout>
        <div class="container pt-20 mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 overflow-auto scroll-smooth  h-full">
                <div class="col-span-1 md:col-span-3 rounded-lg shadow ">
                    <GroupHeader :group="group" />
                </div>

                <!-- Left Sidebar -->
                <aside class="hidden md:flex flex-col gap-2">
                    <GroupSidebar :group="group" :members="members" />
                </aside>

                <main class="col-span-1 md:col-span-2 pb-3 px-2">
                    <!-- Group posts -->
                    <PostList v-if="group.can.view || group.is_public" :canCreate="group.can.post || false" :groupId="group.id" />
                    <p v-else class="text-center p-5 bg-red-500 text-white rounded-md">The Group is private</p>
                </main>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
