<script setup>
import { Disclosure, DisclosureButton, DisclosurePanel } from '@headlessui/vue'
import { ChevronUpIcon } from '@heroicons/vue/20/solid'
import { ChevronRightIcon } from '@heroicons/vue/24/solid';
</script>


<template>
    <div class="py-2 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300">
        <Disclosure v-slot="{ open }">
            <DisclosureButton class="flex w-full justify-between rounded-lg  px-4 text-left text-sm font-medium text-gray-900 focus:outline-none focus-visible:ring
                ">
                <slot name="title"></slot>
                <ChevronRightIcon :class="open ? 'rotate-90 transform transition-all' : ''" class="h-5 w-5 " />
            </DisclosureButton>

            <transition enter-active-class="transition duration-100 ease-out"
                enter-from-class="transform scale-95 opacity-0" enter-to-class="transform scale-100 opacity-100"
                leave-active-class="transition duration-75 ease-out" leave-from-class="transform scale-100 opacity-100"
                leave-to-class="transform scale-95 opacity-0">
                <DisclosurePanel>
                    <slot></slot>
                </DisclosurePanel>
            </transition>
        </Disclosure>
    </div>
</template>
