<script setup>
import { ref } from "vue";
import { router } from "@inertiajs/vue3";
import { createPost } from "@/utils/postUtils";
import UserLayout from "@/Layouts/UserLayout.vue";

const content = ref("");
const image = ref("");
const scheduled_at = ref("");

const submit = () => {
    createPost({
        content: content.value,
        image: image.value,
        scheduled_at: scheduled_at.value || null,
    });

    router.get(route("posts.index"));
};
</script>

<template>
    <UserLayout>
        <div class="container py-4">
            <h3>Create Post</h3>
    
            <textarea v-model="content" class="form-control mb-3" placeholder="What's on your mind?" />
    
            <input v-model="image" class="form-control mb-3" placeholder="Image URL" />
    
            <input type="datetime-local" v-model="scheduled_at" class="form-control mb-3" />
    
            <button class="btn btn-primary" @click="submit">
                Save Post
            </button>
        </div>
    </UserLayout>
</template>