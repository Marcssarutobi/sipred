<template>
    <aside class="sidebar">
        <button type="button" class="sidebar-close-btn">
            <iconify-icon icon="radix-icons:cross-2"></iconify-icon>
        </button>
        <div>
            <a href="#" class="sidebar-logo">
                <img src="assets/images/logo.png" alt="site logo" class="light-logo">
                <img src="assets/images/logo-light.png" alt="site logo" class="dark-logo">
                <img src="assets/images/logo-icon.png" alt="site logo" class="logo-icon">
            </a>
        </div>
        <div class="sidebar-menu-area">
            <ul class="sidebar-menu" id="sidebar-menu">
                <li v-for="item in filteredMenu" :key="item.to">
                    <RouterLink :to="item.to">
                    <iconify-icon :icon="item.icon" class="menu-icon"></iconify-icon>
                    <span>{{ item.label }}</span>
                    </RouterLink>
                </li>
            </ul>
        </div>
    </aside>
</template>

<script setup>

import { computed, onMounted, ref } from 'vue';
import { isAuthenticated } from '../../router';

const currentUser = ref({})

async function Userinfo() {
  currentUser.value = await isAuthenticated()
}

onMounted(() => {
  Userinfo()
})

// Définition des accès par rôle
const menuItems = [
  {
    to: '/',
    icon: 'solar:home-smile-angle-outline',
    label: 'Tableau de bord',
    roles: ['admin', 'gerant', 'magasinier']  // tous
  },
  {
    to: '/category',
    icon: 'mage:tag',
    label: 'Catégorie',
    roles: ['admin', 'gerant']
  },
  {
    to: '/product',
    icon: 'bi:box',
    label: 'Produits',
    roles: ['admin', 'gerant', 'magasinier']
  },
  {
    to: '/mouvement',
    icon: 'tabler:arrows-exchange-2',
    label: 'Mouvement',
    roles: ['admin', 'magasinier']
  },
  {
    to: '/vente',
    icon: 'tabler:shopping-cart',
    label: 'Vente',
    roles: ['admin', 'gerant', 'magasinier']
  },
  {
    to: '/fournisseur',
    icon: 'tabler:truck-delivery',
    label: 'Fournisseur',
    roles: ['admin', 'gerant']
  },
  {
    to: '/approvisionnement',
    icon: 'tabler:package',
    label: 'Approvisionnement',
    roles: ['admin', 'gerant', 'magasinier']
  },
]

// Filtrer selon le rôle de l'utilisateur connecté
const filteredMenu = computed(() => {
  const role = currentUser.value?.role
  if (!role) return []
  return menuItems.filter(item => item.roles.includes(role))
})

</script>

<style>

</style>
