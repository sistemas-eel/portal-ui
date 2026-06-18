<div x-data="{ 
    show: false,
    name: '',
    email: '',
    action: '',
    changePassword: false,
    init() {
        window.openLocalUserEdit = (data) => {
            this.name = data.name;
            this.email = data.email;
            this.action = data.action;
            this.changePassword = false;
            this.show = true;
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
                     class="bg-white rounded-2xl shadow-2xl overflow-hidden w-full max-w-lg transform transition-all pointer-events-auto">
                    
                    <div class="bg-portal-gradient text-white py-4 px-6 flex items-center justify-between">
                        <h5 class="flex items-center gap-2 font-bold text-lg">
                            <i class="fas fa-user-edit"></i>
                            Alterar Usuário Local
                        </h5>
                        <button @click="show = false" class="text-white hover:bg-white/20 rounded-lg p-2 transition-all outline-none">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <div class="modal-body p-6">
                        <form method="POST" :action="action" class="space-y-4">
                          @csrf
                          @method('PUT')
                          
                          <div>
                            <label for="user_name" class="block text-sm font-semibold text-gray-700 mb-1">Nome</label>
                            <input type="text" id="user_name" name="name" x-model="name" required
                                   class="form-input block w-full px-4 py-2.5 bg-gray-100 border-2 border-gray-100 rounded-xl focus:border-portal focus:ring-4 focus:ring-portal/10 transition-all outline-none text-sm disabled:cursor-not-allowed">
                          </div>

                          <div>
                            <label for="user_email" class="block text-sm font-semibold text-gray-700 mb-1">E-mail</label>
                            <input type="email" id="user_email" name="email" x-model="email" required
                                   class="form-input block w-full px-4 py-2.5 bg-gray-100 border-2 border-gray-100 rounded-xl focus:border-portal focus:ring-4 focus:ring-portal/10 transition-all outline-none text-sm disabled:cursor-not-allowed">
                          </div>

                          <div class="bg-gray-50 p-4 rounded-xl space-y-4 border border-gray-100 mt-2">
                            <div class="flex items-center gap-3">
                              <input type="checkbox" id="senha" name="senha" value="1" x-model="changePassword"
                                     class="form-checkbox w-5 h-5 text-portal border-gray-300 rounded focus:ring-portal">
                              <label class="text-sm font-bold text-gray-700" for="senha">Alterar Senha</label>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                              <div>
                                <label for="edit_password" class="block text-sm font-semibold text-gray-700 mb-1">Nova Senha</label>
                                <input type="password" name="password" id="edit_password" :readonly="!changePassword"
                                       :class="!changePassword ? 'bg-gray-100' : 'bg-white'"
                                       class="form-input block w-full px-4 py-2.5 border-2 border-gray-100 rounded-xl focus:border-portal focus:ring-4 focus:ring-portal/10 transition-all outline-none text-sm disabled:cursor-not-allowed">
                              </div>
                              <div>
                                <label for="edit_password_confirmation" class="block text-sm font-semibold text-gray-700 mb-1">Confirmação</label>
                                <input type="password" name="password_confirmation" id="edit_password_confirmation" :readonly="!changePassword"
                                       :class="!changePassword ? 'bg-gray-100' : 'bg-white'"
                                       class="form-input block w-full px-4 py-2.5 border-2 border-gray-100 rounded-xl focus:border-portal focus:ring-4 focus:ring-portal/10 transition-all outline-none text-sm disabled:cursor-not-allowed">
                              </div>
                            </div>
                          </div>

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
