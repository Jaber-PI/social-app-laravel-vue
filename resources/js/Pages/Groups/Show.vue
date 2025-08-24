<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import GroupLayout from '@/Pages/Groups/layouts/GroupLayout.vue'
import { Head } from '@inertiajs/vue3';
import GroupTabs from './Partials/GroupTabs.vue';
import GroupHeader from './Partials/GroupHeader.vue';


const props = defineProps({
    group: { type: Object, required: true },
    members: { type: Array, default: () => [] },
    currentTab: { type: String, default: 'posts' },
    validTabs: { type: Array, default: () => ['posts', 'about', 'members'] }
});

</script>

<template>

    <Head :title="group.name" />
    <AuthenticatedLayout>
        <div class="container pt-20 mx-auto h-full">
            <!-- Delegate layout responsibility to GroupLayout -->
            <GroupLayout>
                <div class="rounded-t-lg shadow">
                    <GroupHeader :group="group" />
                </div>

                <GroupTabs v-bind="{ group, currentTab, members }" />
            </GroupLayout>
        </div>

    </AuthenticatedLayout>
</template>
