<script setup>
import { ref, onMounted } from "vue";
import { router } from "@inertiajs/vue3";
import { getPostById, updatePost } from "@/utils/postUtils";
import PostFormLayout from "@/Layouts/PostFormLayout.vue";

const props = defineProps({
    id: String
});

onMounted(() => {
    const post = getPostById(props.id);

    content.value = post.content;
    image.value = post.image;
    scheduled_at.value = post.display_date
        ? post.display_date.slice(0, 16)
        : "";

    // console.log("Loaded post:", post);
});

const content = ref("");
const image = ref("");
const scheduled_at = ref("");

const submit = () => {
    updatePost(props.id, {
        content: content.value,
        image: image.value,
        scheduled_at: scheduled_at.value
            ? new Date(scheduled_at.value).toISOString()
            : null,
    });

    router.get(route("posts.index"));
};
</script>

<template>
    <PostFormLayout>
        <!-- Header -->
        <div class="page-header">
            <h2>Edit Post</h2>
            <p>Update your post details</p>
        </div>

        <!-- Card -->
        <div class="create-card">

            <!-- Content -->
            <label class="label" for="content">Content</label>
            <textarea v-model="content" id="content" class="input textarea"
                placeholder="Write something meaningful..." rows="5" />

            <!-- Media -->
            <label class="label mt-3" for="image">Image URL</label>

            <div class="img-preview">
                <img v-if="image" :src="image" alt="Image Preview" class="preview-img" />
                <div v-else class="no-preview">No image preview</div>
            </div>
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
