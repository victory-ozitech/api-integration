<template>
    <div v-if="isVisible" class="modal-overlay" @click.self="closeModal">
        <div class="modal-content">
            <!-- Modal Header -->

            <div class="model-header">
                PLACEHOLDER CONTENT
                <button class="close-btn" @click="closeModal">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <button class="btn btn-primary" @click.stop="editPost(post)" >Edit Post</button>

            <!-- Modal Footer -->
            <div class="modal-footer">
                <button @click="addNew" class="btn">
                    + Add New Post
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

// Props
const props = defineProps({
    isVisible: {
        type: Boolean,
        required: true,
    },
    post: {
        type: Object,
        default: null,
    },
    selectedDate: {
        type: String,
        default: '',
        required: true,
        // type: Object,
        // default: () => { },
        // required: true,
        // type: Date,
        // required: true,
    },
});


// Computed Properties

// Utility Methods

// Emit
const emit = defineEmits([
    "closeModal",
    "open-modal",
    "open-edit-modal",
]);

const addNew = (date) => {
    emit("open-modal", selectedDate.value);
};

const editPost = (post) => {
    emit("open-edit-modal", post);
};
const closeModal = () => {
    emit("closeModal");
};
</script>

<style lang="scss" scoped>
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.6);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 10000;
    padding: 20px;
}

.modal-content {
    background: white;
    border-radius: 12px;
    width: 100%;
    max-width: 1000px;
    height: 100%;
    display: flex;
    flex-direction: column;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    overflow: hidden;
}

.close-btn {
    background: none;
    border: none;
    font-size: 20px;
    line-height: 1;
    cursor: pointer;
}
</style>