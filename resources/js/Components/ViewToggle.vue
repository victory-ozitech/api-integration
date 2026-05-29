<script setup>
defineProps(["modelValue"]);
defineEmits(["update:modelValue"]);
</script>

<template>
    <div class="cm-btn-group">
        <button class="btn" :class="modelValue === 'list' ? 'active' : ''" @click="$emit('update:modelValue', 'list')">
            List
        </button>

        <button class="btn" :class="modelValue === 'calendar' ? 'active' : ''"
            @click="$emit('update:modelValue', 'calendar')">
            Calendar
        </button>
    </div>
</template>


<style lang="scss">
@use '@sass/app.scss' as *;
@use "@sass/mixins.scss" as mixin;
@use "@sass/variables.scss" as *;

.cm-btn-group {
    gap: 0.25rem;
    display: flex;
    padding: 0.25rem;
    border-radius: 8px;
    border: 1px solid rgba(148, 163, 184, 0.3);

    .btn {
        border: none;
        background: transparent;
        padding: 0.25rem 0.75rem;

        transition:
            background-color 0.25s ease,
            color 0.25s ease,
            transform 0.15s ease;

        &.active {
            color: #fff;
            background: #7A9B88 !important;
        }

        &:hover {
            // background: #331f000f;
            transform: scale(0.97);
            background: $secondary;
        }
        
        &:active {
            transform: scale(1.01);
        }
    }
}
</style>