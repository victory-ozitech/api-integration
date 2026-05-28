<template>
    <UserLayout>
        <button class="btn btn-primary" @click="selectPage">Select Facebook Page</button>
    </UserLayout>
</template>

<script setup>
import UserLayout from '@/Layouts/UserLayout.vue';
import {router} from '@inertiajs/vue3';

const selectPage = () => {
    router.get(route('facebook.redirect'));
}
</script>