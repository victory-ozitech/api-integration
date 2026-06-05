<template>
    <UserLayout>
        <a :href="route('facebook.redirect')" class="facebook-connect-btn w-100 mt-3">
            <i class="fa-brands fa-facebook"></i>
            Connect Facebook Page
        </a>

        <div class="select-page-wrapper">
            <template v-if="pages.length > 0">
                <div class="row">
                    <div class="col-ll col-md-6 mb-4" v-for="page in pages" :key="page.id">
                        <div class="channel-card">
                            <div class="card-header">
                                <h6 class="text-muted mb-1">{{ page.name }}</h6>
                                <span class="status-badge">Page</span>
                            </div>

                            <div class="profile d-flex align-items-center mt-3">
                                <div class="avatar-ring">
                                    <img :src="page.picture?.data?.url || ''" class="avatar" />
                                </div>

                                <div class="ms-3 text-start">
                                    <h5 class="mb-1 fw-semibold">
                                        {{ page.category || "Facebook Page" }}
                                    </h5>
                                    <p class="text-muted small mb-0">
                                        ID: {{ page.id }}
                                    </p>
                                </div>
                            </div>

                            <button class="btn btn-primary mt-4" @click="disconnect(page.id)">
                                {{ loading ? 'Disconnecting...' : 'Disconnect Account' }}
                            </button>
                        </div>
                    </div>
                </div>
            </template>

            <div class="channel-card" v-if="
                pages.length === 0 &&
                facebookAccount &&
                Object.keys(facebookAccount).length > 0
            ">
                <!-- Header -->
                <div class="card-header">
                    <h6 class="text-muted mb-1">Connected Account</h6>
                    <span class="status-badge">● Connected</span>
                </div>

                <!-- Profile -->
                <div class="profile d-flex align-items-center mt-3">
                    <div class="avatar-ring">
                        <img :src="facebookAccount?.avatar || ''" class="avatar" />
                    </div>

                    <div class="ms-3 text-start">
                        <h5 class="mb-1 fw-semibold">
                            {{ facebookAccount?.name || "Facebook User" }}
                        </h5>
                        <p class="text-muted small mb-0">
                            {{ facebookAccount?.email || "No email available" }}
                        </p>
                    </div>
                </div>

                <!-- Divider -->
                <hr class="my-4" />

                <!-- Action -->
                <div class="text-start">
                    <p class="text-muted small mb-2">
                        You're connected and ready to manage your pages.
                    </p>

                    <button class="btn btn-primary">Change Account</button>
                </div>
            </div>
        </div>
    </UserLayout>
</template>

<script setup>
import UserLayout from "@/Layouts/UserLayout.vue";
import { ref, onMounted } from "vue";

// Props
const props = defineProps({
    facebookAccount: { type: Object, default: () => ({}) },
    pages: { type: Array, default: () => [] },
});

onMounted(() => {
    console.log("Facebook Account:", props.facebookAccount);
    console.log("Pages:", props.pages);
});


const loading = ref(false)


const disconnect = (id) => {
    if (loading.value) return

    router.delete(route('facebook.disconnect-page'), id, {
        onStart: () => loading.value = true,
        onFinish: () => loading.value = false,
    })
}
</script>

<style lang="scss" scoped>
@use "@sass/app.scss" as *;
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

.facebook-connect-btn {
    display: inline-flex;
    justify-content: center;
    align-items: center;
    gap: 0.75rem;
    background: #1877f2;
    color: #ffffff;
    font-size: 1rem;
    font-weight: 600;
    border-radius: 14px;
    padding: 14px 20px;
    box-shadow: 0 14px 30px rgba(24, 119, 242, 0.18);
    transition:
        transform 0.2s ease,
        background 0.2s ease,
        box-shadow 0.2s ease;
    max-width: 300px;
    margin: 20px;
}

.facebook-connect-btn:hover {
    background: #145bc5;
    transform: translateY(-1px);
    box-shadow: 0 18px 36px rgba(24, 119, 242, 0.24);
}

.facebook-connect-btn i {
    font-size: 1.1rem;
}
</style>
