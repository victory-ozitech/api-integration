<template>
    <div class="modal-overlay" @click.self="closeModal">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="premium-card d-flex flex-column">

                <!-- Glow -->
                <div class="card-glow"></div>

                <!-- Header -->
                <div class="card-header-custom">

                    <div class="user-block">

                        <div class="channels-stack">
                            <div v-for="(item, index) in props.post.channels.slice(0, 3)" :key="item.id"
                                class="channel-avatar" :style="{ zIndex: 10 - index }">
                                <img :src="item.channel.avatar" />
                            </div>

                            <div v-if="props.post.channels.length > 3" class="channel-more">
                                +{{ props.post.channels.length - 3 }}
                            </div>
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
                    <!-- If no media -->
                    <div v-if="!props.post.media || props.post.media?.length === 0" class="default-preview">
                        <div class="image-placeholder">

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
                    <template v-if="props.post.media?.length && props.post.media?.every(isImage)">
                        <!-- If multiple images -->
                        <div v-if="props.post.media?.length" class="media-grid">
                            <template v-if="props.post.media.length === 1">
                                <img :src="getMediaUrl(props.post.media[0])" class="single-media" />
                            </template>

                            <template v-else-if="props.post.media.length === 2">
                                <div class="two-grid">
                                    <img v-for="(file, i) in props.post.media" :key="i" :src="getMediaUrl(file)" />
                                </div>
                            </template>

                            <template v-else-if="props.post.media.length === 3">
                                <div class="three-grid">
                                    <img :src="getMediaUrl(props.post.media[0])" class="large" />
                                    <div class="two-small">
                                        <img v-for="(file, i) in props.post.media.slice(1)" :key="i"
                                            :src="getMediaUrl(file)" />
                                    </div>
                                </div>
                            </template>

                            <template v-else>
                                <div class="four-grid">
                                    <img v-for="(file, i) in props.post.media.slice(0, 4)" :key="i"
                                        :src="getMediaUrl(file)" />
                                    <div v-if="props.post.media.length > 4" class="overlay">+{{ props.post.media.length
                                        - 4
                                    }}
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>
                    <template v-else-if="props.post.media?.length && props.post.media?.every(isVideo)">
                        <template v-if="props.post.media?.length">
                            <template v-for="media in props.post.media" :key="media.url">
                                <!-- If video -->
                                <div v-if="isVideo(media)">
                                    <video class="post-video" controls>
                                        <source :src="media.url" :type="media.type" />
                                        Your browser does not support video playback.
                                    </video>
                                    🎥 Video
                                </div>
                            </template>
                        </template>
                    </template>
                    <template v-else>
                        <div class="mixed-warning">Mixed media not supported in preview</div>
                    </template>
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
                        <button v-if="props.post?.is_scheduled" class="action-btn" @click.stop="editPost(post)">
                            <i class="fa-regular fa-pen-to-square"></i>
                        </button>

                <a v-else class="view-post-btn action-btn" :href="route('posts.show', { id: props.post.id })"
                    target="_blank" rel="noopener noreferrer">
                    <i class="fa-solid fa-up-right-from-square"></i>
                    <span>View Post</span>
                </a>

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
import { onMounted } from 'vue';


// Props
const props = defineProps({
    post: Object,
    selectedDate: {
        type: Date,
        default: '',
        required: true,
    },
});

onMounted(() => {
    console.log("Post for Modal:", props.post);
})



// Utility Methods

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

const isImage = (media) => {
    const type = media?.mime_type || media?.type || '';
    return type.includes('image');
};

const isVideo = (media) => {
    const type = media?.mime_type || media?.type || '';
    return type.includes('video');
};

