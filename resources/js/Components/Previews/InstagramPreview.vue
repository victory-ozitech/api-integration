<template>
    <div class="social-post preview-instagram">
        <h5>Instagram Preview</h5>

        <!-- Instagram-style Preview -->
        <div class="instagram-preview">
            <div class="post-header">
                <div class="user-info">
                    <div class="avatar">
                        <img :src="channel.avatar || '/assets/images/profile-img.png'"
                            :alt="channel.channel_name" />
                    </div>
                    <div class="user-details">
                        <div class="username">{{ channel.channel_name }}</div>
                        <!-- <div class="handle">@{{ channel.account_username || channel.channel_name.replace(/\s+/g,
                            '').toLowerCase() }}</div> -->
                        <div class="location">New York, NY</div>
                    </div>
                </div>
                <div class="instagram-actions">
                    <!-- <i class="fas fa-ellipsis-vertical"></i> -->
                    <i class="fas fa-ellipsis-h"></i>
                </div>
            </div>

            <div class="post-content">
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
                            <div :id="`instagramCarousel-${channel.id || channel.channel_id}`" class="carousel slide"
                                data-bs-ride="carousel">
                                <div class="carousel-inner">
                                    <div v-for="(file, i) in form.media" :key="i" class="carousel-item"
                                        :class="{ active: i === 0 }">
                                        <img :src="getMediaUrl(file)" class="d-block w-100" alt="Preview" />
                                    </div>
                                </div>
                                <div class="carousel-indicators">
                                    <button v-for="(file, i) in form.media" :key="i" type="button"
                                        :data-bs-target="`#instagramCarousel-${channel.id}`" :data-bs-slide-to="i"
                                        :class="{ active: i === 0 }">
                                    </button>
                                </div>
                                <button class="carousel-control-prev" type="button"
                                    :data-bs-target="`#instagramCarousel-${channel.id || channel.channel_id}`"
                                    data-bs-slide="prev">
                                    <span class="carousel-control-prev-icon"></span>
                                </button>
                                <button class="carousel-control-next" type="button"
                                    :data-bs-target="`#instagramCarousel-${channel.id || channel.channel_id}`"
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
                                    <video class="w-100" controls>
                                        <source :src="media.url" :type="media.type" />
                                        Your browser does not support video playback.
                                    </video>
                                    <div class="video-duration">0:30</div>
                                    🎥 Video
                                </div>
                            </template>
                        </template>
                    </template>
                    <template v-else>
                        <div class="mixed-warning">Mixed media not supported in preview</div>
                    </template>
                </div>

                <div class="instagram-actions-bar">
                    <div class="left-actions">
                        <i class="far fa-heart"></i>
                        <i class="far fa-comment"></i>
                        <i class="far fa-paper-plane"></i>
                    </div>
                    <div class="right-actions">
                        <i class="far fa-bookmark"></i>
                    </div>
                </div>

                <div class="instagram-stats">
                    <div class="likes">Liked by <strong>user123</strong> and
                        <strong>others</strong>
                    </div>
                </div>

                <div class="text-content">
                    <p class="post-text">
                        <strong>{{ channel.channel_name }}</strong>
                        {{ form.message || 'Your post text will appear here...' }}
                    </p>
                    <div class="view-comments">View all 48 comments</div>
                    <div class="post-time">2 HOURS AGO</div>
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

    // Instagram Preview Styles
    &.preview-instagram {
        background: #fafafa; // app background
        padding: 15px;
        border-radius: 12px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);

        .instagram-preview {
            background: #fff;
            border: 1px solid #dbdbdb;
            border-radius: 8px;
            overflow: hidden;
            max-width: 470px;
            margin: 0 auto;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);

            .mixed-warning {
                color: #ff0000;
                text-align: center;
            }

            .post-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 12px 12px;
                border-bottom: 1px solid #efefef;

                .user-info {
                    display: flex;
                    align-items: center;
                    gap: 12px;

                    .avatar {
                        width: 42px;
                        height: 42px;
                        border-radius: 50%;
                        padding: 2px;
                        background: linear-gradient(45deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888);
                        display: flex;
                        align-items: center;
                        justify-content: center;


                        img {
                            width: 100%;
                            height: 100%;
                            border-radius: 50%;
                            border: 2px solid white;
                            object-fit: cover;
                        }
                    }

                    .user-details {
                        display: flex;
                        flex-direction: column;
                        // line-height: 1.1;
                        line-height: 1.3;

                        .username {
                            font-weight: 600;
                            font-size: 13.5px;
                            color: #262626;
                        }

                        .location {
                            font-size: 12px;
                            color: #8e8e8e;
                        }
                    }
                }

                .instagram-actions {
                    font-size: 18px;

                    svg {
                        height: 16px;
                    }
                }
            }

            .instagram-actions-bar {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 8px 12px 4px;
                border-top: 1px solid #efefef;

                .left-actions {
                    display: flex;
                    gap: 12px;

                    svg {
                        height: 18px;

                        &:hover {
                            transform: scale(1.1);
                            transition: transform 0.15s ease;
                        }
                    }

                    i {
                        font-size: 22px;
                        color: #262626;
                        cursor: pointer;
                    }
                }

                .right-actions {

                    svg {
                        height: 18px;

                        &:hover {
                            transform: scale(1.1);
                            transition: transform 0.15s ease;
                        }
                    }

                    i {
                        font-size: 24px;
                        cursor: pointer;
                    }
                }
            }

            .instagram-stats {
                margin: 4px 0 4px;
                padding: 4px 12px 0;

                .likes {
                    font-size: 13.5px;
                    font-weight: 600;
                    color: #262626;
                }
            }

            .text-content {
                padding: 4px 16px 12px;

                .post-text {
                    font-size: 13.5px;
                    line-height: 1.4;
                    color: #262626;
                    margin: 0 0 8px 0;

                    strong {
                        font-weight: 600;
                    }
                }

                .view-comments {
                    font-size: 13px;
                    color: #8e8e8e;
                    margin-bottom: 2px;
                }

                .post-time {
                    font-size: 10px;
                    color: #8e8e8e;
                    margin-top: 6px;
                    letter-spacing: 0.2px;
                    text-transform: uppercase;
                }
            }
        }
    }

    .carousel {
        position: relative;

        .carousel-control-prev,
        .carousel-control-next {
            display: none;
        }

        .carousel-indicators {
            bottom: 10px;

            button {
                width: 6px;
                height: 6px;
                border-radius: 50%;
                background-color: rgba(255, 255, 255, 0.6);
            }

            .active {
                background-color: white;
            }
        }
    }

    .image-preview {
        position: relative;
        width: 100%;
        aspect-ratio: 1 / 1; // perfect square
        background: #000;
        overflow: hidden;

        img,
        video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
    }

    // Video duration indicator for Instagram
    .video-duration {
        bottom: 10px;
        right: 10px;
        background: rgba(0, 0, 0, 0.65);
        padding: 2px 5px;
        border-radius: 3px;
        font-size: 11px;
        font-weight: 600;
        color: #fff;
        letter-spacing: 0.3px;
    }
}
</style>