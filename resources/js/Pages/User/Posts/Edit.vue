<template>
    <PostFormLayout>
        <!-- Header -->
        <div class="page-header">
            <h2>Edit Post</h2>
            <p>Update your post details</p>
        </div>

        <div class="row g-2">
            <div class="col-md-8">
                <!-- Card -->
                <div class="form-card">
                    <form @submit.prevent.stop="submitForm">

                        <!-- Channels Form Group -->
                        <div class="form-group">
                            <div class="social-media-profile-cont">
                                <!-- Show only the selected channel if editing -->
                                <div v-for="channel in formChannels" :key="channel.id || channel.channel_id"
                                    class="profile-img active disabled" :title="`This post is linked to ${channel?.channel_name ||
                                        'Unknown Channel'}. Channel cannot be changed.`">
                                    <img :src="channel?.avatar || '/assets/images/profile-img.png'"
                                        :alt="channel?.channel_name || 'Channel Avatar'" />
                                    <span class="social-icon">
                                        <i :class="getChannelIcon(channel?.platform || 'facebook')"></i>
                                    </span>
                                    <div class="selected-badge-edit">
                                        ✓
                                    </div>
                                    <div class="channel-lock-overlay">
                                        <i class="fas fa-lock"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Media Upload Form Group -->
                        <div class="form-group">
                            <div class="label">Media</div>
                            <div class="sub-label">Upload your photos or videos</div>

                            <div class="upload-group">
                                <!-- Hidden file inputs -->
                                <input type="file" ref="photoInput" accept="image/*" multiple
                                    @change="handleMediaUpload($event, 'image')" style="display: none" />
                                <input type="file" ref="videoInput" accept="video/*" multiple
                                    @change="handleMediaUpload($event, 'video')" style="display: none" />

                                <!-- Trigger Buttons -->
                                <button type="button" @click="$refs.photoInput.click()">
                                    <div class="img-cont">
                                        <img :src="'/assets/images/add-img.png'" alt="Add" />
                                    </div>
                                    Add Photos
                                </button>
                                <button type="button" @click="$refs.videoInput.click()">
                                    <div class="img-cont">
                                        <img :src="'/assets/images/upload-img.png'" alt="Upload" />
                                    </div>
                                    Add Videos
                                </button>
                                <button type="button">
                                    <div class="img-cont">
                                        <img :src="'/assets/images/library-img.png'" alt="Library" />
                                    </div>
                                    Use From Library
                                </button>
                            </div>

                            <!-- Preview -->
                            <div class="media-preview mt-3">
                                <div v-for="(file, index) in previewMedia" :key="index" class="preview-item">
                                    <img v-if="file.type.startsWith('image/')" :src="file.url" class="preview-img" />
                                    <video v-else-if="file.type.startsWith('video/')" controls class="preview-video">
                                        <source :src="file.url" :type="file.type" />
                                    </video>
                                    <button type="button" class="btn btn-danger btn-sm mt-1"
                                        @click="removeMedia(index)">
                                        Remove
                                    </button>
                                </div>
                            </div>


                            <!-- Media -->
                            <div v-if="form.media?.length" class="media-section">
                                <h5>Media</h5>
                                <div class="media-grid">
                                    <div v-for="media in form.media" :key="media.file_path" class="media-item"
                                        @click.stop="openMedia(media.file_path)">
                                        <img v-if="isImage(media)" :src="getMediaUrl(media)"
                                            :alt="media.original_name || media.name || 'Uploaded image'"
                                            @error="e => console.log('Image failed:', e.target.src)" />
                                        <div v-else-if="isVideo(media)" class="video-placeholder">
                                            🎥 Video
                                        </div>
                                        <div class="media-info">
                                            <div class="media-name">
                                                {{ media.original_name }}
                                            </div>
                                            <div class="media-size">
                                                {{ formatFileSize(media.size) }}
                                            </div>
                                        </div>

                                        <!-- ✳️ Remove button -->
                                        <button type="button" class="remove-btn"
                                            @click.stop="deleteExistingMedia(media)">
                                            ✕
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="form-group">
                            <label class="label" for="message">Post message</label>
                            <textarea v-model="form.message" id="message" class="form-control"
                                placeholder="Enter post message..." rows="3" />
                        </div>


                        <!-- Scheduling Options Form Group -->
                        <div class="form-group">
                            <div class="label m-0">
                                Scheduling Options
                            </div>

                            <div class="sub-label">
                                Schedule your Post... <span class="text-danger">(Choose an option to
                                    submit)</span>
                            </div>
                            <div class="mb-3 form-check">
                                <input type="radio" id="publish_now" value="publish_now" class="form-check-input"
                                    v-model="selectedOption
                                        " />
                                <label class="form-check-label" for="publish_now">Publish
                                    Now</label>
                            </div>
                            <div class="mb-3 form-check">
                                <input type="radio" id="queue" value="queue" class="form-check-input"
                                    v-model="selectedOption" />
                                <label class="form-check-label" for="queue">Add to queue</label>
                            </div>
                            <div class="mb-3 form-check">
                                <input type="radio" id="prioritize" value="prioritize" class="form-check-input"
                                    v-model="selectedOption" />
                                <label class="form-check-label" for="prioritize">Prioritize</label>
                            </div>
                            <div class="form-check">
                                <input type="radio" id="custom_time" value="custom_time" class="form-check-input"
                                    v-model="selectedOption
                                        " />
                                <label class="form-check-label" for="custom_time">Custom
                                    Time</label>
                            </div>

                            <!-- custom date/time -->
                            <div class="right-cont" v-if="
                                selectedOption ===
                                'custom_time'
                            ">
                                <!-- Date -->
                                <div class="mb-3" v-if="selectedOption === 'custom_time'">
                                    <label for="date" class="small-label">Date</label>
                                    <input type="date" id="date" v-model="form.date" :min="minDate"
                                        class="form-control small-input" />
                                </div>

                                <!-- Time -->
                                <div class="mb-3" v-if="selectedOption === 'custom_time'">
                                    <label for="time" class="small-label">Time</label>
                                    <input type="time" id="time" v-model="form.time" class="form-control small-input" />
                                </div>

                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="actions">
                            <button class="btn btn-primary-custom" type="submit">
                                Schedule Post
                            </button>
                        </div>
                    </form>

                </div>
            </div>
            <div v-if="activePreviewChannel" class="col-md-4">
                <div class="right-col">
                    <div class="preview-card">
                        <component :is="getPreviewComponent(activePreviewChannel.platform)" :form="form"
                            :channel="activePreviewChannel" :selected-channels="selectedPreviewChannels" />

                        <!-- Channel Selection Info -->
                        <div v-if="selectedPreviewChannels" class="channel-selection-info">
                            <div class="info-card">
                                <i class="fas fa-info-circle"></i>
                                <div class="info-content">
                                    <strong>Preview:</strong> Showing <strong>{{
                                        activePreviewChannel.channel_name
                                    }}</strong> preview
                                    <div class="other-channels">
                                        Also posting to:
                                        <span v-for="(ch, index) in selectedPreviewChannels.slice(1)"
                                            :key="ch.id || ch.channel_id" class="channel-tag">
                                            {{ ch.channel_name || 'Unknown' }}
                                            <span v-if="index < selectedPreviewChannels.slice(1).length - 1">,
                                            </span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </PostFormLayout>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from "vue";
import PostFormLayout from "@/Layouts/PostFormLayout.vue";

import GooglePreview from '@/Components/Previews/GooglePreview.vue';
import TwitterPreview from '@/Components/Previews/TwitterPreview.vue';
import GenericPreview from '@/Components/Previews/GenericPreview.vue';
import YouTubePreview from '@/Components/Previews/YouTubePreview.vue';
import LinkedInPreview from '@/Components/Previews/LinkedInPreview.vue';
import FacebookPreview from '@/Components/Previews/FacebookPreview.vue';
import InstagramPreview from '@/Components/Previews/InstagramPreview.vue';
import TikTokPreview from '@/Components/Previews/TikTokPreview.vue';


//  ||============PROPS=================||
const props = defineProps({
    post: {
        type: Object,
        default: null,
    },
});

onMounted(() => {
    console.log("Post Prop:", props.post);
})



//  ||============LOCAL STATE=================||
const selectedOption = ref('publish_now');
const selectedChannel = ref(null);
const previewMedia = ref([]);
const deleted_media = ref([]);




//  ||============REACTIVE FORM=================||
const form = reactive({
    message: props.post?.message || "",
    date: props.post?.scheduled_at
        ? props.post.scheduled_at.split(' ')[0]
        : '',
    time: props.post?.scheduled_at
        ? props.post.scheduled_at.split(' ')[1]
        : "09:00",
    channels: props.post?.channels || [],
    media: props.post?.media || [],
    is_scheduled: props.post?.is_scheduled || false,
    scheduled_at: props.post?.scheduled_at || null,
    publish_option: props.post?.publish_option || "now",
    status: props.post?.status || "draft",
});




//  ||============COMPUTED PROPERTIES=================||
// minDate
const minDate = computed(() => {
    const today = new Date();
    return today.toISOString().split('T')[0];
});

// formChannel
const formChannels = computed(() => {
    if (!form.channels?.length) return null;

    const selectedIds = form.channels.map(channel =>
        typeof channel === 'object'
            ? channel.channel_id
            : channel
    );

    return props.post.channels
        .filter(c => selectedIds.includes(c.channel_id))
        .map(c => c.channel);
});

// active preview channel (first selected channel or null)
const activePreviewChannel = computed(() => {
    if (!form.channels?.length) return null;

    const firstId =
        typeof form.channels[0] === 'object'
            ? form.channels[0].channel_id
            // ? form.channels[0].id
            : form.channels[0];

    const foundChannel = props.post.channels.find(c => c.channel_id === firstId) || null;

    // console.log('First Id: ', firstId);
    // console.log('Active Preview Channel: ', foundChannel.channel);

    return foundChannel.channel;
});

// selected preview channels (all selected channels as objects)
const selectedPreviewChannels = computed(() => {
    if (!form.channels?.length) return null;

    const selectedIds = form.channels.map(channel =>
        typeof channel === 'object'
            ? channel.channel_id
            // ? channel.id
            : channel
    );

    // console.log('Selected Id: ', selectedIds);
    // console.log('Selected Preview Channel: ', props.post.channels.filter(c => selectedIds.includes(c.channel_id)));

    return props.post.channels.filter(c => selectedIds.includes(c.channel_id));
});




//  ||============METHODS=================||
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
        // if (media.file_path) return media.file_path;
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

const deleteExistingMedia = (media) => {
    // remove from current list
    form.media = form.media.filter(
        (m) => m.file_path !== media.file_path
    );

    // track removed items for backend if needed
    deleted_media.value.push(media);
};

const openMedia = (url) => {
    window.open(url, '_blank');
};

const formatFileSize = (bytes) => {
    if (!bytes) return '0 Bytes';

    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));

    return (
        parseFloat((bytes / Math.pow(k, i)).toFixed(2)) +
        ' ' +
        sizes[i]
    );
};

const handleMediaUpload = (event, type) => {
    const files = Array.from(event.target.files);

    files.forEach((file) => {
        // Create preview
        const reader = new FileReader();

        reader.onload = (e) => {
            previewMedia.value.push({
                url: e.target.result,
                type: file.type,
            });
        };

        reader.readAsDataURL(file);

        // Push actual file to form
        form.media.push(file);
    });

    // Reset input so same file can be re-selected
    event.target.value = '';
};

const removeMedia = (index) => {
    const media = previewMedia.value[index];

    if (media?.url?.startsWith('blob:')) {
        URL.revokeObjectURL(media.url);
    }

    previewMedia.value.splice(index, 1);
    form.media.splice(index, 1);
};

const getChannelIcon = (channel) => {
    switch (channel.toLowerCase()) {
        case 'facebook':
            return 'fab fa-facebook-f';

        case 'twitter':
            return 'fab fa-twitter';

        case 'instagram':
            return 'fab fa-instagram';

        case 'linkedin':
            return 'fab fa-linkedin-in';

        case 'youtube':
            return 'fab fa-youtube';

        case 'tiktok':
            return 'fab fa-tiktok';

        case 'google':
            return 'fab fa-google';

        default:
            return 'fas fa-user';
    }
};



// ======================================================
// Preview Components
// ======================================================

const getPreviewComponent = (channelName) => {
    // console.log('channel name passed: ', channelName);

    const normalizedKey =
        channelName
            .toLowerCase()
            .replace(/[-_]/g, '');

    // console.log('channel name passed: ', normalizedKey);

    switch (normalizedKey) {
        case 'twitter':
        case 'x':
            return TwitterPreview;

        case 'facebook':
            return FacebookPreview;

        case 'instagram':
            return InstagramPreview;

        case 'tiktok':
            return TikTokPreview;

        case 'linkedin':
            return LinkedInPreview;

        case 'youtube':
            return YouTubePreview;

        case 'google':
        case 'googlebusiness':
            return GooglePreview;

        default:
            return GenericPreview;
    }
};



const submitForm = () => {
    const selectedChannels = props.post.channels.filter((c) =>
        form.channels.some((sel) =>
            typeof sel === 'object'
                ? sel.id === c.id
                : sel === c.id
        )
    );

    if (!selectedChannels.length) {
        alert('Please select a channel first!');
        return;
    }

    if (!selectedOption.value) {
        alert(
            'Please choose a scheduling option (Publish Now or Custom Time).'
        );
        return;
    }

    // Schedule settings
    if (
        selectedOption.value === 'queue' ||
        selectedOption.value === 'prioritize'
    ) {
        form.is_scheduled = true;
        form.scheduled_at = null;
        form.publish_option = selectedOption.value;
        form.status = 'scheduled';
    } else if (
        selectedOption.value === 'custom_time' &&
        form.date &&
        form.time
    ) {
        form.is_scheduled = true;
        form.scheduled_at = `${form.date} ${form.time}:00`;
        form.publish_option = 'schedule';
        form.status = 'scheduled';
    } else if (selectedOption.value === 'publish_now') {
        form.status = 'published';
        form.is_scheduled = false;
        form.publish_option = 'now';
    } else if (selectedOption.value === 'save_draft') {
        form.status = 'draft';
        form.is_scheduled = false;
        form.publish_option = 'now';
    }

    const formData = new FormData();

    // Required for Laravel PUT uploads
    formData.append('_method', 'PUT');

    formData.append('message', form.message);
    formData.append('scheduled_at', form.scheduled_at ?? '');
    formData.append(
        'is_scheduled',
        form.is_scheduled ? '1' : '0'
    );
    formData.append('status', form.status);
    formData.append('publish_option', form.publish_option);

    // Top-level channel info (required by backend update)
    if (selectedChannels.length) {
        formData.append(
            'channel_id',
            selectedChannels[0].id
        );

        formData.append(
            'channel',
            selectedChannels[0].channel_name
        );
    }

    // Also send channels array
    selectedChannels.forEach((channel, index) => {
        formData.append(
            `channels[${index}][channel_id]`,
            channel.id
        );

        formData.append(
            `channels[${index}][channel]`,
            channel.channel_name
        );
    });

    // Deleted media
    if (deleted_media.value.length) {
        deleted_media.value.forEach((media, index) => {
            formData.append(
                `deleted_media[${index}]`,
                media.id || media.file_path
            );
        });
    } else {
        formData.append('deleted_media', '[]');
    }

    // Media handling
    let hasNewFile = false;

    form.media.forEach((media, index) => {
        if (media instanceof File) {
            hasNewFile = true;
            formData.append(`media[${index}]`, media);
        }
    });

    // Existing media only (no new uploads)
    if (!hasNewFile) {
        formData.append(
            'media',
            JSON.stringify(form.media || [])
        );
    }

    // DEBUG
    console.group('🚀 Update FormData');

    for (const [key, value] of formData.entries()) {
        console.log(key, value);
    }

    console.groupEnd();

    router.post(
        route(
            'posts.update',
            props.post.id
        ),
        formData,
        {
            forceFormData: true,

            onSuccess: () => {
                console.log(
                    `✅ Updated post ${props.post.id}`
                );
            },

            onError: (errors) => {
                console.error(
                    `❌ Failed to update post ${props.post.id}`,
                    errors
                );
            },
        }
    );
};
</script>
