<script setup>
import { ref } from "vue";
import { deletePost } from "@/utils/postUtils";

const imageError = ref(false);

const props = defineProps({
    post: Object
});

// onMounted(() => {
//     console.log("Loaded post:", props.post);
// });

const emit = defineEmits(["refresh"]);

const remove = () => {
    if (!confirm("Delete this post?")) return;

    deletePost(props.post.id);
    emit("refresh");
};

const formatDate = (date) => {
    if (!date) return "Not scheduled";
    
    const parsedDate = new Date(date.replace(" ", "T"));
    
    if (isNaN(parsedDate.getTime())) return "Invalid date";

    return new Intl.DateTimeFormat("en-US", {
        month: "short",
        day: "numeric",
        hour: "numeric",
        minute: "2-digit",
    }).format(parsedDate);
};
</script>

<template>
    <div class="premium-card">

        <!-- Glow -->
        <div class="card-glow"></div>

        <!-- Header -->
        <div class="card-header-custom">

            <div class="user-block">

                <div class="avatar-ring">
                    <img :src="props.post.channels[0].channel.avatar" class="avatar" />
                </div>

                <div>
                    <div class="user-name">
                        {{ props.post.channels[0].channel.channel_name }}
                    </div>

                    <div class="post-date">
                        <i class="fa-regular fa-clock"></i>
                        {{ formatDate(props.post.scheduled_at ?? '') }}
                    </div>
                </div>

            </div>

            <div class="status-pill" :class="props.post.status">
                {{ props.post.status }}
            </div>

        </div>

        <!-- Content -->
        <div class="post-content">
            {{ props.post.message }}
        </div>

        <!-- Media -->
        <div class="media-shell">

            <img v-if="props.post.media[0].file_path && !imageError" :src="props.post.media[0].file_path" class="post-image"
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
                    <span>{{ props.post.likes_count ?? 3 }}</span>
                </div>

                <div class="stat-item">
                    <i class="fa-regular fa-comment"></i>
                    <span>{{ props.post.comments_count ?? 3 }}</span>
                </div>

                <div class="stat-item">
                    <i class="fa-solid fa-retweet"></i>
                    <span>{{ props.post.shares_count ?? 3 }}</span>
                </div>

            </div>

            <div class="actions-row">

                <a :href="route('posts.edit', { id: props.post.id })" class="action-btn">
                    <i class="fa-regular fa-pen-to-square"></i>
                </a>

                <button class="action-btn delete" @click="remove">
                    <i class="fa-regular fa-trash-can"></i>
                </button>

            </div>

        </div>

    </div>
</template>


<style scoped>
.premium-card {
    position: relative;
    overflow: hidden;

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

    height: 100%;
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