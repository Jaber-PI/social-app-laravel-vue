<script setup>
import { computed } from 'vue';
import { TabGroup, TabList, Tab, TabPanels, TabPanel } from '@headlessui/vue';
import PostsTab from './PostsTab.vue';
import AboutTab from './AboutTab.vue';
import MembersTab from './MembersTab.vue';
import { ref } from 'vue';
import { usePage } from '@inertiajs/vue3';

// Props
const props = defineProps({
    group: { type: Object, required: true },
    members: { type: Array, default: () => [] },
    currentTab: { type: String, default: 'posts' },
});

const activeTab = ref(props.currentTab || 'posts');

const switchTab = (tabKey) => {
    if (tabKey === activeTab.value) return;

    activeTab.value = tabKey;

    // Update URL for bookmarking
    const url = new URL(window.location);
    url.searchParams.set('tab', tabKey);
    window.history.replaceState(null, '', url.toString());
};

const tabs = [
    { key: 'posts', label: 'Posts' },
    { key: 'about', label: 'About' },
    { key: 'members', label: 'Members' }
];


const membersCount = computed(() => props.members.length);


const getTabClasses = (tabKey) => {
    const isActive = activeTab.value === tabKey;
    return [
        'w-full rounded-lg py-2.5 text-sm font-medium leading-5 transition focus:outline-none cursor-pointer',
        isActive ? 'bg-blue-100 text-blue-700 shadow font-semibold' : 'text-gray-500 hover:bg-gray-100 hover:text-blue-700'
    ].join(' ');
};

const panelClasses = 'rounded-sm bg-white p-3 ring-white/60 ring-offset-2 ring-offset-blue-400 focus:outline-none';

</script>

<template>

    <div class="tabs-container">
        <div class="flex space-x-1 bg-white p-1 border-b mb-4">
            <button v-for="tab in tabs" :key="tab.key" :class="getTabClasses(tab.key)" @click="switchTab(tab.key)">
                {{ tab.label }}
                <span v-if="tab.key === 'members'" class="ml-1">({{ membersCount }})</span>
            </button>
        </div>

        <div class="tab-content">
            <div v-show="activeTab === 'posts'" class="tab-panel">
                <PostsTab :group="group" />
            </div>

            <div v-show="activeTab === 'about'" class="tab-panel">
                <AboutTab :group="group" />
            </div>

            <div v-show="activeTab === 'members'" class="tab-panel">
                <MembersTab :group="group" :members="members" />
            </div>
        </div>
    </div>
</template>


<style scoped>
.tab-panel {
    @apply rounded-sm bg-white p-3 ring-white/60 ring-offset-2 ring-offset-blue-400 focus:outline-none;
}
</style>
