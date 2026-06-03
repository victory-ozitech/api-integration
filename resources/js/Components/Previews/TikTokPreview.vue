<template>
    <div class="social-post preview-tiktok">
        <h2>TikTok Preview</h2>
        <!-- TikTok-style Preview -->
        <div class="tiktok-preview">
            <div class="video-container">
                <div class="video-header">
                    <div class="user-info">
                        <div class="avatar">
                            <img :src="channel.avatar || '/assets/images/profile-img.png'"
                                :alt="channel.channel_name" />
                        </div>
                        <div class="user-details">
                            <div class="username">@{{ channel.account_username ||
                                channel.channel_name.replace(/\s+/g, '').toLowerCase()
                                }}</div>
                            <div class="video-title">{{ form.message || 'Check out this video!'
                                }}</div>
                        </div>
                    </div>
                    <button class="follow-btn">Follow</button>
                </div>

                <div class="video-content">
                    <!-- If no media -->
                    <div v-if="!form.media || form.media?.length === 0" class="default-video-preview">
                        <div class="video-placeholder">
                            <i class="fas fa-video"></i>
                            <p>Add a video to see TikTok preview</p>
                        </div>
                    </div>
                    <template v-if="form.media?.length && form.media?.every(isImage)">

                        <!-- If single image -->
                        <template v-if="(form.media?.length === 1) && isImage(form.media[0])">
                            <img :src="getMediaUrl(form.media[0])" class="w-100"
                                :alt="form.media[0].original_name || form.media[0].name || 'Uploaded image'" />
                        </template>

                        <!-- If multiple images -->
                        <template v-else-if="form.media?.length > 1 && form.media.every(isImage)" class="tiktok-images">
                            <div :id="`tiktokCarousel-${channel.id || channel.channel_id}`" class="carousel slide"
                                data-bs-ride="carousel">
                                <div class="carousel-inner">
                                    <div v-for="(file, i) in form.media" :key="i" class="carousel-item"
                                        :class="{ active: i === 0 }">
                                        <img :src="getMediaUrl(file)" class="d-block w-100" alt="Preview" />
                                    </div>
                                </div>
                                <button class="carousel-control-prev" type="button"
                                    :data-bs-target="`#tiktokCarousel-${channel.id || channel.channel_id}`"
                                    data-bs-slide="prev">
                                    <span class="carousel-control-prev-icon"></span>
                                </button>
                                <button class="carousel-control-next" type="button"
                                    :data-bs-target="`#tiktokCarousel-${channel.id || channel.channel_id}`"
                                    data-bs-slide="next">
                                    <span class="carousel-control-next-icon"></span>
                                </button>
                            </div>
                        </template>
                    </template>
                    <template v-else-if="form.media?.length && form.media?.every(isVideo)">
                        <template v-for="media in form.media" :key="media.url">
                            <!-- If video -->
                            <div v-if="isVideo(media)" class="tiktok-video-player">
                                <video class="tiktok-video" controls>
                                    <source :src="media.url" :type="media.type" />
                                    Your browser does not support video🎥 playback.
                                </video>
                                <div class="video-controls-overlay">
                                    <div class="play-pause">
                                        <i class="fas fa-play"></i>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </template>
                    <template v-else>
                        <div class="mixed-warning">Mixed media not supported in preview</div>
                    </template>
                </div>

                <div class="video-stats">
                    <div class="stats-row">
                        <div class="stat-item">
                            <i class="fas fa-heart"></i>
                            <span>2.5M</span>
                        </div>
                        <div class="stat-item">
                            <i class="fas fa-comment"></i>
                            <span>45.2K</span>
                        </div>
                        <div class="stat-item">
                            <i class="fas fa-share"></i>
                            <span>125K</span>
                        </div>
                    </div>
                </div>

                <div class="video-description">
                    <p class="description-text">{{ form.message || 'Your post text will appear here...' }}</p>
                    <div class="hashtags">
                        <span class="hashtag">#fyp</span>
                        <span class="hashtag">#viral</span>
                        <span class="hashtag">#foryou</span>
                    </div>
                    <div class="music">
                        <i class="fas fa-music"></i>
                        <span>Original Sound - {{ channel.channel_name }}</span>
                    </div>
                </div>

                <div class="side-actions">
                    <div class="action-column">
                        <div class="action-button">
                            <i class="fas fa-heart"></i>
                            <span>2.5M</span>
                        </div>
                        <div class="action-button">
                            <i class="fas fa-comment"></i>
                            <span>45.2K</span>
                        </div>
                        <div class="action-button">
                            <i class="fas fa-share"></i>
                            <span>125K</span>
                        </div>
                        <div class="action-button">
                            <i class="fas fa-music"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Channel Selection Info -->
    <div v-if="selectedChannels" class="channel-selection-info">
        <div class="info-card">
            <i class="fas fa-info-circle"></i>
            <div class="info-content">
                <strong>Preview:</strong> Showing <strong>{{ channel.channel_name
                }}</strong> preview
                <div class="other-channels">
                    Also posting to:
                    <span v-for="(ch, index) in selectedChannels.slice(1)" :key="ch.id || ch.channel_id"
                        class="channel-tag">
                        {{ ch.channel_name || 'Unknown' }}
                        <span v-if="index < selectedChannels.slice(1).length - 1">, </span>
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        form: { type: Object, required: true },
        channel: { type: Object, required: true },
        selectedChannels: { type: Array, default: () => [] },
    },
    methods: {
        isImage(media) {
            const type = media?.mime_type || media?.type || '';
            return type.includes('image');
        },
        isVideo(media) {
            const type = media?.mime_type || media?.type || '';
            return type.includes('video');
        },
        getMediaUrl(media) {
            try {
                if (media.url) return media.url;
                if (typeof File !== "undefined" && media instanceof File) {
                    return URL.createObjectURL(media);
                }
            } catch (e) {
                console.warn("Error getting media URL:", e);
            }
            return "";
        },
    }
};
</script>

