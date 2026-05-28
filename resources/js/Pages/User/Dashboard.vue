<template>

    <Head title="Dashboard - Social Archive" />

    <UserLayout>
        <div class="archive-wrapper d-flex">
            <div class="main-content flex-grow-1">
                <div class="top-cont">
                    <div class="welcome-txt">
                        <span class="large-txt">Hello <span>{{ formattedUsername }}, </span></span><span
                            class="small-txt">Welcome To Your Workspace</span>
                    </div>
                    <Link :href="route('posts.create')" class="img-cont">
                    <!-- <img src="https://fastly.picsum.photos/id/108/200/300.jpg?hmac=66ukSMLRNm61ayt092vMAdSgvIRE5opr1Dj3kxCkC2c"
                        alt="" /> -->
                    <img :src="'/assets/images/arkive-logo.png'" alt="" />
                    </Link>
                </div>
                <div class="white-bg">
                    <div class="cont-top">
                        <div class="outer-wrapper">
                            <div class="add-channel-wrapper">
                                <div v-for="channel in channels" :key="channel.id" class="each-channel">
                                    <div class="icon-cont">
                                        <!-- <img src="/assets/images/instra-icon.png" alt=""> -->
                                        <span class="social-icon" v-html="getPlatformLogo(channel.channel)
                                            "></span>
                                    </div>
                                    @_{{ channel.account_username }}
                                </div>
                            </div>
                        </div>
                        <Link :href="route('posts.create')" class="add-channel">
                        <div class="icon-cont">
                            <i class="fa-solid fa-plus"></i>
                        </div>
                        add a channel
                        </Link>
                    </div>
                    <div class="content">
                        <div class="title">what do you want to do today</div>
                        <div class="options">
                            <div class="row g-3">
                                <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12">
                                    <Link :href="route('posts.create')
                                        " class="each-option">
                                    <div class="img-bg">
                                        <div class="img-cont">
                                            <img :src="'/assets/images/create-post-img.png'" alt="" />
                                        </div>
                                    </div>
                                    <div>
                                        <div class="option-title">
                                            Create New Post
                                        </div>
                                        <div class="sub-title">
                                            Auto Post Accross Your Platforms
                                        </div>
                                    </div>
                                    </Link>
                                </div>
                                <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12">
                                    <Link :href="route('posts.create')
                                        " class="each-option">
                                    <div class="img-bg">
                                        <div class="img-cont">
                                            <img :src="'/assets/images/generate-post-img.png'" alt="" />
                                        </div>
                                    </div>
                                    <div>
                                        <div class="option-title">
                                            Generate Post Ideas
                                        </div>
                                        <div class="sub-title">
                                            Get endless post ideas with AI
                                        </div>
                                    </div>
                                    </Link>
                                </div>
                                <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12">
                                    <Link :href="route(
                                        'create'
                                    )
                                        " class="each-option">
                                    <div class="img-bg">
                                        <div class="img-cont">
                                            <img :src="'/assets/images/plan-campaign-img.png'" alt="" />
                                        </div>
                                    </div>
                                    <div>
                                        <div class="option-title">
                                            Plan A Post Campaign
                                        </div>
                                        <div class="sub-title">
                                            plan your post across all
                                            platforms
                                        </div>
                                    </div>
                                    </Link>
                                </div>
                                <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12">
                                    <Link :href="route('posts.create')" class="each-option">
                                    <div class="img-bg">
                                        <div class="img-cont">
                                            <img :src="'/assets/images/upload-files-img.png'" alt="" />
                                        </div>
                                    </div>
                                    <div>
                                        <div class="option-title">
                                            Uplaod Your Files
                                        </div>
                                        <div class="sub-title">
                                            Create a media library of files
                                        </div>
                                    </div>
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </UserLayout>
</template>

<script setup>
import { computed } from "vue";
import SvgIcon from "@components/SvgIcon.vue";
import UserLayout from "@Layouts/UserLayout.vue";

