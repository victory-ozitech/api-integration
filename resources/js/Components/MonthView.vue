<script setup>
import { computed } from "vue";

// Props
const props = defineProps({
    posts: Array,
    currentDate: Date
});

const weekdayLabels = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];

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

const isToday = (date) => {
    if (!date) return false;
    return new Date(date).toDateString() === new Date().toDateString();
};

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
    <div class="month-calendar">
        <div class="calendar-weekdays">
            <div v-for="label in weekdayLabels" :key="label" class="weekday-label">
                {{ label }}
            </div>
        </div>

        <div class="calendar-grid">
            <div
                v-for="(day, i) in monthDays"
                :key="i"
                :class="['calendar-cell', { 'calendar-cell--empty': !day, 'calendar-cell--today': isToday(day) }]"
            >
                <div v-if="day" :class="['date', { 'date--today': isToday(day) }]">
                    {{ day.getDate() }}
                </div>

                <transition-group name="fade" tag="div" class="event-list">
                    <div v-for="post in getPostsForDate(day)" :key="post.id" class="post-pill">
                        {{ post.content.slice(0, 30) }}
                    </div>
                </transition-group>
            </div>
        </div>
    </div>
</template>

<style>
.month-calendar {
    display: flex;
    flex-direction: column;
    gap: 14px;
}

.calendar-weekdays {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 10px;
}

.weekday-label {
    font-size: 12px;
    font-weight: 700;
    color: #707683;
    text-align: center;
    text-transform: uppercase;
    letter-spacing: 0.08em;
}

.calendar-grid {
    display: grid;
    grid-template-columns: repeat(7, minmax(0, 1fr));
    gap: 12px;
}

.calendar-cell {
    min-height: 170px;
    padding: 14px;
    border-radius: 20px;
    background: #ffffff;
    border: 1px solid rgba(15, 23, 42, 0.08);
    box-shadow: 0 5px 18px rgba(15, 23, 42, 0.04);
    display: flex;
    flex-direction: column;
    transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease;
}

.calendar-cell:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 26px rgba(15, 23, 42, 0.08);
    border-color: rgba(15, 23, 42, 0.1);
}

.calendar-cell--empty {
    background: #f8fafc;
}

.calendar-cell--today {
    border-color: #6366f1;
}

.date {
    width: max-content;
    padding: 8px 12px;
    border-radius: 999px;
    font-size: 13px;
    font-weight: 700;
    color: #334155;
    margin-bottom: 12px;
}

.date--today {
    background: #eef2ff;
    color: #3730a3;
}

.event-list {
    display: grid;
    gap: 10px;
    flex: 1;
}

.post-pill {
    display: block;
    font-size: 12px;
    line-height: 1.4;
    background: linear-gradient(135deg, #4f46e5, #2563eb);
    color: #fff;
    padding: 10px 12px;
    border-radius: 16px;
    box-shadow: 0 6px 16px rgba(15, 23, 42, 0.08);
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.2s ease, transform 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
    transform: translateY(4px);
}
</style>
