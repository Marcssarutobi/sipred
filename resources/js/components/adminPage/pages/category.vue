<template>
  <div class="dashboard-main-body">

    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <h6 class="fw-semibold mb-0">Liste des Catégorie</h6>
        <ul class="d-flex align-items-center gap-2">
            <li class="fw-medium">
                <RouterLink to="/" class="d-flex align-items-center gap-1 hover-text-primary">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                    Tableau de bord
                </RouterLink>
            </li>
            <li>-</li>
            <li class="fw-medium">Liste des Catégorie</li>
        </ul>
    </div>

    <div class="card">
        <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div></div>
            <button @click="showModal" class="btn btn-sm btn-primary-600"><i class="ri-add-line"></i> Ajouter une Catégorie</button>
        </div>
        <div class="card-body">
            <DataTable :data="allCategory" :columns="columns" />
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form class="modal-content" @submit.prevent="!isEdite ? AddCategoryFunction() : UpdateCategoryFunction()">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">{{ modalTitle }}</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <div class="col-lg-12">
                        <label for="nameSlideTop" class="form-label">Nom Category</label>
                        <input type="text" :class="isEmpty.name ? 'is-invalid border border-danger' : ''" v-model="data.name" class="form-control" placeholder="Nom de la catégorie" maxlength="100">
                        <div v-if="isEmpty.name" class="invalid-feedback">
                            {{ msgInput.name }}
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermé</button>
                    <button type="submit" class="btn btn-primary" :disabled="isLoader">
                        <span v-if="!isLoader">{{ modalbutton }}</span>
                        <div v-else class="spinner-border" role="status">
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
    import { onMounted, ref } from 'vue';
    import {postData, getData, getSingleData, putData, deleteData} from '../../plugins/api'

    let addmodal;

    const data = ref({
        id:'',
        name : '',
    })

    const isEmpty = ref({})
    const imagePreview = ref('')
    const msgInput = ref({})
    const isLoader = ref(false)
    const isEdite = ref(false)
    const modalTitle = ref('')
    const modalbutton = ref('')
    const allCategory = ref([])

    function showModal(){
        addmodal.show();
        data.value = {
            id:'',
            name : '',
        }
        modalTitle.value = 'Ajouter une catégorie'
        modalbutton.value = 'Enrégistrer'
        isEmpty.value = {}
        msgInput.value = {}
        isEdite.value = false
    }

    async function AllCategoryFunction() {
        await getData('/categories').then(res=>{
            if (res.status === 200) {
                allCategory.value = res.data
            }
        })
    }

    const columns = ref([
        {
            title: '#',
            data: null,
            render: function (data, type, row, meta) {
                return meta.row + 1; // Index (1-based)
            }
        },
        { title: 'Nom', data: 'name' },
        {
          title: 'Crée le', data: 'created_at', render: (data, type, row) => {
            // Formater la date
            const date = new Date(row.created_at); // Assure-toi que `created_at` est au format ISO ou timestamp
            return new Intl.DateTimeFormat('en-EN', {
              year: 'numeric',
              month: 'short',
              day: 'numeric',
              hour: '2-digit',
              minute: '2-digit',
            }).format(date); // Formater la date à la française
          }
        },

        { title: 'Action', data: null, render: (data,type,row) => {
            return `

                <a class="btn btn-primary me-1" href="#" onclick="ShowCategoryFunction(${row.id})">
                    <i class="fas fa-edit me-2"></i>
                </a>

                <a class="btn btn-danger" href="#" onclick="DeleteCategoryFunction(${row.id})">
                    <i class="fas fa-trash me-2"></i>
                </a>

            `;
            }
        }
    ])

    async function AddCategoryFunction(){
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
            await postData('/categories',data.value).then(res=>{
                if (res.status === 200) {
                    isLoader.value = false;
                    data.value = {
                        name:''
                    }
                    Swal.fire({
                        icon: 'success',
                        text: 'Catégorie ajouté avec succès',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    AllCategoryFunction()
                    addmodal.hide()
                }
            })
        }
    }

    window.ShowCategoryFunction = async function(id){
        await getSingleData('/categories/'+id).then(res=>{
            if (res.status === 200) {
                data.value = res.data
                isEdite.value = true
                modalTitle.value = 'Modifier une catégorie'
                modalbutton.value = 'Modifier'
                addmodal.show()
            }
        })
    }

    async function UpdateCategoryFunction(){
        isLoader.value = true;
        await putData('/categories/'+data.value.id,data.value).then(res=>{
            if (res.status === 200) {
                isLoader.value = false;
                data.value = {
                    name:''
                }
                isEdite.value = false
                Swal.fire({
                    icon: 'success',
                    text: 'Modification effectuer',
                    showConfirmButton: false,
                    timer: 1500
                });
                AllCategoryFunction()
                addmodal.hide()
            }
        })
    }

    window.DeleteCategoryFunction = async function (id) {
        Swal.fire({
            title: "Voulez-vous supprimez cette catégorie ?",
            text: "Vous ne pouvez plus revenir en arrière",
            icon: "warning",
            showCancelButton: true,
            cancelButtonColor: "#d33",
            confirmButtonColor: "#002D5D",
            confirmButtonText: "Delete",
            cancelButtonText: "Close"
        }).then(async (result) => {
            if (result.isConfirmed) {
                await deleteData('/categories/'+id)
                    .then(res=>{
                        if (res.status === 200) {
                            Swal.fire({
                                position: "center",
                                icon: "success",
                                text: "Suppression effectuer",
                                showConfirmButton: false,
                                timer: 1500
                            })
                            AllCategoryFunction()
                        }
                    })
            }
        })
    }

    onMounted(()=>{
        addmodal = new bootstrap.Modal(document.getElementById('categoryModal'));
        AllCategoryFunction()
    })

</script>

<style>

</style>
