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
                    <button
                        type="button"
                        data-theme-toggle
                        class="w-40-px h-40-px bg-neutral-200 rounded-circle d-flex justify-content-center align-items-center"
                    ></button>

                    <div ref="notificationDropdownRef" class="dropdown">
                        <button
                            class="has-indicator w-40-px h-40-px bg-neutral-200 rounded-circle d-flex justify-content-center align-items-center"
                            type="button"
                            @click="toggleNotificationDropdown"
                            :aria-expanded="isNotificationDropdownOpen.toString()"
                        >
                            <iconify-icon icon="iconoir:bell" class="text-primary-light text-xl"></iconify-icon>
                            <span
                                v-if="unreadCount > 0"
                                class="badge bg-danger rounded-circle position-absolute"
                                style="font-size: 10px; top: 4px; right: 4px;"
                            >
                                {{ unreadCount }}
                            </span>
                        </button>

                        <div
                            class="dropdown-menu dropdown-menu-end to-top dropdown-menu-lg p-0"
                            :class="{ show: isNotificationDropdownOpen }"
                        >
                            <div class="m-16 py-12 px-16 radius-8 bg-primary-50 mb-16 d-flex align-items-center justify-content-between gap-2">
                                <h6 class="text-lg text-primary-light fw-semibold mb-0">Notifications</h6>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="text-primary-600 fw-semibold text-lg w-40-px h-40-px rounded-circle bg-base d-flex justify-content-center align-items-center">
                                        {{ unreadCount }}
                                    </span>
                                    <button
                                        v-if="unreadCount > 0"
                                        type="button"
                                        @click="markAllAsRead"
                                        class="btn btn-sm btn-link text-primary-600 p-0"
                                        title="Tout marquer comme lu"
                                    >
                                        <iconify-icon icon="mingcute:check-2-fill"></iconify-icon>
                                    </button>
                                </div>
                            </div>

                            <div class="max-h-400-px overflow-y-auto scroll-sm pe-4">
                                <div v-if="notifications.length === 0" class="px-24 py-12 text-center text-secondary-light">
                                    Aucune notification
                                </div>

                                <button
                                    v-for="notif in notifications"
                                    :key="notif.id"
                                    type="button"
                                    class="notification-item px-24 py-12 d-flex align-items-start gap-3 mb-2 justify-content-between"
                                    :class="{ 'bg-neutral-50': !notif.read_at }"
                                    @click="openNotificationDetails(notif)"
                                >
                                    <div class="text-black hover-bg-transparent hover-text-primary d-flex align-items-center gap-3 text-start">
                                        <span class="w-44-px h-44-px bg-warning-subtle text-warning-main rounded-circle d-flex justify-content-center align-items-center flex-shrink-0">
                                            <iconify-icon icon="tabler:alert-hexagon-filled" class="icon text-xxl"></iconify-icon>
                                        </span>
                                        <div>
                                            <div class="d-flex align-items-center gap-2 mb-4">
                                                <h6 class="text-md fw-semibold mb-0">{{ notificationTitle(notif) }}</h6>
                                                <span v-if="!notif.read_at" class="badge bg-primary-600">Nouveau</span>
                                            </div>
                                            <p class="mb-0 text-sm text-secondary-light text-w-200-px">
                                                {{ notif.data?.message || 'Notification' }}
                                            </p>
                                        </div>
                                    </div>
                                    <span class="text-sm text-secondary-light flex-shrink-0">
                                        {{ timeAgo(notif.created_at) }}
                                    </span>
                                </button>
                            </div>

                            <div class="text-center py-12 px-16">
                                <a href="javascript:void(0)" class="text-primary-600 fw-semibold text-md">
                                    Voir toutes les notifications
                                </a>
                            </div>
                        </div>
                    </div>

                    <div ref="profileDropdownRef" class="dropdown">
                        <button
                            class="d-flex justify-content-center align-items-center rounded-circle"
                            type="button"
                            @click="toggleProfileDropdown"
                            :aria-expanded="isProfileDropdownOpen.toString()"
                        >
                            <img
                                src="assets/images/user.png"
                                alt="image"
                                class="w-40-px h-40-px object-fit-cover rounded-circle"
                            >
                        </button>
                        <div
                            class="dropdown-menu dropdown-menu-end to-top dropdown-menu-sm"
                            :class="{ show: isProfileDropdownOpen }"
                        >
                            <div class="py-12 px-16 radius-8 bg-primary-50 mb-16 d-flex align-items-center justify-content-between gap-2">
                                <div>
                                    <h6 class="text-lg text-primary-light fw-semibold mb-2">{{ currentUser.fullname }}</h6>
                                    <span class="text-secondary-light fw-medium text-sm text-capitalize">{{ currentUser.role }}</span>
                                </div>
                                <button type="button" class="hover-text-danger">
                                    <iconify-icon icon="radix-icons:cross-1" class="icon text-xl"></iconify-icon>
                                </button>
                            </div>
                            <ul class="to-top-list">

                                <li>
                                    <a
                                        class="dropdown-item text-black px-0 py-8 hover-bg-transparent hover-text-danger d-flex align-items-center gap-3 cursor-pointer"
                                        @click="LogoutFunction"
                                    >
                                        <iconify-icon icon="lucide:power" class="icon text-xl"></iconify-icon>
                                        Déconnexion
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;">
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

    <div class="modal fade" id="notificationDetailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-0" style="background:#fff7ed;">
                    <div class="d-flex align-items-center gap-3">
                        <span class="w-48-px h-48-px bg-warning-subtle text-warning-main rounded-circle d-flex justify-content-center align-items-center flex-shrink-0">
                            <iconify-icon icon="tabler:alert-hexagon-filled" class="icon text-xxl"></iconify-icon>
                        </span>
                        <div>
                            <h5 class="modal-title mb-1">
                                {{ selectedNotification ? notificationTitle(selectedNotification) : 'Detail notification' }}
                            </h5>
                            <p class="text-secondary-light mb-0 text-sm">
                                {{ selectedNotification ? formatNotificationDate(selectedNotification.created_at) : '' }}
                            </p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div v-if="selectedNotification" class="modal-body p-24">
                    <div class="alert border-0 mb-20" style="background:#fff7ed; color:#9a3412;">
                        {{ selectedNotification.data?.message || 'Notification' }}
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="border rounded-3 p-16 h-100">
                                <span class="text-secondary-light text-sm">Produit</span>
                                <h6 class="mb-0 mt-4">{{ selectedNotification.data?.product_nom || '-' }}</h6>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded-3 p-16 h-100">
                                <span class="text-secondary-light text-sm">Code barre</span>
                                <h6 class="mb-0 mt-4">{{ selectedNotification.data?.code_barre || '-' }}</h6>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded-3 p-16 h-100">
                                <span class="text-secondary-light text-sm">Stock actuel</span>
                                <h6 class="mb-0 mt-4 text-danger">{{ selectedNotification.data?.quantite ?? '-' }}</h6>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded-3 p-16 h-100">
                                <span class="text-secondary-light text-sm">Seuil d'alerte</span>
                                <h6 class="mb-0 mt-4">{{ selectedNotification.data?.seuil_alerte ?? '-' }}</h6>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="border rounded-3 p-16">
                                <span class="text-secondary-light text-sm">Statut</span>
                                <div class="mt-8">
                                    <span
                                        class="badge"
                                        :class="selectedNotification.read_at ? 'bg-success' : 'bg-primary-600'"
                                    >
                                        {{ selectedNotification.read_at ? 'Marquee comme lue' : 'Non lue' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { onBeforeUnmount, onMounted, ref } from 'vue';
import { getData, putData, postData } from '../../plugins/api';
import { isAuthenticated } from '../../router';

const notifications = ref([]);
const unreadCount = ref(0);
const toasts = ref([]);
const selectedNotification = ref(null);

const isNotificationDropdownOpen = ref(false);
const isProfileDropdownOpen = ref(false);
const notificationDropdownRef = ref(null);
const profileDropdownRef = ref(null);

let notificationRefreshInterval = null;
let notificationDetailModal = null;

const currentUser = ref({})

async function Userinfo() {
  currentUser.value = await isAuthenticated()
}

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

        const notif = notifications.value.find((item) => item.id === id);

        if (notif && !notif.read_at) {
            notif.read_at = new Date().toISOString();
            unreadCount.value = Math.max(0, unreadCount.value - 1);
        }
    } catch (error) {
        console.error('Erreur markAsRead:', error);
    }
};

