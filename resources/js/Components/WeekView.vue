<template>
    <div class="week-grid">
        <div v-for="day in weekDays" :key="day.toISOString()"
            :class="['week-column', { 'week-column--today': isToday(day) }]">
            <div class="week-header">
                <div class="weekday-label">{{ day.toLocaleDateString(undefined, { weekday: 'short' }) }}</div>
                <div class="week-date">{{ day.getDate() }}</div>
            </div>

            <button v-if="isFutureOrToday(day)" class="add-btn" @click.stop="openModal(day)"
                title="Schedule New Post">
                <i class="fa-solid fa-plus"></i>
            </button>

            <div class="event-list">
                <transition-group name="fade" tag="div">
                    <div v-for="post in getPostsForDate(day)" :key="post.id" :title="post.content" class="post-pill"
                        @click.stop="openPostsModal({ post, date: day })">
                        {{ post.content.slice(0, 15) }}
                    </div>
                </transition-group>

                <div v-if="getPostsForDate(day).length === 0" class="empty-week">
                    No posts scheduled
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from "vue";

// Props
const props = defineProps({
    posts: Array,
    currentDate: Date
});

const weekDays = computed(() => {
    const start = new Date(props.currentDate);
    start.setDate(start.getDate() - start.getDay());

    return Array.from({ length: 7 }).map((_, i) => {
        const d = new Date(start);
        d.setDate(start.getDate() + i);
        return d;
    });
});

const isToday = (date) => {
    return new Date(date).toDateString() === new Date().toDateString();
};

const isFutureOrToday = (date) => {
    if (!date) return false;

    const target = new Date(date);
    const today = new Date();

    // Normalize time
    target.setHours(0, 0, 0, 0);
    today.setHours(0, 0, 0, 0);

    return target >= today;
};

const getPostsForDate = (date) => {
    if (!date) return [];

    const dayStr = date.toISOString().split("T")[0];

    return props.posts.filter(p => {
        const d = (p.scheduled_at || p.created_at).split("T")[0];
        return d === dayStr;
    });
};

// Emit
const emit = defineEmits([
    "open-modal",
    "open-posts-modal",
]);

const openModal = (date) => {
    emit("open-modal", date);
};

const openPostsModal = ({ post, date }) => {
    emit("open-posts-modal", { post, date });
};
</script>



<style lang="scss">
@use '@sass/app.scss' as *;
@use "@sass/mixins.scss" as mixin;
@use "@sass/variables.scss" as *;

.week-grid {
    display: grid;
    grid-template-columns: repeat(7, minmax(0, 1fr));
    gap: 14px;
    margin-bottom: 40px;
}

.week-column {
    background: #ffffff;
    border: 1px solid rgba(15, 23, 42, 0.08);
    border-radius: 20px;
    padding: 18px;
    min-height: 220px;
    display: flex;
    flex-direction: column;
    transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
}

.week-column:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 22px rgba(15, 23, 42, 0.08);
}

.week-column:hover .add-btn {
     opacity: 1;
     visibility: visible;
     transform: translateY(0);
 }

.week-column--today {
    border-color: #6366f1;
    background: #f8f8ff;
}

.week-header {
    display: flex;
    align-items: baseline;
    justify-content: space-between;
    gap: 12px;
    // margin-bottom: 16px;
}

.weekday-label {
    font-size: 12px;
    font-weight: 700;
    color: #475569;
    text-transform: uppercase;
    letter-spacing: 0.08em;
}

.week-date {
    font-size: 18px;
    font-weight: 800;
    color: #0f172a;
}

.add-btn {
    border: 1px solid rgba(148, 163, 184, 0.3);
    // background: #f8fafc;
    background: transparent;
    margin: 8px 0;
    border-radius: 4px;
    padding: 2px 4px;
    cursor: pointer;
    @include mixin.dynamic-text(700, 11px, #0f172a, center);
    transition: all 0.18s ease;
    opacity: 0;
    visibility: hidden;
    transform: translateY(4px);
    width: fit-content;

    &:hover {
        background: $secondary;
        // transform: translateY(-1px);
        color: #1F2937;
        border-color: rgba(99, 102, 241, 0.25);
    }
}

.event-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
    flex: 1;
}

.post-pill {
    background: linear-gradient(135deg, $primary, $secondary);
    color: Ssecondary;
    padding: 12px 14px;
    border-radius: 18px;
    margin-bottom: 10px;
    font-size: 13px;
    line-height: 1.5;
    box-shadow: 0 6px 14px rgba(15, 23, 42, 0.05);
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.empty-week {
    margin-top: 6px;
    color: #94a3b8;
    font-size: 12px;
    line-height: 1.6;
}

.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.22s ease, transform 0.22s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
    transform: translateY(8px);
}
</style>
