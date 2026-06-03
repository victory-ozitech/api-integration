<template>
    <div class="calendar-scroll">
        <div class="month-calendar">
            <div class="calendar-weekdays">
                <div v-for="label in weekdayLabels" :key="label" class="weekday-label">
                    {{ label }}
                </div>
            </div>
    
            <div class="calendar-grid">
                <div v-for="(day, i) in monthDays" :key="i" :class="['calendar-cell', { 'calendar-cell--outside': !day.isCurrentMonth, 'calendar-cell--today': isToday(day.date) }
                ]">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div :class="['date', { 'date--today': isToday(day.date), 'date--outside': !day.isCurrentMonth }]">
                            {{ day.date.getDate() }}
                        </div>
    
                        <button v-if="isFutureOrToday(day.date)" class="add-btn" @click.stop="openModal(day.date)"
                            title="Schedule New Post">
                            <i class="fa-solid fa-plus"></i>
                        </button>
                    </div>
    
                    <transition-group name="fade" tag="div" class="event-list">
                        <div v-for="post in getPostsForDate(day.date)" :key="post.id" :title="post.content"
                            class="post-pill" @click.stop="openPostsModal({post, date: day.date})">
                            {{ post.message.slice(0, 30) }}
                        </div>
                    </transition-group>
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

const weekdayLabels = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];

// const monthDays = computed(() => {
//     const date = new Date(props.currentDate);

//     const year = date.getFullYear();
//     const month = date.getMonth();

//     const firstDayIndex = new Date(year, month, 1).getDay();

//     const daysInMonth = new Date(year, month + 1, 0).getDate();

//     const daysInPrevMonth = new Date(year, month, 0).getDate();

//     const days = [];

//     // Previous month days
//     for (let i = firstDayIndex - 1; i >= 0; i--) {
//         days.push({
//             date: new Date(year, month - 1, daysInPrevMonth - i),
//             isCurrentMonth: false,
//         });
//     }

//     // Current month days
//     for (let i = 1; i <= daysInMonth; i++) {
//         days.push({
//             date: new Date(year, month, i),
//             isCurrentMonth: true,
//         });
//     }

//     // Fill remaining cells (42 total = 6 weeks)
//     const remaining = 42 - days.length;

//     for (let i = 1; i <= remaining; i++) {
//         days.push({
//             date: new Date(year, month + 1, i),
//             isCurrentMonth: false,
//         });
//     }

//     return days;
// });

// Methods
const monthDays = computed(() => {
    const base = new Date(props.currentDate);
    const year = base.getFullYear();
    const month = base.getMonth();

    const firstOfMonth = new Date(year, month, 1);
    const startDayIndex = firstOfMonth.getDay(); // 0 = Sunday
    const daysInMonth = new Date(year, month + 1, 0).getDate();

    // Calculate exact cells needed: round up to nearest full week
    const totalCells = Math.ceil((startDayIndex + daysInMonth) / 7) * 7;

    const startDate = new Date(year, month, 1 - startDayIndex);
    const days = [];

    for (let i = 0; i < totalCells; i++) {
        const d = new Date(startDate);
        d.setDate(startDate.getDate() + i);
        days.push({
            date: d,
            isCurrentMonth: d.getMonth() === month
        });
    }

    return days;
});

const isToday = (date) => {
    if (!date) return false;
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

const openPostsModal = ({post, date}) => {
    emit("open-posts-modal", {post, date});
};

// const openPostsModal = (date) => {
//     emit("open-posts-modal", date);
// };
</script>



<style lang="scss">
@use '@sass/app.scss' as *;
@use "@sass/mixins.scss" as mixin;
@use "@sass/variables.scss" as *;

.calendar-scroll {
    width: 100%;
    overflow-x: auto;
    overflow-y: hidden;
}

.month-calendar {
    display: flex;
    flex-direction: column;
    gap: 14px;
    min-width: 900px;
}

.add-btn {
    border: 1px solid rgba(148, 163, 184, 0.3);
    // background: #f8fafc;
    background: transparent;
    border-radius: 4px;
    padding: 2px 4px;
    cursor: pointer;
    @include mixin.dynamic-text(700, 11px, #0f172a, center);
    margin-top: 2px;
    transition: all 0.18s ease;opacity: 0;
        visibility: hidden;
        transform: translateY(4px);

    &:hover {
        background: $secondary;
        // transform: translateY(-1px);
        color: #1F2937;
        border-color: rgba(99, 102, 241, 0.25);
    }
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

.calendar-cell:hover .add-btn {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

.calendar-cell--empty {
    background: #f8fafc;
}

.calendar-cell--today {
    border-color: #6366f1;
}

.date {
    padding: 3px 9px;
    width: max-content;
    border-radius: 50%;
    /* background: #DFF5E7; */
    margin-bottom: 12px;
    @include mixin.dynamic-text(700, 13px, #334155);
}

.date--today {
    background: #eef2ff;
    color: $primary;
}

.calendar-cell--outside {
    background: #f8fafc;
    opacity: 0.65;
}

.date--outside {
    color: #94a3b8;
}

.calendar-cell--outside .post-pill {
    opacity: 0.5;
}

.calendar-cell--outside:hover {
    transform: none;
    box-shadow: 0 5px 18px rgba(15, 23, 42, 0.04);
    border-color: rgba(15, 23, 42, 0.08);
}

.calendar-cell--today .date {
    background: $primary;
    color: white;
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
    background: linear-gradient(135deg, $primary, $secondary);
    color: Ssecondary;
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
