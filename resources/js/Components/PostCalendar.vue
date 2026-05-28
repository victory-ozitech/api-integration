<script setup>
import { computed } from "vue";

const props = defineProps({
    posts: Array
});

// Group posts by date
const grouped = computed(() => {
    const map = {};

    props.posts.forEach(post => {
        const date = post.created_at.split("T")[0];

        if (!map[date]) map[date] = [];
        map[date].push(post);
    });

    return map;
});
</script>

<template>
    <div>
        <div v-for="(posts, date) in grouped" :key="date" class="mb-4">
            <h5 class="mb-2">{{ date }}</h5>

            <div class="row">
                <div class="col-md-4 mb-3" v-for="post in posts" :key="post.id">
                    <div class="card p-2 small">
                        {{ post.content.slice(0, 80) }}...
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>