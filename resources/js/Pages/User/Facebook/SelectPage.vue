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

<style scoped>
.select-page-wrapper {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 70vh;
    padding: 15px;
}

.select-card {
    width: 420px;
    background: #fff;
    border-radius: 16px;
    padding: 30px;
    text-align: center;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
}

.icon {
    font-size: 40px;
    color: #1877f2;
    margin-bottom: 10px;
}

.select-card h2 {
    font-weight: 600;
    margin-bottom: 10px;
}

.btn-primary {
    background: #1877f2;
    border: none;
    border-radius: 10px;
    padding: 12px;
    font-weight: 500;
}

.btn-primary:hover {
    background: #166fe5;
}
</style>
