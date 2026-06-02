<template>
    <PostFormLayout>
        <!-- Header -->
        <div class="page-header">
            <h2>Create Post</h2>
            <p>Share something with your audience</p>
        </div>

        <!-- Card -->
        <div class="create-card">
            <form @submit.prevent.stop="submitForm">

                <!-- Channels Form Group -->
                <div class="form-group">
                    <div class="channel-selection-header">
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
                        <div v-for="channel in channels" :key="channel.id" class="profile-img" :class="{
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
                            <button type="button" class="btn btn-danger btn-sm mt-1" @click="removeMedia(index)">
                                Remove
                            </button>
                        </div>
                    </div>


                    <!-- Media -->
                    <div v-if="form.media?.length" class="media-section">
                        <h5>Media</h5>
                        <div class="media-grid">
                            <div v-for="media in form.media" :key="media.url" class="media-item"
                                @click.stop="openMedia(media.url)">
                                <img v-if="isImage(media)" :src="getMediaUrl(media)"
                                    :alt="media.original_name || media.name || 'Uploaded image'" />
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
                                <button type="button" class="remove-btn" @click.stop="deleteExistingMedia(media)">
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
                        <input type="radio" id="publish_now" value="publish_now" class="form-check-input" v-model="selectedOption
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
                        <input type="radio" id="custom_time" value="custom_time" class="form-check-input" v-model="selectedOption
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
    </PostFormLayout>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from "vue";
import { router } from "@inertiajs/vue3";
import { createPost } from "@/utils/postUtils";
import PostFormLayout from "@/Layouts/PostFormLayout.vue";


//  ||============PROPS=================||
const props = defineProps({
    channels: {
        type: Array,
        required: true,
    }
});

onMounted(() => {
    console.log("Available Channels:", props.channels);
})



//  ||============LOCAL STATE=================||
const content = ref("");
const image = ref("");
const scheduled_at = ref("");

const selectedOption = ref('publish_now');
const selectedChannel = ref(null);
const previewMedia = ref([]);
const deleted_media = ref([]);




//  ||============REACTIVE FORM=================||
const form = reactive({
    message: "",
    date: '',
    time: "09:00",
    channels: [],
    media: [],
    is_scheduled: false,
    scheduled_at: null,
    publish_option: "now",
    status: "draft",
});




//  ||============COMPUTED PROPERTIES=================||
// minDate
const minDate = computed(() => {
    const today = new Date();
    return today.toISOString().split('T')[0];
});

// formChannel
const formChannel = computed(() => {
    if (!form.channels?.length) return null;

    const id = form.channels[0]; // assuming one selected channel

    return props.channels.find(c => c.id === id) || null;
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
        if (media.url) return media.url;

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

const formatDate = (date) => {
    if (!date) return '';

    const d = new Date(date);

    const year = d.getFullYear();
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const day = String(d.getDate()).padStart(2, '0');

    return `${year}-${month}-${day}`;
};

const submitForm = () => {
    const selectedChannels = props.channels.filter((c) =>
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

    formData.append('message', form.message);
    formData.append('scheduled_at', form.scheduled_at ?? '');
    formData.append(
        'is_scheduled',
        form.is_scheduled ? '1' : '0'
    );
    formData.append('status', form.status);
    formData.append('publish_option', form.publish_option);

    // Channels
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

    // Media
    form.media.forEach((file, index) => {
        formData.append(`media[${index}]`, file);
    });

    // Optional deleted media support
    deleted_media.value.forEach((media, index) => {
        formData.append(
            `deleted_media[${index}]`,
            media.id || media.file_path
        );
    });

    // DEBUG
    console.group('🚀 FormData being sent');

    for (const [key, value] of formData.entries()) {
        console.log(key, value);
    }

    console.groupEnd();

    // return; // Remove this line to enable actual submission

    router.post(
        route('facebook.publish'), // Adjust route name as needed
        formData,
        {
            forceFormData: true,

            onSuccess: () => {
                console.log('✅ Created');
            },

            onError: (errors) => {
                console.error('Failed to create post', errors);
            },
        }
    );
};
</script>
