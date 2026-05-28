<script setup>
import PostCard from "./PostCard.vue";

defineProps({
    posts: Array
});

const emit = defineEmits(["load-posts"]);

const handleRefresh = () => {
    emit("load-posts");
};
</script>

<template>
    <div class="row">
        <div class="col-md-6 col-lg-4 mb-4" v-for="post in posts" :key="post.id">
            <PostCard :post="post" @refresh="handleRefresh" />
        </div>
    </div>
</template>