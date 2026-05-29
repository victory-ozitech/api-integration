<script setup>
import { ref, computed, onMounted } from "vue";
import { router } from "@inertiajs/vue3";
import PostList from "@components/PostList.vue";
import PostCalendar from "@components/PostCalendar.vue";
import ViewToggle from "@components/ViewToggle.vue";
import { getPosts } from "@/utils/postUtils";
import UserLayout from "@/Layouts/UserLayout.vue";

const view = ref("list"); // list | calendar
const posts = ref([]);

onMounted(() => {
    posts.value = getPosts();
});

// Optional future filters
const filteredPosts = computed(() => posts.value);

const loadPosts = () => {
    router.get(route("posts.index"));
};
</script>

<template>
    <UserLayout>
        <div class="container py-4">
    
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold">Posts</h2>
    
                <div class="d-flex gap-2">
                    <ViewToggle v-model="view" />
                    <Link :href="route('posts.create')" class="btn btn-create gap-2">
                        <i class="fa-solid fa-plus"></i> Create Post
                    </Link>
                </div>
            </div>
    
            <!-- Content -->
            <div v-if="view === 'list'">
                <PostList :posts="filteredPosts" @onLoadPosts="loadPosts" />
            </div>
    
            <div v-else>
                <PostCalendar :posts="filteredPosts" />
            </div>
    
        </div>
    </UserLayout>
</template>

<style lang="scss">
@use '@sass/app.scss' as *;
@use "@sass/mixins.scss" as mixin;
@use "@sass/variables.scss" as *;

.btn-create{
    border: 1px solid rgba(148, 163, 184, 0.3);
    background: #f8fafc;
    border-radius: 14px;
    transition: all 0.18s ease;
    @include mixin.dynamic-text(700, 13px, #0f172a, center);
    &:hover{
        transform: translateY(-1px);
            background: $secondary;
            color: #1F2937;
            border-color: rgba(99, 102, 241, 0.25);
    }
}
</style>