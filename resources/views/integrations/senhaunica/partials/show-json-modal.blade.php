<div x-data="{ 
    show: false, 
    content: '',
    loading: false,
    init() {
        window.openJsonModal = (id) => {
            this.open(id);
        }
    },
    async open(id) {
        this.show = true;
        this.loading = true;
        this.content = '<div class=\'p-12 text-center text-gray-500\'><i class=\'fas fa-circle-notch fa-spin mr-2\'></i> Carregando dados...</div>';
        try {
            const response = await fetch('{{ route('SenhaunicaGetJsonModalContent', ['id' => '_id_']) }}'.replace('_id_', id));
            this.content = await response.text();
        } catch (e) {
            this.content = '<div class=\'p-12 text-center text-red-500\'>Erro ao carregar dados.</div>';
        } finally {
            this.loading = false;
        }
    }
}" 
     @keydown.escape.window="show = false"
     class="relative inline-block"
     x-cloak>
     
    <template x-teleport="body">
        <div>
            <!-- Modal Backdrop -->
            <div x-show="show" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-[1050]"></div>

            <!-- Modal Content -->
            <div x-show="show"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform scale-95"
                 x-transition:enter-end="opacity-100 transform scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 transform scale-100"
                 x-transition:leave-end="opacity-0 transform scale-95"
                 class="fixed inset-0 z-[1060] overflow-y-auto px-4 py-6 sm:px-0 flex items-center justify-center pointer-events-none">
                
                <div @click.away="show = false"
                     id="jsonModal"
                     class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl overflow-hidden w-full max-w-xl transform transition-all pointer-events-auto">
                    <div class="modal-content border-none" x-html="content">
                        {{-- Conteúdo carregado via Fetch --}}
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700/50 px-6 py-4 border-t border-gray-100 dark:border-gray-700 flex justify-end">
                        <button @click="show = false" class="px-6 py-2 bg-portal text-white font-bold rounded-xl hover:bg-portal-dark transition-all">
                            Fechar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>

