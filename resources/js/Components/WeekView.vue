<script setup>
import { computed } from "vue";

// Props
const props = defineProps({
    posts: Array,
    currentDate: Date
});

// Computed
const weekDays = computed(() => {
    const start = new Date(props.currentDate);
    start.setDate(start.getDate() - start.getDay());

    return Array.from({ length: 7 }).map((_, i) => {
        const d = new Date(start);
        d.setDate(start.getDate() + i);
        return d;
    });
});

// Methods
const getPostsForDate = (date) => {
    if (!date) return [];

    const dayStr = date.toISOString().split("T")[0];

    return props.posts.filter(p => {
        const d = (p.scheduled_at || p.created_at).split("T")[0];
        return d === dayStr;
    });
};
</script>

<template>
    <div class="row">
        <div class="col" v-for="day in weekDays" :key="day">
            <div class="fw-bold mb-2">{{ day.toDateString() }}</div>

            <div v-for="post in getPostsForDate(day)" class="post-pill mb-2">
                {{ post.content.slice(0, 40) }}
            </div>
        </div>
    </div>
</template>