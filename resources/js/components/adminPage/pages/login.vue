<template>

<section class="auth bg-base d-flex flex-wrap">
    <div class="auth-left d-lg-block d-none">
        <div class="d-flex align-items-center flex-column h-100 justify-content-center">
            <img src="assets/images/auth/auth-img.png" alt="Image">
        </div>
    </div>
    <div class="auth-right py-32 px-24 d-flex flex-column justify-content-center">
        <div class="max-w-464-px mx-auto w-100">
            <div>
                <a href="index.html" class="mb-40 max-w-290-px">
                    <img src="assets/images/logo.png" alt="Image">
                </a>
                <h4 class="mb-12">Connectez-vous à votre compte</h4>
                <p class="mb-32 text-secondary-light text-lg">Bon retour parmi nous ! Veuillez saisir vos coordonnées</p>
            </div>
            <form @submit.prevent="LoginForm">
                <div class="icon-field mb-16">
                    <span class="icon top-50 translate-middle-y">
                        <iconify-icon icon="mage:email"></iconify-icon>
                    </span>
                    <input type="email" class="form-control h-56-px bg-neutral-50 radius-12" :class="isEmpty.email ? 'is-invalid border border-danger' : ''" v-model="dataLogin.email" placeholder="Adresse Email">
                    <div v-if="isEmpty.email" class="invalid-feedback">
                        {{ msgInput.email }}
                    </div>
                </div>
                <div class="position-relative mb-20">
                    <div class="icon-field">
                        <span class="icon top-50 translate-middle-y">
                            <iconify-icon icon="solar:lock-password-outline"></iconify-icon>
                        </span>
                        <input :type="showpwd ? 'text' : 'password'" :class="isEmpty.password ? 'is-invalid border border-danger' : ''" v-model="dataLogin.password" class="form-control h-56-px bg-neutral-50 radius-12" id="your-password" placeholder="Mot de passe">
                        <div v-if="isEmpty.password" class="invalid-feedback">
                            {{ msgInput.password }}
                        </div>
                    </div>
                    <span class=" ri-eye-line cursor-pointer position-absolute end-0 top-50 translate-middle-y me-16 text-secondary-light" @click="togglePwd"></span>
                </div>
                <div class="">
                    <div class="d-flex justify-content-between gap-2">
                        <div class="form-check style-check d-flex align-items-center">

                        </div>
                        <a href="javascript:void(0)" class="text-primary-600 fw-medium">Mot de passe oublié ?</a>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary text-sm btn-sm px-12 py-16 w-100 radius-12 mt-32"> Se connecter</button>

            </form>
        </div>
    </div>
</section>

</template>

<script setup>

    import { onMounted,ref } from 'vue';
    import { useRouter } from 'vue-router';
    import {postData} from '../../plugins/api'

    const router = useRouter();
    const dataLogin = ref({
        email:'',
        password:''
    })

    const isEmpty = ref({})
    const msgInput = ref({})
    const isLoader = ref(false)
    const showpwd = ref(false)

   async function LoginForm() {

        for (const field in dataLogin.value) {
            isEmpty.value[field] = !dataLogin.value[field];
            msgInput.value[field] = 'This field is empty';
        }

        const allEmpty = Object.values(isEmpty.value).every(value => value === false)

        if (allEmpty) {
            isLoader.value = true
            await postData('/login', dataLogin.value).then(res => {
                if (res.status === 200) {
                    isLoader.value = false
                    localStorage.setItem('token', res.data.token)
                    // Rediriger vers la route sauvegardée ou par défaut
                    const redirectUrl = localStorage.getItem('redirectAfterLogin');
                    if (redirectUrl) {
                        // Forcer une redirection complète du navigateur
                        window.location.href = redirectUrl;
                        localStorage.removeItem('redirectAfterLogin');
                    } else {
                        //router.push('/');
                        window.location.href = "/admins"
                    }
                }
            }).catch(error => {
                if (error.response) {
                    if (error.response.status === 401) {
                        isLoader.value = false
                        isEmpty.value.email = true
                        isEmpty.value.password = true
                        msgInput.value.email = error.response.data.message
                        msgInput.value.password = error.response.data.message
                    } else {
                        console.error("Erreur du serveur :", error.response.data.message || "Veuillez réessayer plus tard.");
                    }
                }
            })
        }

    }

    const togglePwd = () => {
        showpwd.value = !showpwd.value
    }


</script>

<style>

</style>
