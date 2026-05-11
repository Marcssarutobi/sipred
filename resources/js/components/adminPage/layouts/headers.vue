<template>

    <div class="navbar-header">
        <div class="row align-items-center justify-content-between">
            <div class="col-auto">
                <div class="d-flex flex-wrap align-items-center gap-4">
                    <button type="button" class="sidebar-toggle">
                        <iconify-icon icon="heroicons:bars-3-solid" class="icon text-2xl non-active"></iconify-icon>
                        <iconify-icon icon="iconoir:arrow-right" class="icon text-2xl active"></iconify-icon>
                    </button>
                    <button type="button" class="sidebar-mobile-toggle">
                        <iconify-icon icon="heroicons:bars-3-solid" class="icon"></iconify-icon>
                    </button>
                    <form class="navbar-search">
                        <input type="text" name="search" placeholder="Search">
                        <iconify-icon icon="ion:search-outline" class="icon"></iconify-icon>
                    </form>
                </div>
            </div>
            <div class="col-auto">
                <div class="d-flex flex-wrap align-items-center gap-3">
                    <button type="button" data-theme-toggle
                        class="w-40-px h-40-px bg-neutral-200 rounded-circle d-flex justify-content-center align-items-center"></button>




                        <div ref="notificationDropdownRef" class="dropdown">
                            <button
                                class="has-indicator w-40-px h-40-px bg-neutral-200 rounded-circle d-flex justify-content-center align-items-center"
                                type="button"
                                @click="toggleNotificationDropdown"
                                :aria-expanded="isNotificationDropdownOpen.toString()">
                                <iconify-icon icon="iconoir:bell" class="text-primary-light text-xl"></iconify-icon>
                                <!-- Badge compteur -->
                                <span v-if="unreadCount > 0"
                                    class="badge bg-danger rounded-circle position-absolute"
                                    style="font-size: 10px; top: 4px; right: 4px;">
                                    {{ unreadCount }}
                                </span>
                            </button>

                            <div
                                class="dropdown-menu dropdown-menu-end to-top dropdown-menu-lg p-0"
                                :class="{ show: isNotificationDropdownOpen }">

                                <!-- Header -->
                                <div class="m-16 py-12 px-16 radius-8 bg-primary-50 mb-16 d-flex align-items-center justify-content-between gap-2">
                                    <h6 class="text-lg text-primary-light fw-semibold mb-0">Notifications</h6>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="text-primary-600 fw-semibold text-lg w-40-px h-40-px rounded-circle bg-base d-flex justify-content-center align-items-center">
                                            {{ unreadCount }}
                                        </span>
                                        <!-- Tout marquer comme lu -->
                                        <button v-if="unreadCount > 0"
                                            @click="markAllAsRead"
                                            class="btn btn-sm btn-link text-primary-600 p-0"
                                            title="Tout marquer comme lu">
                                            <iconify-icon icon="mingcute:check-2-fill"></iconify-icon>
                                        </button>
                                    </div>
                                </div>

                                <!-- Liste -->
                                <div class="max-h-400-px overflow-y-auto scroll-sm pe-4">

                                    <!-- Aucune notification -->
                                    <div v-if="notifications.length === 0" class="px-24 py-12 text-center text-secondary-light">
                                        Aucune notification
                                    </div>

                                    <!-- Notification stock alerte -->
                                    <a v-for="notif in notifications" :key="notif.id"
                                        href="javascript:void(0)"
                                        @click="markAsRead(notif.id)"
                                        class="px-24 py-12 d-flex align-items-start gap-3 mb-2 justify-content-between"
                                        :class="{ 'bg-neutral-50': !notif.read_at }">
                                        <div class="text-black hover-bg-transparent hover-text-primary d-flex align-items-center gap-3">
                                            <span class="w-44-px h-44-px bg-warning-subtle text-warning-main rounded-circle d-flex justify-content-center align-items-center flex-shrink-0">
                                                <iconify-icon icon="tabler:alert-hexagon-filled" class="icon text-xxl"></iconify-icon>
                                            </span>
                                            <div>
                                                <h6 class="text-md fw-semibold mb-4">Stock critique</h6>
                                                <p class="mb-0 text-sm text-secondary-light text-w-200-px">
                                                    {{ notif.data.message }}
                                                </p>
                                            </div>
                                        </div>
                                        <span class="text-sm text-secondary-light flex-shrink-0">
                                            {{ timeAgo(notif.created_at) }}
                                        </span>
                                    </a>

                                </div>

                                <div class="text-center py-12 px-16">
                                    <a href="javascript:void(0)" class="text-primary-600 fw-semibold text-md">
                                        Voir toutes les notifications
                                    </a>
                                </div>
                            </div>
                        </div><!-- Notification dropdown end -->

                    <div ref="profileDropdownRef" class="dropdown">
                        <button class="d-flex justify-content-center align-items-center rounded-circle" type="button"
                            @click="toggleProfileDropdown"
                            :aria-expanded="isProfileDropdownOpen.toString()">
                            <img src="assets/images/user.png" alt="image"
                                class="w-40-px h-40-px object-fit-cover rounded-circle">
                        </button>
                        <div
                            class="dropdown-menu dropdown-menu-end to-top dropdown-menu-sm"
                            :class="{ show: isProfileDropdownOpen }">
                            <div
                                class="py-12 px-16 radius-8 bg-primary-50 mb-16 d-flex align-items-center justify-content-between gap-2">
                                <div>
                                    <h6 class="text-lg text-primary-light fw-semibold mb-2">Robiul Hasan</h6>
                                    <span class="text-secondary-light fw-medium text-sm">Admin</span>
                                </div>
                                <button type="button" class="hover-text-danger">
                                    <iconify-icon icon="radix-icons:cross-1" class="icon text-xl"></iconify-icon>
                                </button>
                            </div>
                            <ul class="to-top-list">
                                <li>
                                    <a class="dropdown-item text-black px-0 py-8 hover-bg-transparent hover-text-primary d-flex align-items-center gap-3"
                                        href="view-profile.html">
                                        <iconify-icon icon="solar:user-linear" class="icon text-xl"></iconify-icon> My
                                        Profile</a>
                                </li>
                                <li>
                                    <a class="dropdown-item text-black px-0 py-8 hover-bg-transparent hover-text-primary d-flex align-items-center gap-3"
                                        href="email.html">
                                        <iconify-icon icon="tabler:message-check" class="icon text-xl"></iconify-icon>
                                        Inbox</a>
                                </li>
                                <li>
                                    <a class="dropdown-item text-black px-0 py-8 hover-bg-transparent hover-text-primary d-flex align-items-center gap-3"
                                        href="company.html">
                                        <iconify-icon icon="icon-park-outline:setting-two"
                                            class="icon text-xl"></iconify-icon> Setting</a>
                                </li>
                                <li>
                                    <a class="dropdown-item text-black px-0 py-8 hover-bg-transparent hover-text-danger d-flex align-items-center gap-3"
                                        href="javascript:void(0)">
                                        <iconify-icon icon="lucide:power" class="icon text-xl"></iconify-icon> Log
                                        Out</a>
                                </li>
                            </ul>
                        </div>
                    </div><!-- Profile dropdown end -->
                </div>
            </div>
        </div>
    </div>

    <div
        class="toast-container position-fixed top-0 end-0 p-3"
        style="z-index: 9999;"
    >
        <div
            v-for="toast in toasts"
            :key="toast.id"
            class="toast show align-items-center text-bg-warning border-0 mb-2"
            role="alert"
        >
            <div class="d-flex">
                <div class="toast-body">
                    <strong>Stock critique</strong><br>
                    {{ toast.data.message }}
                </div>

                <button
                    type="button"
                    class="btn-close btn-close-white me-2 m-auto"
                    @click="removeToast(toast.id)"
                ></button>
            </div>
        </div>
    </div>

