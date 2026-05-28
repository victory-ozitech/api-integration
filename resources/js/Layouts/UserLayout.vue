<template>

    <div class="flex-cont">
        <aside :class="{ expanded: isExpanded }">
            <div class="logo-cont" @click="toggleExpand">
                <div class="each-logo logo-state logo-state--compact" :class="{ hidden: isExpanded }"
                    :aria-hidden="isExpanded">
                    <img :src="'/assets/images/arkive_icon.png'" alt="ReliaCRM" />
                    <SvgIcon name="doubleArrowRight"></SvgIcon>
                </div>
                <div class="each-logo logo-state logo-state--full" :class="{ 'is-visible': isExpanded }"
                    :aria-hidden="!isExpanded">
                    <div class="full-logo">
                        <img :src="'/assets/images/arkive-logo.png'" alt="ReliaCRM" />
                    </div>
                    <SvgIcon name="doubleArrowLeft"></SvgIcon>
                </div>
            </div>
            <ul>
                <li :class="{
                    active: isActiveRoute('dashboard'),
                }">
                    <SvgIcon name="dashboard" @click="toggleExpand" />
                    <Link :href="route('dashboard')">
                    <span v-show="isExpanded"> Dashboard </span>
                    </Link>
                </li>

                <li :class="{
                    active: isActiveRoute('facebook.connect'),
                }">
                    <SvgIcon name="channels" @click="toggleExpand" />
                    <Link :href="route('facebook.connect')">
                    <span v-show="isExpanded">Channels</span>
                    </Link>
                </li>

                <li :class="{
                    active: isActiveRoute('posts.index'),
                }">
                    <SvgIcon name="calendar" @click="toggleExpand" />
                    <Link :href="route('posts.index')">
                    <span v-show="isExpanded">Calendar</span>
                    </Link>
                </li>

                <li :class="{
                    active: isActiveRoute('posts.create'),
                }">
                    <SvgIcon name="create" @click="toggleExpand" />
                    <Link :href="route('posts.create')">
                    <span v-show="isExpanded">Create</span>
                    </Link>
                </li>


                <li class="logout-btn" @click="logout">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    <span v-show="isExpanded">Logout</span>
                </li>
            </ul>
        </aside>
        <div class="wrapper-cont">
            <header>
                <form action="" :class="{ open: openSearch }">
                    <input type="text" class="form-control" placeholder="Search" />
                    <!-- <button class="btn btn-primary"> -->
                    <button class="btn btn-secondary">
                        <i class="fa fa-search"></i>
                    </button>
                </form>
                <div class="right-cont">
                    <div id="media-screen-search" @click="toggleSearch">
                        <i class="fa fa-search"></i>
                    </div>
                    <Link :href="route('facebook.connect')" class="header-action" title="Channels">
                    <SvgIcon name="channels"></SvgIcon>
                    </Link>
                    <Link :href="route('posts.create')" class="header-action" title="Create Post">
                    <SvgIcon name="create"></SvgIcon>
                    </Link>
                    <Link :href="route('posts.create')" class="header-action" title="Settings">
                        <SvgIcon name="settings"></SvgIcon>
                    </Link>
                    <div class="profile-img">
                        <img :src="'/assets/images/profile-img.png'" alt="ReliaCRM" />
                    </div>
                    
                    <button type="button" class="header-action header-action--button" title="Help">
                        <SvgIcon name="question"></SvgIcon>
                    </button>
                </div>
            </header>
            <div class="inner-cont">
                <slot></slot>
            </div>
        </div>
    </div>
</template>


<script setup>
import { ref } from "vue";
import SvgIcon from "@/Components/SvgIcon.vue";

// Props
const props = defineProps({
    client_id: {
        type: [Number, String],
        default: 1,
    },
});

// State
const isExpanded = ref(false);
const openSearch = ref(false);
const isTreeview = ref(false);
const openTree = ref(null);

// Methods
const toggleExpand = () => {
    isExpanded.value = !isExpanded.value;
};

const toggleTree = (name) => {
    openTree.value = openTree.value === name ? null : name;
};

