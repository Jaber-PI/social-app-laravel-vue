<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import GroupList from '@/Components/app/GroupList.vue';

import { Head, Link } from '@inertiajs/vue3';
import FriendList from '@/Components/app/FriendList.vue';
import PostList from '@/Components/app/PostList.vue';

const posts = [
    {
        id: 1,
        author: {
            name: "Jaber",
            image: 'images/monir.jpeg',
            email: "jabe@er",
        },
        message: "hello There, Lorem Ipsum, bla bla, hello There, Lorem Ipsum, bla bla , hello There, Lorem Ipsum, bla bla",
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
        message: "hello There, Lorem Ipsum, bla bla, hello There, Lorem Ipsum, bla bla , hello There, Lorem Ipsum, bla bla",
        createdAt: "22/02/2024",

    },
    {
        id: 3,
        author: {
            name: "Jaber",
            image: 'images/monir.jpeg',
            email: "jabe@er",
        },
        message: "hello There, Lorem Ipsum, bla bla, hello There, Lorem Ipsum, bla bla , hello There, Lorem Ipsum, bla bla",
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



function handleImageError() {
    document.getElementById('screenshot-container')?.classList.add('!hidden');
    document.getElementById('docs-card')?.classList.add('!row-span-1');
    document.getElementById('docs-card-content')?.classList.add('!flex-row');
    document.getElementById('background')?.classList.add('!hidden');
}
</script>

<template>

    <Head title="Home" />

    <AuthenticatedLayout>
        <div class="container mx-auto h-full py-1">
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
                    <main class="col-span-1 md:col-span-2 overflow-auto h-full scroll-smooth">
                        <!-- More posts can be added here -->
                        <PostList :posts="posts" />
                    </main>


                </div>
            </div>

        </div>

    </AuthenticatedLayout>


</template>
