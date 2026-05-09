<template>
    <aside class="sidebar">
        <button type="button" class="sidebar-close-btn">
            <iconify-icon icon="radix-icons:cross-2"></iconify-icon>
        </button>
        <div>
            <a href="index.html" class="sidebar-logo">
                <img src="assets/images/logo.png" alt="site logo" class="light-logo">
                <img src="assets/images/logo-light.png" alt="site logo" class="dark-logo">
                <img src="assets/images/logo-icon.png" alt="site logo" class="logo-icon">
            </a>
        </div>
        <div class="sidebar-menu-area">
            <ul class="sidebar-menu" id="sidebar-menu">
                <li>
                    <RouterLink to="/">
                        <iconify-icon icon="solar:home-smile-angle-outline" class="menu-icon"></iconify-icon>
                        <span>Tableau de bord</span>
                    </RouterLink>
                </li>
                <li>
                    <RouterLink to="/category">
                        <iconify-icon icon="mage:tag" class="menu-icon"></iconify-icon>
                        <span>Catégorie</span>
                    </RouterLink>
                </li>
                <li>
                    <RouterLink to="/product">
                        <iconify-icon icon="bi:box" class="menu-icon"></iconify-icon>
                        <span>Produits</span>
                    </RouterLink>
                </li>
                <li>
                    <RouterLink to="/mouvement">
                        <iconify-icon icon="tabler:arrows-exchange-2" class="menu-icon"></iconify-icon>
                        <span>Mouvement</span>
                    </RouterLink>
                </li>
                <li>
                    <RouterLink to="/vente">
                        <iconify-icon icon="tabler:shopping-cart" class="menu-icon"></iconify-icon>
                        <span>Vente</span>
                    </RouterLink>
                </li>
                <li>
                    <RouterLink to="/fournisseur">
                        <iconify-icon icon="tabler:truck-delivery" class="menu-icon"></iconify-icon>
                        <span>Fournisseur</span>
                    </RouterLink>
                </li>
                <li>
                    <RouterLink to="/approvisionnement">
                        <iconify-icon icon="tabler:package" class="menu-icon"></iconify-icon>
                        <span>Approvisionnement</span>
                    </RouterLink>
                </li>

            </ul>
        </div>
    </aside>
</template>

<script>
export default {

}
</script>

<style>

</style>
