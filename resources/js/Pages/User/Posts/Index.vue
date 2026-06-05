<template>
    <UserLayout>
        <PostFormModal :isVisible="showModal" :post="selectedPost" :selectedDate="selectedDate"
            :availableChannels="channels" @formSubmit="handleFormSubmit" @closeModal="showModal = false" />


        <ScheduledPostModal v-if="showScheduledPostsModal" :post="selectedPost" :currentDate="currentDate"
            :selectedDate="selectedDateForModal" @closeModal="closeScheduledPostsModal" @open-modal="handleOpenModal"
            @open-edit-modal="openEditModal" />

        <div class="container py-4">

            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold">Posts</h2>

                <div class="d-flex gap-2">
                    <ViewToggle v-model="view" />
                    <Link :href="route('posts.create')" class="btn btn-create gap-2">
                    <i class="fa-solid fa-plus"></i> Create Post
                    </Link>
                </div>
            </div>

            <!-- Content -->
            <div v-if="view === 'list'">
                <PostList :posts="props.posts" :currentDate="currentDate" @deletePosts="handleDeletePost" />
            </div>

            <div v-else>
                <PostCalendar :posts="props.posts" @open-modal="handleOpenModal" @open-edit-modal="openEditModal"
                    @open-posts-modal="openScheduledPostsModal" v-model:currentDate="currentDate" />
            </div>

        </div>
    </UserLayout>
</template>


<script setup>
import { ref, computed, onMounted } from "vue";
import { router } from "@inertiajs/vue3";
import PostList from "@components/PostList.vue";
import PostCalendar from "@components/PostCalendar.vue";
import ViewToggle from "@components/ViewToggle.vue";
import UserLayout from "@/Layouts/UserLayout.vue";
import PostFormModal from "@/Components/PostFormModal.vue";
import ScheduledPostModal from "@/Components/ScheduledPostModal.vue";


//  ||============PROPS=================||
const props = defineProps({
    channels: {
        type: Array,
        required: true,
    },
    posts: {
        type: Array,
        required: true,
    }
});

onMounted(() => {
    console.log("Posts", props.posts);
    console.log("Available Channels:", props.channels);
});




//  ||============LOCAL STATE=================||
const view = ref("calendar"); // list | calendar

const selectedPost = ref(null);

const showModal = ref(false);
const showScheduledPostsModal = ref(false);

const selectedDate = ref(null);
const currentDate = ref(new Date());
const selectedDateForModal = ref(null);




//  ||============COMPUTED PROPERTIES=================||
// Posts actual value is already in posts.value, so we can directly return it here. This allows us to easily add filters or sorting later if needed.
// const filteredPosts = computed(() => props.posts);





//  ||============METHODS=================||
const handleOpenModal = (date) => {
    selectedDate.value = date; // Store selected date
    // console.log('Handle Open Modal Date:', date);
    selectedPost.value = null; // Reset post (for new post creation)
    showModal.value = true;
    showScheduledPostsModal.value = false;
};

const openScheduledPostsModal = ({ post, date }) => {
    selectedPost.value = post;
    selectedDateForModal.value = date;
    // console.log('Open Scheduled Posts Modal Date:', date, post);
    showScheduledPostsModal.value = true;
};

const closeScheduledPostsModal = () => {
    showScheduledPostsModal.value = false;
    selectedDateForModal.value = null;
};

const openEditModal = (post) => {
    selectedPost.value = post;
    // console.log('Post to be Edited: ', post);

    showModal.value = true; // Open the modal
    showScheduledPostsModal.value = false;
};

const handleDeletePost = (postId) => {
    router.delete(route("posts.destroy", postId), {
        onSuccess: () => {
            console.log("✅ Posts deleted successfully");
        },
        onError: (err) => {
            console.log("❌ Failed to delete posts", err);
        },
    });
};



