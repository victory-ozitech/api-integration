<template>
    <div class="social-post preview-facebook">
        <h5>Facebook Preview</h5>
        <!-- Facebook-style Preview -->
        <div class="facebook-preview">
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
                        <div class="time">Just now · 🌍</div>
                    </div>
                </div>
                <div class="facebook-actions">
                    <i class="fas fa-ellipsis-h"></i>
                </div>
            </div>

            <div class="post-content">
                <div class="text-content">
                    <p class="post-text">{{ props.form.message || 'Your post text will appear here...' }}</p>
                </div>

                <div class="image-preview">
                    <!-- If no media -->
                    <div v-if="!props.form.media || props.form.media?.length === 0" class="default-preview">
                        <div class="img-cont">
                            <img :src="'/assets/images/camera-img.png'" alt="Default" />
                        </div>
                    </div>
                    <template v-if="props.form.media?.length && props.form.media?.every(isImage)">
                        <!-- If multiple images -->
                        <div v-if="props.form.media?.length" class="media-grid">
                            <template v-if="props.form.media.length === 1">
                                <img :src="getMediaUrl(props.form.media[0])" class="single-media" />
                            </template>

                            <template v-else-if="props.form.media.length === 2">
                                <div class="two-grid">
                                    <img v-for="(file, i) in props.form.media" :key="i" :src="getMediaUrl(file)" />
                                </div>
                            </template>

                            <template v-else-if="props.form.media.length === 3">
                                <div class="three-grid">
                                    <img :src="getMediaUrl(props.form.media[0])" class="large" />
                                    <div class="two-small">
                                        <img v-for="(file, i) in props.form.media.slice(1)" :key="i"
                                            :src="getMediaUrl(file)" />
                                    </div>
                                </div>
                            </template>

                            <template v-else>
                                <div class="four-grid">
                                    <img v-for="(file, i) in props.form.media.slice(0, 4)" :key="i"
                                        :src="getMediaUrl(file)" />
                                    <div v-if="props.form.media.length > 4" class="overlay">+{{ props.form.media.length - 4 }}</div>
                                </div>
                            </template>
                        </div>
                    </template>
                    <template v-else-if="props.form.media?.length && props.form.media?.every(isVideo)">
                        <template v-if="props.form.media?.length">
                            <template v-for="media in props.form.media" :key="media.url">
                                <!-- If video -->
                                <div v-if="isVideo(media)">
                                    <video class="w-100 rounded" controls>
                                        <source :src="media.file_path || media.url" :type="media.type" />
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

                <div class="facebook-stats">
                    <div class="reactions">
                        <span class="reaction-icons">👍😍😮😢😡</span>
                        <span class="reaction-count">125</span>
                    </div>
                    <div class="comments-shares">
                        <span>48 comments · 12 shares</span>
                    </div>
                </div>

                <div class="facebook-action-buttons">
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

<script setup>
import { onMounted } from "vue";

const props = defineProps({
    form: {
        type: Object,
        required: true,
    },
    channel: {
        type: Object,
        required: true,
    },
    selectedChannels: {
        type: Array,
        default: () => [],
    },
});

onMounted(() => {
    // console.log("channel:", props.channel);
    // console.log("Selected channels:", props.selectedChannels);
})

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
</script>

<style lang="scss" scoped>
.social-post {
    margin: 0 auto 20px;

    // Facebook Preview Styles
    &.preview-facebook {
        padding: 15px;
        border-radius: 8px;
        background: #f0f2f5;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);

        .facebook-preview {
            max-width: 500px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            font-family: "Segoe UI", Helvetica, Arial, sans-serif;
            color: #050505;

            .post-header {
                display: flex;
                padding: 12px 16px;
                background: #fff;
                align-items: center;
                justify-content: space-between;

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
                        display: flex;
                        flex-direction: column;
                        line-height: 1.2;

                        .username {
                            font-weight: 600;
                            font-size: 15px;
                            color: #050505;
                        }

                        .handle {
                            color: #65676b;
                            font-weight: 400;
                            font-size: 13px;
                        }

                        .time {
                            font-size: 13px;
                            color: #65676b;
                        }
                    }
                }

                .facebook-actions {
                    color: #65676b;
                    cursor: pointer;
                }
            }

            .post-content {
                .text-content {
                    padding: 0 16px 12px;
                    font-size: 15px;
                    line-height: 1.4;
                    color: #050505;

                    .post-text {
                        font-size: 15px;
                        line-height: 1.33;
                        // color: #050505;
                        color: v-bind('form.message ? "#050505" : "#8d949e"');
                        margin: 0;
                    }
                }

                .mixed-warning {
                    margin-left: 5%;
                    color: #ff0000;
                }

                .media-grid {
                    width: 100%;

                    img {
                        object-fit: cover;
                        border: 1px solid #e4e6eb;
                    }

                    .single-media img {
                        width: 100%;
                        border-radius: 0;
                    }

                    .two-grid {
                        display: grid;
                        grid-template-columns: 1fr 1fr;

                        img {
                            width: 100%;
                            height: 250px;
                        }
                    }

                    .three-grid {
                        display: grid;
                        grid-template-columns: 2fr 1fr;
                        gap: 2px;

                        .large {
                            width: 100%;
                            height: 100%;
                            grid-row: span 2;
                        }

                        .two-small {
                            display: grid;
                            grid-template-rows: 1fr 1fr;
                            gap: 2px;

                            img {
                                width: 100%;
                                height: 125px;
                            }
                        }
                    }

                    .four-grid {
                        object-fit: cover;
                        display: grid;
                        grid-template-columns: 1fr 1fr;
                        gap: 2px;
                        position: relative;

                        img {
                            width: 100%;
                            height: 150px;
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

                .facebook-stats {
                    font-size: 13px;
                    color: #65676b;
                    padding: 10px 16px;
                    border-top: 1px solid #e4e6eb;
                    border-bottom: 1px solid #e4e6eb;
                    display: flex;
                    justify-content: space-between;
                    align-items: center;

                    .reactions {
                        display: flex;
                        align-items: center;
                        gap: 6px;
                        margin-bottom: 4px;
                    }
                }

                .facebook-action-buttons {
                    display: flex;
                    justify-content: space-around;
                    padding: 4px 0;

                    .action-btn {
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        gap: 4px;
                        background: none;
                        border: none;
                        color: #65676b;
                        font-size: 14px;
                        font-weight: 600;
                        padding: 8px 0;
                        border-radius: 6px;
                        flex: 1;
                        cursor: pointer;

                        &:hover {
                            background: #f2f3f5;
                        }
                    }
                }
            }
        }
    }
}
</style>
