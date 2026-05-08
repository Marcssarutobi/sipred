<template>
    <div class="dashboard-main-body">

        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
            <h6 class="fw-semibold mb-0">Liste des Ventes</h6>
            <ul class="d-flex align-items-center gap-2">
                <li class="fw-medium">
                    <RouterLink to="/" class="d-flex align-items-center gap-1 hover-text-primary">
                        <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                        Tableau de bord
                    </RouterLink>
                </li>
                <li>-</li>
                <li class="fw-medium">Liste des Ventes</li>
            </ul>
        </div>

        <!-- Cards statistiques -->
        <div class="row g-3 mb-24">
            <div class="col-lg-3 col-sm-6">
                <div class="card border-0 shadow-sm" style="border-left:4px solid #0d6efd !important;">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                            style="width:48px;height:48px;background:#cfe2ff;">
                            <iconify-icon icon="solar:cart-bold" style="color:#0d6efd;font-size:22px;"></iconify-icon>
                        </div>
                        <div>
                            <p class="mb-0 text-muted small">Total Ventes</p>
                            <h5 class="fw-bold mb-0" style="color:#0d6efd;">{{ stats.total_ventes }}</h5>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="card border-0 shadow-sm" style="border-left:4px solid #28a745 !important;">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                            style="width:48px;height:48px;background:#d4edda;">
                            <iconify-icon icon="solar:wallet-money-bold" style="color:#28a745;font-size:22px;"></iconify-icon>
                        </div>
                        <div>
                            <p class="mb-0 text-muted small">Chiffre d'affaires</p>
                            <h5 class="fw-bold mb-0" style="color:#28a745;">
                                {{ formatCurrency(stats.chiffre_affaires) }}
                            </h5>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="card border-0 shadow-sm" style="border-left:4px solid #198754 !important;">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                            style="width:48px;height:48px;background:#d1e7dd;">
                            <iconify-icon icon="solar:check-circle-bold" style="color:#198754;font-size:22px;"></iconify-icon>
                        </div>
                        <div>
                            <p class="mb-0 text-muted small">Ventes Payées</p>
                            <h5 class="fw-bold mb-0" style="color:#198754;">{{ stats.ventes_payees }}</h5>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="card border-0 shadow-sm" style="border-left:4px solid #dc3545 !important;">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                            style="width:48px;height:48px;background:#f8d7da;">
                            <iconify-icon icon="solar:close-circle-bold" style="color:#dc3545;font-size:22px;"></iconify-icon>
                        </div>
                        <div>
                            <p class="mb-0 text-muted small">Ventes Impayées</p>
                            <h5 class="fw-bold mb-0" style="color:#dc3545;">{{ stats.ventes_impayees }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="card">
            <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="d-flex gap-2">
                    <select v-model="filterStatus" class="form-select form-select-sm" style="width:auto;">
                        <option value="">Tous les statuts</option>
                        <option value="paye">Payées</option>
                        <option value="non_paye">Impayées</option>
                    </select>
                </div>
                <button @click="showModal" class="btn btn-sm btn-primary-600">
                    <i class="ri-add-line"></i> Nouvelle Vente
                </button>
            </div>
            <div class="card-body">
                <DataTable :data="filteredVentes" :columns="columns" />
            </div>
        </div>

        <!-- ─── Modal Nouvelle Vente ──────────────────────────────────────────── -->
        <div class="modal fade" id="venteModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <form class="modal-content" @submit.prevent="AddVenteFunction()">
                    <div class="modal-header" style="background:#002D5D;">
                        <h1 class="modal-title fs-5 text-white">
                            <i class="ri-shopping-cart-line me-2"></i>Nouvelle Vente
                        </h1>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">

                            <!-- Date -->
                            <div class="col-lg-4">
                                <label class="form-label fw-medium">Date <span class="text-danger">*</span></label>
                                <input
                                    type="date"
                                    v-model="data.date"
                                    :class="isEmpty.date ? 'is-invalid' : ''"
                                    class="form-control"
                                >
                                <div v-if="isEmpty.date" class="invalid-feedback">La date est requise.</div>
                            </div>

                            <!-- Séparateur -->
                            <div class="col-12">
                                <hr class="my-1">
                                <p class="fw-semibold mb-2" style="color:#002D5D;">
                                    <i class="ri-list-check me-1"></i>Articles
                                </p>

                                <!-- Ligne d'ajout produit -->
                                <div class="row g-2 align-items-end mb-2">
                                    <div class="col-lg-5">
                                        <label class="form-label small">Produit</label>
                                        <select v-model="newItem.product_id" class="form-select form-select-sm"
                                            @change="onProductSelect">
                                            <option value="" disabled>-- Choisir --</option>
                                            <option v-for="p in products" :key="p.id" :value="p.id">
                                                {{ p.nom }} (stock: {{ p.quantite }})
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col-lg-2">
                                        <label class="form-label small">Qté</label>
                                        <input type="number" v-model="newItem.quantite"
                                            class="form-control form-control-sm" min="1" placeholder="1">
                                    </div>
                                    <div class="col-lg-3">
                                        <label class="form-label small">Prix unitaire</label>
                                        <input type="number" v-model="newItem.prix_unitaire"
                                            class="form-control form-control-sm" min="0" step="0.01" placeholder="0">
                                    </div>
                                    <div class="col-lg-2">
                                        <button type="button" class="btn btn-success btn-sm w-100" @click="addItem">
                                            <i class="ri-add-line"></i> Ajouter
                                        </button>
                                    </div>
                                </div>

                                <!-- Message erreur item -->
                                <div v-if="itemError" class="alert alert-danger py-1 px-2 small">{{ itemError }}</div>

                                <!-- Tableau des articles -->
                                <div v-if="data.items.length > 0" class="table-responsive">
                                    <table class="table table-sm table-bordered mb-0">
                                        <thead style="background:#f0f4ff;">
                                            <tr>
                                                <th>#</th>
                                                <th>Produit</th>
                                                <th>Qté</th>
                                                <th>Prix unit.</th>
                                                <th>Total</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(item, index) in data.items" :key="index">
                                                <td>{{ index + 1 }}</td>
                                                <td class="fw-medium">{{ item.product_nom }}</td>
                                                <td>
                                                    <input type="number" v-model="item.quantite"
                                                        class="form-control form-control-sm"
                                                        style="width:70px;" min="1"
                                                        @input="updateItemTotal(item)">
                                                </td>
                                                <td>
                                                    <input type="number" v-model="item.prix_unitaire"
                                                        class="form-control form-control-sm"
                                                        style="width:100px;" min="0" step="0.01"
                                                        @input="updateItemTotal(item)">
                                                </td>
                                                <td class="fw-bold" style="color:#002D5D;">
                                                    {{ formatCurrency(item.prix_total) }}
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-danger btn-sm"
                                                        @click="removeItem(index)">
                                                        <i class="ri-delete-bin-line"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Message si vide -->
                                <div v-else class="text-center text-muted py-3 border rounded"
                                    style="background:#f8f9fa;">
                                    <iconify-icon icon="solar:cart-large-minimalistic-bold"
                                        style="font-size:32px;opacity:.4;"></iconify-icon>
                                    <p class="mb-0 small mt-1">Aucun article ajouté</p>
                                </div>

                                <div v-if="isEmpty.items" class="text-danger small mt-1">
                                    Veuillez ajouter au moins un article.
                                </div>
                            </div>

                            <!-- Récapitulatif paiement -->
                            <div class="col-12" v-if="data.items.length > 0">
                                <div class="card border-0" style="background:#f0f4ff;">
                                    <div class="card-body">
                                        <div class="row g-3 align-items-center">
                                            <div class="col-lg-4">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <span class="text-muted">Montant total :</span>
                                                    <span class="fw-bold fs-5" style="color:#002D5D;">
                                                        {{ formatCurrency(montantTotal) }}
                                                    </span>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <span class="text-muted">Monnaie à rendre :</span>
                                                    <span class="fw-bold" :style="monnaie < 0 ? 'color:#dc3545' : 'color:#28a745'">
                                                        {{ formatCurrency(Math.max(0, monnaie)) }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <label class="form-label fw-medium">
                                                    Montant payé <span class="text-danger">*</span>
                                                </label>
                                                <input
                                                    type="number"
                                                    v-model="data.montant_paye"
                                                    :class="isEmpty.montant_paye ? 'is-invalid' : ''"
                                                    class="form-control"
                                                    min="0"
                                                    step="0.01"
                                                    placeholder="0"
                                                >
                                                <div v-if="isEmpty.montant_paye" class="invalid-feedback">
                                                    Le montant payé est requis.
                                                </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <!-- Badge statut paiement -->
                                                <div class="text-center p-3 rounded"
                                                    :style="data.montant_paye >= montantTotal && montantTotal > 0
                                                        ? 'background:#d4edda;'
                                                        : 'background:#f8d7da;'">
                                                    <iconify-icon
                                                        :icon="data.montant_paye >= montantTotal && montantTotal > 0
                                                            ? 'solar:check-circle-bold'
                                                            : 'solar:close-circle-bold'"
                                                        :style="data.montant_paye >= montantTotal && montantTotal > 0
                                                            ? 'font-size:28px;color:#28a745;'
                                                            : 'font-size:28px;color:#dc3545;'">
                                                    </iconify-icon>
                                                    <p class="mb-0 fw-semibold mt-1"
                                                        :style="data.montant_paye >= montantTotal && montantTotal > 0
                                                            ? 'color:#28a745;'
                                                            : 'color:#dc3545;'">
                                                        {{ data.montant_paye >= montantTotal && montantTotal > 0
                                                            ? 'Payé'
                                                            : 'Impayé / Partiel' }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary" :disabled="isLoader || data.items.length === 0">
                            <span v-if="!isLoader">
                                <i class="ri-save-line me-1"></i>Valider la vente
                            </span>
                            <div v-else class="spinner-border spinner-border-sm" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ─── Modal Détail Vente ────────────────────────────────────────────── -->
        <div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header" style="background:#002D5D;">
                        <h5 class="modal-title text-white">
                            <i class="ri-file-text-line me-2"></i>
                            Détail — {{ detailVente?.reference }}
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body" v-if="detailVente">

                        <!-- Infos générales -->
                        <div class="row g-2 mb-3">
                            <div class="col-sm-4">
                                <span class="text-muted small">Référence</span>
                                <p class="fw-bold mb-0">{{ detailVente.reference }}</p>
                            </div>
                            <div class="col-sm-4">
                                <span class="text-muted small">Date</span>
                                <p class="fw-bold mb-0">
                                    {{ new Intl.DateTimeFormat('fr-FR', { dateStyle: 'long' }).format(new Date(detailVente.date)) }}
                                </p>
                            </div>
                            <div class="col-sm-4">
                                <span class="text-muted small">Statut</span>
                                <p class="mb-0">
                                    <span class="badge"
                                        :style="detailVente.status === 'paye'
                                            ? 'background:#d4edda;color:#28a745;'
                                            : 'background:#f8d7da;color:#dc3545;'">
                                        {{ detailVente.status === 'paye' ? '✓ Payé' : '✗ Impayé' }}
                                    </span>
                                </p>
                            </div>
                            <div class="col-sm-4">
                                <span class="text-muted small">Opérateur</span>
                                <p class="fw-bold mb-0">{{ detailVente.user?.fullname }}</p>
                            </div>
                            <div class="col-sm-4">
                                <span class="text-muted small">Montant total</span>
                                <p class="fw-bold mb-0" style="color:#002D5D;">
                                    {{ formatCurrency(detailVente.montant_total) }}
                                </p>
                            </div>
                            <div class="col-sm-4">
                                <span class="text-muted small">Montant payé</span>
                                <p class="fw-bold mb-0" style="color:#28a745;">
                                    {{ formatCurrency(detailVente.montant_paye) }}
                                </p>
                            </div>
                        </div>

                        <!-- Articles -->
                        <table class="table table-sm table-bordered">
                            <thead style="background:#f0f4ff;">
                                <tr>
                                    <th>#</th>
                                    <th>Produit</th>
                                    <th>Qté</th>
                                    <th>Prix unit.</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item, i) in detailVente.items" :key="i">
                                    <td>{{ i + 1 }}</td>
                                    <td class="fw-medium">{{ item.product?.nom }}</td>
                                    <td>{{ item.quantite }}</td>
                                    <td>{{ formatCurrency(item.prix_unitaire) }}</td>
                                    <td class="fw-bold">{{ formatCurrency(item.prix_total) }}</td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" class="text-end fw-bold">Total</td>
                                    <td class="fw-bold" style="color:#002D5D;">
                                        {{ formatCurrency(detailVente.montant_total) }}
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="text-end fw-bold">Monnaie rendue</td>
                                    <td class="fw-bold" style="color:#28a745;">
                                        {{ formatCurrency(detailVente.monnaie) }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>

<script setup>

    import Swal from 'sweetalert2';
    import DataTable from '../DataTable/Datatable.vue';
    import { onMounted, ref, computed } from 'vue';
    import { postData, getData, getSingleData, deleteData } from '../../plugins/api';

    let addmodal;
    let detailModal;

    // ─── State ───────────────────────────────────────────────────────────────────

    const data = ref({
        date: new Date().toISOString().split('T')[0],
        montant_paye: '',
        items: [],
    })

    const newItem = ref({ product_id: '', quantite: 1, prix_unitaire: '' })

    const isEmpty     = ref({})
    const itemError   = ref('')
    const isLoader    = ref(false)
    const allVentes   = ref([])
    const products    = ref([])
    const filterStatus = ref('')
    const detailVente  = ref(null)
    const stats        = ref({
        total_ventes: 0,
        chiffre_affaires: 0,
        ventes_payees: 0,
        ventes_impayees: 0,
    })

    // ─── Computed ────────────────────────────────────────────────────────────────

    const montantTotal = computed(() =>
        data.value.items.reduce((sum, i) => sum + Number(i.prix_total), 0)
    )

    const monnaie = computed(() =>
        Number(data.value.montant_paye) - montantTotal.value
    )

    const filteredVentes = computed(() => {
        if (!filterStatus.value) return allVentes.value
        return allVentes.value.filter(v => v.status === filterStatus.value)
    })

    // ─── Helpers ─────────────────────────────────────────────────────────────────

    function formatCurrency(val) {
        return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XOF' }).format(val || 0)
    }

    // ─── Gestion des items ────────────────────────────────────────────────────────

    function onProductSelect() {
        const p = products.value.find(p => p.id === newItem.value.product_id)
        if (p) newItem.value.prix_unitaire = p.prix_unitaire
    }

    function addItem() {
        itemError.value = ''
        const { product_id, quantite, prix_unitaire } = newItem.value
        const product = products.value.find(p => p.id === product_id)

        if (!product_id || !product) return itemError.value = 'Veuillez choisir un produit.'
        if (!quantite || quantite < 1) return itemError.value = 'Quantité invalide.'
        if (!prix_unitaire || prix_unitaire < 0) return itemError.value = 'Prix invalide.'
        if (quantite > product.quantite) return itemError.value = `Stock insuffisant (${product.quantite} disponibles).`

        // Vérifier si le produit est déjà dans la liste
        const existing = data.value.items.find(i => i.product_id === product_id)
        if (existing) {
            existing.quantite   = Number(existing.quantite) + Number(quantite)
            existing.prix_total = existing.quantite * existing.prix_unitaire
        } else {
            data.value.items.push({
                product_id,
                product_nom:   product.nom,
                quantite:      Number(quantite),
                prix_unitaire: Number(prix_unitaire),
                prix_total:    Number(quantite) * Number(prix_unitaire),
            })
        }

        // Reset ligne
        newItem.value = { product_id: '', quantite: 1, prix_unitaire: '' }
    }

    function removeItem(index) {
        data.value.items.splice(index, 1)
    }

    function updateItemTotal(item) {
        item.prix_total = Number(item.quantite) * Number(item.prix_unitaire)
    }

    // ─── Modal ───────────────────────────────────────────────────────────────────

    function showModal() {
        data.value = {
            date: new Date().toISOString().split('T')[0],
            montant_paye: '',
            items: [],
        }
        newItem.value = { product_id: '', quantite: 1, prix_unitaire: '' }
        isEmpty.value = {}
        itemError.value = ''
        addmodal.show()
    }

    // ─── Colonnes DataTable ───────────────────────────────────────────────────────

    const columns = ref([
        {
            title: '#', data: null,
            render: (data, type, row, meta) => meta.row + 1
        },
        {
            title: 'Référence', data: 'reference',
            render: (data, type, row) =>
                `<span class="fw-medium" style="color:#002D5D;">${row.reference}</span>`
        },
        {
            title: 'Articles', data: 'items',
            render: (data, type, row) => {
                const count = row.items?.length || 0
                return `<span class="badge bg-light text-dark border">${count} article(s)</span>`
            }
        },
        {
            title: 'Montant total', data: 'montant_total',
            render: (data, type, row) =>
                `<span class="fw-bold" style="color:#002D5D;">${new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XOF' }).format(row.montant_total)}</span>`
        },
        {
            title: 'Montant payé', data: 'montant_paye',
            render: (data, type, row) =>
                `<span style="color:#28a745;">${new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XOF' }).format(row.montant_paye)}</span>`
        },
        {
            title: 'Monnaie', data: 'monnaie',
            render: (data, type, row) =>
                `<span class="text-muted">${new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XOF' }).format(row.monnaie)}</span>`
        },
        {
            title: 'Statut', data: 'status',
            render: (data, type, row) => row.status === 'paye'
                ? `<span class="badge" style="background:#d4edda;color:#28a745;">✓ Payé</span>`
                : `<span class="badge" style="background:#f8d7da;color:#dc3545;">✗ Impayé</span>`
        },
        {
            title: 'Opérateur', data: 'user',
            render: (data, type, row) =>
                `<span class="badge bg-light text-dark border">${row.user?.fullname || '-'}</span>`
        },
        {
            title: 'Date', data: 'date',
            render: (data, type, row) => new Intl.DateTimeFormat('fr-FR', {
                year: 'numeric', month: 'short', day: 'numeric'
            }).format(new Date(row.date))
        },
        {
            title: 'Action', data: null,
            render: (data, type, row) => `
                <a class="btn btn-info btn-sm me-1 text-white" href="#" onclick="ShowDetailVente(${row.id})">
                    <i class="fas fa-eye"></i>
                </a>
                <a class="btn btn-danger btn-sm" href="#" onclick="DeleteVenteFunction(${row.id})">
                    <i class="fas fa-trash"></i>
                </a>
            `
        }
    ])

    // ─── API calls ───────────────────────────────────────────────────────────────

    async function AllVentesFunction() {
        await getData('/ventes').then(res => {
            if (res.status === 200) allVentes.value = res.data
        })
    }

    async function AllProductsFunction() {
        await getData('/products').then(res => {
            if (res.status === 200) products.value = res.data
        })
    }

    async function LoadStats() {
        await getData('/ventes/statistiques').then(res => {
            if (res.status === 200) {
                stats.value = {
                    ...stats.value,
                    ...res.data
                }
            }
        })
    }

    async function AddVenteFunction() {
        isEmpty.value = {}

        if (!data.value.date)         isEmpty.value.date         = true
        if (!data.value.montant_paye && data.value.montant_paye !== 0)
                                      isEmpty.value.montant_paye = true
        if (data.value.items.length === 0) isEmpty.value.items   = true

        const hasError = Object.values(isEmpty.value).some(v => v === true)
        if (hasError) return

        isLoader.value = true

        // Formater les items pour l'API
        const payload = {
            date:          data.value.date,
            montant_paye:  data.value.montant_paye,
            items: data.value.items.map(i => ({
                product_id:    i.product_id,
                quantite:      i.quantite,
                prix_unitaire: i.prix_unitaire,
            }))
        }

        await postData('/ventes', payload).then(res => {
            if (res.status === 201 || res.status === 200) {
                Swal.fire({
                    icon: 'success',
                    title: 'Vente enregistrée !',
                    html: `Référence : <strong>${res.data.reference}</strong><br>
                           Total : <strong>${new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XOF' }).format(res.data.montant_total)}</strong>`,
                    showConfirmButton: false,
                    timer: 2500
                })
                AllVentesFunction()
                AllProductsFunction()
                LoadStats()
                addmodal.hide()
            }
        }).catch(err => {
            if (err.response?.status === 422) {
                Swal.fire({
                    icon: 'error',
                    title: 'Stock insuffisant',
                    text: err.response.data.message,
                })
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: err.response?.data?.message || 'Une erreur est survenue.',
                })
            }
        })

        isLoader.value = false
    }

    window.ShowDetailVente = async function (id) {
        await getSingleData('/ventes/' + id).then(res => {
            if (res.status === 200) {
                detailVente.value = res.data
                detailModal.show()
            }
        })
    }

    window.DeleteVenteFunction = async function (id) {
        Swal.fire({
            title: 'Annuler cette vente ?',
            text: 'Le stock sera restauré automatiquement.',
            icon: 'warning',
            showCancelButton: true,
            cancelButtonColor: '#6c757d',
            confirmButtonColor: '#dc3545',
            confirmButtonText: 'Oui, annuler',
            cancelButtonText: 'Fermer'
        }).then(async result => {
            if (result.isConfirmed) {
                await deleteData('/ventes/' + id).then(res => {
                    if (res.status === 200) {
                        Swal.fire({
                            icon: 'success',
                            text: 'Vente annulée et stock restauré.',
                            showConfirmButton: false,
                            timer: 1500
                        })
                        AllVentesFunction()
                        AllProductsFunction()
                        LoadStats()
                    }
                }).catch(err => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur',
                        text: err.response?.data?.message || 'Une erreur est survenue.',
                    })
                })
            }
        })
    }

    // ─── Lifecycle ───────────────────────────────────────────────────────────────

    onMounted(() => {
        addmodal    = new bootstrap.Modal(document.getElementById('venteModal'))
        detailModal = new bootstrap.Modal(document.getElementById('detailModal'))
        AllVentesFunction()
        AllProductsFunction()
        LoadStats()
    })

</script>
