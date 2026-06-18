@if ($json = $user->hasSenhaunicaJson())
  <button class="text-gray-400 hover:text-blue-500 dark:hover:text-blue-400 transition-all p-1.5 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/30"
          onclick="openJsonModal({{ $user->id }})"
          title="Ver JSON de autenticação">
    <i class="far fa-file-code text-lg"></i>
  </button>
@else
  <span class="text-gray-300 dark:text-gray-600 text-xs">-</span>
@endif