const props = defineProps({
    user: {
        type: Object,
        default: null,
    },
    channels: {
        type: Object,
        // required: true,
    },
});

const formattedUsername = computed(() => {
    if (!props.user?.email) return "";

    const username = props.user.email.split("@")[0];

    return username.charAt(0).toUpperCase() + username.slice(1);
});

const getPlatformLogo = (platform) => {
    const logos = {
        twitter: `
    <svg width="22" height="22" viewBox="0 0 24 24" fill="#000000">
    <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
    </svg>
    `,
        instagram: `
    <svg width="15" height="15" viewBox="0 0 24 24">
    <defs>
    <linearGradient id="instagramGradient" x1="0%" y1="0%" x2="100%" y2="100%">
    <stop offset="0%" stop-color="#FEDA75"/>
    <stop offset="25%" stop-color="#FA7E1E"/>
    <stop offset="50%" stop-color="#D62976"/>
    <stop offset="75%" stop-color="#962FBF"/>
    <stop offset="100%" stop-color="#4F5BD5"/>
    </linearGradient>
    </defs>
    <path d="M12.017 0C8.396 0 7.987.016 6.756.072 5.527.127 4.712.333 3.995.63c-.789.306-1.459.717-2.126 1.384S.935 3.35.63 4.139C.333 4.856.127 5.672.072 6.901.016 8.13 0 8.539 0 12.017c0 3.478.016 3.886.072 5.115.055 1.229.261 2.045.558 2.762.306.789.717 1.459 1.384 2.126.667.667 1.337 1.078 2.126 1.384.717.297 1.533.503 2.762.558 1.229.056 1.638.072 5.116.072 3.478 0 3.886-.016 5.115-.072 1.229-.055 2.045-.261 2.762-.558.789-.306 1.459-.717 2.126-1.384.667-.667 1.078-1.337 1.384-2.126.297-.717.503-1.533.558-2.762.056-1.229.072-1.638.072-5.116 0-3.478-.016-3.886-.072-5.115-.055-1.229-.261-2.045-.558-2.762-.306-.789-.717-1.459-1.384-2.126C21.319 1.459 20.649 1.048 19.86.742c-.717-.297-1.533-.503-2.762-.558C15.869.016 15.46 0 12.982 0H12.017zM12 5.838a6.162 6.162 0 110 12.324 6.162 6.162 0 010-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 110 2.881 1.44 1.44 0 010-2.881z" fill="url(#instagramGradient)"/>
    </svg>
    `,
        facebook: `
    <svg width="15" height="15" viewBox="0 0 24 24" fill="#1877F2">
    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
    </svg>
    `,
        linkedin: `
    <svg width="15" height="15" viewBox="0 0 24 24" fill="#0A66C2">
    <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
    </svg>
    `,
        youtube: `
    <svg width="15" height="15" viewBox="0 0 24 24" fill="#FF0000">
    <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
    </svg>
    `,
        tiktok: `
    <svg width="15" height="15" viewBox="0 0 24 24" fill="#000000">
    <path d="M19.589 6.686a4.793 4.793 0 0 1-3.77-4.245V2h-3.445v13.672a2.896 2.896 0 0 1-5.201 1.743l-.002-.001.002.001a2.895 2.895 0 0 1 3.183-4.51v-3.5a6.329 6.329 0 0 0-5.394 10.692 6.33 6.33 0 0 0 10.857-4.424V8.687a8.182 8.182 0 0 0 4.773 1.526V6.79a4.831 4.831 0 0 1-1.003-.104z"/>
    </svg>
    `,
        pinterest: `
    <svg width="15" height="15" viewBox="0 0 24 24" fill="#BD081C">
    <path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 5.079 3.158 9.417 7.618 11.174-.105-.949-.199-2.403.042-3.441.219-.937 1.407-5.965 1.407-5.965s-.359-.719-.359-1.782c0-1.668.967-2.914 2.171-2.914 1.023 0 1.518.769 1.518 1.69 0 1.029-.655 2.567-.992 3.992-.285 1.193.6 2.165 1.775 2.165 2.128 0 3.768-2.245 3.768-5.487 0-2.861-2.063-4.869-5.008-4.869-3.41 0-5.409 2.562-5.409 5.199 0 1.033.394 2.143.889 2.741.099.12.112.225.085.347-.09.375-.293 1.199-.334 1.363-.053.225-.172.271-.402.165-1.495-.69-2.433-2.878-2.433-4.646 0-3.776 2.748-7.252 7.92-7.252 4.158 0 7.392 2.967 7.392 6.923 0 4.135-2.607 7.462-6.233 7.462-1.214 0-2.357-.629-2.75-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146C9.57 23.812 10.763 24.009 12.017 24.009c6.624 0 11.99-5.367 11.99-11.988C24.007 5.367 18.641.001 12.017.001z"/>
    </svg>
    `,
        googlebusiness: `
    <svg width="15" height="15" viewBox="0 0 24 24" fill="#4285F4">
    <path d="M12 11.5h8.5c.3 0 .5.2.5.5v8.5c0 .3-.2.5-.5.5H12v-9zm0-1V2H3.5c-.3 0-.5.2-.5.5v8H12zm1 0h8.5c.3 0 .5-.2.5-.5V3.5c0-.3-.2-.5-.5-.5H13v8zM3 3h8v8H3V3zm0 9h8v8H3.5c-.3 0-.5-.2-.5-.5V12zm17 0v8h-8v-8h8z"/>
    </svg>
    `,
    };

    const key = platform.toLowerCase();
    const normalizedKey = key.replace('-', '');

    return (
        logos[key] ||
        logos[normalizedKey] ||
        `
    <svg width="15" height="15" viewBox="0 0 24 24" fill="#6B7280">
    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
    </svg>
    `
    );
};
</script>
<style lang="scss" scoped>
@use '@sass/app.scss' as *;
@use "@sass/mixins.scss" as mixin;
@use "@sass/variables.scss" as *;

