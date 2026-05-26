import './bootstrap';
import { createApp, nextTick } from 'vue';
import VueApexCharts from 'vue3-apexcharts';
import App from './components/App.vue';
import router from './components/router';

const templateScripts = [
    // Core
    '/assets/js/lib/jquery-3.7.1.min.js',
    '/assets/js/lib/bootstrap.bundle.min.js',
  
    // DataTables
    '/assets/js/lib/dataTables.min.js',
  
    // Icons
    '/assets/js/lib/iconify-icon.min.js',
  
    // jQuery UI
    '/assets/js/lib/jquery-ui.min.js',
  
    // Vector Map
    '/assets/js/lib/jquery-jvectormap-2.0.5.min.js',
    '/assets/js/lib/jquery-jvectormap-world-mill-en.js',
  
    // Popup
    '/assets/js/lib/magnifc-popup.min.js',
  
    // Slick Slider
    '/assets/js/lib/slick.min.js',
  
    // Prism (syntax highlighting)
    '/assets/js/lib/prism.js',
  
    // File Upload
    '/assets/js/lib/file-upload.js',
  
    // Audio Player
    '/assets/js/lib/audioplayer.js',
  
    // Script principal du template (fichier existant)
    '/assets/js/app.js',
  
    // Dashboard charts
  ];
  
  // Charge un script une seule fois
  function loadScript(src) {
    return new Promise((resolve) => {
      if (document.querySelector(`script[src="${src}"]`)) {
        resolve();
        return;
      }
  
      const script = document.createElement('script');
      script.src = src;
      script.onload = resolve;
      script.onerror = () => {
        console.warn(`[app.js] Échec du chargement : ${src}`);
        resolve();
      };
      document.body.appendChild(script);
    });
  }
  
  // Charge tous les scripts dans l'ordre
  async function loadAllScripts() {
    for (const src of templateScripts) {
      await loadScript(src);
    }
  }
  
  // Réinitialise les libs après chaque navigation
  function reinitScripts() {
    // Bootstrap tooltips & popovers
    if (window.bootstrap) {
      document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
        new bootstrap.Tooltip(el);
      });
      document.querySelectorAll('[data-bs-toggle="popover"]').forEach(el => {
        new bootstrap.Popover(el);
      });
    }
  
    // Bootstrap Select
    if (window.$ && $.fn.selectpicker) {
      $('.selectpicker').selectpicker('refresh');
    }
  
    // DataTables
    if (window.$ && $.fn.DataTable) {
      $('table[data-datatable]').each(function () {
        if (!$.fn.DataTable.isDataTable(this)) {
          $(this).DataTable();
        }
      });
    }
  
    // Slick Slider
    if (window.$ && $.fn.slick) {
      $('.slick-slider-init:not(.slick-initialized)').slick({
        // adapte les options selon ton template
      });
    }
  
    // Vector Map
    if (window.$ && $.fn.vectorMap) {
      $('#world-map').vectorMap({
        map: 'world_mill_en',
        // adapte les options selon ton template
      });
    }
  
    // Magnific Popup
    if (window.$ && $.fn.magnificPopup) {
      $('[data-magnific-popup]').magnificPopup({
        type: 'image',
      });
    }
  
    // Prism syntax highlighting
    if (window.Prism) {
      Prism.highlightAll();
    }
  
    // Iconify
    if (window.iconify) {
      iconify.scan();
    }
  
    // Charts homepage — réinitialise si la fonction est exposée globalement
    // Script principal du template
    if (window.initTemplate) initTemplate();
  }
  
  // Chargement initial
  loadAllScripts().then(() => {
    reinitScripts();
  });
  
  // Réinitialisation à chaque changement de route
  router.afterEach(() => {
    nextTick(() => {
      reinitScripts();
    });
  });

const app = createApp(App);
app.use(router);
app.use(VueApexCharts);
app.mount('#app');
