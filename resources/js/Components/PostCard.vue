<script setup>
import { deletePost } from "@/utils/postUtils";

defineProps({
    post: Object
});

const remove = () => {
    if (!confirm("Delete this post?")) return;

    deletePost(post.id);
    emit("refresh");
};
</script>

<template>
    <div class="card shadow-sm h-100">
        <div class="card-body">

            <!-- User -->
            <div class="d-flex justify-content-between gap-3 flex-wrap">
                <div class="d-flex align-items-center mb-3">
                    <img :src="post.user_avatar" class="rounded-circle me-2" style="width: 40px;" />
                    <div>
                        <div class="fw-bold">{{ post.user_name }}</div>
                        <small class="text-muted">{{ post.created_at }}</small>
                    </div>
                </div>
                <span style="height: fit-content;" class="badge bg-secondary" v-if="post.status === 'draft'">Draft</span>
                <span style="height: fit-content;" class="badge bg-warning" v-if="post.status === 'scheduled'">Scheduled</span>
                <span style="height: fit-content;" class="badge bg-success" v-if="post.status === 'published'">Published</span>
            </div>

            <!-- Content -->
            <p>{{ post.content }} <small>{{ post.display_date }}</small></p>

            <!-- Image -->
            <img v-if="post.image" :src="post.image" class="img-fluid rounded mb-3" />

            <!-- Stats -->
            <div class="d-flex justify-content-between text-muted small">
                <span>👍 {{ post.likes_count }}</span>
                <span>💬 {{ post.comments_count }}</span>
                <span>🔁 {{ post.shares_count }}</span>
            </div>

            <!-- Actions -->
            <div class="mt-3 d-flex justify-content-between">
                <a :href="route('posts.edit', { id: post.id })" class="btn btn-sm btn-outline-primary">
                    Edit
                </a>

                <button class="btn btn-sm btn-outline-danger" @click="remove">
                    Delete
                </button>
            </div>

        </div>
    </div>
</template>