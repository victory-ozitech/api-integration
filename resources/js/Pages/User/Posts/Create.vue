<script setup>
import { ref } from "vue";
import { router } from "@inertiajs/vue3";
import { createPost } from "@/utils/postUtils";
import UserLayout from "@/Layouts/UserLayout.vue";

const content = ref("");
const image = ref("");
const scheduled_at = ref("");

const submit = () => {
    createPost({
        content: content.value,
        image: image.value,
        scheduled_at: scheduled_at.value || null,
    });

    router.get(route("posts.index"));
};
</script>

<template>
    <UserLayout>
        <div class="create-wrap">

            <!-- Header -->
            <div class="page-header">
                <h2>Create Post</h2>
                <p>Share something with your audience</p>
            </div>

            <!-- Card -->
            <div class="create-card">

                <!-- Content -->
                <label class="label" for="content">Content</label>
                <textarea v-model="content" id="content" class="input textarea" placeholder="Write something meaningful..."
                    rows="5" />

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
                    <button class="btn btn-primary-custom" @click="submit">
                        Publish Post
                    </button>
                </div>

            </div>

        </div>
    </UserLayout>
</template>

<style scoped>
.create-wrap {
    max-width: 720px;
    margin: 0 auto;
    padding: 30px 20px;
}

/* Header */
.page-header h2 {
    font-weight: 800;
    color: #0f172a;
    margin-bottom: 4px;
}

.page-header p {
    color: #64748b;
    margin-bottom: 24px;
}

/* Card */
.create-card {
    background: rgba(255, 255, 255, 0.75);
    backdrop-filter: blur(12px);

    border-radius: 28px;
    padding: 26px;

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