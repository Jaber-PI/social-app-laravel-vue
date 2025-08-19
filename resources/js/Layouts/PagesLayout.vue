<script setup>
import AuthenticatedLayout from './AuthenticatedLayout.vue';
import GroupList from '@/Components/app/GroupList.vue';

import FriendList from '@/Components/app/FriendList.vue';

import { Head, Link } from '@inertiajs/vue3';

</script>

<template>
    <Head title="Home" />
    <AuthenticatedLayout>
        <div class="container pt-20 mx-auto h-full">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 h-full overflow-auto">
                <!-- Left Sidebar -->
                <aside class="hidden md:flex flex-col gap-2">
                    <div class=" bg-white rounded-lg shadow max-h-[440px] overflow-auto">
                        <GroupList />
                    </div>
                    <div class=" bg-white rounded-lg shadow">
                        <FriendList />
                    </div>
                </aside>
                <!-- Main Feed -->
                <main class="col-span-1 md:col-span-2 overflow-auto  scroll-smooth pb-3 px-2">
                    <slot />
                </main>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
