<template>
    <div class="social-post">
        <h2>Preview</h2>

        <!-- Default Preview (for other platforms) -->
        <div class="default-preview-style">
            <div class="post-header">
                <div class="user-info">
                    <div class="avatar">
                        <img :src="channel.avatar || '/assets/images/profile-img.png'"
                            :alt="channel.channel_name" />
                    </div>
                    <div class="user-details">
                        <div class="username">{{ channel.channel_name }}</div>
                        <div class="handle">@{{ channel.account_username ||
                            channel.channel_name.replace(/\s+/g, '').toLowerCase() }}</div>
                    </div>
                </div>
            </div>

            <div class="post-content">
                <div class="text-content">
                    <p class="post-text">{{ form.message }}</p>
                </div>

                <div class="image-preview">
                    <!-- If no media -->
                    <div v-if="!form.media || form.media?.length === 0" class="default-preview">
                        <div class="img-cont">
                            <img :src="'/assets/images/camera-img.png'" alt="Default" />
                        </div>
                    </div>
                    <template v-if="form.media?.length && form.media?.every(isImage)">

                        <!-- If single image -->
                        <template v-if="(form.media?.length === 1) && isImage(form.media[0])">
                            <img :src="getMediaUrl(form.media[0])" class="w-100 rounded"
                                :alt="form.media[0].original_name || form.media[0].name || 'Uploaded image'" />
                        </template>

                        <!-- If multiple images -->
                        <template v-else-if="form.media?.length > 1 && form.media.every(isImage)">
                            <div :id="`defaultCarousel-${channel.id || channel.channel_id}`" class="carousel slide"
                                data-bs-ride="carousel" data-bs-interval="3000">
                                <div class="carousel-indicators">
                                    <button v-for="(file, i) in form.media" :key="i" type="button"
                                        :data-bs-target="`#defaultCarousel-${channel.id || channel.channel_id}`"
                                        :data-bs-slide-to="i" :class="{ active: i === 0 }"
                                        :aria-label="`Slide ${i + 1}`"></button>
                                </div>
                                <div class="carousel-inner">
                                    <div v-for="(file, i) in form.media" :key="i" class="carousel-item"
                                        :class="{ active: i === 0 }">
                                        <img :src="getMediaUrl(file)" class="d-block w-100 rounded" alt="Preview" />
                                    </div>
                                </div>
                                <button class="carousel-control-prev" type="button"
                                    :data-bs-target="`#defaultCarousel-${channel.id || channel.channel_id}`"
                                    data-bs-slide="prev">
                                    <span class="carousel-control-prev-icon"></span>
                                </button>
                                <button class="carousel-control-next" type="button"
                                    :data-bs-target="`#defaultCarousel-${channel.id || channel.channel_id}`"
                                    data-bs-slide="next">
                                    <span class="carousel-control-next-icon"></span>
                                </button>
                            </div>
                        </template>
                    </template>
                    <template v-else-if="form.media?.length && form.media?.every(isVideo)">
                        <template v-if="form.media?.length">
                            <template v-for="media in form.media" :key="media.url">
                                <!-- If video -->
                                <div v-if="isVideo(media)">
                                    <video class="w-100 rounded" controls>
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

    // Default Preview Styles
    &.preview-default {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);

        .default-preview-style {
            .post-header {
                padding: 16px;

                .user-info {
                    display: flex;
                    align-items: center;
                    gap: 12px;

                    .avatar {
                        width: 40px;
                        height: 40px;
                        border-radius: 50%;
                        overflow: hidden;

                        img {
                            width: 100%;
                            height: 100%;
                            object-fit: cover;
                        }
                    }

                    .user-details {
                        .username {
                            font-weight: 600;
                            font-size: 14px;
                            color: #1a1a1a;
                        }

                        .handle {
                            font-size: 12px;
                            color: #666;
                        }
                    }
                }
            }

            .post-content {
                .text-content {
                    padding: 0 16px 16px;

                    .post-text {
                        font-size: 14px;
                        line-height: 1.4;
                        color: #1a1a1a;
                        margin: 0;
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
