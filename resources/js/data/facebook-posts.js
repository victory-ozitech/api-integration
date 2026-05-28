export const facebookPosts = {
    data: [
        {
            id: "post_1001",
            user: {
                id: "user_1",
                name: "John Doe",
                username: "johndoe",
                avatar: "https://i.pravatar.cc/150?img=1",
            },
            content: "Just finished building my VILB stack project 🚀",
            image: "https://picsum.photos/800/400?random=1",
            likes_count: 124,
            comments_count: 18,
            shares_count: 7,
            status: "published", // draft | scheduled | published
            scheduled_at: null,
            created_at: "2026-05-28T09:30:00Z",
            visibility: "public",
            liked: true,
        },
        {
            id: "post_1002",
            user: {
                id: "user_1",
                name: "John Doe",
                username: "johndoe",
                avatar: "https://i.pravatar.cc/150?img=1",
            },
            content: "Vue + Laravel + Inertia is such a smooth combo.",
            image: null,
            likes_count: 89,
            comments_count: 12,
            shares_count: 2,
            status: "scheduled", // draft | scheduled | published
            scheduled_at: "2026-06-27T16:10:00Z",
            created_at: "2026-05-27T16:10:00Z",
            visibility: "friends",
            liked: false,
        },
        {
            id: "post_1003",
            user: {
                id: "user_1",
                name: "John Doe",
                username: "johndoe",
                avatar: "https://i.pravatar.cc/150?img=1",
            },
            content: "Posting with Social Arkive is seamlessly smooth.",
            image: "https://picsum.photos/800/400?random=2",
            likes_count: 89,
            comments_count: 12,
            shares_count: 2,
            status: "published", // draft | scheduled | published
            scheduled_at: null,
            created_at: "2026-05-27T16:10:00Z",
            visibility: "friends",
            liked: false,
        },
    ],
    paging: {
        next: "/api/facebook/posts?page=2",
        previous: null,
    },
    meta: {
        total: 2,
    },
};