<style lang="scss" scoped>
.social-post {
    margin-bottom: 20px;

    // TikTok Preview Styles
    &.preview-tiktok {
        background: #000;
        border-radius: 10px;
        overflow: hidden;
        position: relative;
        color: white;

        .tiktok-preview {
            .video-container {
                position: relative;
                max-width: 375px; // TikTok mobile aspect ratio
                margin: 0 auto;
            }

            .video-header {
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                padding: 16px 12px 8px;

                .user-info {
                    display: flex;
                    align-items: flex-start;
                    gap: 8px;
                    flex: 1;

                    .avatar {
                        width: 40px;
                        height: 40px;
                        border-radius: 50%;
                        overflow: hidden;
                        border: 2px solid #fe2c55;

                        img {
                            width: 100%;
                            height: 100%;
                            object-fit: cover;
                        }
                    }

                    .user-details {
                        .username {
                            font-weight: 700;
                            font-size: 16px;
                            color: white;
                            margin-bottom: 2px;
                        }

                        .video-title {
                            font-size: 14px;
                            color: rgba(255, 255, 255, 0.9);
                            line-height: 1.3;
                            max-width: 200px;
                            overflow: hidden;
                            text-overflow: ellipsis;
                            display: -webkit-box;
                            -webkit-line-clamp: 2;
                            -webkit-box-orient: vertical;
                        }
                    }
                }

                .follow-btn {
                    background: #fe2c55;
                    color: white;
                    border: none;
                    border-radius: 4px;
                    padding: 6px 16px;
                    font-size: 14px;
                    font-weight: 600;
                    cursor: pointer;
                    white-space: nowrap;

                    &:hover {
                        background: #e0254a;
                    }
                }
            }

            .video-content {
                position: relative;

                .default-video-preview {
                    background: #161823;
                    aspect-ratio: 9/16;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    border-radius: 8px;
                    margin: 0 12px;

                    .video-placeholder {
                        text-align: center;
                        color: rgba(255, 255, 255, 0.6);

                        i {
                            font-size: 48px;
                            margin-bottom: 12px;
                            display: block;
                        }

                        p {
                            font-size: 14px;
                            margin: 0;
                        }
                    }
                }

                .tiktok-video-player {
                    position: relative;
                    margin: 0 12px;
                    border-radius: 8px;
                    overflow: hidden;
                    background: #000;

                    .tiktok-video {
                        width: 100%;
                        height: auto;
                        aspect-ratio: 9/16;
                        object-fit: cover;
                        display: block;
                    }

                    .video-controls-overlay {
                        position: absolute;
                        bottom: 20px;
                        left: 0;
                        right: 0;
                        display: flex;
                        justify-content: center;

                        .play-pause {
                            width: 40px;
                            height: 40px;
                            background: rgba(255, 255, 255, 0.2);
                            border-radius: 50%;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            backdrop-filter: blur(10px);

                            i {
                                color: white;
                                font-size: 16px;
                            }
                        }
                    }
                }

                .tiktok-images {
                    margin: 0 12px;
                    border-radius: 8px;
                    overflow: hidden;
                    background: #000;

                    .carousel-inner {
                        aspect-ratio: 9/16;
                    }

                    .carousel-item {
                        img {
                            width: 100%;
                            height: 100%;
                            object-fit: cover;
                        }
                    }

                    .carousel-control-prev,
                    .carousel-control-next {
                        width: 40px;
                        opacity: 0.7;

                        &:hover {
                            opacity: 1;
                        }
                    }
                }
            }

            .video-stats {
                padding: 12px;

                .stats-row {
                    display: flex;
                    gap: 20px;

                    .stat-item {
                        display: flex;
                        align-items: center;
                        gap: 6px;
                        font-size: 13px;
                        color: rgba(255, 255, 255, 0.9);

                        i {
                            color: rgba(255, 255, 255, 0.7);
                            font-size: 14px;
                        }
                    }
                }
            }

            .video-description {
                padding: 0 12px 12px;

                .description-text {
                    font-size: 14px;
                    line-height: 1.4;
                    color: white;
                    margin: 0 0 8px 0;
                }

                .hashtags {
                    margin-bottom: 8px;

                    .hashtag {
                        color: #fe2c55;
                        font-weight: 600;
                        margin-right: 8px;
                        font-size: 14px;
                    }
                }

                .music {
                    display: flex;
                    align-items: center;
                    gap: 6px;
                    font-size: 14px;
                    color: rgba(255, 255, 255, 0.9);

                    i {
                        color: rgba(255, 255, 255, 0.7);
                    }
                }
            }

            .side-actions {
                position: absolute;
                right: 12px;
                bottom: 100px;
                display: flex;
                flex-direction: column;
                gap: 16px;

                .action-column {
                    display: flex;
                    flex-direction: column;
                    gap: 16px;

                    .action-button {
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        gap: 4px;
                        cursor: pointer;

                        i {
                            width: 48px;
                            height: 48px;
                            background: rgba(255, 255, 255, 0.1);
                            border-radius: 50%;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            font-size: 20px;
                            backdrop-filter: blur(10px);
                            transition: all 0.2s ease;

                            &:hover {
                                background: rgba(255, 255, 255, 0.2);
                            }
                        }

                        span {
                            font-size: 12px;
                            font-weight: 600;
                            color: rgba(255, 255, 255, 0.9);
                        }

                        // Special style for heart icon
                        &:first-child i {
                            color: #fe2c55;
                        }
                    }
                }
            }
        }
    }
}

// Channel Selection Info
.channel-selection-info {
    .info-card {
        display: flex;
        align-items: flex-start;
        gap: 8px;
        padding: 12px;
        background: #f8f9fa;
        border-radius: 8px;
        border: 1px solid #e9ecef;

        i {
            color: #6c757d;
            margin-top: 2px;
        }

        .info-content {
            font-size: 12px;
            color: #495057;

            .other-channels {
                margin-top: 4px;

                .channel-tag {
                    font-weight: 500;
                    color: #007bff;
                }
            }
        }
    }
}
</style>
