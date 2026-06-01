import { facebookPosts } from "@/data/facebook-posts";

const formatPost = (post) => {
    return {
        id: post.id,
        content: post.content,
        image: post.image,
        created_at: post.created_at,
        visibility: post.visibility,
        likes_count: post.likes_count,
        comments_count: post.comments_count,
        shares_count: post.shares_count,
        status: post.status,

        // 🔥 NEW (UI-friendly date)
        display_date:
            post.status === "scheduled" ? post.scheduled_at : post.created_at,
        liked: post.liked,

        // Flatten user
        user_name: post.user.name,
        user_avatar: post.user.avatar,
    };
};

const STORAGE_KEY = "posts";

// export const getPosts = () => {
//     let posts = JSON.parse(localStorage.getItem(STORAGE_KEY));

//     if (!posts) {
//         // Seed from mock API
//         posts = facebookPosts.data;
//         localStorage.setItem(STORAGE_KEY, JSON.stringify(posts));
//     }

//     // Transform before UI uses it
//     return posts.map(formatPost);
// };

export const getPosts = () => {
    let posts = JSON.parse(localStorage.getItem(STORAGE_KEY));

    if (!posts) {
        posts = facebookPosts.data;
        localStorage.setItem(STORAGE_KEY, JSON.stringify(posts));
    }

    const now = new Date();

    // 🔥 Auto-publish scheduled posts
    posts = posts.map((post) => {
        if (
            post.status === "scheduled" &&
            post.scheduled_at &&
            new Date(post.scheduled_at) <= now
        ) {
            return {
                ...post,
                status: "published",
                scheduled_at: null,
            };
        }

        return post;
    });

    // Persist updates
    localStorage.setItem(STORAGE_KEY, JSON.stringify(posts));

    return posts.map(formatPost);
};

export const getPostById = (id) => {
    if (!id) return null;

    const posts = JSON.parse(localStorage.getItem(STORAGE_KEY) || "[]");
    const post = posts.find((p) => p.id === id);

    return post ? formatPost(post) : null;
};

export const createPost = (postData) => {
    const posts = JSON.parse(localStorage.getItem(STORAGE_KEY) || "[]");

    const isScheduled = !!postData.scheduled_at;

    const newPost = {
        id: `post_${Date.now()}`,
        user: {
            id: "user_1",
            name: "John Doe",
            username: "johndoe",
            avatar: "https://i.pravatar.cc/150?img=1",
        },
        content: postData.content,
        image: postData.image || null,
        likes_count: 0,
        comments_count: 0,
        shares_count: 0,
        scheduled_at: postData.scheduled_at || null,
        status: isScheduled ? "scheduled" : "published",
        created_at: new Date().toISOString(),
        visibility: "public",
        liked: false,
    };

    posts.unshift(newPost);
    localStorage.setItem(STORAGE_KEY, JSON.stringify(posts));

    return formatPost(newPost);
};


export const updatePost = (id, updatedData) => {
    const posts = JSON.parse(localStorage.getItem(STORAGE_KEY) || "[]");

    const updatedPosts = posts.map((post) => {
        if (post.id !== id) return post;

        const isScheduled =
            updatedData.scheduled_at !== null &&
            updatedData.scheduled_at !== "";

        return {
            ...post,
            content: updatedData.content,
            image: updatedData.image,
            scheduled_at: updatedData.scheduled_at || null,
            status: updatedData.scheduled_at
                ? "scheduled"
                : post.status === "scheduled"
                  ? "scheduled"
                  : "published",
        };
    });

    localStorage.setItem(STORAGE_KEY, JSON.stringify(updatedPosts));

    return updatedPosts.map(formatPost);
};

export const deletePost = (id) => {
    const posts = JSON.parse(localStorage.getItem(STORAGE_KEY) || "[]");

    const filteredPosts = posts.filter((post) => post.id !== id);

    localStorage.setItem(STORAGE_KEY, JSON.stringify(filteredPosts));

    return filteredPosts.map(formatPost);
};