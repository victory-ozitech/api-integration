<script setup>
defineProps(["modelValue"]);
defineEmits(["update:modelValue"]);
</script>

<template>
    <div class="btn-group">
        <button class="btn" :class="modelValue === 'list' ? 'btn-dark' : 'btn-outline-dark'"
            @click="$emit('update:modelValue', 'list')">
            List
        </button>

        <button class="btn" :class="modelValue === 'calendar' ? 'btn-dark' : 'btn-outline-dark'"
            @click="$emit('update:modelValue', 'calendar')">
            Calendar
        </button>
    </div>
</template>