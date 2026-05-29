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
    <div class="day-view">
        <div v-for="hour in hours" :key="hour" class="day-row">
            <div class="hour-label">{{ hour.toString().padStart(2, '0') }}:00</div>
            <div class="hour-content">
                <transition-group name="slide-fade" tag="div" class="hour-events">
                    <div v-for="post in postsForHour(hour)" :key="post.id" class="post-pill">
                        {{ post.content }}
                    </div>
                </transition-group>

                <div v-if="postsForHour(hour).length === 0" class="empty-hour">
                    No events
                </div>
            </div>
        </div>
    </div>
</template>

<style lang="scss">
@use '@sass/app.scss' as *;
@use "@sass/mixins.scss" as mixin;
@use "@sass/variables.scss" as *;

.day-view {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.day-row {
    display: flex;
    gap: 14px;
    align-items: flex-start;
    padding: 14px 0;
    border-bottom: 1px solid rgba(148, 163, 184, 0.18);
    transition: background-color 0.18s ease;
    border-radius: 10px;
}

.day-row:hover {
    background-color: rgba(238, 242, 255, 0.55);
}

.hour-label {
    width: 76px;
    font-size: 13px;
    color: #64748b;
    font-weight: 600;
    text-align: right;
    padding-top: 4px;
}

.hour-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.hour-events {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.post-pill {
    padding: 14px 16px;
    border-radius: 18px;
    // background: #eef2ff;
    // color: #1e293b;
    background: linear-gradient(135deg, $primary, $secondary);
    color: $secondary;
    box-shadow: 0 10px 24px rgba(15, 23, 42, 0.06);
    font-size: 13px;
    line-height: 1.6;
}

.empty-hour {
    font-size: 12px;
    color: #94a3b8;
    padding: 10px 14px;
    border-radius: 16px;
    background: rgba(148, 163, 184, 0.1);
    width: max-content;
}

.slide-fade-enter-active,
.slide-fade-leave-active {
    transition: all 0.22s ease;
}

.slide-fade-enter-from,
.slide-fade-leave-to {
    opacity: 0;
    transform: translateY(4px);
}
</style>
