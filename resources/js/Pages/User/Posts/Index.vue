<template>
    <UserLayout>
        <PostFormModal :isVisible="showModal" :post="selectedPost" :selectedDate="selectedDate"
            @formSubmit="handleFormSubmit" @closeModal="showModal = false" />


        <ScheduledPostModal v-if="showScheduledPostsModal" :post="selectedPost"
            :currentDate="currentDate" :selectedDate="selectedDateForModal" @closeModal="closeScheduledPostsModal"
            @open-modal="handleOpenModal" @open-edit-modal="openEditModal" />

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
                <PostList :posts="filteredPosts" :currentDate="currentDate" @onLoadPosts="loadPosts" />
            </div>

            <div v-else>
                <PostCalendar :posts="filteredPosts" :currentDate="currentDate" @open-modal="handleOpenModal"
                    @open-edit-modal="openEditModal" @open-posts-modal="openScheduledPostsModal" />
            </div>

        </div>
    </UserLayout>
</template>


<script setup>
import { ref, computed, onMounted } from "vue";
import { router } from "@inertiajs/vue3";
import PostList from "@components/PostList.vue";
import PostCalendar from "@components/PostCalendar.vue";
import ViewToggle from "@components/ViewToggle.vue";
import { getPosts } from "@/utils/postUtils";
import UserLayout from "@/Layouts/UserLayout.vue";
import PostFormModal from "@/Components/PostFormModal.vue";
import ScheduledPostModal from "@/Components/ScheduledPostModal.vue";

// State
const posts = ref([]);
const selectedPost = ref(null);

const showModal = ref(false);
const showScheduledPostsModal = ref(false);

const selectedDate = ref(null);
const currentDate = ref(new Date());
const selectedDateForModal = ref(null);

const view = ref("calendar"); // list | calendar

onMounted(() => {
    posts.value = getPosts();
});

// Optional future filters
const filteredPosts = computed(() => posts.value);

// Methods
const handleOpenModal = (date) => {
    selectedDate.value = date; // Store selected date
    // console.log('Handle Open Modal Date:', date);
    selectedPost.value = null; // Reset post (for new post creation)
    showModal.value = true;
    showScheduledPostsModal.value = false;
};

const openScheduledPostsModal = ({post, date}) => {
    selectedPost.value = post;
    selectedDateForModal.value = date;
    // console.log('Open Scheduled Posts Modal Date:', date, post);
    showScheduledPostsModal.value = true;
};

const closeScheduledPostsModal = () => {
    showScheduledPostsModal.value = false;
    selectedDateForModal.value = null;
};

const openEditModal = (post) => {
    selectedPost.value = post;
    console.log('Post to be Edited: ', post);
    
    showModal.value = true; // Open the modal
    showScheduledPostsModal.value = false;
};

const handleFormSubmit = () => {
    showModal.value = false;
    selectedPost.value = null;
};

const loadPosts = () => {
    router.get(route("posts.index"));
};
</script>

<style lang="scss">
@use '@sass/app.scss' as *;
@use "@sass/mixins.scss" as mixin;
@use "@sass/variables.scss" as *;

.btn-create {
    border: 1px solid rgba(148, 163, 184, 0.3);
    background: #f8fafc;
    border-radius: 14px;
    transition: all 0.18s ease;
    @include mixin.dynamic-text(700, 13px, #0f172a, center);

    &:hover {
        transform: translateY(-1px);
        background: $secondary;
        color: #1F2937;
        border-color: rgba(99, 102, 241, 0.25);
    }
}
</style>