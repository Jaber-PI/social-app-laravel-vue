<script setup>

import { Head, usePage } from '@inertiajs/vue3';

import { TabGroup, TabList, Tab, TabPanels, TabPanel } from '@headlessui/vue';
import TabItem from './Partials/TabItem.vue';
import About from './Partials/About.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import MainLayout from '@/Layouts/MainLayout.vue';
import { PhotoIcon } from '@heroicons/vue/20/solid';
import { ref } from 'vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Cover from './Partials/Cover.vue';
import Avatar from './Partials/Avatar.vue';
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
});


</script>


<template>

    <MainLayout>

        <Head :title="profile.name + ' | Profile'" />

        <div class="h-full overflow-auto">

            <section class="relative flex justify-center items-center h-[500px]">

                <Cover :can="can" :profile="profile" />

                <div class="-mt-44 text-center flex flex-col items-center z-10">
                    <Avatar :can="can" :user="profile" />
                    <h3 class="text-3xl font-semibold text-center leading-normal text-gray-200 my-2">
                        {{ profile.name.toUpperCase() }}
                    </h3>
                    <div v-if="user && can.connect" class="py-2 px-3">
                        <button
                            class="bg-pink-500 active:bg-pink-600 uppercase text-white font-bold hover:shadow-md shadow text-xs px-4 py-2 rounded outline-none focus:outline-none  mb-1 ease-linear transition-all duration-150"
                            type="button">
                            Connect
                        </button>
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
                                                class="text-xl font-bold block uppercase tracking-wide text-blueGray-600">22</span><span
                                                class="text-sm text-blueGray-400">Friends</span>
                                        </div>
                                        <div class="mr-4 p-3 text-center">
                                            <span
                                                class="text-xl font-bold block uppercase tracking-wide text-blueGray-600">10</span><span
                                                class="text-sm text-blueGray-400">Photos</span>
                                        </div>
                                        <div class="lg:mr-4 p-3 text-center">
                                            <span
                                                class="text-xl font-bold block uppercase tracking-wide text-blueGray-600">89</span><span
                                                class="text-sm text-blueGray-400">Comments</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- tabs  -->
                            <div class="">
                                <div class="w-full px-2 ">
                                    <TabGroup>
                                        <TabList class="flex space-x-1 rounded-xl border-b">
                                            <Tab as="template" v-slot="{ selected }">
                                                <TabItem :selected="selected">
                                                    About
                                                </TabItem>
                                            </Tab>
                                            <Tab as="template" v-slot="{ selected }">
                                                <TabItem :selected="selected">
                                                    Profile
                                                </TabItem>
                                            </Tab>
                                            <Tab as="template" v-slot="{ selected }">
                                                <TabItem :selected="selected">
                                                    Photos
                                                </TabItem>
                                            </Tab>
                                            <Tab as="template" v-slot="{ selected }">
                                                <TabItem :selected="selected">
                                                    Followings
                                                </TabItem>
                                            </Tab>
                                        </TabList>

                                        <TabPanels class="mt-2">

                                            <!-- about section tab  -->
                                            <TabPanel>
                                                <!-- about component  -->
                                                <About :user="profile" :canEdit="can.edit"
                                                    :must-verify-email="mustVerifyEmail" :status="status" />
                                            </TabPanel>

                                            <TabPanel :class="[
                                                'rounded-xl p-3',
                                                '',
                                            ]">
                                                Posts
                                            </TabPanel>

                                            <TabPanel :class="[
                                                'rounded-xl bg-white p-3',
                                                'ring-white/60 ring-offset-2 ring-offset-blue-400 focus:outline-none focus:ring-2',
                                            ]">
                                                Photos
                                            </TabPanel>
                                            <TabPanel :class="[
                                                'rounded-xl bg-white p-3',
                                                'ring-white/60 ring-offset-2 ring-offset-blue-400 focus:outline-none focus:ring-2',
                                            ]">
                                                Followings
                                            </TabPanel>
                                        </TabPanels>
                                    </TabGroup>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

    </MainLayout>

</template>
