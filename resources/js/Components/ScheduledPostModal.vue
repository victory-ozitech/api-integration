<template>
    <div class="modal-overlay" @click.self="closeModal">
        <div class="modal-content">
            <!-- Modal Header -->


            <div class="premium-card">

                <!-- Glow -->
                <div class="card-glow"></div>

                <!-- Header -->
                <div class="card-header-custom">

                    <div class="user-block">

                        <div class="avatar-ring">
                            <img :src="post.user_avatar" class="avatar" />
                        </div>

                        <div>
                            <div class="user-name">
                                {{ post.user_name }}
                            </div>

                            <div class="post-date">
                                <i class="fa-regular fa-clock"></i>
                                {{ formatDate(post.created_at) }}
                            </div>
                        </div>

                    </div>

                    <div class="d-flex gap-2">
                        <div class="status-pill" :class="post.status">
                            {{ post.status }}
                        </div>

                        <button class="close-btn" @click="closeModal">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                </div>

                <!-- Content -->
                <div class="post-content">
                    {{ post.content }}
                </div>

                <!-- Media -->
                <div class="media-shell">

                    <img v-if="post.image && !imageError" :src="post.image" class="post-image"
                        @error="imageError = true" />

                    <!-- Elegant Placeholder -->
                    <div v-else class="image-placeholder">

                        <div class="placeholder-orb"></div>

                        <div class="placeholder-content">

                            <div class="placeholder-icon">
                                <i class="fa-regular fa-image"></i>
                            </div>

                            <div class="placeholder-title">
                                No Media Preview
                            </div>

                            <div class="placeholder-subtitle">
                                Visual content will appear here
                            </div>

                        </div>

                    </div>

                </div>

                <!-- Footer -->
                <div class="card-footer-custom">

                    <div class="stats-row">

                        <div class="stat-item">
                            <i class="fa-regular fa-heart"></i>
                            <span>{{ post.likes_count }}</span>
                        </div>

                        <div class="stat-item">
                            <i class="fa-regular fa-comment"></i>
                            <span>{{ post.comments_count }}</span>
                        </div>

                        <div class="stat-item">
                            <i class="fa-solid fa-retweet"></i>
                            <span>{{ post.shares_count }}</span>
                        </div>

                    </div>

                    <div class="actions-row">
                        <button class="action-btn" @click.stop="editPost(post)">
                            <i class="fa-regular fa-pen-to-square"></i>
                        </button>

                        <button @click="addNew" class="action-btn">
                            <i class="fa-solid fa-plus"></i>
                        </button>
                    </div>

                </div>

            </div>

        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';


// Props
const props = defineProps({
    post: Object,
    selectedDate: {
        type: Date,
        default: '',
        required: true,
        // type: Object,
        // default: () => { },
        // required: true,
    },
});


const imageError = ref(false);


// Utility Methods
const formatDate = (date) => {
    return new Intl.DateTimeFormat("en-US", {
        month: "short",
        day: "numeric",
        hour: "numeric",
        minute: "2-digit",
    }).format(new Date(date));
};

// Emit
const emit = defineEmits([
    "closeModal",
    "open-modal",
    "open-edit-modal",
]);

