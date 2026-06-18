<div x-data="{ 
    show: false, 
    loading: false,
    userName: '',
    action: '',
    env: null,
    vinculoNs: '',
    permissoesVinculo: [],
    permissoesHierarquia: [],
    hierarquiaNs: '',
    appNs: '',
    userPermissions: [],
    userRoles: [],
    vinculoPermissionsString: '',
    
    init() {
        window.openPermissionsModal = (route) => {
            this.open(route);
        }
        document.addEventListener('click', (e) => {
            const btn = e.target.closest('[data-open-permissions]');
            if (btn && typeof window.openPermissionsModal === 'function') {
                e.preventDefault();
                window.openPermissionsModal(btn.getAttribute('data-open-permissions'));
            }
        });
    },

    async open(route) {
        this.show = true;
        this.loading = true;
        this.action = route;
        
        try {
            const response = await fetch(route);
            const user = await response.json();
            
            this.userName = user.name;
            this.env = user.env;
            this.vinculoNs = user.vinculoNs;
            this.permissoesVinculo = user.permissoesVinculo;
            this.permissoesHierarquia = user.permissoesHierarquia;
            this.hierarquiaNs = user.hierarquiaNs;
            this.appNs = '{{ \App\Models\User::$appNs }}';
            this.userPermissions = user.permissions.map(p => ({ name: p.name, guard: p.guard_name }));
            this.userRoles = user.roles.map(r => ({ name: r.name, guard: r.guard_name }));
            
            // Format vículo permissions string
            let vPermissions = [];
            this.userPermissions.forEach(p => {
                if (p.guard === this.vinculoNs && this.permissoesVinculo.includes(p.name.split('.')[0])) {
                    vPermissions.push(p.name);
                }
            });
            this.vinculoPermissionsString = vPermissions.join(', ') || 'Nenhuma';

        } catch (e) {
            console.error('Erro ao carregar permissões:', e);
        } finally {
            this.loading = false;
        }
    },

    hasPermission(name, guard) {
        return this.userPermissions.some(p => p.name === name && p.guard === guard);
    },

    hasRole(name, guard) {
        return this.userRoles.some(r => r.name === name && r.guard === guard);
    }
}" 
     @keydown.escape.window="show = false"
     class="relative inline-block"
     x-cloak>
     
    <template x-teleport="body">
        <div>
            <!-- Modal Backdrop -->
            <div x-show="show" 
                 @click="show = false"
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
                 @click.self="show = false"
                 class="fixed inset-0 z-[1060] overflow-y-auto px-4 py-6 sm:px-0 flex items-center justify-center">
                
                <div id="senhaunica-users-permission-modal"
                     @click.stop
                     class="bg-white rounded-2xl shadow-2xl overflow-hidden w-full max-w-4xl transform transition-all">
                    
                    <div class="bg-portal-gradient text-white py-4 px-6 flex items-center justify-between">
                        <h5 class="flex items-center gap-2 font-bold text-lg">
                            <i class="fas fa-user-shield"></i>
                            Permissões de <span class="name font-black underline decoration-white/30 truncate max-w-[200px]" x-text="userName"></span>
                        </h5>
                        <button @click="show = false" class="text-white hover:bg-white/20 rounded-lg p-2 transition-all outline-none">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>

                    <div class="p-6 relative">
                        <div x-show="loading" class="absolute inset-0 bg-white/50 backdrop-blur-[1px] z-10 flex items-center justify-center">
                            <div class="text-portal flex flex-col items-center gap-2">
                                <i class="fas fa-circle-notch fa-spin text-3xl"></i>
                                <span class="font-bold">Carregando...</span>
                            </div>
                        </div>

                        <form method="POST" :action="action">
                            @csrf
                            @method('PUT')
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                                    <div class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4">Permissões da Aplicação</div>
                                    <div class="space-y-2">
                                        @foreach($permissoesAplicacao as $p)
                                            <label class="flex items-center gap-3 p-2 hover:bg-white rounded-lg transition-all cursor-pointer border border-transparent hover:border-gray-100">
                                                <input type="checkbox" name="permission_app[]" value="{{ $p->name }}" 
                                                       :checked="hasPermission('{{ $p->name }}', '{{ \App\Models\User::$appNs }}')"
                                                       class="form-checkbox w-5 h-5 text-portal border-gray-300 rounded focus:ring-portal">
                                                <span class="text-sm font-medium text-gray-700">{{ $p->name }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                                    <div class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4">Roles da Aplicação</div>
                                    <div class="space-y-2">
                                        @foreach($rolesAplicacao as $r)
                                            <label class="flex items-center gap-3 p-2 hover:bg-white rounded-lg transition-all cursor-pointer border border-transparent hover:border-gray-100">
                                                <input type="checkbox" name="role_app[]" value="{{ $r->name }}" 
                                                       :checked="hasRole('{{ $r->name }}', '{{ \App\Models\User::$appNs }}')"
                                                       class="form-checkbox w-5 h-5 text-portal border-gray-300 rounded focus:ring-portal">
                                                <span class="text-sm font-medium text-gray-700">{{ $r->name }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="flex flex-col gap-4">
                                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                                        <div class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4">Permissão Hierárquica</div>
                                        <div class="permissao-hierarquica">
                                            <template x-if="env">
                                                <div class="p-2 bg-red-50 border border-red-100 rounded-lg">
                                                    <span class="text-red-700 text-xs font-bold" x-text="env + ' (env)'"></span>
                                                </div>
                                            </template>
                                            <template x-if="!env">
                                                <div class="space-y-2">
                                                    @foreach(App\Models\User::$permissoesHierarquia as $p)
                                                        <label class="flex items-center gap-3 p-2 hover:bg-white rounded-lg transition-all cursor-pointer border border-transparent hover:border-gray-100">
                                                            <input type="radio" name="level" value="{{ $p }}" 
                                                                   :checked="hasPermission('{{ $p }}', '{{ \App\Models\User::$hierarquiaNs }}')"
                                                                   class="form-radio w-5 h-5 text-portal border-gray-300 focus:ring-portal">
                                                            <span class="text-sm font-medium text-gray-700">{{ $p }}</span>
                                                        </label>
                                                    @endforeach
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                                        <div class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4">Vínculos (Replicado)</div>
                                        <div class="permissoes-vinculo text-sm text-gray-600 font-medium bg-white p-3 rounded-lg border border-gray-100" x-text="vinculoPermissionsString">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-8 pt-6 border-t border-gray-100 flex items-center justify-end gap-3">
                                <button type="button" @click="show = false"
                                        class="px-6 py-2.5 border-2 border-gray-100 text-gray-500 font-bold rounded-xl hover:bg-gray-50 transition-all">
                                    Cancelar
                                </button>
                                <button type="submit" 
                                        class="px-8 py-2.5 bg-portal-gradient text-white font-bold rounded-xl hover:brightness-110 transition-all shadow-md">
                                    <i class="fas fa-save mr-1"></i>
                                    Salvar Alterações
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>
