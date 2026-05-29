<script setup>
import { ref } from "vue";
import { router } from "@inertiajs/vue3";
import { createPost } from "@/utils/postUtils";
import PostFormLayout from "@/Layouts/PostFormLayout.vue";

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
    <PostFormLayout>
        <!-- Header -->
        <div class="page-header">
            <h2>Create Post</h2>
            <p>Share something with your audience</p>
        </div>
    
        <!-- Card -->
        <div class="create-card">
    
            <!-- Content -->
            <label class="label" for="content">Content</label>
            <textarea v-model="content" id="content" class="input textarea" placeholder="Write something meaningful..."
                rows="5" />
    
            <!-- Media -->
            <label class="label mt-3" for="image">Image URL</label>
            <div class="input-wrapper">
                <i class="fa-regular fa-image"></i>
                <input v-model="image" id="image" class="input" placeholder="Paste image link (optional)" />
            </div>
    
            <!-- Scheduling -->
            <label class="label mt-3" for="scheduled_at">Schedule</label>
            <div class="input-wrapper">
                <i class="fa-regular fa-clock"></i>
                <input type="datetime-local" v-model="scheduled_at" id="scheduled_at" class="input" />
            </div>
    
            <!-- Actions -->
            <div class="actions">
                <button class="btn btn-primary-custom" @click="submit">
                    Publish Post
                </button>
            </div>
    
        </div>
    </PostFormLayout>
</template>
