<template>
    <div class="flex items-center mb-4">
        <img :src="user.avatar" alt="User Avatar" class="rounded-full w-16 h-16 mr-4" />
        <input type="file" @change="onFileChange" class="border rounded p-2" />
    </div>
</template>


<script>
export default {
    props: ['user'],
    methods: {
        onFileChange(event) {
            const file = event.target.files[0];
            const reader = new FileReader();
            reader.onloadend = () => {
                this.$emit('update-avatar', reader.result);
            };
            if (file) {
                reader.readAsDataURL(file);
            }
        }
    }
}
</script>

<style scoped>
.avatar img {
    width: 100px;
    height: auto;
}
</style>
