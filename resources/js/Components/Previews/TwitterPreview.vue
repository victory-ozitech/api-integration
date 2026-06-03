<template>
    <div class="social-post preview-twitter">
        <h5>X Preview</h5>

        <!-- Twitter-style Preview -->
        <div class="twitter-preview">
            <div class="post-header">
                <div class="user-info">
                    <div class="avatar">
                        <img :src="channel.avatar || '/assets/images/profile-img.png'"
                            :alt="channel.channel_name" />
                    </div>
                    <div class="user-details">
                        <div class="username">{{ channel.channel_name }}
                            <div class="twitter-verified">
                                <i class="fas fa-certificate"></i>
                            </div>
                        </div>
                        <div class="handle">@{{ channel.account_username ||
                            channel.channel_name.replace(/\s+/g, '').toLowerCase() }}
                        </div>
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
                            <img :src="getMediaUrl(form.media[0])" class="w-100 rounded"
                                :alt="form.media[0].original_name || form.media[0].name || 'Uploaded image'" />
                        </template>

                        <!-- If multiple images -->

                        <template v-else-if="form.media?.length > 1 && form.media.every(isImage)">
                            <div class="twitter-grid" :class="`grid-${Math.min(form.media?.length, 4)}`">
                                <div v-for="(file, i) in form.media.slice(0, 4)" :key="i" class="grid-item">
                                    <img :src="getMediaUrl(file)" alt="Preview" />
                                    <!-- Overlay for extra images -->
                                    <div v-if="i === 3 && form.media.length > 4" class="overlay">
                                        +{{ form.media.length - 4 }}
                                    </div>
                                </div>
                            </div>
                        </template>
                        <!-- <template v-else-if="form.media?.length > 1 && form.media.every(isImage)">
                            <div class="twitter-grid" :class="`grid-${form.media?.length}`">
                                <div v-for="(file, i) in form.media" :key="i" class="grid-item">
                                    <img :src="getMediaUrl(file)" alt="Preview" />
                                </div>
                            </div>
                        </template> -->
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

                <div class="post-time">10:30 AM · Feb 15, 2024 · <span>2,345 Views</span></div>

                <div class="twitter-stats">
                    <div class="stat">
                        <strong>245</strong> Retweets
                    </div>
                    <div class="stat">
                        <strong>1.2K</strong> Likes
                    </div>
                </div>

                <div class="twitter-action-buttons">
                    <button class="action-btn">
                        <i class="far fa-comment"></i>
                    </button>
                    <button class="action-btn">
                        <i class="fas fa-retweet"></i>
                    </button>
                    <button class="action-btn">
                        <i class="far fa-heart"></i>
                    </button>
                    <button class="action-btn">
                        <i class="far fa-share-square"></i>
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

    // Twitter Preview Styles
    &.preview-twitter {
        padding: 15px;
        border-radius: 8px;
        background: #f0f2f5;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);

        .twitter-preview {
            // background: #15202b;
            // border-radius: 16px;
            color: white;
            background: #000; // instead of #15202b
            border-radius: 0; // flat, modern
            border: 1px solid #2f3336;
            max-width: 600px;
            margin: 0 auto;

            .post-header {
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                padding: 12px 16px;

                .user-info {
                    display: flex;
                    align-items: flex-start;
                    gap: 12px;

                    .avatar {
                        width: 48px;
                        height: 48px;
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
                        align-items: center;
                        gap: 2px 5px;
                        flex-wrap: wrap;

                        .username {
                            font-weight: 700;
                            font-size: 15px;
                            color: #e7e9ea;
                            display: flex;
                            gap: 5px;
                        }

                        .handle {
                            color: #71767b;
                            font-size: 14px;
                        }
                    }
                }

                .twitter-verified {
                    color: #1d9bf0;
                }
            }

            .post-content {
                .text-content {
                    padding: 0 16px 12px;

                    .post-text {
                        font-size: 15px;
                        line-height: 1.33;
                        color: white;
                        margin: 0;
                    }
                }

                .post-time {
                    padding: 0 16px 12px;
                    color: #71767b;
                    font-size: 14px;
                }

                .twitter-stats {
                    display: flex;
                    gap: 20px;
                    padding: 12px 16px;
                    border-top: 1px solid #38444d;
                    border-bottom: 1px solid #38444d;
                    font-size: 13px;
                    color: #8b98a5;
                }

                .twitter-action-buttons {
                    display: flex;
                    justify-content: space-around;
                    padding: 4px 0 8px;
                    border-top: 1px solid #2f3336;

                    .action-btn {
                        color: #71767b;
                        font-size: 16px;
                        border: none;
                        background: none;
                        cursor: pointer;
                        transition: color 0.2s;

                        &:hover:nth-child(1) {
                            color: #1d9bf0;
                        }

                        // reply
                        &:hover:nth-child(2) {
                            color: #00ba7c;
                        }

                        // retweet
                        &:hover:nth-child(3) {
                            color: #f91880;
                        }

                        // like
                        &:hover:nth-child(4) {
                            color: #1d9bf0;
                        }

                        // share
                    }
                }

                .twitter-grid {
                    position: relative;
                    display: grid;
                    gap: 2px;
                    margin: 0 16px 12px;

                    &.grid-2 {
                        grid-template-columns: 1fr 1fr;
                    }

                    &.grid-3 {
                        grid-template-columns: 1fr 1fr;
                        grid-template-rows: 1fr 1fr;

                        .grid-item:first-child {
                            grid-column: 1 / -1;
                        }
                    }

                    &.grid-4 {
                        grid-template-columns: 1fr 1fr;
                        grid-template-rows: 1fr 1fr;
                    }

                    .grid-item {
                        position: relative;
                        overflow: hidden;

                        img {
                            width: 100%;
                            height: 100%;
                            object-fit: cover;
                            display: block;
                        }

                        .overlay {
                            position: absolute;
                            inset: 0;
                            background: rgba(0, 0, 0, 0.65);
                            color: #fff;
                            font-size: 24px;
                            font-weight: 600;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            border-radius: 12px;
                        }
                    }
                }
            }
        }
    }
}

.image-preview {
    margin-top: 8px;
}

.twitter-grid img,
.twitter-preview img.w-100 {
    border-radius: 12px;
    border: 1px solid #2f3336;
}
</style>
