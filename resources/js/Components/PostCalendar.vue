<script setup>
import { ref, computed } from "vue";

//Views
import MonthView from "./MonthView.vue";
import WeekView from "./WeekView.vue";
import DayView from "./DayView.vue";

// Props
const props = defineProps({
    posts: Array
});

//State(s)
const view = ref("month"); // month | week | day
const currentDate = ref(new Date());

const views = {
    month: MonthView,
    week: WeekView,
    day: DayView,
};

const currentView = computed(() => views[view.value]);

// Navigation
const next = () => {
    const d = new Date(currentDate.value);

    if (view.value === "month") d.setMonth(d.getMonth() + 1);
    if (view.value === "week") d.setDate(d.getDate() + 7);
    if (view.value === "day") d.setDate(d.getDate() + 1);

    currentDate.value = d;
};

const prev = () => {
    const d = new Date(currentDate.value);

    if (view.value === "month") d.setMonth(d.getMonth() - 1);
    if (view.value === "week") d.setDate(d.getDate() - 7);
    if (view.value === "day") d.setDate(d.getDate() - 1);

    currentDate.value = d;
};
</script>

<template>
    <div>

        <!-- Controls -->
        <div class="d-flex justify-content-between mb-3">
            <div>
                <button class="btn btn-sm btn-outline-dark me-2" @click="prev">←</button>
                <button class="btn btn-sm btn-outline-dark" @click="next">→</button>
            </div>

            <div>
                <button @click="view = 'month'" class="btn btn-sm"
                    :class="view === 'month' ? 'btn-dark' : 'btn-outline-dark'">Month</button>

                <button @click="view = 'week'" class="btn btn-sm"
                    :class="view === 'week' ? 'btn-dark' : 'btn-outline-dark'">Week</button>

                <button @click="view = 'day'" class="btn btn-sm"
                    :class="view === 'day' ? 'btn-dark' : 'btn-outline-dark'">Day</button>
            </div>
        </div>

      <!-- Views -->
        <component :is="currentView" :posts="posts" :currentDate="currentDate" />

    </div>
</template>