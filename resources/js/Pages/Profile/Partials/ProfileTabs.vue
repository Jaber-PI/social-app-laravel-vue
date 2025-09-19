<script setup>
import { computed } from 'vue';
import { ref } from 'vue';
import { PhotoIcon } from '@heroicons/vue/24/solid';
import PostsTab from './PostsTab.vue';
import AboutTab from './ProfileAboutTab.vue';
import FollowersTab from './FollowersTab.vue';

// Props
const props = defineProps({
    profile: { type: Object, required: true },
    followersCount: { type: Number, default: 0 },
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
    { key: 'posts', label: 'Posts (' + props.profile.posts_count + ')', icon: PhotoIcon },
    { key: 'about', label: 'About', icon: PhotoIcon },
    // { key: 'photos', label: 'Photos', icon: PhotoIcon },
    { key: 'followers', label: 'Followers (' + props.followersCount + ')', icon: PhotoIcon },
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
            </button>
        </div>

        <div class="tab-content">
            <div v-show="activeTab === 'posts'" :class="panelClasses" >
                <PostsTab :user="profile" />
            </div>

            <div v-show="activeTab === 'about'" :class="panelClasses" >
                <AboutTab :user="profile" />
            </div>

            <div v-show="activeTab === 'followers'" :class="panelClasses" >
                <FollowersTab :followers_count="followersCount" :user="profile" />
            </div>
        </div>
    </div>
</template>


