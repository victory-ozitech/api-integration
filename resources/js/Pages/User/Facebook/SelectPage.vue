<template>
    <UserLayout>
        <div class="select-page-wrapper">

            <div class="select-card">

                <!-- Icon -->
                <div class="icon">
                    <i class="bi bi-facebook"></i>
                </div>

                <!-- Title -->
                <h2>Connect a Facebook Page</h2>

                <!-- Subtitle -->
                <p class="text-muted">
                    Select a Facebook page to manage and schedule your posts.
                </p>

                <!-- CTA -->
                <!-- <button class="btn btn-primary btn-lg w-100 mt-3" @click="selectPage">
                    Continue with Facebook
                    Connect Facebook
                </button> -->
                <a :href="route('facebook.redirect')" class="btn btn-primary btn-lg w-100 mt-3">
                    Continue with Facebook
                </a>

                <!-- Extra info -->
                <p class="small text-muted mt-3">
                    You’ll be redirected to Facebook to choose a page and grant access.
                </p>

            </div>

        </div>
    </UserLayout>
</template>

<script setup>
import UserLayout from '@/Layouts/UserLayout.vue';

// Props
// const props = defineProps({
//     facebookAccount: Array,
//     pages: Date
// });
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
