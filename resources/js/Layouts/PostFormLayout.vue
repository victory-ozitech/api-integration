<script setup>
import UserLayout from "@/Layouts/UserLayout.vue";
</script>

<template>
    <UserLayout>
        <div class="create-wrap">

            <slot></slot>

        </div>
    </UserLayout>
</template>

<style lang="scss">
@use '@sass/app.scss' as *;
@use "@sass/mixins.scss" as mixin;
@use "@sass/variables.scss" as *;

.create-wrap {
    max-width: 720px;
    margin: 0 auto;
    padding: 30px 20px;
}

/* Header */
.page-header h2 {
    font-weight: 800;
    color: #0f172a;
    margin-bottom: 4px;
}

.page-header p {
    color: #64748b;
    margin-bottom: 24px;
}

/* Card */
.create-card {
    background: rgba(255, 255, 255, 0.75);
    backdrop-filter: blur(12px);

    border-radius: 28px;
    padding: 26px;

    box-shadow:
        0 20px 60px rgba(15, 23, 42, 0.06);

    border: 1px solid rgba(255, 255, 255, 0.6);
}


.form-group {
    margin-bottom: 16px;
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

input{
    box-shadow: none !important;
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

.form-check-input{
    width: 18px;
    height: 18px;
    border-radius: 4px;
    border: 1px solid #94a3b8;

    &:checked {
        background-color: #10b981;
        border-color: #10b981;
    }
}

/* Textarea */
.textarea {
    line-height: 1.6;
}

/* Input with icon */
.input-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}

.input-wrapper i {
    position: absolute;
    left: 14px;
    color: #94a3b8;
    font-size: 14px;
}

.input-wrapper input {
    padding-left: 38px;
}

/* Actions */
.actions {
    margin-top: 24px;
}

/* Button */
.btn-primary-custom {
    border: none;

    padding: 14px;

    border-radius: 16px;

    font-weight: 700;
    font-size: 14px;

    color: white;

    background: linear-gradient(135deg,
            #10b981,
            #34d399);

    box-shadow: 0 12px 30px rgba(16, 185, 129, 0.25);

    transition: all 0.25s ease;
}

.btn-primary-custom:hover {
    transform: translateY(-2px);
    box-shadow: 0 18px 40px rgba(16, 185, 129, 0.35);
}
</style>