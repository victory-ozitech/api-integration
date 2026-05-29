<template>
    <div v-if="isVisible" class="modal-overlay" @click.self="closeModal">
        <div class="modal-content">
            
            <!-- Card -->
            <div class="create-card">
            
                <!-- Modal Header -->
                <div class="cm-modal-header">
                    <h3>{{ isEditMode ? "Edit Scheduled Post" : "Create New Post" }}</h3>
                    <button class="close-btn" @click="closeModal">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <!-- Content -->
                <label class="label" for="content">Content</label>
                <textarea v-model="content" id="content" class="input textarea"
                    placeholder="Write something meaningful..." rows="5" />

                <!-- Media -->
                <label class="label mt-3" for="image">Image URL</label>
                <div class="input-wrapper">
                    <i class="fa-regular fa-image"></i>
                    <input v-model="image" id="image" class="input" placeholder="Paste image link (optional)" />
                </div>

                <!-- Scheduling -->
                <label class="label mt-3" for="scheduled_at">Schedule</label>
                <div class="input-wrapper">
                    <i class="fa-regular fa-clock"></i>
                    <input type="datetime-local" v-model="scheduled_at" id="scheduled_at" class="input" />
                </div>

                <!-- Actions -->
                <div class="actions">
                    <button class="btn btn-primary-custom" @click="submitForm">
                        Publish Post
                    </button>
                </div>

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
        type: [String, Date, null],
        required: true,
    },
});

// Emits
const emit = defineEmits(["closeModal", "formSubmit"]);


// Computed Properties
const isEditMode = computed(() => {
    return !!props.post;
});

// Utility Methods
const submitForm = () => {
    emit("formSubmit");

    closeModal();
};

const closeModal = () => {
    emit("closeModal");
};
</script>

<style lang="scss" scoped>
@use '@sass/app.scss' as *;
@use "@sass/mixins.scss" as mixin;
@use "@sass/variables.scss" as *;

.modal-overlay {position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
}

.modal-content {
    width: 90%;
    max-width: 90%;
    max-height: 90vh;
    // display: flex;
    // flex-direction: column;
    overflow-y: auto;
        -webkit-overflow-scrolling: touch;
    overflow-x: hidden;
    // overflow: hidden;
    
    /* Firefox */
        scrollbar-width: thin;
        scrollbar-color: rgba(255, 255, 255, 0.35) transparent;
    
        /* Optional glass effect */
        backdrop-filter: blur(10px);
}

.close-btn {
    background: none;
    border: none;
    font-size: 20px;
    line-height: 1;
    cursor: pointer;
}

.cm-modal-header {
    @include mixin.spread(10px, center, row, wrap);
}

/* Card */
.create-card {
    background: rgba(255, 255, 255, 0.75);
    backdrop-filter: blur(12px);

    border-radius: 28px;
    padding: 20px;

    box-shadow:
        0 20px 60px rgba(15, 23, 42, 0.06);

    border: 1px solid rgba(255, 255, 255, 0.6);
}

/* Labels */
.label {
    display: block;
    font-size: 13px;
    font-weight: 700;
    color: #475569;
    margin-bottom: 8px;
}

/* Image Preview */
.img-preview {
    margin-bottom: 16px;
}

.preview-img {
    max-width: 100px;
    height: auto;
    border-radius: 4px;
}

.no-preview {
    background: rgba(241, 245, 249, 0.6);
    border-radius: 16px;
    padding: 14px;
    text-align: center;
    color: #94a3b8;
}

/* Inputs */
.input {
    width: 100%;
    border: none;
    outline: none;

    background: rgba(241, 245, 249, 0.6);
    border-radius: 16px;

    padding: 14px 14px;

    font-size: 14px;
    color: #0f172a;

    transition: all 0.2s ease;
}

.input:focus {
    background: white;
    box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.15);
}

/* Textarea */
.textarea {
    resize: none;
    line-height: 1.6;
}

/* Input with icon */
.input-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}

.input-wrapper i {
    position: absolute;
    left: 14px;
    color: #94a3b8;
    font-size: 14px;
}

.input-wrapper input {
    padding-left: 38px;
}

/* Actions */
.actions {
    margin-top: 24px;
}

/* Button */
.btn-primary-custom {
    border: none;

    padding: 14px;

    border-radius: 16px;

    font-weight: 700;
    font-size: 14px;

    color: white;

    background: linear-gradient(135deg,
            #10b981,
            #34d399);

    box-shadow: 0 12px 30px rgba(16, 185, 129, 0.25);

    transition: all 0.25s ease;
}

.btn-primary-custom:hover {
    transform: translateY(-2px);
    box-shadow: 0 18px 40px rgba(16, 185, 129, 0.35);
}
</style>