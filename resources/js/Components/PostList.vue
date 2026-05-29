<script setup>
import PostCard from "./PostCard.vue";

const props = defineProps({
    posts: Array
});

const emit = defineEmits(["load-posts"]);

const handleRefresh = () => {
    emit("load-posts");
};
</script>

<template>
    <div class="row">
        <div class="col-md-6 col-lg-4 mb-4" v-for="(post, index) in props.posts" :key="post.id"
            :style="{ animationDelay: `${index * 80}ms` }">
            <PostCard :post="post" @refresh="handleRefresh" />
        </div>
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