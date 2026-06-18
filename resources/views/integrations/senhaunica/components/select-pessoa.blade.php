@props([
    'prepend' => '',
    'append' => '',
    'label' => '',
    'groupClass' => '',
    'class' => '',
    'id' => 'select-' . mt_rand(1000000, 9999999),
])

<div x-data="{ 
    open: false,
    search: '',
    results: [],
    selectedText: 'Digite o nome ou número USP..',
    selectedId: '',
    loading: false,
    
    async fetchResults() {
        if (this.search.length < 4) {
            this.results = [];
            return;
        }
        
        this.loading = true;
        try {
            const response = await fetch('{{ route('SenhaunicaFindUsers') }}?term=' + encodeURIComponent(this.search));
            const data = await response.json();
            this.results = data.results || [];
        } catch (e) {
            console.error('Erro ao buscar pessoas:', e);
        } finally {
            this.loading = false;
        }
    },
    
    select(result) {
        this.selectedText = result.text;
        this.selectedId = result.id;
        this.open = false;
        this.search = '';
    },

    reset() {
        this.selectedText = 'Digite o nome ou número USP..';
        this.selectedId = '';
        this.search = '';
        this.results = [];
    }
}" 
     class="senhaunica-select-pessoa mb-4 {{ $groupClass }} relative">
  
  @if ($label)
    <label for="{{ $id }}" class="block text-sm font-semibold text-gray-700 mb-1 border-none">{{ $label }}</label>
  @endif
  
  <div class="relative flex items-stretch">
    @if ($prepend)
      <div class="flex items-center px-3 bg-gray-50 border-2 border-r-0 border-gray-100 rounded-l-xl text-gray-500 text-sm">
        {!! $prepend !!}
      </div>
    @endif

    <div class="relative flex-1">
      <!-- Hidden input for form submission -->
      <input type="hidden" name="codpes" :value="selectedId">
      
      <button id="{{ $id }}" type="button" @click="open = !open; if(open) $nextTick(() => $refs.searchInput.focus())"
              class="group block w-full text-left px-4 py-3 bg-white border-2 border-gray-100 {{ $prepend ? 'rounded-r-none' : 'rounded-l-xl' }} {{ $append ? 'rounded-l-none' : 'rounded-r-xl' }} focus:border-portal focus:ring-4 focus:ring-portal/10 transition-all outline-none text-sm shadow-sm hover:border-portal/40 hover:shadow-md {{ $class }}"
              {{ $attributes ?? '' }}
              :class="selectedId ? 'text-gray-900' : 'text-gray-500'">
        <span class="flex items-center gap-3 pr-8">
          <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-portal/10 text-portal">
            <i class="fas fa-user text-sm"></i>
          </span>
          <span class="min-w-0 flex-1">
            <span class="block text-[11px] font-black uppercase tracking-[0.16em] text-gray-400">Busca de pessoa</span>
            <span class="block truncate font-medium" x-text="selectedText"></span>
          </span>
        </span>
        <span class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-gray-400 transition-transform duration-200"
              :class="open ? 'rotate-180' : ''">
          <i class="fas fa-chevron-down text-xs"></i>
        </span>
      </button>

      <!-- Dropdown -->
      <div x-show="open" @click.away="open = false" 
           x-transition:enter="transition ease-out duration-100"
           x-transition:enter-start="opacity-0 transform scale-95"
           x-transition:enter-end="opacity-100 transform scale-100"
           x-transition:leave="transition ease-in duration-75"
           x-transition:leave-start="opacity-100 transform scale-100"
           x-transition:leave-end="opacity-0 transform scale-95"
           class="absolute z-[1100] mt-2 w-full overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-2xl ring-1 ring-black/5">
        
        <div class="border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white p-3">
          <div class="mb-2 flex items-center justify-between gap-3">
            <span class="text-[11px] font-black uppercase tracking-[0.16em] text-gray-400">Digite para buscar</span>
            <button x-show="selectedId" type="button" @click="reset()"
                    class="text-[11px] font-bold text-portal hover:text-portal-dark transition-colors">
              Limpar
            </button>
          </div>
          <div class="relative">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
            <input x-ref="searchInput" type="text" x-model="search" @input.debounce.500ms="fetchResults()"
                   placeholder="Mínimo 4 caracteres..."
                   class="form-input w-full rounded-xl border-0 bg-gray-100 pl-9 pr-4 py-2.5 text-sm focus:bg-white focus:ring-2 focus:ring-portal/20 outline-none">
          </div>
        </div>

        <div class="max-h-72 overflow-y-scroll border-t border-gray-100 scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100">
          <template x-if="loading">
            <div class="p-5 text-center text-xs italic text-gray-500">
              <i class="fas fa-circle-notch fa-spin mr-2"></i> Buscando...
            </div>
          </template>

          <template x-if="!loading && results.length > 0">
            <div class="p-2">
              <template x-for="result in results" :key="result.id">
                <button type="button" @click="select(result)"
                        class="mb-1 flex w-full items-start gap-3 rounded-xl px-3 py-3 text-left text-sm text-gray-700 transition-colors hover:bg-portal/8 focus:bg-portal/8 last:mb-0">
                  <span class="mt-0.5 inline-flex shrink-0 rounded-lg bg-portal/10 px-2 py-1 text-[11px] font-black tracking-wide text-portal" x-text="result.id"></span>
                  <span class="min-w-0 flex-1">
                    <span class="block font-semibold text-gray-900" x-text="result.text.replace(result.id, '').trim()"></span>
                    <span class="mt-0.5 block text-[11px] font-medium uppercase tracking-wide text-gray-400">Selecionar pessoa</span>
                  </span>
                </button>
              </template>
            </div>
          </template>

          <template x-if="!loading && search.length >= 4 && results.length === 0">
            <div class="p-5 text-center text-xs text-gray-500">
              Nenhuma pessoa encontrada.
            </div>
          </template>

          <template x-if="search.length < 4">
            <div class="p-5 text-center text-[10px] font-bold uppercase tracking-widest text-gray-400">
              Digite pelo menos 4 caracteres
            </div>
          </template>
        </div>
      </div>
    </div>

    @if (isset($slot) && (string)$slot !== '')
      {{ $slot }}
    @endif

    @if ($append)
      <div class="flex items-center px-3 bg-gray-50 border-2 border-l-0 border-gray-100 rounded-r-xl text-gray-500 text-sm">
        {!! $append !!}
      </div>
    @endif
  </div>

  @error('codpes') <span class="block mt-1 text-xs text-red-600 font-medium">{{ $message }}</span> @enderror
</div>
