<template>
    <div class="dashboard-main-body">

        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
          <h6 class="fw-semibold mb-0">Liste des Produits</h6>
          <ul class="d-flex align-items-center gap-2">
              <li class="fw-medium">
                  <RouterLink to="/" class="d-flex align-items-center gap-1 hover-text-primary">
                      <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                      Tableau de bord
                  </RouterLink>
              </li>
              <li>-</li>
              <li class="fw-medium">Liste des Produits</li>
          </ul>
        </div>

        <div class="card">
          <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-3">
              <div></div>
              <button v-if="!isMagasinier" @click="showModal" class="btn btn-sm btn-primary-600"><i class="ri-add-line"></i> Ajouter un Produits</button>
          </div>
          <div class="card-body">
              <DataTable :data="allProduct" :columns="columns" />
          </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <form class="modal-content" @submit.prevent="!isEdite ? AddProductFunction() : UpdateProductFunction()">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="productModalLabel">{{ modalTitle }}</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">

                            <!-- Code Barre -->
                            <div class="col-lg-6">
                              <label class="form-label">Code Barre</label>
                              <div class="input-group">
                                  <input
                                      ref="codeBareInput"
                                      type="text"
                                      v-model="data.code_barre"
                                      :class="isEmpty.code_barre ? 'is-invalid' : ''"
                                      class="form-control"
                                      placeholder="Scannez ou saisissez le code"
                                      maxlength="100"
                                      @keydown="onScannerKeydown"
                                  >
                                  <!-- Bouton caméra (mobile) -->
                                  <button
                                      type="button"
                                      class="btn btn-outline-secondary"
                                      @click="openCamera"
                                      title="Scanner avec la caméra"
                                  >
                                      <i class="ri-camera-line"></i>
                                  </button>
                                  <div v-if="isEmpty.code_barre" class="invalid-feedback">
                                      {{ msgInput.code_barre }}
                                  </div>
                              </div>
                              <!-- Indicateur scanning en cours -->
                              <small v-if="isScanning" class="text-success">
                                  <i class="ri-barcode-line"></i> Scan en cours...
                              </small>
                          </div>

                            <!-- Nom -->
                            <div class="col-lg-6">
                                <label class="form-label">Nom du produit</label>
                                <input
                                    type="text"
                                    v-model="data.nom"
                                    :class="isEmpty.nom ? 'is-invalid' : ''"
                                    class="form-control"
                                    placeholder="Nom du produit"
                                    maxlength="255"
                                >
                                <div v-if="isEmpty.nom" class="invalid-feedback">
                                    {{ msgInput.nom }}
                                </div>
                            </div>

                            <!-- Prix Achat -->
                            <div class="col-lg-6">
                                <label class="form-label">Prix d'achat</label>
                                <input
                                    type="number"
                                    v-model="data.prix_achat"
                                    :class="isEmpty.prix_achat ? 'is-invalid' : ''"
                                    class="form-control"
                                    placeholder="0"
                                    min="0"
                                    step="0.01"
                                >
                                <div v-if="isEmpty.prix_achat" class="invalid-feedback">
                                    {{ msgInput.prix_achat }}
                                </div>
                            </div>

                            <!-- Prix Vente -->
                            <div class="col-lg-6">
                                <label class="form-label">Prix de vente</label>
                                <input
                                    type="number"
                                    v-model="data.prix_unitaire"
                                    :class="isEmpty.prix_unitaire ? 'is-invalid' : ''"
                                    class="form-control"
                                    placeholder="0"
                                    min="0"
                                    step="0.01"
                                >
                                <div v-if="isEmpty.prix_unitaire" class="invalid-feedback">
                                    {{ msgInput.prix_unitaire }}
                                </div>
                            </div>

                            <!-- Quantité -->
                            <div class="col-lg-6">
                                <label class="form-label">Quantité</label>
                                <input
                                    type="number"
                                    v-model="data.quantite"
                                    :class="isEmpty.quantite ? 'is-invalid' : ''"
                                    class="form-control"
                                    placeholder="0"
                                    min="0"
                                >
                                <div v-if="isEmpty.quantite" class="invalid-feedback">
                                    {{ msgInput.quantite }}
                                </div>
                            </div>

                            <!-- Seuil Alerte -->
                            <div class="col-lg-6">
                                <label class="form-label">Seuil d'alerte</label>
                                <input
                                    type="number"
                                    v-model="data.seuil_alerte"
                                    :class="isEmpty.seuil_alerte ? 'is-invalid' : ''"
                                    class="form-control"
                                    placeholder="0"
                                    min="0"
                                >
                                <div v-if="isEmpty.seuil_alerte" class="invalid-feedback">
                                    {{ msgInput.seuil_alerte }}
                                </div>
                            </div>

                            <!-- Catégorie -->
                            <div class="col-lg-6">
                                <label class="form-label">Catégorie</label>
                                <select
                                    v-model="data.category_id"
                                    :class="isEmpty.category_id ? 'is-invalid' : ''"
                                    class="form-select"
                                >
                                    <option value="" disabled>-- Choisir une catégorie --</option>
                                    <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                                        {{ cat.name }}
                                    </option>
                                </select>
                                <div v-if="isEmpty.category_id" class="invalid-feedback">
                                    {{ msgInput.category_id }}
                                </div>
                            </div>

                            <!-- Fournisseur -->
                            <div class="col-lg-6">
                                <label class="form-label">Fournisseur</label>
                                <select
                                    v-model="data.fournisseur_id"
                                    :class="isEmpty.fournisseur_id ? 'is-invalid' : ''"
                                    class="form-select"
                                >
                                    <option value="" disabled>-- Choisir un fournisseur --</option>
                                    <option v-for="four in allFournisseur" :key="four.id" :value="four.id">
                                        {{ four.nom }}
                                    </option>
                                </select>
                                <div v-if="isEmpty.fournisseur_id" class="invalid-feedback">
                                    {{ msgInput.fournisseur_id }}
                                </div>
                            </div>

                            <!-- Date Expiration -->
                            <div class="col-lg-6">
                                <label class="form-label">Date d'expiration <span class="text-muted">(optionnel)</span></label>
                                <input
                                    type="date"
                                    v-model="data.date_expiration"
                                    class="form-control"
                                >
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermé</button>
                        <button type="submit" class="btn btn-primary" :disabled="isLoader">
                            <span v-if="!isLoader">{{ modalbutton }}</span>
                            <div v-else class="spinner-border spinner-border-sm" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Caméra -->
        <div class="modal fade" id="cameraModal" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">
                  <div class="modal-header">
                      <h5 class="modal-title">Scanner le code barre</h5>
                      <button type="button" class="btn-close" @click="closeCamera"></button>
                  </div>
                  <div class="modal-body text-center">
                      <video ref="videoRef" class="w-100 rounded" autoplay playsinline></video>
                      <canvas ref="canvasRef" class="d-none"></canvas>
                      <p class="text-muted mt-2 small">Pointez la caméra vers le code barre</p>
                  </div>
                  <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" @click="closeCamera">Annuler</button>
                  </div>
              </div>
          </div>
        </div>



    </div>
  </template>

  <script setup>

    import Swal from 'sweetalert2';
    import DataTable from '../DataTable/Datatable.vue';
    import { onMounted, onUnmounted, ref, computed } from 'vue';
    import {postData, getData, getSingleData, putData, deleteData} from '../../plugins/api'
    import { isAuthenticated } from '../../router';

    let addmodal;
    let cameraModal;
    let cameraStream = null;
    let barcodeDetector = null;
    let scanInterval = null;

    // ─── Refs ────────────────────────────────────────────────────────────────────
    const codeBareInput = ref(null)
    const videoRef      = ref(null)
    const canvasRef     = ref(null)
    const isScanning    = ref(false)
    const currentUser   = ref(null)
    const isMagasinier  = computed(() => currentUser.value?.role === 'magasinier')
    const isGerant      = computed(() => currentUser.value?.role === 'gerant')

    async function loadCurrentUser() {
      currentUser.value = await isAuthenticated()
    }

    const data = ref({
        id:'',
        code_barre:'',
        nom:'',
        prix_unitaire:'',
        prix_achat:'',
        quantite:'',
        seuil_alerte:'',
        date_expiration:'',
        category_id:'',
        fournisseur_id:''
    })

    const isEmpty = ref({})
    const imagePreview = ref('')
    const msgInput = ref({})
    const isLoader = ref(false)
    const isEdite = ref(false)
    const modalTitle = ref('')
    const modalbutton = ref('')
    const allProduct = ref([])
    const categories = ref([])
    const allFournisseur = ref([])

    // ─── Scanner USB / Bluetooth ─────────────────────────────────────────────────
    // Les scanners de code barre envoient les caractères très rapidement
    // puis envoient "Enter" à la fin. On détecte ça avec le délai entre les touches.

    let scanBuffer   = ''
    let scanTimer    = null
    const SCAN_DELAY = 50 // ms — délai max entre 2 caractères d'un scanner

    function onScannerKeydown(e) {
        // Le scanner envoie Enter à la fin
        if (e.key === 'Enter') {
            e.preventDefault()
            if (scanBuffer.length > 0) {
                data.value.code_barre = scanBuffer
                scanBuffer = ''
                isScanning.value = false
            }
            return
        }
    }

    // Écoute globale (fonctionne même si le champ n'est pas focus)
    function onGlobalKeydown(e) {
        // On ignore si l'utilisateur est dans un autre champ de saisie (sauf code_barre)
        const active = document.activeElement
        const isOtherInput = active &&
            (active.tagName === 'INPUT' || active.tagName === 'TEXTAREA') &&
            active !== codeBareInput.value

        if (isOtherInput) return

        // Le scanner tape très vite — on bufferise
        if (e.key.length === 1) {
            scanBuffer += e.key
            isScanning.value = true

            clearTimeout(scanTimer)
            scanTimer = setTimeout(() => {
                // Si Enter n'est pas venu mais le buffer est rempli → c'est un scan
                if (scanBuffer.length >= 4) {
                    data.value.code_barre = scanBuffer
                }
                scanBuffer = ''
                isScanning.value = false
            }, 300)
        }

        if (e.key === 'Enter' && scanBuffer.length >= 4) {
            data.value.code_barre = scanBuffer
            scanBuffer = ''
            isScanning.value = false
            clearTimeout(scanTimer)
        }
    }

    // ─── Caméra (BarcodeDetector API) ────────────────────────────────────────────

    async function openCamera() {
        // Vérifier support BarcodeDetector
        if (!('BarcodeDetector' in window)) {
            Swal.fire({
                icon: 'warning',
                title: 'Non supporté',
                text: 'Votre navigateur ne supporte pas la détection de code barre. Utilisez Chrome sur Android.',
            })
            return
        }

        if (!cameraModal) {
            cameraModal = new bootstrap.Modal(document.getElementById('cameraModal'))
        }
        cameraModal.show()

        try {
            // Demander accès caméra arrière sur mobile
            cameraStream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: 'environment' }
            })

            videoRef.value.srcObject = cameraStream

            barcodeDetector = new BarcodeDetector({
                formats: ['ean_13', 'ean_8', 'code_128', 'code_39', 'qr_code', 'upc_a', 'upc_e']
            })

            // Scan toutes les 300ms
            scanInterval = setInterval(async () => {
                if (!videoRef.value || videoRef.value.readyState < 2) return

                try {
                    const barcodes = await barcodeDetector.detect(videoRef.value)
                    if (barcodes.length > 0) {
                        data.value.code_barre = barcodes[0].rawValue
                        closeCamera()
                        Swal.fire({
                            icon: 'success',
                            text: `Code scanné : ${barcodes[0].rawValue}`,
                            showConfirmButton: false,
                            timer: 1200
                        })
                    }
                } catch (err) {
                    // Erreur de détection silencieuse
                }
            }, 300)

        } catch (err) {
            Swal.fire({
                icon: 'error',
                title: 'Accès caméra refusé',
                text: 'Veuillez autoriser l\'accès à la caméra.',
            })
            if (cameraModal) {
                cameraModal.hide()
            }
        }
    }

    function closeCamera() {
        clearInterval(scanInterval)
        if (cameraStream) {
            cameraStream.getTracks().forEach(t => t.stop())
            cameraStream = null
        }
        if (cameraModal) {
            cameraModal.hide()
        }
    }

    function showModal(){
        addmodal.show();
        data.value = {
            id:'',
            code_barre:'',
            nom:'',
            prix_unitaire:'',
            prix_achat:'',
            quantite:'',
            seuil_alerte:'',
            date_expiration:'',
            category_id:'',
            fournisseur_id:''
        }
        modalTitle.value = 'Ajouter un produits'
        modalbutton.value = 'Enrégistrer'
        isEmpty.value = {}
        msgInput.value = {}
        isEdite.value = false
    }

    async function AllProductFunction() {
        await getData('/products').then(res=>{
            if (res.status === 200) {
                allProduct.value = res.data
            }
        })
    }

    async function AllCategoryFunction() {
        await getData('/categories').then(res=>{
            if (res.status === 200) {
                categories.value = res.data
            }
        })
    }

    async function AllFournisseurFunction() {
        await getData('/fournisseurs').then(res=>{
            if (res.status === 200) {
                allFournisseur.value = res.data
            }
        })
    }

    const columns = ref([
            {
                title: '#',
                data: null,
                render: function (data, type, row, meta) {
                    return meta.row + 1;
                }
            },
            {
                title: 'Code Barre',
                data: 'code_barre'
            },
            {
                title: 'Nom',
                data: 'nom'
            },
            {
                title: 'Catégorie',
                data: 'category',
                render: (data, type, row) => {
                    return row.category ? row.category.name : '-';
                }
            },
            {
                title: 'Fournisseur',
                data: 'fournisseur',
                render: (data, type, row) => {
                    return row.fournisseur ? row.fournisseur.nom : '-';
                }
            },
            {
                title: 'Prix Achat',
                data: 'prix_achat',
                render: (data, type, row) => {
                    return new Intl.NumberFormat('fr-FR', {
                        style: 'currency',
                        currency: 'XOF' // Change selon ta devise
                    }).format(row.prix_achat);
                }
            },
            {
                title: 'Prix Vente',
                data: 'prix_unitaire',
                render: (data, type, row) => {
                    return new Intl.NumberFormat('fr-FR', {
                        style: 'currency',
                        currency: 'XOF'
                    }).format(row.prix_unitaire);
                }
            },
            {
                title: 'Quantité',
                data: 'quantite',
                render: (data, type, row) => {
                    // Badge rouge si en alerte
                    if (row.quantite === 0) {
                        return `<span class="badge bg-danger">Rupture</span>`;
                    } else if (row.quantite <= row.seuil_alerte) {
                        return `<span class="badge bg-warning text-dark">${row.quantite} ⚠️</span>`;
                    }
                    return `<span class="badge bg-success">${row.quantite}</span>`;
                }
            },
            {
                title: 'Seuil Alerte',
                data: 'seuil_alerte'
            },
            {
                title: 'Expiration',
                data: 'date_expiration',
                render: (data, type, row) => {
                    if (!row.date_expiration) return '-';
                    const date = new Date(row.date_expiration);
                    return new Intl.DateTimeFormat('fr-FR', {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric',
                    }).format(date);
                }
            },
            {
                title: 'Crée le',
                data: 'created_at',
                render: (data, type, row) => {
                    const date = new Date(row.created_at);
                    return new Intl.DateTimeFormat('fr-FR', {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit',
                    }).format(date);
                }
            },
            {
                title: 'Action',
                data: null,
                render: (data, type, row) => {
                    if (isMagasinier.value) {
                        return `<span class="text-muted">Aucune action</span>`;
                    }
                    const editButton = `
                        <a class="btn btn-primary btn-sm me-1" href="#" onclick="ShowProductFunction(${row.id})">
                            <i class="fas fa-edit"></i>
                        </a>
                    `;
                    if (isGerant.value) {
                        return editButton;
                    }
                    return `${editButton}
                        <a class="btn btn-danger btn-sm" href="#" onclick="DeleteProductFunction(${row.id})">
                            <i class="fas fa-trash"></i>
                        </a>
                    `;
                }
            }
    ])

    async function AddProductFunction(){
        // 1. Validation visuelle (votre code existant)
        const ignoredFields = ['id'];
        for (const field in data.value) {
            if (ignoredFields.includes(field)) continue;
            isEmpty.value[field] = !data.value[field];
            msgInput.value[field] = `Please enter ${field.replace('_', ' ')}`;
        }

        const allEmpty = Object.values(isEmpty.value).every(value => value === false);

        if (allEmpty) {
            isLoader.value = true;
            await postData('/products',data.value).then(res=>{
                if (res.status === 200) {
                    isLoader.value = false;
                    data.value = {
                        id:'',
                        code_barre:'',
                        nom:'',
                        prix_unitaire:'',
                        prix_achat:'',
                        quantite:'',
                        seuil_alerte:'',
                        date_expiration:'',
                        category_id:'',
                        fournisseur_id:''
                    }
                    Swal.fire({
                        icon: 'success',
                        text: 'Produit ajouté avec succès',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    AllProductFunction()
                    addmodal.hide()
                }
            })
        }
    }

    window.ShowProductFunction = async function(id){
        await getSingleData('/products/'+id).then(res=>{
            if (res.status === 200) {
                data.value = res.data
                isEdite.value = true
                modalTitle.value = 'Modifier un produit'
                modalbutton.value = 'Modifier'
                addmodal.show()
            }
        })
    }

    async function UpdateProductFunction(){
        isLoader.value = true;
        await putData('/products/'+data.value.id,data.value).then(res=>{
            if (res.status === 200) {
                isLoader.value = false;
                data.value = {
                    id:'',
                    code_barre:'',
                    nom:'',
                    prix_unitaire:'',
                    prix_achat:'',
                    quantite:'',
                    seuil_alerte:'',
                    date_expiration:'',
                    category_id:'',
                    fournisseur_id:''
                }
                isEdite.value = false
                Swal.fire({
                    icon: 'success',
                    text: 'Modification effectuer',
                    showConfirmButton: false,
                    timer: 1500
                });
                AllProductFunction()
                addmodal.hide()
            }
        })
    }

    window.DeleteProductFunction = async function (id) {
        if (isMagasinier.value || isGerant.value) {
            Swal.fire({
                icon: 'error',
                title: 'Accès refusé',
                text: 'Vous ne pouvez pas supprimer de produit.',
            })
            return;
        }

        Swal.fire({
            title: "Voulez-vous supprimez ce produits ?",
            text: "Vous ne pouvez plus revenir en arrière",
            icon: "warning",
            showCancelButton: true,
            cancelButtonColor: "#d33",
            confirmButtonColor: "#002D5D",
            confirmButtonText: "Delete",
            cancelButtonText: "Close"
        }).then(async (result) => {
            if (result.isConfirmed) {
                await deleteData('/products/'+id)
                    .then(res=>{
                        if (res.status === 200) {
                        Swal.fire({
                            position: "center",
                            icon: "success",
                            text: "Suppression effectuer",
                            showConfirmButton: false,
                            timer: 1500
                        })
                        AllProductFunction()
                        }
                    })
            }
        })
    }

    onMounted(async ()=>{
        await loadCurrentUser()
        addmodal = new bootstrap.Modal(document.getElementById('productModal'));
        cameraModal = new bootstrap.Modal(document.getElementById('cameraModal'));
        AllProductFunction()
        AllCategoryFunction()
        AllFournisseurFunction()

        // Écoute globale du scanner USB/Bluetooth
        window.addEventListener('keydown', onGlobalKeydown)
    })

    onUnmounted(() => {
        window.removeEventListener('keydown', onGlobalKeydown)
        closeCamera()
    })

  </script>

  <style>

  </style>