const markAllAsRead = async () => {
    try {
        await putData('/notifications/read-all');
        notifications.value.forEach((notif) => {
            notif.read_at = new Date().toISOString();
        });
        unreadCount.value = 0;
    } catch (error) {
        console.error('Erreur markAllAsRead:', error);
    }
};

const openNotificationDetails = async (notif) => {
    selectedNotification.value = notif;

    if (!notif.read_at) {
        await markAsRead(notif.id);
    }

    isNotificationDropdownOpen.value = false;
    notificationDetailModal?.show();
};

const removeToast = (id) => {
    toasts.value = toasts.value.filter((toast) => toast.id !== id);
};

const timeAgo = (dateString) => {
    const diff = Math.floor((new Date() - new Date(dateString)) / 1000);

    if (diff < 60) return `Il y a ${diff}s`;
    if (diff < 3600) return `Il y a ${Math.floor(diff / 60)} min`;
    if (diff < 86400) return `Il y a ${Math.floor(diff / 3600)}h`;

    return `Il y a ${Math.floor(diff / 86400)}j`;
};

const formatNotificationDate = (dateString) => {
    if (!dateString) {
        return '';
    }

    return new Intl.DateTimeFormat('fr-FR', {
        dateStyle: 'full',
        timeStyle: 'short',
    }).format(new Date(dateString));
};

const notificationTitle = (notif) => {
    if (notif?.data?.product_nom) {
        return `Alerte stock - ${notif.data.product_nom}`;
    }

    return 'Notification';
};

async function LogoutFunction() {
  await postData('/logout',null).then(res=>{
    if (res.status === 200) {
      localStorage.removeItem('token')
      currentUser.value = {}
      window.location.href = '/login'
    }
  })
}

onMounted(() => {
    Userinfo()
    document.addEventListener('click', handleDocumentClick);
    notificationDetailModal = new bootstrap.Modal(document.getElementById('notificationDetailModal'));

    fetchNotifications();
    fetchUnreadCount();

    notificationRefreshInterval = setInterval(() => {
        fetchNotifications();
        fetchUnreadCount();
    }, 60000);
});

onBeforeUnmount(() => {
    document.removeEventListener('click', handleDocumentClick);

    if (notificationRefreshInterval) {
        clearInterval(notificationRefreshInterval);
    }
});
</script>

<style scoped>
.notification-item {
    width: 100%;
    border: 0;
    background: transparent;
    text-align: left;
}

.notification-item:hover {
    background: #f8fafc;
}
</style>
