<div x-data="{ show: false }" @keydown.escape.window="show = false" class="relative inline-block">
    <button @click="show = true" 
            class="flex items-center gap-2 px-4 py-2 bg-white border-2 border-portal text-portal text-sm font-bold rounded-xl hover:bg-portal hover:text-white transition-all shadow-sm">
        <i class="fas fa-plus"></i> Adicionar Usuário Local
    </button>

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
                 class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-[1050]" 
                 x-cloak></div>

            <!-- Modal Content -->
            <div x-show="show"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform scale-95"
                 x-transition:enter-end="opacity-100 transform scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 transform scale-100"
                 x-transition:leave-end="opacity-0 transform scale-95"
                 class="fixed inset-0 z-[1060] overflow-y-auto px-4 py-6 sm:px-0 flex items-center justify-center"
                 x-cloak>
                
                <div @click.stop
                     class="bg-white rounded-2xl shadow-2xl overflow-hidden w-full max-w-lg transform transition-all">
                    
                    <div class="bg-portal-gradient text-white py-4 px-6 flex items-center justify-between">
                        <h5 class="flex items-center gap-2 font-bold text-lg">
                            <i class="fas fa-user-plus"></i>
                            Adicionar Usuário Local
                        </h5>
                        <button @click="show = false" class="text-white hover:bg-white/20 rounded-lg p-2 transition-all outline-none">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    
                    <div class="p-6">
                        <form method="POST" action="{{ route(config('senhaunica.localUserRoutes') . '.store') }}" class="space-y-4">
                          @csrf
                          <div>
                            <label for="name" class="block text-sm font-semibold text-gray-700 mb-1">Nome</label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                   class="form-input block w-full px-4 py-2.5 border-2 border-gray-100 rounded-xl focus:border-portal focus:ring-4 focus:ring-portal/10 transition-all outline-none text-sm">
                          </div>

                          <div>
                            <label for="email" class="block text-sm font-semibold text-gray-700 mb-1">E-mail</label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" required
                                   class="form-input block w-full px-4 py-2.5 border-2 border-gray-100 rounded-xl focus:border-portal focus:ring-4 focus:ring-portal/10 transition-all outline-none text-sm">
                          </div>

                          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                              <label for="password" class="block text-sm font-semibold text-gray-700 mb-1">Senha</label>
                              <input type="password" name="password" id="password" required
                                     class="form-input block w-full px-4 py-2.5 border-2 border-gray-100 rounded-xl focus:border-portal focus:ring-4 focus:ring-portal/10 transition-all outline-none text-sm">
                            </div>
                            <div>
                              <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-1">Confirmação</label>
                              <input type="password" name="password_confirmation" id="password_confirmation" required
                                     class="form-input block w-full px-4 py-2.5 border-2 border-gray-100 rounded-xl focus:border-portal focus:ring-4 focus:ring-portal/10 transition-all outline-none text-sm">
                            </div>
                          </div>

                          <p class="text-xs text-gray-500 italic">* Todos os campos são obrigatórios.</p>

                          <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-3">
                            <button type="button" @click="show = false"
                                    class="px-6 py-2 border-2 border-gray-100 text-gray-500 font-bold rounded-xl hover:bg-gray-50 transition-all">
                                Cancelar
                            </button>
                            <button type="submit" 
                                    class="px-8 py-2.5 bg-portal-gradient text-white font-bold rounded-xl hover:brightness-110 transition-all shadow-md">
                                <i class="fas fa-save mr-1"></i>
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
