<script setup>

import MainLayout from '@/Layouts/MainLayout.vue';


import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import GroupList from '@/Components/app/GroupList.vue';


import { Head, Link } from '@inertiajs/vue3';
import FriendList from '@/Components/app/FriendList.vue';
import PostList from '@/Components/app/PostList.vue';

import NewPost from '@/Components/app/NewPost.vue';


defineProps({
    auth: Object,
    groups: Object
});
</script>

<template>

    <Head title="Home" />
    <MainLayout>

        <template v-slot:sidebar>
            <div class=" bg-white rounded-lg  overflow-auto">
                <GroupList :groups="groups" />
            </div>
            <div class=" bg-white rounded-lg shadow">
                <FriendList :friends="auth.user.friends" />
            </div>
        </template>


        <!-- Main Feed -->
        <main class="col-span-1 md:col-span-2 overflow-auto  scroll-smooth pb-3 px-2">
            <!-- More posts can be added here -->
            <PostList :canCreate="true" />
        </main>

    </MainLayout>
</template>
