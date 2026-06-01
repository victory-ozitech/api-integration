<template>
    <div class="calendar-shell">
        <div class="calendar-header mb-3">
            <div class="calendar-controls">
                <div class="navigation-group">
                    <button type="button" class="nav-btn" @click="prev"><i class="fa-solid fa-arrow-left"></i></button>
                    <button type="button" class="nav-btn" @click="next"><i class="fa-solid fa-arrow-right"></i></button>
                    <button type="button" class="today-btn" @click="goToToday">Today</button>
                </div>

                <div class="calendar-title">
                    <h4>{{ currentLabel }}</h4>
                </div>

                <div class="view-switcher">
                    <button type="button" @click="view = 'month'" class="view-btn"
                        :class="{ 'view-btn--active': view === 'month' }">
                        Month
                    </button>
                    <button type="button" @click="view = 'week'" class="view-btn"
                        :class="{ 'view-btn--active': view === 'week' }">
                        Week
                    </button>
                    <button type="button" @click="view = 'day'" class="view-btn"
                        :class="{ 'view-btn--active': view === 'day' }">
                        Day
                    </button>
                </div>
            </div>
        </div>

        <transition name="calendar-fade" mode="out-in">
            <component :is="currentView" :posts="posts" :currentDate="currentDate" :key="view"
                @open-modal="handleOpenModal" @open-edit-modal="forwardOpenEditModal"
                @open-posts-modal="handleOpenPostsModal" />
        </transition>
    </div>
</template>



<script setup>
import { ref, computed } from "vue";

//Views
import MonthView from "./MonthView.vue";
import WeekView from "./WeekView.vue";
import DayView from "./DayView.vue";

// Props
const props = defineProps({
    posts: Array,
    currentDate: {
        type: [Date, String],
        required: true,
    },
});

//State(s)
const view = ref("month"); // month | week | day

const views = {
    month: MonthView,
    week: WeekView,
    day: DayView,
};

// Computed
const currentView = computed(() => views[view.value]);

const currentLabel = computed(() => {
    const d = props.currentDate;

    if (view.value === "month") return formatMonth(d);
    if (view.value === "week") return formatWeek(d);
    return formatDay(d);
});

// Navigation
const next = () => {
    const d = new Date(props.currentDate);

    if (view.value === "month") d.setMonth(d.getMonth() + 1);
    if (view.value === "week") d.setDate(d.getDate() + 7);
    if (view.value === "day") d.setDate(d.getDate() + 1);

    // props.currentDate = d;

    emit("update:currentDate", d);
};

const prev = () => {
    const d = new Date(props.currentDate);

    if (view.value === "month") d.setMonth(d.getMonth() - 1);
    if (view.value === "week") d.setDate(d.getDate() - 7);
    if (view.value === "day") d.setDate(d.getDate() - 1);

    // props.currentDate = d;

    emit("update:currentDate", d);
};

const goToToday = () => {
    emit("update:currentDate", new Date());
};

// Formatting
const formatMonth = (date) =>
    date.toLocaleString("default", { month: "long", year: "numeric" });

const formatDay = (date) =>
    date.toLocaleString("default", {
        month: "long",
        day: "numeric",
        year: "numeric",
    });

const formatWeek = (date) => {
    const start = new Date(date);
    start.setDate(start.getDate() - start.getDay());

    const end = new Date(start);
    end.setDate(start.getDate() + 6);

    const startStr = start.toLocaleString("default", {
        month: "short",
        day: "numeric",
    });

    const endStr = end.toLocaleString("default", {
        month: "short",
        day: "numeric",
    });

    return `${startStr} - ${endStr}`;
};

// Emit
const emit = defineEmits([
    "open-modal",
    "open-posts-modal",
    "open-edit-modal",
    "update:currentDate",
]);

const handleOpenModal = (date) => {
    emit("open-modal", date);
};

const handleOpenPostsModal = ({post, date}) => {
    emit("open-posts-modal", { post, date });
};

const forwardOpenEditModal = (post) => {
    emit("open-edit-modal", post);
};
</script>



<style lang="scss">
@use '@sass/app.scss' as *;
@use "@sass/mixins.scss" as mixin;
@use "@sass/variables.scss" as *;

.calendar-shell {
    display: flex;
    flex-direction: column;
    gap: 18px;
}

.calendar-header {
    padding: 18px 20px;
    background: #ffffff;
    border-radius: 24px;
    box-shadow: 0 18px 48px rgba(15, 23, 42, 0.08);
    border: 1px solid rgba(148, 163, 184, 0.18);
}

.calendar-controls {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
}

.navigation-group {
    display: flex;
    align-items: center;
    gap: 12px;
}

.nav-btn,
.today-btn,
.view-btn {
    border: 1px solid rgba(148, 163, 184, 0.3);
    background: #f8fafc;
    color: #0f172a;
    border-radius: 14px;
    padding: 10px 16px;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.18s ease;
}

.nav-btn {
    width: 42px;
    min-width: 42px;
    height: 42px;
    padding: 0;
    font-size: 16px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.nav-btn:hover,
.today-btn:hover,
.view-btn:hover {
    transform: translateY(-1px);
    background: $secondary;
    color: #1F2937;
    border-color: rgba(99, 102, 241, 0.25);
}

.today-btn {
    color: #334155;
}

.view-switcher {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(99, 102, 241, 0.08);
    border-radius: 999px;
    padding: 6px;
}

.view-btn {
    background: transparent;
    border: none;
    color: #475569;
    min-width: 72px;
    border-radius: 999px;
}

.view-btn--active {
    background: $primary;
    color: white;
    box-shadow: 0 10px 24px rgba(99, 102, 241, 0.18);
}

.calendar-title h4 {
    margin: 0;
    font-size: 20px;
    font-weight: 800;
    color: #0f172a;
}

.calendar-fade-enter-active,
.calendar-fade-leave-active {
    transition: opacity 0.24s ease;
}

.calendar-fade-enter-from,
.calendar-fade-leave-to {
    opacity: 0;
}
</style>
