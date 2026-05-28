<script setup>
import { ref, computed, onMounted } from "vue";
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
    posts.value = getPosts();
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
                    <Link :href="route('posts.create')" class="btn btn-primary">
                        + Create Post
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