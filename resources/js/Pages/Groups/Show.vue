<script setup>
import { Head } from '@inertiajs/vue3';
import GroupTabs from './Partials/GroupTabs.vue';
import GroupHeader from './Partials/GroupHeader.vue';
import MainLayout from '@/Layouts/MainLayout.vue';

const props = defineProps({
    group: { type: Object, required: true },
    members: { type: Array, default: () => [] },
    currentTab: { type: String, default: 'posts' },
});

</script>

<template>

    <Head :title="group.name" />
    <!-- Delegate layout responsibility to GroupLayout -->
    <MainLayout>
        <div class="rounded-t-lg shadow">
            <GroupHeader :group="group" />
        </div>
        <GroupTabs v-bind="{ group, currentTab, members }" />
    </MainLayout>
</template>
