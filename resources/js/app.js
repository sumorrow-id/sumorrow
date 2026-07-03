import './bootstrap';
import { ExploreSearch } from './features/ExploreSearch';
import { FilterDrawer } from './features/FilterDrawer';
import { FlashBanner } from './features/FlashBanner';
import { PasswordVisibilityToggle } from './features/PasswordVisibilityToggle';
import { EmailValidityMessage } from './features/EmailValidityMessage';
import { HamburgerMenu } from './features/HamburgerMenu';
import { AvatarBioPreview } from './features/AvatarBioPreview';
import { PostImageUploadValidator } from './features/PostImageUploadValidator';
import { ProfileTabs } from './features/ProfileTabs';
import { CommentModal } from './features/CommentModal';
import { FollowToggle } from './features/FollowToggle';
import { CommunityForum } from './features/CommunityForum';
import { GearModal } from './features/GearModal';
import { EmailVerificationPoll } from './features/EmailVerificationPoll';
import { MountainWeatherForecast } from './features/MountainWeatherForecast';
import { FeedLike } from './features/FeedLike';
import { HomeWeatherHero } from './features/HomeWeatherHero';
import { FeedComposer } from './features/FeedComposer';
import { ConfirmSubmit } from './features/ConfirmSubmit';

document.addEventListener('DOMContentLoaded', () => {
    const exploreForm = document.getElementById('explore-form');
    const searchInput = document.getElementById('search-input');

    if (exploreForm && searchInput) {
        new ExploreSearch(exploreForm, searchInput);
    }

    // Explore page: mobile filter drawer
    const filterBtn = document.getElementById('mobile-filter-btn');
    const closeFilterBtn = document.getElementById('close-filter-btn');
    const filterDrawer = document.getElementById('filter-drawer');
    const filterBackdrop = document.getElementById('filter-backdrop');

    if (filterDrawer && filterBackdrop && exploreForm) {
        new FilterDrawer(filterBtn, closeFilterBtn, filterDrawer, filterBackdrop, exploreForm);
    }

    const flashWarning = document.getElementById('flash-warning');

    if (flashWarning) {
        new FlashBanner(flashWarning);
    }

    const flashSuccess = document.getElementById('flash-success');

    if (flashSuccess) {
        new FlashBanner(flashSuccess);
    }

    // Login page password toggle, and reset-password page's new-password toggle
    // (both pages share the #toggleBtn / #password / #eyeIcon ids)
    const loginToggleBtn = document.getElementById('toggleBtn');
    const loginPasswordInput = document.getElementById('password');
    const loginEyeIcon = document.getElementById('eyeIcon');

    if (loginToggleBtn && loginPasswordInput && loginEyeIcon) {
        new PasswordVisibilityToggle(loginToggleBtn, loginPasswordInput, loginEyeIcon);
    }

    // Register page: password toggle
    const regToggleBtn = document.getElementById('regToggleBtn');
    const regPasswordInput = document.getElementById('regPassword');
    const regEyeIcon = document.getElementById('regEyeIcon');

    if (regToggleBtn && regPasswordInput && regEyeIcon) {
        new PasswordVisibilityToggle(regToggleBtn, regPasswordInput, regEyeIcon);
    }

    // Register page: confirm password toggle
    const confirmToggleBtn = document.getElementById('confirmToggleBtn');
    const confirmPasswordInput = document.getElementById('confirmPassword');
    const confirmEyeIcon = document.getElementById('confirmEyeIcon');

    if (confirmToggleBtn && confirmPasswordInput && confirmEyeIcon) {
        new PasswordVisibilityToggle(confirmToggleBtn, confirmPasswordInput, confirmEyeIcon);
    }

    // Reset-password page: confirm password toggle (shares #toggleBtn/#password ids with login)
    const toggleBtnConfirm = document.getElementById('toggleBtnConfirm');
    const passwordConfirmationInput = document.getElementById('password_confirmation');
    const eyeIconConfirm = document.getElementById('eyeIconConfirm');

    if (toggleBtnConfirm && passwordConfirmationInput && eyeIconConfirm) {
        new PasswordVisibilityToggle(toggleBtnConfirm, passwordConfirmationInput, eyeIconConfirm);
    }

    // Any page with an email input validates it generically
    document.querySelectorAll('input[type="email"]').forEach((emailInput) => {
        new EmailValidityMessage(emailInput);
    });

    // Dark navbar hamburger menu
    const hamburgerBtn = document.getElementById('hamburger-btn');
    const mobileMenu = document.getElementById('mobile-menu');
    const hamburgerIcon = document.getElementById('hamburger-icon');

    if (hamburgerBtn && mobileMenu && hamburgerIcon) {
        new HamburgerMenu(hamburgerBtn, mobileMenu, hamburgerIcon);
    }

    // Light navbar hamburger menu
    const hamburgerBtnLight = document.getElementById('hamburger-btn-light');
    const mobileMenuLight = document.getElementById('mobile-menu-light');
    const hamburgerIconLight = document.getElementById('hamburger-icon-light');

    if (hamburgerBtnLight && mobileMenuLight && hamburgerIconLight) {
        new HamburgerMenu(hamburgerBtnLight, mobileMenuLight, hamburgerIconLight);
    }

    // Profile edit page: avatar/cover preview + bio counter
    const avatarUpload = document.getElementById('avatar-upload');
    const avatarPreview = document.getElementById('avatar-preview');
    const coverUpload = document.getElementById('cover-upload');
    const coverPreview = document.getElementById('cover-preview');
    const bio = document.getElementById('bio');
    const bioCounter = document.getElementById('bio-counter');

    if (avatarUpload || coverUpload || bio) {
        new AvatarBioPreview(avatarUpload, avatarPreview, coverUpload, coverPreview, bio, bioCounter);
    }

    // Create post page: image upload validation
    const imagesInput = document.getElementById('images');
    const imageError = document.getElementById('image-error');
    const submitBtn = document.getElementById('submit-btn');

    if (imagesInput && imageError && submitBtn && imagesInput.form) {
        new PostImageUploadValidator(imagesInput, imageError, submitBtn, imagesInput.form);
    }

    // Profile page: tab switching
    const tabButtons = document.querySelectorAll('.tab-btn');

    if (tabButtons.length) {
        new ProfileTabs(tabButtons);
    }

    // Community page: reply/comment modal
    const commentModal = document.getElementById('comment-modal');
    const commentModalContent = document.getElementById('comment-modal-content');
    const commentModalCloseTriggers = document.querySelectorAll('.comment-modal-close');

    if (commentModal && commentModalContent) {
        new CommentModal(commentModal, commentModalContent, commentModalCloseTriggers);
    }

    // Community sidebar: follow/unfollow buttons
    const followButtons = document.querySelectorAll('.follow-btn');

    if (followButtons.length) {
        new FollowToggle(followButtons);
    }

    // Community page: forum tabs, create-community modal, mobile sidebar drawer
    const forumTabTriggers = document.querySelectorAll('[data-forum-tab]');

    if (forumTabTriggers.length) {
        const createCommunityModal = document.getElementById('modal-create-community');
        const createCommunityOpenTriggers = document.querySelectorAll('.open-create-community-modal');
        const createCommunityCloseTriggers = document.querySelectorAll('.close-create-community-modal');
        const mobileSidebarOverlay = document.getElementById('mobile-sidebar-overlay');
        const mobileSidebarDrawer = document.getElementById('mobile-sidebar-drawer');
        const mobileSidebarToggleTriggers = document.querySelectorAll('.mobile-sidebar-toggle');

        new CommunityForum(
            forumTabTriggers,
            createCommunityModal,
            createCommunityOpenTriggers,
            createCommunityCloseTriggers,
            mobileSidebarOverlay,
            mobileSidebarDrawer,
            mobileSidebarToggleTriggers
        );
    }

    // Profile page: gear modal (add/edit/filter)
    const gearModal = document.getElementById('gear-modal');
    const gearForm = document.getElementById('gear-form');
    const gearModalOpenTriggers = document.querySelectorAll('.open-gear-modal-btn');
    const gearModalCloseTriggers = document.querySelectorAll('.close-gear-modal-btn');

    if (gearModal && gearForm) {
        new GearModal(gearModal, gearForm, gearModalOpenTriggers, gearModalCloseTriggers);
    }

    // Verify-email page: poll for verification status
    const emailVerificationEl = document.querySelector('[data-redirect-url]');

    if (emailVerificationEl) {
        new EmailVerificationPoll(emailVerificationEl);
    }

    // Mountain detail page: weather forecast, hero card, scroll handlers, back-to-top
    const weatherContainerEl = document.getElementById('weather-container');

    if (weatherContainerEl) {
        new MountainWeatherForecast(weatherContainerEl);
    }

    // Community feed: like buttons (async)
    if (document.querySelector('.like-btn')) {
        new FeedLike();
    }

    // Home page: weather widget cycling, hero image crossfade, elevation slider, mountain carousel
    const weatherWidgetEl = document.getElementById('weather-widget');

    if (weatherWidgetEl) {
        new HomeWeatherHero(weatherWidgetEl);
    }

    // Community feed: post composer (image/GIF/emoji picker)
    const postComposerForm = document.getElementById('post-composer-form');

    if (postComposerForm) {
        new FeedComposer(postComposerForm);
    }

    // Confirm-before-submit modal (logout forms, profile edit save, ...)
    const confirmModal = document.getElementById('confirm-submit-modal');
    const confirmForms = document.querySelectorAll('form.confirm-submit-form');

    if (confirmModal && confirmForms.length) {
        new ConfirmSubmit(
            confirmModal,
            document.getElementById('confirm-submit-title'),
            document.getElementById('confirm-submit-message'),
            document.getElementById('confirm-submit-cancel'),
            document.getElementById('confirm-submit-yes'),
            document.getElementById('confirm-submit-backdrop'),
            confirmForms
        );
    }
});
