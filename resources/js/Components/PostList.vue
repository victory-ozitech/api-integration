<script setup>
import PostCard from "./PostCard.vue";

const props = defineProps({
    posts: Array
});

const emit = defineEmits(["delete-posts"]);

const handleDeletePost = () => {
    emit("delete-posts");
};
</script>

<template>
    <div class="row">
        <div class="col-12" v-if="props.posts.length === 0">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h5 class="card-title">No posts yet</h5>
                    <p class="card-text">Start by creating your first post!</p>
                </div>
            </div>
        </div>
        <template v-else>
            <div class="col-md-6 col-lg-4 mb-4" v-for="(post, index) in props.posts" :key="post.id"
                :style="{ animationDelay: `${index * 80}ms` }">
                <PostCard :post="post" @deletePost="handleDeletePost" />
            </div>
        </template>
    </div>
</template>

<style lang="scss">
.col-md-6 {
    animation: fadeUp .5s ease both;
}

@keyframes fadeUp {
    from {
        opacity: 0;
        transform: translateY(14px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>