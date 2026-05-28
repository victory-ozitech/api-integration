<script setup>
import { ref, onMounted } from "vue";
import { router } from "@inertiajs/vue3";
import { getPostById, updatePost } from "@/utils/postUtils";

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
    <div class="container py-4">
        <h3>Edit Post</h3>

        <textarea v-model="content" class="form-control mb-3" placeholder="What's on your mind?" />

        <input v-model="image" class="form-control mb-3" placeholder="Image URL" />

        <input type="datetime-local" v-model="scheduled_at" class="form-control mb-3" />

        <button class="btn btn-primary" @click="submit">
            Save Post
        </button>
    </div>
</template>