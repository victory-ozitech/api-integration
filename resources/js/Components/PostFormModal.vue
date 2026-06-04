<template>
    <div v-if="isVisible" class="modal-overlay" @click.self="closeModal">
        <div class="modal-content">
            <div class="modal-body">
                <div class="row g-2">
                    <div class="col-md-8" :class="{ alone: !activePreviewChannel }">
                        <div class="left-col">
                            <h3 class="form-header">
                                {{ isEditMode ? "Edit Scheduled Post" : "Create New Post" }}
                            </h3>
                            <form @submit.prevent.stop="submitForm">
                                <!-- Channels Form Group -->
                                <div class="form-group">
                                    <div v-if="!isEditMode" class="channel-selection-header">
                                        <div class="label">Select Channels</div>
                                        <div class="sub-label">Choose one or multiple channels to post to</div>
                                        <div class="selection-info">
                                            <span v-if="form.channels.length === 0" class="text-muted">No channels
                                                selected</span>
                                            <span v-else class="selected-count">
                                                {{ form.channels.length }} channel{{ form.channels.length > 1 ? 's' : ''
                                                }} selected
                                            </span>
                                            <button v-if="form.channels.length > 0" type="button" class="btn-clear"
                                                @click="clearSelection">
                                                Clear All
                                            </button>
                                        </div>
                                    </div>
                                    <div class="social-media-profile-cont">
                                        <!-- Show only the selected channel if editing -->
                                         <template v-if="isEditMode && formChannels">
                                             <div v-for="channel in formChannels" :key="channel.id || channel.channel_id"
                                                class="profile-img active disabled"
                                                 :title="`This post is linked to ${channel.channel_name ||
                                                     'Unknown Channel'}. Channel cannot be changed.`">
                                                 <img :src="channel.avatar || '/assets/images/profile-img.png'"
                                                     :alt="channel.channel_name || 'Channel Avatar'" />
                                                 <span class="social-icon">
                                                     <i :class="getChannelIcon(channel.platform || 'facebook')"></i>
                                                 </span>
                                                 <div class="selected-badge-edit">
                                                     ✓
                                                 </div>
                                                 <div class="channel-lock-overlay">
                                                     <i class="fas fa-lock"></i>
                                                 </div>
                                             </div>
                                         </template>
                                        <template v-else>
                                            <div v-for="channel in availableChannels" :key="channel.id"
                                                class="profile-img" :class="{
                                                    grayscale: channel.status !== 'active',
                                                    active: isChannelSelected(channel.id)
                                                }" @click="toggleChannel(channel)">
                                                <img :src="channel.avatar || '/assets/images/profile-img.png'"
                                                    :alt="channel.channel_name" />
                                                <span class="social-icon">
                                                    <i :class="getChannelIcon(channel.platform)"></i>
                                                </span>
                                                <div v-if="isChannelSelected(channel.id)" class="selected-badge">
                                                    ✓
                                                </div>
                                            </div>
                                        </template>
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
                                            <img v-if="file.type.startsWith('image/')" :src="file.url"
                                                class="preview-img" />
                                            <video v-else-if="file.type.startsWith('video/')" controls
                                                class="preview-video">
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

                                <!-- Message Form Group -->
                                <div class="form-group">
                                    <label for="message">Post message</label>
                                    <textarea name="message" rows="3" id="message" v-model="form.message"
                                        placeholder="Enter post message" class="form-control border-dark"
                                        required></textarea>
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
                                        <input type="radio" id="publish_now" value="publish_now"
                                            class="form-check-input" v-model="selectedOption
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
                                        <input type="radio" id="custom_time" value="custom_time"
                                            class="form-check-input" v-model="selectedOption
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
                                            <!-- <div v-if="selectedOption === 'custom_time' && form.errors.date"
                                                class="invalid-feedback">
                                                {{ form.errors.date }}
                                            </div> -->
                                        </div>

                                        <!-- Time -->
                                        <div class="mb-3" v-if="selectedOption === 'custom_time'">
                                            <label for="time" class="small-label">Time</label>
                                            <input type="time" id="time" v-model="form.time"
                                                class="form-control small-input" />
                                            <!-- <div v-if="selectedOption === 'custom_time' && form.errors.time"
                                                class="invalid-feedback">
                                                {{ form.errors.time }}
                                            </div> -->
                                        </div>

                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="form-actions">
                                    <button type="submit" class="btn btn-primary">
                                        {{ isEditMode ? "Update" : "Create" }}
                                    </button>
                                    <button type="button" class="btn btn-secondary" @click="closeModal">
                                        Cancel
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
            </div>
        </div>
    </div>
</template>

<script setup>

// ======================================================
// Imports
// ======================================================

import { ref, reactive, computed, watch } from 'vue';

import GooglePreview from '@/Components/Previews/GooglePreview.vue';
import TwitterPreview from '@/Components/Previews/TwitterPreview.vue';
import GenericPreview from '@/Components/Previews/GenericPreview.vue';
import YouTubePreview from '@/Components/Previews/YouTubePreview.vue';
import LinkedInPreview from '@/Components/Previews/LinkedInPreview.vue';
import FacebookPreview from '@/Components/Previews/FacebookPreview.vue';
import InstagramPreview from '@/Components/Previews/InstagramPreview.vue';
import TikTokPreview from '@/Components/Previews/TikTokPreview.vue';


// ======================================================
// Props / Emits
// ======================================================

const props = defineProps({
    isVisible: {
        type: Boolean,
        required: true,
    },

    post: {
        type: Object,
        default: null,
    },

    availableChannels: {
        type: Array,
        required: true,
    },

    selectedDate: {
        type: [String, Date, null],
        required: true,
    },
});

const emit = defineEmits(['closeModal', 'formSubmit']);


// ======================================================
// Date Helpers
// ======================================================

const formatDate = (date) => {
    if (!date) return '';

    const d = new Date(date);

    const year = d.getFullYear();
    const month = String(
        d.getMonth() + 1
    ).padStart(2, '0');

    const day = String(
        d.getDate()
    ).padStart(2, '0');

    return `${year}-${month}-${day}`;
};

const parseScheduledAt = (scheduledAt) => {
    if (!scheduledAt) {
        return {
            date: formatDate(props.selectedDate),
            time: '09:00',
            dateObj: null,
        };
    }

    const d = new Date(scheduledAt);

    return {
        date: formatDate(d),
        time: d.toTimeString().slice(0, 5),
        dateObj: d,
    };
};


// ======================================================
// State
// ======================================================

const selectedOption = ref('publish_now');

const selectedChannel = ref(null);

const previewMedia = ref([]);

const deleted_media = ref([]);

const form = reactive({
    message: props.post?.message || "",
    date: formatDate(props.selectedDate),
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


// ======================================================
// Computed
// ======================================================

// minDate
const minDate = computed(() => {
    const today = new Date();

    return today.toISOString().split('T')[0];
});

// Is Edit Mode
const isEditMode = computed(() => {
    return !!props.post;
});

// formChannel
const formChannels = computed(() => {
    if (!form.channels?.length) return null;

    // console.log('Post prop: ', props.post);
    // console.log('Form: ', form);
    // console.log('Form Channels: ', form.channels);

    const selectedIds = form.channels; // assuming one selected channel

    // return props.post?.channels.find(c => c.id === id) || null;

    // console.log('Selected Id: ', props.post.channels
    //     .filter(c => selectedIds.includes(c.channel_id))
    //     // .filter(c => selectedIds.includes(c.id))
    //     .map(c => c.channel));

    return props.post.channels
        .filter(c => selectedIds.includes(c.channel_id))
        // .filter(c => selectedIds.includes(c.id))
        .map(c => c.channel);
});

// active preview channel (first selected channel or null)
const activePreviewChannel = computed(() => {
    if (!form.channels?.length) return null;

    const firstId =
        typeof form.channels[0] === 'object'
            // ? form.channels[0].channel_id
            ? form.channels[0].id
            : form.channels[0];

    // console.log('First Id: ', firstId);
    // console.log('Active Preview Channel: ', props.availableChannels.find(c => c.id === firstId));

    return props.availableChannels.find(c => c.id === firstId) || null;
});

// selected preview channels (all selected channels as objects)
const selectedPreviewChannels = computed(() => {
    if (!form.channels?.length) return null;

    const selectedIds = form.channels.map(channel =>
        typeof channel === 'object'
            // ? channel.channel_id
            ? channel.id
            : channel
    );

    // console.log('Selected Id: ', selectedIds);
    // console.log('Active Preview Channel: ', props.availableChannels.filter(c => selectedIds.includes(c.id)));

    return props.availableChannels.filter(c => selectedIds.includes(c.id));
});


// ======================================================
// METHODS (Helpers that are needed before watchers)
// ======================================================



// ======================================================
// Channel Helpers
// ======================================================

const extractChannelsFromPost = (post) => {
    if (!post) {
        return [];
    }

    // Multi-channel structure
    if (Array.isArray(post.channels)) {
        return post.channels
            .map((channel) =>
                typeof channel === 'object'
                    ? (
                        channel.channel_id ??
                        channel.id ??
                        null
                    )
                    : channel
            )
            .filter(Boolean);
    }

    // Grouped post structure
    if (
        post.is_grouped &&
        Array.isArray(post.posts)
    ) {
        return post.posts
            .map(
                (postItem) =>
                    postItem.channel_id ??
                    postItem.id ??
                    postItem.channel ??
                    null
            )
            .filter(Boolean);
    }

    // Single post structure
    return [
        post.channel_id ??
        post.id ??
        null,
    ].filter(Boolean);
};



// ======================================================
// Post Normalization
// ======================================================

const normalizePostForForm = (post) => {
    const blank = {
        message: '',
        date: formatDate(
            props.selectedDate
        ),
        time: '09:00',
        channels: [],
        media: [],
        is_scheduled: false,
        scheduled_at: null,
        publish_option: 'now',
        status: 'draft',
        id: null,
        group_id: null,
        is_grouped: false,
        posts: [],
    };

    if (!post) {
        return blank;
    }

    // --------------------------------------------------
    // Grouped Post
    // --------------------------------------------------

    if (
        post.is_grouped ||
        Array.isArray(post.posts)
    ) {
        const first =
            Array.isArray(post.posts) &&
                post.posts.length
                ? post.posts[0]
                : {};

        const { date, time } =
            parseScheduledAt(
                first.scheduled_at ??
                post.scheduled_at
            );

        return {
            message:
                first.message ??
                post.message ??
                '',

            date,
            time,

            channels:
                extractChannelsFromPost(post),

            media:
                first.media ??
                post.media ??
                [],

            is_scheduled: Boolean(
                first.is_scheduled ??
                post.is_scheduled ??
                post.scheduled_at
            ),

            scheduled_at:
                first.scheduled_at ??
                post.scheduled_at ??
                null,

            status:
                post.status ??
                first.status ??
                'draft',

            publish_option:
                post.publish_option ??
                first.publish_option ??
                'schedule',

            id: post.id ?? null,

            group_id:
                post.group_id ?? null,

            is_grouped: true,

            posts: (
                post.posts || []
            ).map((item) => ({
                id: item.id,

                message:
                    item.message,

                scheduled_at:
                    item.scheduled_at,

                channel_id:
                    item.channel_id ??
                    item.id ??
                    null,

                media:
                    item.media ?? [],
            })),
        };
    }

    // --------------------------------------------------
    // Single Post
    // --------------------------------------------------

    const scheduledDate =
        post.scheduled_at ??
        post.dateTime ??
        (
            post.date
                ? `${post.date}T${post.time ?? '00:00'}`
                : null
        );

    const { date, time } =
        parseScheduledAt(
            scheduledDate
        );

    return {
        message:
            post.message ??
            post.content ??
            '',

        date,
        time,

        channels:
            extractChannelsFromPost(post),

        media:
            post.media ?? [],

        is_scheduled: Boolean(
            post.is_scheduled ??
            post.scheduled_at
        ),

        scheduled_at:
            post.scheduled_at ?? null,

        status:
            post.status ??
            'draft',

        publish_option:
            post.publish_option ??
            'schedule',

        id:
            post.id ?? null,

        group_id:
            post.group_id ?? null,

        is_grouped: false,

        posts: [],
    };
};


// ======================================================
// Watchers
// ======================================================

watch(
    () => props.isVisible,
    (visible) => {
        if (!visible) return;

        // Create mode
        if (!props.post) {
            resetForm();
        }
    }
);

watch(
    formChannels,
    (value) => {
        // console.log('formChannels changed', value);
    },
    {
        deep: true,
    }
);

watch(
    () => props.selectedDate,
    (newDate) => {
        if (!props.post) {
            form.date = formatDate(newDate);
        }
    }
);

watch(
    () => props.post,
    (newPost) => {
        // Deep clone first
        const cloned = newPost
            ? JSON.parse(JSON.stringify(newPost))
            : null;

        const normalized =
            normalizePostForForm(cloned);

        // Preserve reactivity
        Object.assign(form, normalized);

        // Sync scheduling UI
        if (
            form.publish_option === 'queue' ||
            form.publish_option === 'prioritize'
        ) {
            selectedOption.value =
                form.publish_option;
        } else if (form.is_scheduled) {
            selectedOption.value =
                'custom_time';
        } else if (form.status === 'published') {
            selectedOption.value =
                'publish_now';
        } else {
            selectedOption.value =
                'save_draft';
        }

        // Debug
        // console.log('Incoming post:', newPost);
        // console.log('Normalized form:', JSON.parse(JSON.stringify(form)));
    },
    {
        immediate: true,
    }
);


// ======================================================
// METHODS
// ======================================================


// ======================================================
// Reset Helpers
// ======================================================
const getBlankForm = () => ({
    message: '',
    date: formatDate(props.selectedDate),
    time: '09:00',
    channels: [],
    media: [],
    is_scheduled: false,
    scheduled_at: null,
    publish_option: 'now',
    status: 'draft',
    id: null,
    group_id: null,
    is_grouped: false,
    posts: [],
});

const resetForm = () => {
    Object.assign(form, getBlankForm());

    selectedOption.value = 'publish_now';
    selectedChannel.value = null;
    previewMedia.value = [];
    deleted_media.value = [];
};


// ======================================================
// Media Helpers
// ======================================================

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



// ======================================================
// Media Actions
// ======================================================

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


// ======================================================
// Channel Helpers
// ======================================================

const toggleChannel = (id) => {
    const index = form.channels.indexOf(id);

    if (index === -1) {
        form.channels.push(id);
    } else {
        form.channels.splice(index, 1);
    }
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

// Check if channel is selected
const isChannelSelected = (channelId) => {
    return form.channels.some((c) => c.id === channelId);
};

// Clear all selections
const clearSelection = () => {
    form.channels = [];
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



// ======================================================
// Modal Actions
// ======================================================

const closeModal = () => {
    previewMedia.value = [];
    deleted_media.value = [];

    emit('closeModal');
};



// ======================================================
// Form Submission
// ======================================================

const submitForm = () => {
    let selectedChannels = [];

    // --------------------------------------------------
    // Channel Validation
    // --------------------------------------------------

    if (isEditMode.value) {
        selectedChannels =
            props.availableChannels.filter(
                (channel) =>
                    form.channels.includes(
                        channel.id
                    )
            );
    } else {
        selectedChannels =
            props.availableChannels.filter(
                (channel) =>
                    form.channels.some(
                        (selected) =>
                            typeof selected ===
                                'object'
                                ? selected.id ===
                                channel.id
                                : selected ===
                                channel.id
                    )
            );

        if (!selectedChannels.length) {
            alert(
                'Please select a channel first!'
            );
            return;
        }
    }

    // --------------------------------------------------
    // Scheduling
    // --------------------------------------------------

    if (
        selectedOption.value ===
        'queue' ||
        selectedOption.value ===
        'prioritize'
    ) {
        form.is_scheduled = true;
        form.scheduled_at = null;
        form.publish_option =
            selectedOption.value;
        form.status = 'scheduled';
    }

    else if (
        selectedOption.value ===
        'custom_time' &&
        form.date &&
        form.time
    ) {
        form.is_scheduled = true;

        form.scheduled_at =
            `${form.date} ${form.time}:00`;

        form.publish_option =
            'schedule';

        form.status = 'scheduled';
    }

    else if (
        selectedOption.value ===
        'publish_now'
    ) {
        form.status = 'published';

        form.is_scheduled = false;

        form.publish_option = 'now';
    }

    else if (
        selectedOption.value ===
        'save_draft'
    ) {
        form.status = 'draft';

        form.is_scheduled = false;
    }

    if (!selectedOption.value) {
        alert(
            'Please choose a scheduling option.'
        );

        return;
    }

    // --------------------------------------------------
    // Base Payload
    // --------------------------------------------------

    const postData = {
        message: form.message,

        scheduled_at:
            form.scheduled_at,

        is_scheduled:
            form.is_scheduled,

        status: form.status,

        publish_option:
            form.publish_option,

        media: form.media,

        deleted_media:
            deleted_media.value.map(
                (media) =>
                    media.id ||
                    media.file_path
            ),

        channels:
            selectedChannels.map(
                (channel) => ({
                    channel:
                        channel.channel,

                    channel_id:
                        channel.id,
                })
            ),
    };

    // --------------------------------------------------
    // Grouped Post Edit
    // --------------------------------------------------

    if (
        form.is_grouped &&
        form.id &&
        form.group_id
    ) {
        postData.id = form.id;
    }

    if (
        form.is_grouped &&
        form.group_id
    ) {
        postData.is_grouped = true;

        postData.group_id =
            form.group_id;

        if (
            Array.isArray(form.posts)
        ) {
            postData.posts =
                form.posts.map(
                    (post) => ({
                        id: post.id,

                        channel:
                            selectedChannels.find(
                                (channel) =>
                                    channel.id ===
                                    post.channel_id
                            )?.channel ??
                            post.channel,

                        channel_id:
                            post.channel_id,

                        message:
                            form.message,

                        scheduled_at:
                            postData.scheduled_at,

                        is_scheduled:
                            postData.is_scheduled
                                ? 'yes'
                                : 'no',

                        status:
                            postData.status,
                    })
                );
        }
    }

    // --------------------------------------------------
    // Single Post Edit
    // --------------------------------------------------

    if (
        !form.is_grouped &&
        form.id
    ) {
        postData.id = form.id;
    }

    // --------------------------------------------------
    // Submit
    // --------------------------------------------------

    emit('formSubmit', postData);

    resetForm();
    closeModal();
};

</script>

<style lang="scss" scoped>
@use '@sass/app.scss' as *;
@use "@sass/mixins.scss" as mixin;
@use "@sass/variables.scss" as *;

form {

    .media-section {
        h5 {
            font-size: 0.8rem;
            color: #444;
            margin-bottom: 4px;
        }

        .media-grid {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
        }

        .media-item {
            position: relative;
            width: 100px;
            height: 100px;
            border-radius: 8px;
            overflow: hidden;
            background: #f3f4f6;
            box-shadow: inset 0 0 3px rgba(0, 0, 0, 0.08);
            cursor: pointer;
            transition: transform 0.15s ease;

            .remove-btn {
                position: absolute;
                top: 4px;
                right: 4px;
                background: rgba(0, 0, 0, 0.6);
                color: white;
                border: none;
                border-radius: 50%;
                width: 18px;
                height: 18px;
                font-size: 12px;
                line-height: 1;
                cursor: pointer;
                display: none;
            }

            &:hover .remove-btn {
                display: block;
            }

            &:hover {
                transform: scale(1.05);
            }

            img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            .video-placeholder {
                display: flex;
                align-items: center;
                justify-content: center;
                background: #e2e8f0;
                font-size: 0.8rem;
            }

            .media-info {
                position: absolute;
                bottom: 0;
                left: 0;
                width: 100%;
                background: rgba(0, 0, 0, 0.55);
                color: #fff;
                font-size: 0.7rem;
                line-height: 1.2;
                padding: 2px 4px;
                text-overflow: ellipsis;
                white-space: nowrap;
                overflow: hidden;

                .media-name {
                    font-weight: 500;
                }

                .media-size {
                    opacity: 0.85;
                    font-size: 0.65rem;
                }
            }
        }
    }

    .channel-selection-header {
        margin-bottom: 16px;

        .selection-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 8px;

            .selected-count {
                font-weight: 600;
                color: #007bff;
            }

            .btn-clear {
                background: none;
                border: none;
                color: #dc3545;
                font-size: 12px;
                cursor: pointer;
                text-decoration: underline;

                &:hover {
                    color: #c82333;
                }
            }
        }
    }

    .social-media-profile-cont {
        .profile-img {
            position: relative;

            &.multiple {
                border: 2px solid #007bff !important;
            }

            .selected-badge {
                position: absolute;
                top: -5px;
                right: -5px;
                background: #007bff;
                color: white;
                border-radius: 50%;
                width: 20px;
                height: 20px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 12px;
                font-weight: bold;
            }
        }
    }

    .channel-badge {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 12px;
        padding: 4px 8px;
        font-size: 11px;
        font-weight: 600;
        color: #6c757d;
    }

    .no-selection {
        .placeholder-preview {
            background: #f8f9fa;
            border: 2px dashed #dee2e6;
            border-radius: 12px;
            padding: 40px 20px;
            text-align: center;

            .placeholder-text {
                color: #6c757d;
                font-size: 14px;
            }
        }
    }

    .social-media-profile-cont {
        gap: 15px;
        display: flex;
        flex-wrap: wrap;
        margin-bottom: 16px;

        .profile-img {
            position: relative;
            width: 60px;
            height: 60px;
            cursor: pointer;
            transition: transform 0.2s ease;

            &.disabled {
                cursor: not-allowed !important;
                opacity: 0.8;
                position: relative;

                &:hover {
                    transform: none !important;
                }

                .channel-lock-overlay {
                    position: absolute;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background: rgba(0, 0, 0, 0.3);
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    color: white;
                    font-size: 16px;
                }
            }

            .selected-badge-edit {
                position: absolute;
                top: -5px;
                right: -5px;
                background: #007bff;
                color: white;
                border-radius: 50%;
                width: 20px;
                height: 20px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 12px;
                font-weight: bold;
            }

            &:hover {
                transform: scale(1.05);
            }

            &.active {
                border: 2px solid #006167;
                /* highlight color */
                border-radius: 50%;
            }

            img {
                width: 100%;
                height: 100%;
                border-radius: 50%;
                object-fit: cover;
            }

            .social-icon {
                position: absolute;
                bottom: 0;
                right: 0;
                background: #fff;
                border-radius: 50%;
                padding: 1px 4px;
                font-size: 14px;
                color: #1877f2;
                /* default Facebook blue */

                i {
                    width: 100%;
                    height: 100%;
                }
            }
        }

        .grayscale img {
            filter: grayscale(100%);
        }
    }

    .input-control {
        height: 45px;
        border: 1px solid #9f9193;
        border-radius: 5px;
        font-size: 12px;
    }

    .each-group {
        .label {
            font-weight: 900;
            font-size: 13px;
            margin-bottom: 17px;
        }

        .sub-label {
            font-size: 10px;
        }

        .form-check-input {
            box-shadow: none;
        }

        .form-check-label {
            font-size: 11px;
        }

        .upload-group {
            display: flex;
            margin-top: 19px;
            gap: 30px;

            button {
                display: flex;
                justify-content: center;
                align-items: center;
                height: 37px;
                font-size: 10px;
                border: 1px solid #78dda7;
                background-color: #e9ffe4;
                border-radius: 3px;
                padding: 10px 15px;
                gap: 10px;

                .img-cont {
                    max-width: 19px;
                }
            }
        }

        .input-container {
            position: relative;
            border: 1px solid #d9d2d2;
            border-radius: 6px;
            padding: 8px;
            display: flex;
            flex-direction: column;
            margin-top: 5px;

            textarea {
                border: none;
                resize: none;
                height: 100px;
                outline: none;
                font-size: 14px;
                width: 100%;
            }

            .input-actions {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-top: 8px;

                .icon {
                    font-size: 16px;
                    cursor: pointer;
                }

                .ad-icon {
                    color: purple;
                    font-weight: bold;
                }

                .hashtag {
                    color: black;
                }

                .emoji {
                    color: black;
                }
            }
        }

        .switch-cont {
            position: relative;
            width: 43px;
            height: 23px;
            background-color: #494e4c;
            border-radius: 999px;
            padding: 3px 4px;
            transition: background-color 0.3s ease;

            &.on {
                background-color: #d4f7e0;
            }

            .toggle-switch {
                position: absolute;
                width: 17px;
                height: 17px;
                border-radius: 50%;
                cursor: pointer;
                transition: all 0.3s ease;

                &.off {
                    background-color: #d4f7e0;
                    left: 5px;
                }

                &.on {
                    background-color: #118a53;
                    left: 22px;
                }
            }
        }

        .date-time-container {
            display: flex;
            gap: 16px;

            .date-box,
            .time-box {
                display: flex;
                align-items: center;
                border: 1px solid #ccc;
                border-radius: 4px;
                padding: 8px;
                gap: 15px;

                i {
                    color: #4b5563;
                    margin-right: 8px;
                }
            }
        }

        .fade-enter-active,
        .fade-leave-active {
            transition: opacity 0.5s ease;
        }

        .fade-enter,
        .fade-leave-to {
            opacity: 0;
        }

        .right-cont {
            margin-left: 70px;

            .small-label {
                font-size: 13px;
                font-weight: 900;
                margin-bottom: 3px;
            }

            .input-contain {
                display: flex;
                height: 26px;
                border: 1px solid #78dda7;
                border-radius: 2px;

                .form-control {
                    height: 100%;
                    border: 0;
                    border-radius: 0;
                    padding: 2px 4px;
                    font-size: 12px;
                    color: #1eab68;
                }

                .fa-cont {
                    height: 100%;
                    width: 50px;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    background-color: #adedc7;

                    .fa-calender {
                        color: #1eab68;
                    }
                }
            }
        }
    }

    select:disabled {
        background-color: #f3f3f3;
        cursor: not-allowed;
        color: #555;
    }

    .upload-group {
        display: flex;
        margin-top: 19px;
        gap: 30px;

        button {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 37px;
            font-size: 10px;
            border: 1px solid #78dda7;
            background-color: #e9ffe4;
            border-radius: 3px;
            padding: 10px 15px;
            gap: 10px;

            .img-cont {
                max-width: 19px;
            }
        }
    }

    .label {
        font-weight: 900;
        font-size: 13px;
        margin-bottom: 17px;
    }

    .sub-label {
        font-size: 10px;
    }

    .media-preview {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
    }

    .preview-item {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .preview-img {
        width: 120px;
        height: auto;
        border-radius: 6px;
    }

    .preview-video {
        width: 200px;
        border-radius: 6px;
    }

    .form-group {
        margin-bottom: 15px;
    }

    label {
        display: block;
        font-weight: 500;
        margin-bottom: 5px;
    }

    input:not([type='radio']),
    select {
        width: 100%;
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 4px;
        font-size: 14px;
    }

    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }

    .btn {
        padding: 10px 15px;
        font-size: 14px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    .btn-primary {
        background-color: #37b776;
        color: white;
    }

    .btn-secondary {
        background-color: #ccc;
        color: black;
    }
}

.alone {
    margin: auto;
}

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
    overflow-y: auto;
    -webkit-overflow-scrolling: touch;
}

.modal-content {
    width: 90%;
    max-width: 90%;
    max-height: 90vh;
    // display: flex;
    // flex-direction: column;
    overflow: hidden;
}

.right-col {
    width: 100%;
    height: 100%;
    border-radius: 8px;
    background-color: #e8e8e8;
    padding: 25px 15px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.left-col {
    border-radius: 8px;
    position: relative;
    padding: 0 20px 20px;
    background-color: #e8e8e8;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);

    .form-header {
        top: 0;
        z-index: 1;
        padding: 20px;
        flex-shrink: 0;
        font-size: 18px;
        position: sticky;
        font-weight: bold;
        background-color: #e8e8e8;
        border-bottom: 1px solid #eee;
    }
}

.modal-body {
    overflow-y: auto;
    flex-grow: 1;
}

/* Hide scrollbar */
.modal-body::-webkit-scrollbar {
    display: none;
}

.modal-body {
    -ms-overflow-style: none;
    scrollbar-width: none;
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