const handleFormSubmit = async (data) => {
    // ==================================================
    // Helper: Dump FormData
    // ==================================================

    const dumpFormData = (fd) => {
        console.group('FormData contents');

        for (const pair of fd.entries()) {
            console.log(pair[0], pair[1]);
        }

        console.groupEnd();
    };

    // ==================================================
    // Grouped Edit
    // ==================================================

    if (Array.isArray(data.posts) && data.posts.length > 0) {
        for (const post of data.posts) {
            const formData = new FormData();

            formData.append('_method', 'PUT');
            formData.append('message', data.message);
            formData.append(
                'scheduled_at',
                data.scheduled_at ?? ''
            );
            formData.append(
                'is_scheduled',
                data.is_scheduled ? '1' : '0'
            );
            formData.append(
                'status',
                data.status ?? 'scheduled'
            );

            // Deleted Media
            if (
                Array.isArray(data.deleted_media) &&
                data.deleted_media.length > 0
            ) {
                data.deleted_media.forEach(
                    (url, index) => {
                        formData.append(
                            `deleted_media[${index}]`,
                            url
                        );
                    }
                );
            } else {
                formData.append(
                    'deleted_media',
                    '[]'
                );
            }

            // Backend expects top-level channel
            const channelId =
                post.channel_id ??
                (
                    Array.isArray(data.channels) &&
                    data.channels[0]?.channel_id
                ) ??
                null;

            const channelName =
                post.channel ??
                (
                    Array.isArray(data.channels) &&
                    data.channels[0]?.channel
                ) ??
                null;

            if (channelId) {
                formData.append(
                    'channel_id',
                    channelId
                );
            }

            if (channelName) {
                formData.append(
                    'channel',
                    channelName
                );
            }

            // Channels
            if (Array.isArray(data.channels)) {
                data.channels.forEach(
                    (ch, index) => {
                        formData.append(
                            `channels[${index}][channel_id]`,
                            ch.channel_id
                        );

                        formData.append(
                            `channels[${index}][channel]`,
                            ch.channel
                        );
                    }
                );
            }

            // Media
            if (
                Array.isArray(data.media) &&
                data.media.length > 0
            ) {
                let hasNewFile = false;

                data.media.forEach(
                    (file, index) => {
                        if (file instanceof File) {
                            hasNewFile = true;

                            formData.append(
                                `media[${index}]`,
                                file
                            );
                        }
                    }
                );

                if (!hasNewFile) {
                    formData.append(
                        'media',
                        JSON.stringify(
                            data.media
                        )
                    );
                }
            } else {
                formData.append(
                    'media',
                    '[]'
                );
            }

            // dumpFormData(formData);
            // ==================================================
            // UPDATE MULTIPLE EXISTING POSTS (grouped post edit)
            // Route should point to an UPDATE endpoint
            // Example: posts.update
            // ==================================================

            router.post(
                route(
                    'posts.update',
                    post.id
                ),
                formData,
                {
                    method: 'put',
                    forceFormData: true,

                    onSuccess: () => {
                        console.log(
                            `✅ Updated post ${post.id}`
                        );

                        // modalRef.value?.resetForm();
                    },

                    onError: (err) => {
                        console.log(
                            `❌ Failed to update post ${post.id}`,
                            err
                        );
                    },
                }
            );
        }

        // showModal.value = false;

        return;
    }

    // ==================================================
    // Single Edit
    // ==================================================

    if (data.id) {
        const formData = new FormData();

        formData.append('_method', 'PUT');
        formData.append('message', data.message);
        formData.append(
            'scheduled_at',
            data.scheduled_at ?? ''
        );
        formData.append(
            'is_scheduled',
            data.is_scheduled ? '1' : '0'
        );
        formData.append(
            'status',
            data.status ?? 'scheduled'
        );

        // Deleted Media
        if (
            Array.isArray(data.deleted_media) &&
            data.deleted_media.length > 0
        ) {
            data.deleted_media.forEach(
                (url, index) => {
                    formData.append(
                        `deleted_media[${index}]`,
                        url
                    );
                }
            );
        } else {
            formData.append(
                'deleted_media',
                '[]'
            );
        }

        // Top-level channel
        if (
            Array.isArray(data.channels) &&
            data.channels.length > 0
        ) {
            formData.append(
                'channel_id',
                data.channels[0].channel_id
            );

            formData.append(
                'channel',
                data.channels[0].channel
            );
        } else if (
            data.channel_id &&
            data.channel
        ) {
            formData.append(
                'channel_id',
                data.channel_id
            );

            formData.append(
                'channel',
                data.channel
            );
        }

        // Channels array
        if (Array.isArray(data.channels)) {
            data.channels.forEach(
                (ch, index) => {
                    formData.append(
                        `channels[${index}][channel_id]`,
                        ch.channel_id
                    );

                    formData.append(
                        `channels[${index}][channel]`,
                        ch.channel
                    );
                }
            );
        }

        // Media
        if (
            Array.isArray(data.media) &&
            data.media.length > 0
        ) {
            let hasNewFile = false;

            data.media.forEach(
                (file, index) => {
                    if (file instanceof File) {
                        hasNewFile = true;

                        formData.append(
                            `media[${index}]`,
                            file
                        );
                    }
                }
            );

            if (!hasNewFile) {
                formData.append(
                    'media',
                    JSON.stringify(
                        data.media
                    )
                );
            }
        } else {
            formData.append(
                'media',
                '[]'
            );
        }

        // dumpFormData(formData);
        // ==================================================
        // UPDATE SINGLE EXISTING POST
        // Route should point to an UPDATE endpoint
        // Example: posts.update
        // ==================================================
        router.post(
            route(
                'posts.update',
                data.id
            ),
            formData,
            {
                method: 'put',
                forceFormData: true,

                onSuccess: () => {
                    console.log(
                        `✅ Updated post ${data.id}`
                    );

                    // modalRef.value?.resetForm();
                },

                onError: (err) => {
                    console.log(
                        `❌ Failed to update post ${data.id}`,
                        err
                    );
                },
            }
        );

        // showModal.value = false;

        return;
    }

    // ==================================================
    // Create New Post
    // ==================================================

    if (!data.id) {
        const formData = new FormData();

        formData.append(
            'message',
            data.message
        );

        formData.append(
            'scheduled_at',
            data.scheduled_at ?? ''
        );

        formData.append(
            'is_scheduled',
            data.is_scheduled ? '1' : '0'
        );

        formData.append(
            'status',
            data.status
        );

        if (Array.isArray(data.channels)) {
            data.channels.forEach(
                (ch, index) => {
                    formData.append(
                        `channels[${index}][channel_id]`,
                        ch.channel_id
                    );

                    formData.append(
                        `channels[${index}][channel]`,
                        ch.channel
                    );
                }
            );
        } else {
            console.warn(
                '⚠️ No valid channels found:',
                data
            );
        }

        if (Array.isArray(data.media)) {
            data.media.forEach(
                (file, index) => {
                    formData.append(
                        `media[${index}]`,
                        file
                    );
                }
            );
        }
        // ==================================================
        // CREATE NEW POST
        // Route should point to a STORE/CREATE endpoint
        // Example: posts.store
        // ==================================================

        router.post(
            route(
                'posts.store'
            ),
            formData,
            {
                forceFormData: true,

                onSuccess: () => {
                    console.log(
                        '✅ Post created successfully'
                    );

                    // modalRef.value?.resetForm();
                },

                onError: (
                    errors
                ) => {
                    console.log(
                        '❌ Failed to create post',
                        errors
                    );
                },
            }
        );

        // showModal.value = false;
    }
};
</script>

<style lang="scss">
@use '@sass/app.scss' as *;
@use "@sass/mixins.scss" as mixin;
@use "@sass/variables.scss" as *;

.btn-create {
    border: 1px solid rgba(148, 163, 184, 0.3);
    background: #f8fafc;
    border-radius: 14px;
    transition: all 0.18s ease;
    @include mixin.dynamic-text(700, 13px, #0f172a, center);

    &:hover {
        transform: translateY(-1px);
        background: $secondary;
        color: #1F2937;
        border-color: rgba(99, 102, 241, 0.25);
    }
}
</style>