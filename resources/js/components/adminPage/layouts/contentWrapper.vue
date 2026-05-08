<template>
    <Sidebars />
    <main class="dashboard-main">
        <Headers />
        <RouterView />
    </main>
</template>

<script setup>

    import Headers from './headers.vue';
    import Sidebars from './sidebars.vue';

</script>

<style>

</style>