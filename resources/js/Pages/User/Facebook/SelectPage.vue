<template>
    <UserLayout>
        <div class="select-page-wrapper">

            <!-- list of pages will be displayed here -->
             <div v-for="page in pages" :key="page.id">
                <div class="channel-card">
                    <div class="card-header">
                        <h3>{{ page.name }}</h3>
                        <span class="status-badge">Active</span>
                    </div>
                    <div class="avatar-ring">
                        <img :src="page.picture.data.url" alt="Avatar" class="avatar">
                    </div>
                    <hr>
                </div>
            </div>

        </div>
    </UserLayout>
</template>

<script setup>
import UserLayout from '@/Layouts/UserLayout.vue';
import { onMounted } from 'vue';

// Props
const props = defineProps({
    pages: Array,
});

onMounted(() => {
    console.log('Pages:', props.pages);
});
</script>

<style lang="scss" scoped>
@use '@sass/app.scss' as *;
@use "@sass/mixins.scss" as mixin;
@use "@sass/variables.scss" as *;

.select-page-wrapper {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 70vh;
    padding: 15px;
}

.channel-card {
    width: 420px;
    border-radius: 18px;
    padding: 28px;
    text-align: left;

    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(10px);

    border: 1px solid rgba(0, 0, 0, 0.05);

    box-shadow:
        0 10px 40px rgba(0, 0, 0, 0.08),
        0 2px 8px rgba(0, 0, 0, 0.04);

    transition: all 0.25s ease;

    &:hover {
        transform: translateY(-2px);
        box-shadow:
            0 14px 50px rgba(0, 0, 0, 0.12),
            0 4px 12px rgba(0, 0, 0, 0.06);
    }

    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;

        .status-badge {
            font-size: 12px;
            color: #16a34a;
            background: #dcfce7;
            padding: 4px 10px;
            border-radius: 999px;
            font-weight: 500;
        }
    }

    .avatar-ring {
        padding: 3px;
        border-radius: 50%;

        background: linear-gradient(135deg, #1877f2, #42a5f5);

        display: inline-block;

        .avatar {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            object-fit: cover;

            border: 3px solid white;
        }
    }

    hr {
        border: none;
        height: 1px;
        background: rgba(0, 0, 0, 0.06);
    }
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
