<script setup>
import { ref } from 'vue';
import { PencilIcon } from '@heroicons/vue/20/solid';
import UpdateProfileAboutForm from './UpdateProfileAboutForm.vue';
import { usePage } from '@inertiajs/vue3';

defineProps({
    user:  Object,
    canEdit: {
        type: Boolean,
    },
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});


const editing = ref(false);

</script>

<template>
    <div class="py-2">
        <div class="mx-auto space-y-6 sm:px-6 lg:px-5 relative">

            <button v-if="canEdit" @click="editing = !editing" class="absolute right-0 top-0 underline text-red-600 flex gap-2">
                <span>
                    {{ editing ? "Cancel" : "Edit" }}
                </span>
            </button>
            <div v-if="!editing" class="about">
                <div class="text-center mt-2">
                    <div class="text-sm leading-normal mt-0 mb-2 text-blueGray-400 font-bold uppercase">
                        <i class="fas fa-map-marker-alt mr-2 text-lg text-blueGray-400"></i>
                        Los Angeles, California
                    </div>
                    <div class="mb-2 text-blueGray-600 mt-10">
                        <i class="fas fa-briefcase mr-2 text-lg text-blueGray-400"></i>Solution
                        Manager -
                        Creative Tim Officer
                    </div>
                    <div class="mb-2 text-blueGray-600">
                        <i class="fas fa-university mr-2 text-lg text-blueGray-400"></i>University
                        of Computer
                        Science
                    </div>
                </div>
                <!-- about  -->
                <div class="mt-2 py-10 text-center">
                    <div class="flex flex-wrap justify-center">
                        <div class="w-full lg:w-9/12 px-4">
                            <p class="mb-4 text-lg leading-relaxed text-blueGray-700">
                                An artist of considerable range, Jenna the name taken by
                                Melbourne-raised, Brooklyn-based Nick Murphy writes,
                                performs and records all of his own music, giving it a
                                warm, intimate feel with a solid groove structure. An
                                artist of considerable range.
                            </p>
                            <a href="#pablo" class="font-normal text-pink-500">Show
                                more</a>
                        </div>
                    </div>
                </div>
            </div>
            <div v-else class="bg-white p-4 shadow sm:rounded-lg sm:p-8">
                <UpdateProfileAboutForm :user="user" :must-verify-email="mustVerifyEmail" :status="status" class="max-w-xl" />
            </div>
        </div>
    </div>
</template>