/* @include archive-page-shell;
@include archive-page-top;
@include archive-page-panel; */

.archive-wrapper {
    display: flex;
    position: relative;

    .main-content {
        flex: 1;
        padding: 45px 23px;

        @media (max-width: 1200px) {
            padding: 32px 20px;
        }

        @media (max-width: 990px) {
            padding: 24px 16px;
        }

        @media (max-width: 576px) {
            padding: 18px 12px;
        }
    }
}

.top-cont {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 42px;
    flex-wrap: wrap;
    gap: 20px;

    @media (max-width: 768px) {
        margin-bottom: 28px;
    }

    @media (max-width: 576px) {
        align-items: flex-start;
        margin-bottom: 20px;
    }

    .img-cont {
        max-width: 191px;
        width: 100%;

        img {
            width: 100%;
            height: auto;
            display: block;
        }

        @media (max-width: 768px) {
            max-width: 150px;
        }
    }
}

.white-bg {
    background-color: #ffffff;
    padding: 22px 20px;
    border-radius: 3px;

    @media (max-width: 768px) {
        padding: 18px 16px;
    }

    @media (max-width: 576px) {
        padding: 14px 12px;
    }
}

.main-content {
    .sidebar-toggle {
        margin-bottom: 16px;
    }

    .top-cont {
        @media (max-width: 576px) {
            margin-left: 10px;
        }

        .welcome-txt {
            flex: 1 1 320px;

            .large-txt {
                font-size: 32px;
                font-weight: 900;

                @media (max-width: 750px) {
                    font-size: 24px;
                }

                @media (max-width: 576px) {
                    font-size: 20px;
                    line-height: 1.3;
                }
            }

            .small-txt {
                font-size: 20px;

                @media (max-width: 750px) {
                    display: none;
                }
            }

            @media (max-width: 600px) {
                display: flex;
                flex-direction: column;
                gap: 10px;
            }
        }

        .img-cont {
            max-width: 191px;
            width: 100%;

            img {
                width: 100%;
                height: auto;
                display: block;
            }

            @media (max-width: 768px) {
                max-width: 150px;
            }

            /*@media (max-width: 576px) {
                max-width: 120px;
            }*/
        }
    }

    .white-bg {
        .cont-top {
            display: flex;
            align-items: center;
            border-bottom: 1px solid #118a53;
            padding: 4px 0 24px;
            gap: 30px;
            flex-wrap: wrap;
            justify-content: space-between;

            @media (max-width: 768px) {
                padding-bottom: 18px;
                gap: 18px;
            }

            @media (max-width: 650px) {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            .outer-wrapper {
                overflow: hidden;
                flex: 1 1 0;
                width: 100%;
                max-width: 100%;
            }

            .add-channel-wrapper {
                gap: 10px;
                display: flex;
                overflow-x: auto;
                scrollbar-width: thin;
                padding-bottom: 6px;
                width: 100%;

                .each-channel {
                    gap: 6px;
                    display: flex;
                    font-size: 14px;
                    padding: 4px 8px;
                    border-radius: 4px;
                    align-items: center;
                    flex-shrink: 0;
                    white-space: nowrap;
                    // background-color: #118A53;

                    @media (max-width: 500px) {
                        font-size: 12px;
                    }

                    .icon-cont {
                        width: 25px;
                        height: 25px;
                        @include mixin.centered();
                        border-radius: 50%;
                        background-color: #d4f7e0;
                        // background-color: #118A53;
                    }
                }
            }

            .add-channel {
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 14px;
                // color: #C1B8B9;
                color: #000;
                gap: 8px;
                white-space: nowrap;

                .icon-cont {
                    padding: 2px 3px;
                    border-radius: 50%;
                    border: 1.4px solid #c1b8b9;
                    // border: 1.4px solid #C1B8B9;
                    border: 1.4px solid #000;

                    svg {
                        height: 10px;
                        display: block;
                    }
                }

                @media (max-width: 500px) {
                    font-size: 12px;
                }

                @media (max-width: 650px) {
                    width: 100%;
                    justify-content: flex-start;
                }
            }
        }

        .content {
            padding: 130px 148px;

            @media (max-width: 1300px) {
                padding: 80px;
            }

            @media (max-width: 850px) {
                padding: 50px;
            }

            @media (max-width: 750px) {
                padding: 40px;
            }

            @media (max-width: 650px) {
                padding: 20px;
            }

            @media (max-width: 576px) {
                padding: 20px 8px 8px;
            }

            .title {
                font-size: 31px;
                font-weight: 500;
                text-align: center;

                @media (max-width: 750px) {
                    font-size: 20px;
                }

                @media (max-width: 576px) {
                    font-size: 18px;
                    line-height: 1.4;
                }
            }

            .options {
                margin-top: 30px;

                @media (max-width: 576px) {
                    margin-top: 20px;
                }

                .each-option {
                    display: flex;
                    align-items: center;
                    border: 1px solid #000000;
                    color: #000000;
                    padding: 20px;
                    border-radius: 3px;
                    height: 100%;
                    gap: 20px;

                    @media (max-width: 600px) {
                        flex-direction: column;
                        text-align: center;
                        gap: 10px;
                        padding: 16px 14px;
                    }

                    .img-bg {
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        height: 50px;
                        width: 50px;
                        background-color: crimson;
                        border-radius: 3px;
                        padding: 7px;

                        @media (max-width: 500px) {
                            height: 40px;
                            width: 40px;
                        }

                        .img-cont {
                            max-width: 30px;

                            @media (max-width: 500px) {
                                max-width: 24px;
                            }
                        }
                    }

                    .option-title {
                        font-size: 14px;
                        font-weight: 500;
                        margin-bottom: 5px;
                        line-height: 1.4;

                        @media (max-width: 500px) {
                            font-size: 12px;
                        }
                    }

                    .sub-title {
                        font-size: 12px;
                        line-height: 1.5;

                        @media (max-width: 500px) {
                            font-size: 11px;
                        }
                    }
                }
            }
        }
    }
}
</style>