const toggleSearch = () => {
    openSearch.value = !openSearch.value;
};

const isActiveRoute = (path) => {
    return window.location.pathname.startsWith(path);
    // or use usePage() from @inertiajs/vue3 if preferred
};

const logout = () => {
    router.post(route("create"));
    // router.post(route("logout"));
};
</script>

<style lang="scss" scoped>
@use '@sass/app.scss' as *;
@use "@sass/mixins.scss" as mixin;
@use "@sass/variables.scss" as *;

.active {
    background-color: $secondary-color;
    border-radius: 5px;
    // ul{
    //     display: block !important;
    // }
}

.logout-btn {
    color: #000;
    border: none;
    border-radius: 30px;
    // padding: 10px 22px;
    font-weight: 600;
    letter-spacing: 0.5px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.logout-btn:hover {
    background-color: #e63946;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(255, 77, 79, 0.3);
}

.logout-btn:active {
    transform: translateY(0);
    box-shadow: none;
}

.activeChild {
    color: $primary-color;
}

.flex-cont {
    display: flex;
    min-height: 100vh;

    aside {
        background-color: $primary-color;
        width: 105px;
        display: flex;
        flex-direction: column;
        padding: 15px 10px 60px;
        height: 100vh;
        overflow-y: auto;
        overflow-x: hidden;
        scrollbar-width: thin;
        scrollbar-color: #000000 transparent;
        transition: width 0.3s;
        position: fixed;
        left: 0;
        top: 0;
        bottom: 0;
        z-index: 100;

        &::-webkit-scrollbar {
            width: 6px;
        }

        &::-webkit-scrollbar-track {
            background: transparent;
        }

        &::-webkit-scrollbar-thumb {
            background-color: rgba(17, 138, 83, 0.55);
            border-radius: 999px;
        }

        // @media(max-width: 650px) {
        //     width: 70px;
        // }

        @media (max-width: 500px) {
            width: 100%;
            height: 78px;
            padding: 15px 12px;
            overflow: hidden;
        }

        ul {
            padding: 0;
            margin: 0;
            list-style: none;

            li {
                margin-bottom: 25px;
                display: flex;
                gap: 20px;
                align-items: center;
                padding: 10px 20px;

                @media (max-width: 650px) {
                    padding: 10px;
                    gap: 10px;
                }

                &:hover {
                    background-color: $secondary-color;
                    border-radius: 5px;
                }

                :deep(.svg-icon svg) {
                    width: 22px;
                    height: 22px;
                }

                a {
                    text-decoration: none;
                    font-size: 18px;
                    font-weight: 500;
                    color: #000;
                    display: flex;
                    flex: 1;
                }
            }
        }

        .logo-cont {
            margin-bottom: 37px;
            min-height: 56px;
            position: relative;
            display: flex;
            align-items: center;
            cursor: pointer;

            @media (max-width: 500px) {
                margin-bottom: 20px;
                min-height: 48px;
            }

            .each-logo {
                display: flex;
                gap: 10px;
                justify-content: space-between;
                align-items: center;
                width: 100%;
                min-height: 56px;
                padding: 10px 8px;
                border-radius: 12px;
                transition: opacity 0.3s ease, transform 0.3s ease,
                    max-width 0.3s ease;

                i {
                    font-size: 22px;
                    color: #ffffff;
                }

                img {
                    display: block;
                    width: 100%;
                    height: auto;
                }
            }

            .logo-state {
                position: absolute;
                inset: 0;
            }

            .logo-state--compact {
                max-width: 70px;
                opacity: 1;
                transform: translateX(0);

                img {
                    max-width: 32px;
                }
            }

            .logo-state--compact.hidden {
                opacity: 0;
                transform: translateX(-12px);
                pointer-events: none;
            }

            .logo-state--full {
                opacity: 0;
                transform: translateX(12px);
                pointer-events: none;
                padding-right: 4px;
            }

            .logo-state--full.is-visible {
                opacity: 1;
                transform: translateX(0);
                pointer-events: auto;
            }

            .full-logo {
                max-width: 132px;

                @media (max-width: 500px) {
                    max-width: 112px;
                }
            }

            :deep(.svg-icon) {
                flex-shrink: 0;
            }

            @media (max-width: 500px) {
                .each-logo {
                    min-height: 48px;
                    padding: 8px 4px;
                }

                .logo-state--compact {
                    max-width: 64px;

                    img {
                        max-width: 28px;
                    }
                }
            }
        }

        .treeview ul {
            display: none;
        }

        .treeview.open ul {
            display: block;
            list-style: disc;
            gap: 7px;
            width: 100%;

            &::marker {
                color: $primary-color;
            }
        }

        .treeview {
            flex-direction: column;
            align-items: start !important;

            li {
                margin-bottom: 5px;
                padding: 8px;
            }

            a {
                display: flex;
                gap: 20px;
                align-items: center;
                width: 100%;
            }
        }
    }

    aside.expanded {
        width: 250px;
        height: 100vh;
        overflow-y: auto;
        position: fixed;

        @media (max-width: 500px) {
            height: auto;
            min-height: 78px;
            max-height: 100vh;
        }
    }

    .wrapper-cont {
        display: flex;
        flex-direction: column;
        flex: 1;
        margin-left: 105px;
        min-width: 0;
        min-height: 100vh;
        transition: margin-left 0.3s ease;

        @media (max-width: 992px) {
            margin-left: 105px;
        }

        @media (max-width: 500px) {
            margin-left: 0;
            padding-top: 78px;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 25px;
            height: 78px;
            padding: 0 60px;
            // background-color: $secondary-color;
            background-color: $primary-color;
            position: relative;

            @media (max-width: 996px) {
                padding: 0 24px;
            }

            @media (max-width: 768px) {
                gap: 16px;
                padding: 0 16px;
            }

            @media (max-width: 650px) {
                justify-content: flex-end;
            }

            @media (max-width: 500px) {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                z-index: 90;
                padding: 0 12px;
            }

            form {
                display: flex;
                height: 37px;
                border-radius: 5px;
                max-width: 400px;
                width: 100%;

                @media (max-width: 650px) {
                    display: none;
                    position: absolute;
                    top: 78px;
                    left: 0;
                    right: 0;
                    max-width: 100%;
                    transition: width 0.3s;
                    height: 45px;
                    border-bottom: 1px solid $secondary-color;

                    input {
                        background-color: #ffffff !important;
                    }

                    input,
                    button {
                        border-radius: 0;
                    }
                }

                input {
                    // background-color: #f2e3e4;
                    border-top-right-radius: 0;
                    border-bottom-right-radius: 0;
                    border: 1px solid $secondary-green;
                }

                .btn-secondary {
                    border-top-left-radius: 0;
                    border-bottom-left-radius: 0;
                    max-width: fit-content;
                    color: #fff !important;
                }
            }

            form.open {
                display: flex;
            }

            .right-cont {
                display: flex;
                gap: 27px;
                align-items: center;

                .header-action {
                    color: #000;
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;
                    text-decoration: none;
                    transition: transform 0.2s ease, opacity 0.2s ease;

                    &:hover {
                        opacity: 0.8;
                        transform: translateY(-1px);
                    }
                }

                .header-action--button {
                    background: transparent;
                    border: none;
                    padding: 0;
                }

                @media (max-width: 768px) {
                    gap: 16px;
                }

                @media (max-width: 500px) {
                    width: 100%;
                    justify-content: flex-end;
                    gap: 12px;
                }
            }

            #media-screen-search {
                @media (min-width: 650px) {
                    display: none;
                    cursor: pointer;
                }
            }
        }

        .inner-cont {
            // background-color: #fafbff;
            background-color: #EDFCF3;
            min-height: calc(100vh - 78px);

            @media (max-width: 500px) {
                min-height: calc(100vh - 156px);
            }
        }

        .white-bg {
            background-color: #ffffff;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
        }
    }

    .profile-img {
        max-width: 30px;
        border-radius: 50%;
        overflow: hidden;
    }
}
</style>
