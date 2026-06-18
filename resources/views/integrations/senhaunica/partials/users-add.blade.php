@push('portal-theme-head')
    <style>
        .senhaunica-loginas-btn {
            background: linear-gradient(135deg, #eab308 0%, #d97706 100%);
            color: #fff;
            box-shadow: 0 8px 20px rgba(217, 119, 6, 0.22);
        }

        .senhaunica-loginas-btn:hover {
            filter: brightness(1.05);
        }
    </style>
@endpush

<div x-data="{ open: false }"
    @keydown.escape.window="open = false">
    <button @click="open = true"
        class="inline-flex items-center gap-1 px-3 py-1.5 text-sm font-medium border border-portal text-portal dark:text-cyan-300 rounded-lg hover:bg-portal hover:text-white dark:hover:text-white transition-colors disabled:opacity-50 disabled:cursor-not-allowed senhaunicaUseraddBtn"
        @if (!hasReplicado()) disabled title="Necessário Replicado para adicionar usuários" @endif>
        <i class="fas fa-plus text-xs"></i> Adicionar
        @if (!config('senhaunica.disableLoginas'))
            / Assumir
        @endif
    </button>

    <template x-teleport="body">
        <div>
            <div x-show="open" x-cloak
                 @click="open = false"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-[1050]"></div>

            <div x-show="open" x-cloak
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform scale-95"
                 x-transition:enter-end="opacity-100 transform scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 transform scale-100"
                 x-transition:leave-end="opacity-0 transform scale-95"
                 @click.self="open = false"
                 class="fixed inset-0 z-[1060] overflow-y-auto px-4 py-6 sm:px-0 flex items-center justify-center">

                <div @click.stop
                     class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl overflow-visible w-full max-w-2xl transform transition-all">
                    <div class="bg-portal-gradient text-white py-4 px-6 flex items-center justify-between">
                        <h5 class="flex items-center gap-2 font-bold text-lg">
                            <i class="fas fa-user-plus"></i>
                            Adicionar Pessoas
                        </h5>
                        <button @click="open = false" type="button" class="text-white hover:bg-white/20 rounded-lg p-2 transition-all outline-none">
                            <span class="sr-only">Fechar</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <div class="p-6">
                        <form method="POST" action="{{ route(config('senhaunica.userRoutes') . '.store') }}" class="space-y-4">
                            @csrf
                            @include('senhaunica::components.select-pessoa', [
                                'label' => 'Pessoa',
                                'groupClass' => 'mb-0'
                            ])
                            <div class="flex flex-wrap items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                                @if (!config('senhaunica.disableLoginas'))
                                    <button type="submit" name="loginas" value="1"
                                        class="senhaunica-loginas-btn inline-flex items-center justify-center px-5 py-2.5 text-sm font-bold rounded-xl transition-all">
                                        Assumir identidade
                                    </button>
                                @endif
                                <button type="button" @click="open = false"
                                    class="inline-flex items-center justify-center px-5 py-2.5 border-2 border-gray-200 text-sm font-bold text-gray-600 dark:border-gray-600 dark:text-gray-200 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-600/80 transition-all">
                                    Cancelar
                                </button>
                                <button type="submit"
                                    class="inline-flex items-center justify-center px-6 py-2.5 bg-portal-gradient text-sm font-bold text-white rounded-xl hover:brightness-110 transition-all shadow-md shadow-portal/20">
                                    Salvar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>
