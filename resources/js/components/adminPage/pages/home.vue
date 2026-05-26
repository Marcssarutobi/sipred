<template>
    <div class="dashboard-main-body">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
            <div>
                <h6 class="fw-semibold mb-0">Dashboard</h6>
                <p class="text-secondary-light mb-0">Vue generale des ventes, stocks et produits performants.</p>
            </div>
            <ul class="d-flex align-items-center gap-2 mb-0">
                <li class="fw-medium">
                    <RouterLink to="/" class="d-flex align-items-center gap-1 hover-text-primary">
                        <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                        Tableau de bord
                    </RouterLink>
                </li>
                <li>-</li>
                <li class="fw-medium">Accueil</li>
            </ul>
        </div>

        <div v-if="loadError" class="alert alert-danger d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
            <span>{{ loadError }}</span>
            <button type="button" class="btn btn-sm btn-outline-danger" @click="loadDashboard">
                Reessayer
            </button>
        </div>

        <div v-if="isLoading" class="card border-0 shadow-sm mb-24">
            <div class="card-body p-32 text-center">
                <div class="spinner-border text-primary" role="status"></div>
                <p class="text-secondary-light mb-0 mt-3">Chargement du dashboard...</p>
            </div>
        </div>

        <template v-else>
            <div class="row g-3 mb-24">
                <div v-for="card in summaryCards" :key="card.key" class="col-xxl-2 col-xl-4 col-sm-6">
                    <div class="card border-0 shadow-sm h-100 dashboard-kpi-card">
                        <div class="card-body p-20">
                            <div class="d-flex align-items-start justify-content-between gap-3">
                                <div>
                                    <p class="text-secondary-light mb-1">{{ card.label }}</p>
                                    <h5 class="fw-bold mb-1">{{ card.value }}</h5>
                                    <span class="text-xs text-secondary-light">{{ card.caption }}</span>
                                </div>
                                <div class="dashboard-kpi-icon" :style="{ background: card.iconBg, color: card.iconColor }">
                                    <iconify-icon :icon="card.icon"></iconify-icon>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4 mb-24">
                <div class="col-xxl-8">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-24">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-20">
                                <div>
                                    <h6 class="mb-1">ProduitVenteStats - {{ chartSeasonLabel }}</h6>
                                    <p class="text-secondary-light mb-0">
                                        {{ summary.paid_sales_count }} ventes payees et {{ summary.unpaid_sales_count }} ventes impayees.
                                    </p>
                                </div>
                                <span class="badge dashboard-source-badge">
                                    {{ chartSourceLabel }}
                                </span>
                            </div>

                            <div v-if="chartHasData">
                                <apexchart
                                    :key="chartRenderKey"
                                    type="bar"
                                    width="100%"
                                    height="360"
                                    :options="chartOptions"
                                    :series="chartSeries"
                                />
                            </div>
                            <div v-else class="dashboard-empty-state">
                                Aucune vente analysee pour cette saison.
                            </div>

                            
                        </div>
                    </div>
                </div>

                <div class="col-xxl-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-24">
                            <div class="d-flex align-items-center justify-content-between gap-3 mb-16">
                                <div>
                                    <h6 class="mb-1">Stocks critiques</h6>
                                    <p class="text-secondary-light mb-0">Produits a surveiller rapidement.</p>
                                </div>
                                <span class="badge text-bg-light">{{ lowStockProducts.length }}</span>
                            </div>

                            <div v-if="lowStockProducts.length" class="d-flex flex-column gap-16">
                                <div
                                    v-for="product in lowStockProducts"
                                    :key="product.id"
                                    class="dashboard-list-item"
                                >
                                    <div>
                                        <p class="fw-semibold mb-1">{{ product.nom }}</p>
                                        <span class="text-secondary-light text-sm">
                                            {{ product.category?.name || 'Sans categorie' }}
                                        </span>
                                    </div>
                                    <div class="text-end">
                                        <span
                                            class="badge"
                                            :class="product.quantite === 0 ? 'bg-danger' : 'bg-warning text-dark'"
                                        >
                                            {{ product.quantite }} en stock
                                        </span>
                                        <div class="text-secondary-light text-xs mt-1">
                                            Seuil: {{ product.seuil_alerte }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="dashboard-empty-state">
                                Aucun stock critique pour le moment.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-xxl-5">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-24">
                            <div class="d-flex align-items-center justify-content-between gap-3 mb-16">
                                <div>
                                    <h6 class="mb-1">Top produits vendus</h6>
                                    <p class="text-secondary-light mb-0">Classement detaille de la saison.</p>
                                </div>
                                <span class="badge text-bg-light">{{ topProducts.length }}</span>
                            </div>

                            <div v-if="topProducts.length" class="d-flex flex-column gap-14">
                                <div v-for="product in topProducts" :key="`${product.product_id}-${product.rang}`" class="dashboard-list-item">
                                    <div class="d-flex align-items-center gap-12">
                                        <span class="dashboard-rank">{{ product.rang }}</span>
                                        <div>
                                            <p class="fw-semibold mb-1">{{ product.produit_nom }}</p>
                                            <span class="text-secondary-light text-sm">
                                                {{ product.total_quantite_vendue }} unites - {{ product.nombre_ventes }} vente(s)
                                            </span>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <p class="fw-semibold mb-1">{{ formatCurrency(product.total_chiffre_affaires) }}</p>
                                        <span class="text-secondary-light text-xs">
                                            Stock: {{ product.stock_actuel }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="dashboard-empty-state">
                                Les statistiques des produits vendus seront affichees ici.
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xxl-7">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-24">
                            <div class="d-flex align-items-center justify-content-between gap-3 mb-16">
                                <div>
                                    <h6 class="mb-1">Dernieres ventes</h6>
                                    <p class="text-secondary-light mb-0">Les operations les plus recentes de la caisse.</p>
                                </div>
                                <RouterLink to="/vente" class="btn btn-sm btn-outline-primary">
                                    Voir les ventes
                                </RouterLink>
                            </div>

                            <div v-if="recentSales.length" class="table-responsive">
                                <table class="table bordered-table mb-0">
                                    <thead>
                                        <tr>
                                            <th>Reference</th>
                                            <th>Operateur</th>
                                            <th>Date</th>
                                            <th>Montant</th>
                                            <th>Statut</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="sale in recentSales" :key="sale.id">
                                            <td class="fw-semibold">{{ sale.reference }}</td>
                                            <td>{{ sale.user?.fullname || '-' }}</td>
                                            <td>{{ formatDate(sale.date) }}</td>
                                            <td>{{ formatCurrency(sale.montant_total) }}</td>
                                            <td>
                                                <span
                                                    class="badge"
                                                    :class="sale.status === 'paye' ? 'bg-success' : 'bg-danger'"
                                                >
                                                    {{ sale.status === 'paye' ? 'Payee' : 'Impayee' }}
                                                </span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div v-else class="dashboard-empty-state">
                                Aucune vente enregistree pour le moment.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
</template>

<script setup>
import { computed, onMounted, ref, watch } from 'vue';
import { getData } from '../../plugins/api';

const isLoading = ref(true);
const loadError = ref('');
const summary = ref({
    products_count: 0,
    categories_count: 0,
    sales_count: 0,
    revenue: 0,
    paid_sales_count: 0,
    unpaid_sales_count: 0,
    low_stock_count: 0,
    pending_supplies_count: 0,
});
const produitVenteStats = ref({
    source: 'live_fallback',
    saison: '',
    categories: [],
    series: [{ name: 'Quantite vendue', data: [] }],
    items: [],
});
const lowStockProducts = ref([]);
const recentSales = ref([]);

const currencyFormatter = new Intl.NumberFormat('fr-FR', {
    style: 'currency',
    currency: 'XOF',
});

const summaryCards = computed(() => [
    {
        key: 'products',
        label: 'Produits',
        value: formatNumber(summary.value.products_count),
        caption: 'Articles disponibles',
        icon: 'solar:box-bold',
        iconBg: '#e7f1ff',
        iconColor: '#0d6efd',
    },
    {
        key: 'categories',
        label: 'Categories',
        value: formatNumber(summary.value.categories_count),
        caption: 'Familles de produits',
        icon: 'solar:layers-bold',
        iconBg: '#f3e8ff',
        iconColor: '#7c3aed',
    },
    {
        key: 'sales',
        label: 'Ventes',
        value: formatNumber(summary.value.sales_count),
        caption: 'Operations enregistrees',
        icon: 'solar:cart-large-4-bold',
        iconBg: '#e8fff3',
        iconColor: '#16a34a',
    },
    {
        key: 'revenue',
        label: "Chiffre d'affaires",
        value: formatCurrency(summary.value.revenue),
        caption: 'Montant cumule',
        icon: 'solar:wallet-money-bold',
        iconBg: '#fff4e5',
        iconColor: '#ea580c',
    },
    {
        key: 'low-stock',
        label: 'Stocks critiques',
        value: formatNumber(summary.value.low_stock_count),
        caption: 'A traiter rapidement',
        icon: 'solar:danger-triangle-bold',
        iconBg: '#ffe5e5',
        iconColor: '#dc2626',
    },
    {
        key: 'supplies',
        label: 'Appro en attente',
        value: formatNumber(summary.value.pending_supplies_count),
        caption: 'Bons non livres',
        icon: 'solar:inbox-archive-bold',
        iconBg: '#ecfeff',
        iconColor: '#0891b2',
    },
]);

const topProducts = computed(() => produitVenteStats.value.items || []);
const chartSeries = computed(() => produitVenteStats.value.series || [{ name: 'Quantite vendue', data: [] }]);
const chartHasData = computed(() => chartSeries.value[0]?.data?.length > 0);
const chartSeasonLabel = computed(() => produitVenteStats.value.saison || 'Saison courante');
const chartSourceLabel = computed(() =>
    produitVenteStats.value.source === 'produit_vente_stats'
        ? 'Analyse enregistree'
        : 'Donnees live'
);
const chartRenderKey = computed(() => JSON.stringify({
    categories: produitVenteStats.value.categories || [],
    series: chartSeries.value,
}));
const chartDebug = computed(() => JSON.stringify({
    chartHasData: chartHasData.value,
    source: produitVenteStats.value.source,
    saison: produitVenteStats.value.saison,
    categories: produitVenteStats.value.categories,
    series: chartSeries.value,
    itemsCount: topProducts.value.length,
}, null, 2));

const chartOptions = computed(() => ({
    chart: {
        id: 'produit-vente-stats-chart',
        toolbar: {
            show: false,
        },
        animations: {
            easing: 'easeinout',
            speed: 500,
        },
        fontFamily: 'inherit',
    },
    colors: ['#0d6efd', '#20c997', '#fd7e14', '#6f42c1', '#dc3545', '#198754'],
    plotOptions: {
        bar: {
            horizontal: true,
            borderRadius: 6,
            borderRadiusApplication: 'end',
            distributed: true,
            barHeight: '58%',
        },
    },
    dataLabels: {
        enabled: true,
        formatter: (value) => formatNumber(value),
        offsetX: 18,
        style: {
            fontSize: '12px',
            colors: ['#6b7280'],
        },
    },
    grid: {
        borderColor: '#e5e7eb',
        strokeDashArray: 4,
        xaxis: {
            lines: {
                show: true,
            },
        },
    },
    legend: {
        show: false,
    },
    xaxis: {
        categories: produitVenteStats.value.categories || [],
        labels: {
            formatter: (value) => formatNumber(value),
        },
    },
    yaxis: {
        labels: {
            style: {
                fontSize: '12px',
            },
        },
    },
    tooltip: {
        y: {
            formatter: (value, { dataPointIndex }) => {
                const product = topProducts.value[dataPointIndex];

                if (!product) {
                    return `${formatNumber(value)} unites`;
                }

                return `${formatNumber(value)} unites - ${formatCurrency(product.total_chiffre_affaires)}`;
            },
        },
    },
}));

function formatCurrency(value) {
    return currencyFormatter.format(Number(value || 0));
}

function formatNumber(value) {
    return new Intl.NumberFormat('fr-FR').format(Number(value || 0));
}

function formatDate(value) {
    if (!value) {
        return '-';
    }

    return new Intl.DateTimeFormat('fr-FR', { dateStyle: 'medium' }).format(new Date(value));
}

async function loadDashboard() {
    isLoading.value = true;
    loadError.value = '';

    try {
        const response = await getData('/dashboard');

        if (response.status === 200) {
            summary.value = response.data.summary || summary.value;
            produitVenteStats.value = response.data.produit_vente_stats || produitVenteStats.value;
            lowStockProducts.value = response.data.low_stock_products || [];
            recentSales.value = response.data.recent_sales || [];
        }
    } catch (error) {
        loadError.value = error.response?.data?.message || 'Impossible de charger le dashboard.';
    } finally {
        isLoading.value = false;
    }
}

onMounted(() => {
    loadDashboard();
});

watch(
    () => ({
        chartHasData: chartHasData.value,
        categories: produitVenteStats.value.categories,
        series: chartSeries.value,
    }),
    (value) => {
        console.log('[Dashboard][ApexChart]', value);
    },
    { deep: true }
);
</script>

<style scoped>
.dashboard-kpi-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.dashboard-kpi-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 14px 34px rgba(15, 23, 42, 0.08) !important;
}

.dashboard-kpi-icon {
    width: 48px;
    height: 48px;
    border-radius: 14px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    flex-shrink: 0;
}

.dashboard-source-badge {
    background: #eef6ff;
    color: #0d6efd;
    font-weight: 600;
    padding: 8px 12px;
}

.dashboard-list-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding: 14px 16px;
    border: 1px solid #edf2f7;
    border-radius: 14px;
    background: #fbfdff;
}

.dashboard-rank {
    width: 34px;
    height: 34px;
    border-radius: 999px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #0d6efd;
    color: #fff;
    font-weight: 700;
    flex-shrink: 0;
}

.dashboard-empty-state {
    min-height: 220px;
    border: 1px dashed #dbe3ef;
    border-radius: 16px;
    background: #fbfcff;
    color: #6b7280;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    padding: 24px;
}

.dashboard-debug-box {
    background: #0f172a;
    border-radius: 12px;
    color: #e2e8f0;
    padding: 14px 16px;
}

.dashboard-debug-pre {
    color: #cbd5e1;
    font-size: 12px;
    line-height: 1.5;
    white-space: pre-wrap;
    word-break: break-word;
}
</style>
