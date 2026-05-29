<template>
    <UserLayout>
        <a :href="route('facebook.redirect')" class="btn btn-primary">
            Connect Facebook
        </a>
    </UserLayout>
</template>

<script setup>
import UserLayout from '@/Layouts/UserLayout.vue';
</script>
