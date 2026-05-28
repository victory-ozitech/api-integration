<script setup>
// Props
const props = defineProps({
    posts: Array,
    currentDate: Date
});

// Computed
const hours = Array.from({ length: 24 }, (_, i) => i);

// Methods
const postsForHour = (hour) => {
    return props.posts.filter(p => {
        const d = new Date(p.scheduled_at || p.created_at);
        return d.getHours() === hour &&
            d.toDateString() === props.currentDate.toDateString();
    });
};
</script>

<template>
    <div v-for="hour in hours" :key="hour" class="d-flex border-bottom py-2">
        <div style="width:80px">{{ hour }}:00</div>

        <div class="flex-grow-1">
            <div v-for="post in postsForHour(hour)" class="post-pill mb-1">
                {{ post.content }}
            </div>
        </div>
    </div>
</template>