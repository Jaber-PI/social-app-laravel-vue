<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import GroupList from '@/Components/app/GroupList.vue';

import { Head, Link } from '@inertiajs/vue3';
import FriendList from '@/Components/app/FriendList.vue';
import PostList from '@/Components/app/PostList.vue';

import NewPost from '@/Components/app/NewPost.vue';

const props = defineProps({
    posts: Object,
});

const posts2 = [
    {
        id: 1,
        author: {
            name: "Jaber",
            image: 'images/monir.jpeg',
            email: "jabe@er",
        },
        body: "hello There, Lorem Ipsum, bla bla, hello There, Lorem Ipsum, bla bla , hello There, Lorem Ipsum, bla bla",
        createdAt: "22/02/2024",
        group: {
            id: 1,
            name: "LaraCast Forum"
        },
        attachments: [
            {
                id: 1,
                name: 'test1.png',
                url: 'https://picsum.photos/1000',
                mime: 'image/png'
            },
            {
                id: 2,
                name: 'test2.png',
                url: 'https://picsum.photos/1000',
                mime: 'image/png'
            },
            {
                id: 3,
                name: 'test3.png',
                url: 'https://picsum.photos/1000',
                mime: 'image/png'
            }, {
                id: 4,
                name: 'test4.png',
                url: '.',
                mime: 'application/msdoc'
            },
        ]
    },
    {
        id: 2,
        author: {
            name: "Jaber",
            image: 'images/monir.jpeg',
            email: "jabe@er",
        },
        body: "hello There, Lorem Ipsum, bla bla, hello There, Lorem Ipsum, bla bla , hello There, Lorem Ipsum, bla bla",
        createdAt: "22/02/2024",

    },
    {
        id: 3,
        author: {
            name: "Jaber",
            image: 'images/monir.jpeg',
            email: "jabe@er",
        },
        body: "hello There, Lorem Ipsum, bla bla, hello There, Lorem Ipsum, bla bla , hello There, Lorem Ipsum, bla bla",
        createdAt: "22/02/2024",
        attachments: [
            {
                id: 1,
                name: 'test1.png',
                url: "images/post1.png",
                mime: 'image/png'
            },
        ]
    },
]

</script>

<template>
    <Head title="Home" />
    <AuthenticatedLayout>
        <div class="container pt-16 mx-auto h-full py-1">
            <div class=" px-3 py-4 h-full">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 h-full  overflow-auto">
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
                    <main class="col-span-1 md:col-span-2 overflow-auto h-full scroll-smooth px-2">
                        <!-- More posts can be added here -->
                        <div class="">
                            <div class="mb-3">
                                <NewPost />
                            </div>
                            <PostList :posts="posts.data" />
                            <!-- <PostList :posts="posts2" /> -->
                        </div>
                    </main>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