const getMediaUrl = (media) => {
    try {
        if (media.file_path) {
            return media.file_path.startsWith('/')
                ? media.file_path
                : `/${media.file_path}`;
        }

        if (typeof File !== 'undefined' && media instanceof File) {
            return URL.createObjectURL(media);
        }
    } catch (e) {
        console.warn('Error getting media URL:', e);
    }

    return '';
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


    &:hover {
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

        @media (max-width: 350px) {
            gap: 10px;
            flex-direction: column;
        }

        .user-block {
            display: flex;
            align-items: center;
            gap: 14px;
            flex-direction: column;

            .channels-stack {
                display: flex;
                align-items: center;
                position: relative;

                .channel-avatar {
                    width: 48px;
                    height: 48px;
                    border-radius: 50%;
                    overflow: hidden;
                    border: 3px solid white;
                    box-shadow: 0 8px 24px rgba(15, 23, 42, 0.08);
                    position: relative;
                    z-index: 10;

                    margin-left: -16px;
                    /* More overlap */
                    transition: transform 0.25s ease;

                    &:first-child {
                        margin-left: 0;
                        z-index: 20;
                    }

                    &:hover {
                        transform: translateY(-4px) scale(1.05);
                        z-index: 30;
                    }

                    img {
                        width: 100%;
                        height: 100%;
                        object-fit: cover;
                        display: block;
                    }
                }

                .channel-more {
                    width: 48px;
                    height: 48px;
                    border-radius: 50%;
                    margin-left: -16px;
                    background: linear-gradient(135deg, #10b981, #34d399);
                    color: white;
                    font-size: 12px;
                    font-weight: 700;
                    border: 3px solid white;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    box-shadow: 0 8px 24px rgba(15, 23, 42, 0.08);
                    z-index: 5;
                }
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
        }

        /* Status */
        .status-pill {
            padding: 8px 14px;

            border-radius: 999px;

            font-size: 11px;
            font-weight: 700;

            text-transform: uppercase;
            letter-spacing: .6px;

            &.draft {
                background: rgba(148, 163, 184, .12);
                color: #64748b;
            }

            &.scheduled {
                background: rgba(251, 191, 36, .12);
                color: #d97706;
            }

            &.published {
                background: rgba(16, 185, 129, .12);
                color: #059669;
            }
        }
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
    .mixed-warning {
        margin-left: 5%;
        color: #ff0000;
    }

    .media-shell {
        position: relative;
        margin-bottom: 22px;
        flex-grow: 1; // media takes remaining height
        display: flex;
        flex-direction: column;
        max-height: 350px;
        max-width: 350px;

        /* Placeholder */
        .image-placeholder {
            position: relative;

            height: 250px;

            border-radius: 24px;

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
            }
        }

        .media-grid {
            display: grid;
            gap: 4px;
            border-radius: 24px;
            max-height: 350px;
            overflow: hidden; // Make sure children respect radius

            img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                display: block;
                border-radius: 0; // Rounded corners handled by parent wrapper
            }

            .two-grid,
            .three-grid,
            .four-grid {
                display: grid;
            }

            .single-media,
            .two-grid>div,
            .three-grid .large,
            .three-grid .two-small>div,
            .four-grid>div {
                border-radius: 0;
                overflow: hidden;
            }

            /* 1 image */
            .single-media {
                width: 100%;
                // height: 350px;
                border-radius: 24px;
                overflow: hidden;
            }

            /* 2 images */
            .two-grid {
                grid-template-columns: 1fr 1fr;

                >div {
                    overflow: hidden;
                    border-radius: 24px;
                    // height: 350px;
                }
            }

            /* 3 images */
            .three-grid {
                grid-template-columns: 2fr 1fr;
                grid-template-rows: 1fr 1fr;
                gap: 2px;

                .large {
                    grid-row: span 2;
                    overflow: hidden;
                    border-radius: 24px 0 0 24px;
                }

                .two-small {
                    display: grid;
                    grid-template-rows: 1fr 1fr;
                    gap: 2px;

                    >div {
                        overflow: hidden;
                        border-radius: 0 24px 24px 0;
                    }
                }
            }

            /* 4+ images */
            .four-grid {
                grid-template-columns: 1fr 1fr;
                grid-template-rows: 1fr 1fr;
                gap: 2px;
                position: relative;

                >div {
                    overflow: hidden;
                    border-radius: 24px;
                }
                img {
                    height: 100px;
                }

                .overlay {
                    position: absolute;
                    bottom: 8px;
                    right: 8px;
                    background: rgba(0, 0, 0, 0.6);
                    color: #fff;
                    font-weight: bold;
                    padding: 6px 10px;
                    border-radius: 8px;
                }
            }
        }

        .post-video {
            width: 100%;
            border-radius: 24px;
            overflow: hidden;
            max-height: 250px;
            box-shadow: 0 12px 32px rgba(15, 23, 42, .12);
            margin-bottom: 6px;
        }
    }

    /* Footer */
    .card-footer-custom {
        display: flex;
        justify-content: space-between;
        align-items: center;

        /* Stats */
        .stats-row {
            display: flex;
            gap: 18px;

            .stat-item {
                display: flex;
                align-items: center;
                gap: 8px;

                color: #475569;

                font-size: 14px;
                font-weight: 600;

                i {
                    color: #94a3b8;
                }
            }
        }


        /* Actions */
        .actions-row {
            display: flex;
            gap: 10px;

            

            .view-post-btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                height: 42px;
                width: 42px;
                padding: 0 12px;
                border: none;
                border-radius: 14px;
                background: rgba(255, 255, 255, 0.7);
                color: #334155;
                text-decoration: none;
                transition: width 0.3s ease, background 0.3s ease;
                box-shadow: 0 6px 18px rgba(15, 23, 42, 0.05);

                i {
                    font-size: 18px;
                    flex-shrink: 0;
                    /* ✅ Icon stays visible */
                }

                span {
                    opacity: 0;
                    width: 0;
                    overflow: hidden;
                    white-space: nowrap;
                    transition: opacity 0.3s ease, width 0.3s ease;
                }

                &:hover {
                    gap: 8px;
                    width: 130px;
                    background: white;

                    span {
                        opacity: 1;
                        width: auto;
                    }
                }
            }




            .action-btn {
                min-width: 42px;    
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

                &:hover {
                    transform: translateY(-2px);

                    background: white;
                }

                &delete:hover {
                    color: #ef4444;
                }
            }
        }
    }
}
</style>