  <!-- Shared Block Context Menu -->
  <div id="block-menu" class="fixed z-[9999] hidden">
    <div class="bg-white rounded-2xl shadow-2xl border border-slate-100 w-64 py-2 overflow-y-auto max-h-[380px] scrollbar-thin-hidden animate-in fade-in zoom-in duration-200">
        <div class="px-3 py-2 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Transformer en</div>
        <button onclick="executeBlockAction('transform', 'h2')" class="w-full px-3 py-2 flex items-center gap-3 hover:bg-slate-50 text-slate-700 transition-colors text-sm">
            <i data-lucide="heading-2" class="h-4 w-4"></i> Titre
        </button>
        <button onclick="executeBlockAction('transform', 'text')" class="w-full px-3 py-2 flex items-center gap-3 hover:bg-slate-50 text-slate-700 transition-colors text-sm">
            <i data-lucide="type" class="h-4 w-4"></i> Texte
        </button>
        <button onclick="executeBlockAction('transform', 'todo')" class="w-full px-3 py-2 flex items-center gap-3 hover:bg-slate-50 text-slate-700 transition-colors text-sm">
            <i data-lucide="check-square" class="h-4 w-4"></i> À faire
        </button>
        <button onclick="executeBlockAction('transform', 'bullet')" class="w-full px-3 py-2 flex items-center gap-3 hover:bg-slate-50 text-slate-700 transition-colors text-sm">
            <i data-lucide="list" class="h-4 w-4"></i> Liste à puces
        </button>
        <button onclick="executeBlockAction('transform', 'quote')" class="w-full px-3 py-2 flex items-center gap-3 hover:bg-slate-50 text-slate-700 transition-colors text-sm">
            <i data-lucide="quote" class="h-4 w-4"></i> Citation
        </button>
        <button onclick="executeBlockAction('transform', 'table')" class="w-full px-3 py-2 flex items-center gap-3 hover:bg-slate-50 text-slate-700 transition-colors text-sm">
            <i data-lucide="table" class="h-4 w-4"></i> Tableau de tâches
        </button>
        <button onclick="executeBlockAction('transform', 'hr')" class="w-full px-3 py-2 flex items-center gap-3 hover:bg-slate-50 text-slate-700 transition-colors text-sm">
            <i data-lucide="minus" class="h-4 w-4"></i> Séparateur
        </button>

        <div class="h-px bg-slate-100 my-2"></div>
        <div class="px-3 py-2 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Insérer après</div>
        <button onclick="executeBlockAction('insert', 'text')" class="w-full px-3 py-2 flex items-center gap-3 hover:bg-slate-50 text-slate-700 transition-colors text-sm">
            <i data-lucide="plus" class="h-4 w-4 text-slate-300"></i> Texte
        </button>
        <button onclick="executeBlockAction('insert', 'todo')" class="w-full px-3 py-2 flex items-center gap-3 hover:bg-slate-50 text-slate-700 transition-colors text-sm">
            <i data-lucide="check-square" class="h-4 w-4 text-slate-300"></i> Liste de tâches
        </button>
        <button onclick="executeBlockAction('insert', 'file')" class="w-full px-3 py-2 flex items-center gap-3 hover:bg-slate-50 text-slate-700 transition-colors text-sm">
            <i data-lucide="paperclip" class="h-4 w-4 text-slate-300"></i> Pièce jointe
        </button>

        <div class="h-px bg-slate-100 my-2"></div>
        <button onclick="executeBlockAction('delete')" class="w-full px-3 py-2 flex items-center gap-3 hover:bg-red-50 text-red-500 transition-colors text-sm font-semibold">
            <i data-lucide="trash-2" class="h-4 w-4"></i> Supprimer
        </button>
    </div>
  </div>
<?php /**PATH C:\laragon\www\CRM\resources\views/notes/block_menu.blade.php ENDPATH**/ ?>