const addNew = (date) => {
    emit("open-modal", props.selectedDate);
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
    padding: 15px;
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


.premium-card {
    position: relative;

    background:
        rgba(255, 255, 255, 0.72);

    backdrop-filter: blur(18px);

    border-radius: 30px;

    padding: 22px;

    box-shadow:
        0 10px 40px rgba(15, 23, 42, 0.06),
        inset 0 1px rgba(255, 255, 255, 0.8);

    transition:
        transform .35s ease,
        box-shadow .35s ease;

    // height: 100%;
}

.premium-card:hover {
    transform: translateY(-6px);

    box-shadow:
        0 24px 60px rgba(15, 23, 42, 0.12);
}

/* Ambient glow */
.card-glow {
    position: absolute;

    width: 220px;
    height: 220px;

    background:
        radial-gradient(circle,
            rgba(52, 211, 153, .18),
            transparent 70%);

    top: -80px;
    right: -80px;

    pointer-events: none;
}

/* Header */
.card-header-custom {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;

    margin-bottom: 18px;
}

.user-block {
    display: flex;
    align-items: center;
    gap: 14px;
}

.avatar-ring {
    padding: 2px;

    border-radius: 50%;

    background:
        linear-gradient(135deg,
            #d1fae5,
            #a7f3d0);
}

.avatar {
    width: 50px;
    height: 50px;

    border-radius: 50%;
    object-fit: cover;

    display: block;

    border: 3px solid white;
}

.user-name {
    font-weight: 700;
    color: #0f172a;
    font-size: 15px;
}

.post-date {
    color: #64748b;
    font-size: 13px;

    display: flex;
    align-items: center;
    gap: 6px;

    margin-top: 4px;
}

/* Status */
.status-pill {
    padding: 8px 14px;

    border-radius: 999px;

    font-size: 11px;
    font-weight: 700;

    text-transform: uppercase;
    letter-spacing: .6px;
}

.status-pill.draft {
    background: rgba(148, 163, 184, .12);
    color: #64748b;
}

.status-pill.scheduled {
    background: rgba(251, 191, 36, .12);
    color: #d97706;
}

.status-pill.published {
    background: rgba(16, 185, 129, .12);
    color: #059669;
}

/* Content */
.post-content {
    font-size: 16px;
    line-height: 1.9;

    color: #0f172a;

    margin-bottom: 20px;

    font-weight: 500;
}

/* Media */
.media-shell {
    position: relative;
    margin-bottom: 22px;
}

.post-image,
.image-placeholder {
    width: 100%;
    height: 250px;

    border-radius: 24px;
}

.post-image {
    object-fit: cover;

    display: block;

    box-shadow:
        0 10px 30px rgba(15, 23, 42, .10);

    transition:
        transform .5s ease;
}

.post-image:hover {
    transform: scale(1.02);
}

/* Placeholder */
.image-placeholder {
    position: relative;

    overflow: hidden;

    background:
        linear-gradient(145deg,
            rgba(255, 255, 255, .7),
            rgba(240, 253, 244, .95));

    border:
        1px solid rgba(255, 255, 255, .7);

    display: flex;
    align-items: center;
    justify-content: center;
}

.placeholder-orb {
    position: absolute;

    width: 180px;
    height: 180px;

    background:
        radial-gradient(circle,
            rgba(52, 211, 153, .18),
            transparent 70%);

    border-radius: 50%;
}

.placeholder-content {
    position: relative;
    z-index: 2;

    text-align: center;
}

.placeholder-icon {
    width: 72px;
    height: 72px;

    margin: 0 auto 14px;

    border-radius: 24px;

    background:
        rgba(255, 255, 255, .75);

    display: flex;
    align-items: center;
    justify-content: center;

    font-size: 28px;

    color: #10b981;

    box-shadow:
        0 10px 30px rgba(16, 185, 129, .12);
}

.placeholder-title {
    font-weight: 700;
    color: #0f172a;

    margin-bottom: 6px;
}

.placeholder-subtitle {
    color: #64748b;
    font-size: 14px;
}

/* Footer */
.card-footer-custom {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.stats-row {
    display: flex;
    gap: 18px;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 8px;

    color: #475569;

    font-size: 14px;
    font-weight: 600;
}

.stat-item i {
    color: #94a3b8;
}

/* Actions */
.actions-row {
    display: flex;
    gap: 10px;
}

.action-btn {
    width: 42px;
    height: 42px;

    border: none;
    border-radius: 14px;

    background:
        rgba(255, 255, 255, .7);

    display: flex;
    align-items: center;
    justify-content: center;

    color: #334155;

    text-decoration: none;

    transition: all .25s ease;

    box-shadow:
        0 6px 18px rgba(15, 23, 42, .05);
}

.action-btn:hover {
    transform: translateY(-2px);

    background: white;
}

.action-btn.delete:hover {
    color: #ef4444;
}
</style>