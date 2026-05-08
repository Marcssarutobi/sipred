<template>
    <div class="dashboard-main-body">

        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
            <h6 class="fw-semibold mb-0">Liste des Mouvements</h6>
            <ul class="d-flex align-items-center gap-2">
                <li class="fw-medium">
                    <RouterLink to="/" class="d-flex align-items-center gap-1 hover-text-primary">
                        <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                        Tableau de bord
                    </RouterLink>
                </li>
                <li>-</li>
                <li class="fw-medium">Liste des Mouvements</li>
            </ul>
        </div>

        <!-- Cards résumé -->
        <div class="row g-3 mb-24">
            <div class="col-lg-4 col-sm-6">
                <div class="card border-0 shadow-sm" style="border-left: 4px solid #28a745 !important;">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                            style="width:48px;height:48px;background:#d4edda;">
                            <iconify-icon icon="solar:arrow-down-bold" style="color:#28a745;font-size:22px;"></iconify-icon>
                        </div>
                        <div>
                            <p class="mb-0 text-muted small">Total Entrées</p>
                            <h5 class="fw-bold mb-0" style="color:#28a745;">{{ stats.entrees }}</h5>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-sm-6">
                <div class="card border-0 shadow-sm" style="border-left: 4px solid #dc3545 !important;">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                            style="width:48px;height:48px;background:#f8d7da;">
                            <iconify-icon icon="solar:arrow-up-bold" style="color:#dc3545;font-size:22px;"></iconify-icon>
                        </div>
                        <div>
                            <p class="mb-0 text-muted small">Total Sorties</p>
                            <h5 class="fw-bold mb-0" style="color:#dc3545;">{{ stats.sorties }}</h5>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-sm-6">
                <div class="card border-0 shadow-sm" style="border-left: 4px solid #0d6efd !important;">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                            style="width:48px;height:48px;background:#cfe2ff;">
                            <iconify-icon icon="solar:list-bold" style="color:#0d6efd;font-size:22px;"></iconify-icon>
                        </div>
                        <div>
                            <p class="mb-0 text-muted small">Total Mouvements</p>
                            <h5 class="fw-bold mb-0" style="color:#0d6efd;">{{ stats.total }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="d-flex gap-2">
                    <!-- Filtre type -->
                    <select v-model="filterType" class="form-select form-select-sm" style="width:auto;">
                        <option value="">Tous les types</option>
                        <option value="entree">Entrées</option>
                        <option value="sortie">Sorties</option>
                    </select>
                </div>
                <button @click="showModal" class="btn btn-sm btn-primary-600">
                    <i class="ri-add-line"></i> Ajouter un Mouvement
                </button>
            </div>
            <div class="card-body">
                <DataTable :data="filteredMouvements" :columns="columns" />
            </div>
        </div>

        <!-- ─── Modal Ajout ─────────────────────────────────────────────── -->
        <div class="modal fade" id="mouvementModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <form class="modal-content" @submit.prevent="AddMouvementFunction()">
                    <div class="modal-header" style="background:#002D5D;">
                        <h1 class="modal-title fs-5 text-white">
                            <i class="ri-arrow-left-right-line me-2"></i>Nouveau Mouvement
                        </h1>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">

                            <!-- Produit -->
                            <div class="col-12">
                                <label class="form-label fw-medium">Produit <span class="text-danger">*</span></label>
                                <select
                                    v-model="data.product_id"
                                    :class="isEmpty.product_id ? 'is-invalid' : ''"
                                    class="form-select"
                                >
                                    <option value="" disabled>-- Choisir un produit --</option>
                                    <option v-for="p in products" :key="p.id" :value="p.id">
                                        {{ p.nom }}
                                        <template v-if="p.quantite <= p.seuil_alerte"> ⚠️ ({{ p.quantite }} restants)</template>
                                    </option>
                                </select>
                                <div v-if="isEmpty.product_id" class="invalid-feedback">Veuillez choisir un produit.</div>
                            </div>

                            <!-- Type -->
                            <div class="col-12">
                                <label class="form-label fw-medium">Type de mouvement <span class="text-danger">*</span></label>
                                <div class="d-flex gap-3">
                                    <div
                                        class="flex-fill text-center p-3 rounded cursor-pointer border"
                                        :style="data.type === 'entree'
                                            ? 'background:#d4edda;border-color:#28a745!important;'
                                            : 'background:#f8f9fa;'"
                                        @click="data.type = 'entree'"
                                        style="cursor:pointer;"
                                    >
                                        <iconify-icon icon="solar:arrow-down-bold" style="font-size:28px;color:#28a745;"></iconify-icon>
                                        <p class="mb-0 fw-semibold mt-1" style="color:#28a745;">Entrée</p>
                                        <small class="text-muted">Stock réceptionné</small>
                                    </div>
                                    <div
                                        class="flex-fill text-center p-3 rounded border"
                                        :style="data.type === 'sortie'
                                            ? 'background:#f8d7da;border-color:#dc3545!important;'
                                            : 'background:#f8f9fa;'"
                                        @click="data.type = 'sortie'"
                                        style="cursor:pointer;"
                                    >
                                        <iconify-icon icon="solar:arrow-up-bold" style="font-size:28px;color:#dc3545;"></iconify-icon>
                                        <p class="mb-0 fw-semibold mt-1" style="color:#dc3545;">Sortie</p>
                                        <small class="text-muted">Stock retiré</small>
                                    </div>
                                </div>
                                <div v-if="isEmpty.type" class="text-danger small mt-1">Veuillez choisir un type.</div>
                            </div>

                            <!-- Stock disponible (info) -->
                            <div v-if="selectedProduct" class="col-12">
                                <div class="alert alert-info py-2 mb-0 d-flex align-items-center gap-2">
                                    <iconify-icon icon="solar:info-circle-bold"></iconify-icon>
                                    Stock actuel de <strong class="mx-1">{{ selectedProduct.nom }}</strong> :
                                    <span
                                        class="badge ms-1"
                                        :style="selectedProduct.quantite === 0
                                            ? 'background:#dc3545'
                                            : selectedProduct.quantite <= selectedProduct.seuil_alerte
                                                ? 'background:#ffc107;color:#000'
                                                : 'background:#28a745'"
                                    >
                                        {{ selectedProduct.quantite }}
                                    </span>
                                </div>
                            </div>

                            <!-- Quantité -->
                            <div class="col-lg-6">
                                <label class="form-label fw-medium">Quantité <span class="text-danger">*</span></label>
                                <input
                                    type="number"
                                    v-model="data.quantite"
                                    :class="isEmpty.quantite ? 'is-invalid' : ''"
                                    class="form-control"
                                    placeholder="0"
                                    min="1"
                                >
                                <div v-if="isEmpty.quantite" class="invalid-feedback">La quantité est requise (min: 1).</div>
                            </div>

                            <!-- Date -->
                            <div class="col-lg-6">
                                <label class="form-label fw-medium">Date <span class="text-danger">*</span></label>
                                <input
                                    type="date"
                                    v-model="data.date"
                                    :class="isEmpty.date ? 'is-invalid' : ''"
                                    class="form-control"
                                >
                                <div v-if="isEmpty.date" class="invalid-feedback">La date est requise.</div>
                            </div>

                            <!-- Motif -->
                            <div class="col-12">
                                <label class="form-label fw-medium">Motif <span class="text-muted">(optionnel)</span></label>
                                <input
                                    type="text"
                                    v-model="data.motif"
                                    class="form-control"
                                    placeholder="Ex: Réapprovisionnement, Casse, Retour client..."
                                    maxlength="255"
                                >
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button
                            type="submit"
                            class="btn text-white"
                            :style="data.type === 'sortie' ? 'background:#dc3545;' : 'background:#28a745;'"
                            :disabled="isLoader"
                        >
                            <span v-if="!isLoader">
                                <i :class="data.type === 'sortie' ? 'ri-arrow-up-line' : 'ri-arrow-down-line'"></i>
                                {{ data.type === 'sortie' ? 'Enregistrer la sortie' : 'Enregistrer l\'entrée' }}
                            </span>
                            <div v-else class="spinner-border spinner-border-sm" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</template>

<script setup>

    import Swal from 'sweetalert2';
    import DataTable from '../DataTable/Datatable.vue';
    import { onMounted, ref, computed } from 'vue';
    import { postData, getData, deleteData } from '../../plugins/api';

    let addmodal;

    // ─── State ───────────────────────────────────────────────────────────────────

    const data = ref({
        product_id: '',
        quantite: '',
        type: 'entree',
        date: new Date().toISOString().split('T')[0], // date du jour par défaut
        motif: '',
    })

    const isEmpty    = ref({})
    const isLoader   = ref(false)
    const allMouvements = ref([])
    const products   = ref([])
    const filterType = ref('')

    // ─── Computed ────────────────────────────────────────────────────────────────

    // Produit sélectionné (pour afficher le stock dispo)
    const selectedProduct = computed(() =>
        products.value.find(p => p.id === data.value.product_id) || null
    )

    // Mouvements filtrés selon le type sélectionné
    const filteredMouvements = computed(() => {
        if (!filterType.value) return allMouvements.value
        return allMouvements.value.filter(m => m.type === filterType.value)
    })

    // Stats résumé
    const stats = computed(() => ({
        total:   allMouvements.value.length,
        entrees: allMouvements.value.filter(m => m.type === 'entree').length,
        sorties: allMouvements.value.filter(m => m.type === 'sortie').length,
    }))

    // ─── Modal ───────────────────────────────────────────────────────────────────

    function showModal() {
        data.value = {
            product_id: '',
            quantite: '',
            type: 'entree',
            date: new Date().toISOString().split('T')[0],
            motif: '',
        }
        isEmpty.value = {}
        addmodal.show()
    }

    // ─── Colonnes DataTable ───────────────────────────────────────────────────────

    const columns = ref([
        {
            title: '#', data: null,
            render: (data, type, row, meta) => meta.row + 1
        },
        {
            title: 'Produit', data: 'product',
            render: (data, type, row) => row.product
                ? `<span class="fw-medium">${row.product.nom}</span>`
                : '-'
        },
        {
            title: 'Type', data: 'type',
            render: (data, type, row) => row.type === 'entree'
                ? `<span class="badge d-inline-flex align-items-center gap-1" style="background:#d4edda;color:#28a745;font-size:13px;">
                        ↓ Entrée
                   </span>`
                : `<span class="badge d-inline-flex align-items-center gap-1" style="background:#f8d7da;color:#dc3545;font-size:13px;">
                        ↑ Sortie
                   </span>`
        },
        {
            title: 'Quantité', data: 'quantite',
            render: (data, type, row) => {
                const color = row.type === 'entree' ? '#28a745' : '#dc3545'
                const sign  = row.type === 'entree' ? '+' : '-'
                return `<span class="fw-bold" style="color:${color};">${sign}${row.quantite}</span>`
            }
        },
        {
            title: 'Motif', data: 'motif',
            render: (data, type, row) => row.motif
                ? `<span class="text-muted">${row.motif}</span>`
                : '<span class="text-muted fst-italic">—</span>'
        },
        {
            title: 'Opérateur', data: 'user',
            render: (data, type, row) => row.user
                ? `<span class="badge bg-light text-dark border">${row.user.fullname}</span>`
                : '-'
        },
        {
            title: 'Date', data: 'date',
            render: (data, type, row) => new Intl.DateTimeFormat('fr-FR', {
                year: 'numeric', month: 'short', day: 'numeric'
            }).format(new Date(row.date))
        },
        {
            title: 'Enregistré le', data: 'created_at',
            render: (data, type, row) => new Intl.DateTimeFormat('fr-FR', {
                year: 'numeric', month: 'short', day: 'numeric',
                hour: '2-digit', minute: '2-digit'
            }).format(new Date(row.created_at))
        },
        {
            title: 'Action', data: null,
            render: (data, type, row) => `
                <a class="btn btn-danger btn-sm" href="#" onclick="DeleteMouvementFunction(${row.id})">
                    <i class="fas fa-trash"></i>
                </a>
            `
        }
    ])

    // ─── API calls ───────────────────────────────────────────────────────────────

    async function AllMouvementsFunction() {
        await getData('/mouvements').then(res => {
            if (res.status === 200) allMouvements.value = res.data
        })
    }

    async function AllProductsFunction() {
        await getData('/products').then(res => {
            if (res.status === 200) products.value = res.data
        })
    }

    async function AddMouvementFunction() {
        // Validation
        isEmpty.value = {}
        if (!data.value.product_id) isEmpty.value.product_id = true
        if (!data.value.type)       isEmpty.value.type       = true
        if (!data.value.quantite || data.value.quantite < 1) isEmpty.value.quantite = true
        if (!data.value.date)       isEmpty.value.date       = true

        const hasError = Object.values(isEmpty.value).some(v => v === true)
        if (hasError) return

        isLoader.value = true

        await postData('/mouvements', data.value).then(res => {
            if (res.status === 200 || res.status === 201) {
                Swal.fire({
                    icon: 'success',
                    title: data.value.type === 'entree' ? 'Entrée enregistrée !' : 'Sortie enregistrée !',
                    text: `${data.value.quantite} unité(s) ${data.value.type === 'entree' ? 'ajoutée(s)' : 'retirée(s)'} du stock.`,
                    showConfirmButton: false,
                    timer: 1800
                })
                AllMouvementsFunction()
                AllProductsFunction() // rafraîchir le stock affiché
                addmodal.hide()
            }
        }).catch(err => {
            // Gestion erreur stock insuffisant (422)
            if (err.response?.status === 422) {
                Swal.fire({
                    icon: 'error',
                    title: 'Stock insuffisant',
                    text: err.response.data.message,
                })
            }
        })

        isLoader.value = false
    }

    window.DeleteMouvementFunction = async function (id) {
        Swal.fire({
            title: 'Annuler ce mouvement ?',
            text: 'Le stock sera ajusté en conséquence.',
            icon: 'warning',
            showCancelButton: true,
            cancelButtonColor: '#6c757d',
            confirmButtonColor: '#dc3545',
            confirmButtonText: 'Oui, annuler',
            cancelButtonText: 'Fermer'
        }).then(async result => {
            if (result.isConfirmed) {
                await deleteData('/mouvements/' + id).then(res => {
                    if (res.status === 200) {
                        Swal.fire({
                            icon: 'success',
                            text: 'Mouvement annulé et stock ajusté.',
                            showConfirmButton: false,
                            timer: 1500
                        })
                        AllMouvementsFunction()
                        AllProductsFunction()
                    }
                }).catch(err => {
                    if (err.response?.status === 422) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Impossible d\'annuler',
                            text: err.response.data.message,
                        })
                    }
                })
            }
        })
    }

    // ─── Lifecycle ───────────────────────────────────────────────────────────────

    onMounted(() => {
        addmodal = new bootstrap.Modal(document.getElementById('mouvementModal'))
        AllMouvementsFunction()
        AllProductsFunction()
    })

</script>
