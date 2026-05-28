<script setup>
import { onMounted } from "vue";
import { deletePost } from "@/utils/postUtils";

const props = defineProps({
    post: Object
});

onMounted(() => {
    console.log("Loaded post:", props.post);
});

const emit = defineEmits(["refresh"]);

const remove = () => {
    if (!confirm("Delete this post?")) return;

    deletePost(props.post.id);
    emit("refresh");
};
</script>

<template>
    <div class="card shadow-sm h-100">
        <div class="card-body">

            <!-- User -->
            <div class="d-flex justify-content-between gap-3 flex-wrap">
                <div class="d-flex align-items-center mb-3">
                    <img :src="props.post.user_avatar" class="rounded-circle me-2" style="width: 40px;" />
                    <div>
                        <div class="fw-bold">{{ props.post.user_name }}</div>
                        <small class="text-muted">{{ props.post.created_at }}</small>
                    </div>
                </div>
                <span style="height: fit-content;" class="badge bg-secondary" v-if="props.post.status === 'draft'">Draft</span>
                <span style="height: fit-content;" class="badge bg-warning" v-if="props.post.status === 'scheduled'">Scheduled</span>
                <span style="height: fit-content;" class="badge bg-success" v-if="props.post.status === 'published'">Published</span>
            </div>

            <!-- Content -->
            <p>{{ props.post.content }} <small>{{ props.post.display_date }}</small></p>

            <!-- Image -->
            <img v-if="props.post.image" :src="props.post.image" class="img-fluid rounded mb-3" />

            <!-- Stats -->
            <div class="d-flex justify-content-between text-muted small">
                <span>👍 {{ props.post.likes_count }}</span>
                <span>💬 {{ props.post.comments_count }}</span>
                <span>🔁 {{ props.post.shares_count }}</span>
            </div>

            <!-- Actions -->
            <div class="mt-3 d-flex justify-content-between">
                <a :href="route('posts.edit', { id: props.post.id })" class="btn btn-sm btn-outline-primary">
                    Edit
                </a>

                <button class="btn btn-sm btn-outline-danger" @click="remove">
                    Delete
                </button>
            </div>

        </div>
    </div>
</template>