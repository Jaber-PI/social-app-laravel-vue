<script setup>

import { Head, router, usePage } from '@inertiajs/vue3';

import ProfileTabs from './Partials/ProfileTabs.vue';
import MainLayout from '@/Layouts/MainLayout.vue';
import { PhotoIcon } from '@heroicons/vue/20/solid';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Cover from './Partials/Cover.vue';
import Avatar from './Partials/Avatar.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const user = usePage().props.auth.user;

const props = defineProps({
    profile: {
        type: Object,
    },
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
    can: {
        type: Object,
    },
    is_current_user: {
        type: Boolean,
    },
    is_following: {
        type: Boolean,
    },
    current_tab: { type: String, default: 'posts' },
});

function followUser() {
    router.post(route('users.follow', props.profile.id), {}, {
        preserveScroll: true,
    });
}

function unFollowUser() {
    router.delete(route('users.unfollow', props.profile.id), {}, {
        preserveScroll: true,
    });
}


</script>


<template>

    <Head :title="profile.name + ' | Profile'" />
    <MainLayout>

        <div class="h-full overflow-auto">

            <!-- header  -->
            <section class="relative flex justify-center items-center h-[500px]">

                <Cover :can="can" :profile="profile" />

                <div class="-mt-44 text-center flex flex-col items-center z-10">
                    <Avatar :can="can" :user="profile" />
                    <h3 class="text-3xl font-semibold text-center leading-normal text-gray-200 my-2">
                        {{ profile.name.toUpperCase() }}
                    </h3>
                    <div v-if="!is_current_user" class="py-2 px-3">
                        <SecondaryButton v-if="(!is_following) && can.follow" @click="followUser">
                            Follow
                        </SecondaryButton>
                        <DangerButton v-else-if="is_following" @click="unFollowUser">
                            Unfollow
                        </DangerButton>
                    </div>
                </div>

            </section>

            <section class="relative py-16 bg-blueGray-200">
                <div class="container mx-auto px-4">
                    <div
                        class="relative flex flex-col min-w-0 break-words bg-white w-full mb-6 shadow-xl rounded-lg -mt-64">
                        <div class="px-6">

                            <!-- stats  -->
                            <div class="flex flex-wrap justify-center">
                                <div class="w-full lg:w-4/12 px-4 lg:order-1">
                                    <div class="flex justify-center py-4 lg:pt-4 pt-8">
                                        <div class="mr-4 p-3 text-center">
                                            <span
                                                class="text-xl font-bold block uppercase tracking-wide text-blueGray-600">{{
                                                    profile.followers_count }}</span><span
                                                class="text-sm text-blueGray-400">Followers</span>
                                        </div>
                                        <div class="mr-4 p-3 text-center">
                                            <span
                                                class="text-xl font-bold block uppercase tracking-wide text-blueGray-600">{{
                                                    profile.posts_count }}</span><span
                                                class="text-sm text-blueGray-400">Posts</span>
                                        </div>
                                        <div class="lg:mr-4 p-3 text-center">
                                            <span
                                                class="text-xl font-bold block uppercase tracking-wide text-blueGray-600">{{
                                                    profile.comments_count }}</span><span
                                                class="text-sm text-blueGray-400">Comments</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- tabs  -->
                            <ProfileTabs :profile="profile" :followersCount="profile.followers_count" :currentTab="current_tab" />
                        </div>
                    </div>
                </div>
            </section>
        </div>

    </MainLayout>

</template>
