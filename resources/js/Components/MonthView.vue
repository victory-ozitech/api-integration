<script setup>
import { computed } from "vue";

// Props
const props = defineProps({
    posts: Array,
    currentDate: Date
});

// Computed
const monthDays = computed(() => {
    const date = new Date(props.currentDate);
    const year = date.getFullYear();
    const month = date.getMonth();

    const firstDay = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();

    const days = [];

    // Empty slots
    for (let i = 0; i < firstDay; i++) {
        days.push(null);
    }

    // Real days
    for (let i = 1; i <= daysInMonth; i++) {
        days.push(new Date(year, month, i));
    }

    return days;
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
    <div>
        <div class="calendar-grid">
            <div v-for="(day, i) in monthDays" :key="i" class="calendar-cell">

                <div v-if="day" class="date">{{ day.getDate() }}</div>

                <div v-for="post in getPostsForDate(day)" :key="post.id" class="post-pill">
                    {{ post.content.slice(0, 30) }}
                </div>

            </div>
        </div>

    </div>
</template>

<style>
.calendar-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 8px;
}

.calendar-cell {
    border: 1px solid #eee;
    min-height: 120px;
    padding: 6px;
    border-radius: 8px;
    background: #fff;
}

.date {
    font-size: 12px;
    font-weight: bold;
    margin-bottom: 4px;
}

.post-pill {
    font-size: 11px;
    background: #0d6efd;
    color: white;
    padding: 3px 6px;
    border-radius: 6px;
    margin-bottom: 4px;
}
</style>