</template>

<script setup>
import { onBeforeUnmount, onMounted, ref } from 'vue';
import { getData, putData } from '../../plugins/api';

const notifications = ref([]);
const unreadCount = ref(0);

const isNotificationDropdownOpen = ref(false);
const isProfileDropdownOpen = ref(false);
const notificationDropdownRef = ref(null);
const profileDropdownRef = ref(null);

const toggleNotificationDropdown = () => {
    isNotificationDropdownOpen.value = !isNotificationDropdownOpen.value;
    if (isNotificationDropdownOpen.value) {
        isProfileDropdownOpen.value = false;
    }
};

const toggleProfileDropdown = () => {
    isProfileDropdownOpen.value = !isProfileDropdownOpen.value;
    if (isProfileDropdownOpen.value) {
        isNotificationDropdownOpen.value = false;
    }
};

const handleDocumentClick = (event) => {
    const target = event.target;

    if (
        notificationDropdownRef.value &&
        !notificationDropdownRef.value.contains(target)
    ) {
        isNotificationDropdownOpen.value = false;
    }

    if (
        profileDropdownRef.value &&
        !profileDropdownRef.value.contains(target)
    ) {
        isProfileDropdownOpen.value = false;
    }
};

const fetchNotifications = async () => {
    try {
        const response = await getData('/notifications');
        notifications.value = response.data;
    } catch (error) {
        console.error('Erreur chargement notifications:', error);
    }
};

const fetchUnreadCount = async () => {
    try {
        const response = await getData('/notifications/unread-count');
        unreadCount.value = response.data.count;
    } catch (error) {
        console.error('Erreur comptage notifications:', error);
    }
};

const markAsRead = async (id) => {
    try {
        await putData(`/notifications/${id}/read`);
        // Mettre à jour localement
        const notif = notifications.value.find(n => n.id === id);
        if (notif) notif.read_at = new Date().toISOString();
        unreadCount.value = Math.max(0, unreadCount.value - 1);
    } catch (error) {
        console.error('Erreur markAsRead:', error);
    }
};

const markAllAsRead = async () => {
    try {
        await putData('/notifications/read-all');
        notifications.value.forEach(n => n.read_at = new Date().toISOString());
        unreadCount.value = 0;
    } catch (error) {
        console.error('Erreur markAllAsRead:', error);
    }
};

// Formate la date (ex: "Il y a 2 min")
const timeAgo = (dateString) => {
    const diff = Math.floor((new Date() - new Date(dateString)) / 1000);
    if (diff < 60) return `Il y a ${diff}s`;
    if (diff < 3600) return `Il y a ${Math.floor(diff / 60)} min`;
    if (diff < 86400) return `Il y a ${Math.floor(diff / 3600)}h`;
    return `Il y a ${Math.floor(diff / 86400)}j`;
};

onMounted(() => {
    document.addEventListener('click', handleDocumentClick);
    fetchNotifications();
    fetchUnreadCount();

    // Rafraîchit toutes les minutes
    setInterval(() => {
        fetchNotifications();
        fetchUnreadCount();
    }, 60000);
});

onBeforeUnmount(() => {
    document.removeEventListener('click', handleDocumentClick);
});
</script>

<style>

</style>
