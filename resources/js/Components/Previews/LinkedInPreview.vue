<template>
    <div class="social-post preview-linkedin">
        <h5>LinkedIn Preview</h5>

        <!-- LinkedIn-style Preview -->
        <div class="linkedin-preview">
            <div class="post-header">
                <div class="user-info">
                    <div class="avatar">
                        <img :src="channel.avatar || '/assets/images/profile-img.png'"
                            :alt="channel.channel_name" />
                    </div>
                    <div class="user-details">
                        <div class="username">{{ channel.channel_name }}</div>
                        <div class="title">Software Engineer at Tech Company</div>
                        <div class="time">2h · <i class="fas fa-globe-americas"></i></div>
                    </div>
                </div>
            </div>

            <div class="post-content">
                <div class="text-content">
                    <p class="post-text">{{ form.message || 'Your post text will appear here...' }}</p>
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
                            <img :src="getMediaUrl(form.media[0])" class="w-100"
                                :alt="form.media[0].original_name || form.media[0].name || 'Uploaded image'" />
                        </template>

                        <!-- If multiple images -->
                        <template v-else-if="form.media?.length > 1 && form.media.every(isImage)">
                            <div class="linkedin-grid">
                                <div v-for="(file, i) in form.media" :key="i" class="grid-item">
                                    <img :src="getMediaUrl(file)" alt="Preview" />
                                </div>
                            </div>
                        </template>
                    </template>
                    <template v-else-if="form.media?.length && form.media?.every(isVideo)">
                        <template v-if="form.media?.length">
                            <template v-for="media in form.media" :key="media.url">
                                <!-- If video -->
                                <div v-if="isVideo(media)">
                                    <video class="w-100" controls>
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

                <div class="linkedin-stats">
                    <div class="reactions">
                        <span class="reaction-icons">👍🎉❤️</span>
                        <span class="reaction-count">124</span>
                    </div>
                    <div class="comments">
                        <span>12 comments</span>
                    </div>
                </div>

                <div class="linkedin-action-buttons">
                    <button class="action-btn">
                        <i class="far fa-thumbs-up"></i> Like
                    </button>
                    <button class="action-btn">
                        <i class="far fa-comment"></i> Comment
                    </button>
                    <button class="action-btn">
                        <i class="far fa-share-square"></i> Share
                    </button>
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
    margin: 0 auto 20px;

    // LinkedIn Preview Styles
    &.preview-linkedin {
        padding: 15px;
        border-radius: 8px;
        background: #f0f2f5;
        box-shadow: 0 0 0 1px rgba(0, 0, 0, 0.08), 0 2px 4px rgba(0, 0, 0, 0.1);

        .linkedin-preview {
            .post-header {
                padding: 12px 16px 0;

                .user-info {
                    gap: 8px;
                    display: flex;
                    align-items: center;

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
                            color: #050505;
                        }

                        .title {
                            font-size: 12px;
                            color: #65676b;
                        }

                        .time {
                            font-size: 12px;
                            color: #65676b;
                            display: flex;
                            align-items: center;
                            gap: 4px;
                        }
                    }
                }
            }

            .post-content {
                .text-content {
                    padding: 0 16px 8px;

                    .post-text {
                        font-size: 14px;
                        line-height: 1.43;
                        color: rgba(0, 0, 0, 0.9);
                        margin: 0;
                    }
                }

                .linkedin-stats {
                    padding: 8px 16px;
                    border-top: 1px solid #e6e9ec;
                    font-size: 12px;
                    color: rgba(0, 0, 0, 0.6);

                    .reactions {
                        display: flex;
                        align-items: center;
                        gap: 4px;
                        margin-bottom: 4px;
                    }
                }

                .linkedin-action-buttons {
                    display: flex;
                    justify-content: space-around;
                    padding: 4px 0;
                    border-top: 1px solid #e6e9ec;

                    .action-btn {
                        background: none;
                        border: none;
                        padding: 8px 12px;
                        color: rgba(0, 0, 0, 0.6);
                        font-size: 12px;
                        font-weight: 600;
                        cursor: pointer;
                        border-radius: 4px;
                        flex: 1;

                        &:hover {
                            background: #f3f6f8;
                        }
                    }
                }

                .linkedin-grid {
                    display: grid;
                    grid-template-columns: 1fr 1fr;
                    gap: 2px;
                    margin: 0 16px 12px;

                    .grid-item {
                        img {
                            width: 100%;
                            height: 100%;
                            object-fit: cover;
                        }
                    }
                }
            }
        }
    }
}
</style>
