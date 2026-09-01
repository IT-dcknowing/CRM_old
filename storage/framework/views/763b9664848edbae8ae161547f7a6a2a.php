<?php $__env->startPush('styles'); ?>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
  <script>
    tailwind.config = {
      darkMode: ['selector', '.dark-theme'],
      theme: {
        extend: {
          colors: {
            background: "oklch(var(--background) / <alpha-value>)",
            foreground: "oklch(var(--foreground) / <alpha-value>)",
            card: "oklch(var(--card) / <alpha-value>)",
            "card-foreground": "oklch(var(--card-foreground) / <alpha-value>)",
            popover: "oklch(var(--popover) / <alpha-value>)",
            "popover-foreground": "oklch(var(--popover-foreground) / <alpha-value>)",
            primary: "oklch(var(--primary) / <alpha-value>)",
            "primary-foreground": "oklch(var(--primary-foreground) / <alpha-value>)",
            secondary: "oklch(var(--secondary) / <alpha-value>)",
            "secondary-foreground": "oklch(var(--secondary-foreground) / <alpha-value>)",
            muted: "oklch(var(--muted) / <alpha-value>)",
            "muted-foreground": "oklch(var(--muted-foreground) / <alpha-value>)",
            accent: "oklch(var(--accent) / <alpha-value>)",
            "accent-foreground": "oklch(var(--accent-foreground) / <alpha-value>)",
            destructive: "oklch(var(--destructive) / <alpha-value>)",
            "destructive-foreground": "oklch(var(--destructive-foreground) / <alpha-value>)",
            border: "oklch(var(--border) / <alpha-value>)",
            input: "oklch(var(--input) / <alpha-value>)",
            ring: "oklch(var(--ring) / <alpha-value>)",
          },
          borderRadius: {
            lg: "var(--radius)",
            md: "calc(var(--radius) - 2px)",
            sm: "calc(var(--radius) - 4px)",
          }
        }
      }
    }
  </script>
  <style>
    :root {
      --background: 1 0 0;
      --foreground: 0.18 0.02 260;
      --card: 1 0 0;
      --card-foreground: 0.18 0.02 260;
      --popover: 1 0 0;
      --popover-foreground: 0.18 0.02 260;
      --primary: 0.22 0.04 265;
      --primary-foreground: 1 0 0;
      --secondary: 1 0 0;
      --secondary-foreground: 0.22 0.04 265;
      --muted: 0.98 0.005 240;
      --muted-foreground: 0.52 0.02 260;
      --accent: 0.97 0.002 247;
      --accent-foreground: 0.22 0.04 265;
      --destructive: 0.62 0.22 27;
      --destructive-foreground: 1 0 0;
      --border: 0.93 0.003 247;
      --input: 0.93 0.003 247;
      --ring: 0.22 0.04 265;
      --radius: 0.625rem;
    }

    .dark-theme {
      --background: 0.22 0.02 260;
      --foreground: 0.98 0.005 240;
      --card: 0.22 0.02 260;
      --card-foreground: 0.98 0.005 240;
      --popover: 0.22 0.02 260;
      --popover-foreground: 0.98 0.005 240;
      --primary: 0.98 0.005 240;
      --primary-foreground: 0.22 0.02 260;
      --secondary: 0.22 0.02 260;
      --secondary-foreground: 0.98 0.005 240;
      --muted: 0.28 0.02 260;
      --muted-foreground: 0.70 0.02 260;
      --accent: 0.28 0.02 260;
      --accent-foreground: 0.98 0.005 240;
      --destructive: 0.50 0.20 27;
      --destructive-foreground: 0.98 0.005 240;
      --border: 0.32 0.02 260;
      --input: 0.32 0.02 260;
      --ring: 0.98 0.005 240;
    }
    .notes-body {
      background-color: oklch(var(--background));
      color: oklch(var(--foreground));
      font-family: Inter, system-ui, sans-serif;
    }
    .btn-notes {
      display: inline-flex; align-items: center; justify-content: center;
      border-radius: var(--radius); font-weight: 500; font-size: 0.875rem;
      transition: background-color 0.2s, color 0.2s; height: 2.25rem; padding: 0 1rem;
      cursor: pointer;
    }
    .btn-ghost-notes { background: transparent; color: inherit; }
    .btn-ghost-notes:hover { background-color: oklch(var(--accent)); color: oklch(var(--accent-foreground)); }
    .btn-icon-notes { height: 2.25rem; width: 2.25rem; padding: 0; }
    .btn-primary-notes { background-color: oklch(var(--primary)); color: oklch(var(--primary-foreground)); }
    .input-field-notes {
      display: flex; height: 2rem; width: 100%; border-radius: var(--radius);
      border: 1px solid oklch(var(--input)); background-color: transparent;
      padding: 0.25rem 0.75rem; font-size: 0.875rem; transition: border-color 0.2s;
    }
    .block-row:hover .handle-trigger { opacity: 1; }
    .scrollbar-thin-hidden::-webkit-scrollbar { width: 4px; }
    .scrollbar-thin-hidden::-webkit-scrollbar-track { background: transparent; }
    .scrollbar-thin-hidden::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    .scrollbar-thin-hidden::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }
  </style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="flex h-[calc(100vh-75px)] overflow-hidden rounded-xl border border-border bg-card text-card-foreground shadow-sm">

  <!-- Sidebar -->
  <aside class="w-72 border-r border-border flex flex-col bg-muted/50 shrink-0 hidden md:flex h-full">
    <div class="p-4 border-b border-border bg-muted/30 space-y-3">
      <div class="flex items-center justify-between">
        <h3 class="text-sm font-bold text-slate-900 dark:text-white">Espace Notes</h3>
      </div>
      <div class="relative">
        <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 h-3.5 w-3.5 text-slate-400"></i>
        <input type="text" id="search-input" placeholder="Rechercher…" class="w-full pl-9 h-9 text-sm rounded-lg border border-border bg-card text-slate-900 dark:text-white focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none">
      </div>
    </div>
    <div id="sidebar-list" class="flex-1 overflow-y-auto p-2 space-y-1">
      <!-- Notes list populated here -->
    </div>
    <div class="p-4 border-t border-border bg-card/50 space-y-2">
      <button onclick="createNote()" class="group w-full flex items-center gap-3 px-3 py-2.5 rounded-xl border border-dashed border-border bg-transparent hover:border-slate-400 dark:hover:border-slate-500 hover:bg-muted transition-all text-sm font-semibold text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white">
        <span class="flex items-center justify-center h-7 w-7 rounded-lg border border-border bg-card text-slate-400 transition-colors group-hover:border-slate-400 group-hover:bg-slate-900 dark:group-hover:bg-white group-hover:text-white dark:group-hover:text-slate-900">
          <i data-lucide="plus" class="h-4 w-4"></i>
        </span>
        <span>Nouvelle page</span>
      </button>
      <button onclick="createNote(null, true)" class="group w-full flex items-center gap-3 px-3 py-2 rounded-xl bg-muted/50 hover:bg-muted transition-all text-xs font-bold text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white">
        <span class="flex items-center justify-center h-6 w-6 rounded-lg bg-card text-slate-400">
          <i data-lucide="folder-plus" class="h-3 w-3"></i>
        </span>
        <span>Nouveau dossier</span>
      </button>
    </div>
  </aside>

  <?php echo $__env->make('notes.block_menu', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

  <main class="flex-1 min-w-0 overflow-y-auto bg-card relative" id="editor-container">
    <!-- Editor populated here -->
  </main>

</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<?php echo $__env->make('notes.editor_scripts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<script>
    const CSRF_TOKEN = "<?php echo e(csrf_token()); ?>";
    const SYNC_URL = "<?php echo e(route('notes.sync_blocks', ':id')); ?>";
    const STORE_URL = "<?php echo e(route('notes.store')); ?>";
    const UPDATE_URL = "<?php echo e(route('notes.update', ':id')); ?>";
    const DELETE_URL = "<?php echo e(route('notes.destroy', ':id')); ?>";
    const SHOW_URL = "<?php echo e(route('notes.show', ':id')); ?>";

    window.state = { notes: <?php echo json_encode($notes, 15, 512) ?> };
    window.selectedId = <?php echo e($notes->where('is_folder', 0)->first() ? $notes->where('is_folder', 0)->first()->id : 'null'); ?>;
    let searchQuery = "";
    window.activeBlockId = null;
    window.saveTimeout = null;

    // Actions
    async function createNote(parentId = null, isFolder = false) {
      try {
          const response = await fetch(STORE_URL, {
              method: 'POST',
              headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
              body: JSON.stringify({ 
                  title: isFolder ? "Nouveau Dossier" : "", 
                  icon: isFolder ? "Folder" : "FileText",
                  is_folder: isFolder,
                  parent_id: parentId
              })
          });
          const data = await response.json();
          if (data.status === "success") {
              const note = data.note;
              note.blocks = isFolder ? [] : [{ id: null, type: "text", content: "" }];
              note.children = [];
              
              if (parentId) {
                  // Trouver le parent et ajouter l'enfant
                  const findAndAdd = (list) => {
                      for (let n of list) {
                          if (n.id == parentId) { n.children.push(note); return true; }
                          if (n.children && findAndAdd(n.children)) return true;
                      }
                      return false;
                  };
                  findAndAdd(state.notes);
              } else {
                  state.notes.unshift(note);
              }
              
              selectedId = note.id;
              renderSidebar();
              renderEditor();
          }
      } catch (e) { console.error("Create failed", e); }
    }

    function showDeleteModal(id, isFolder, title) {
        Swal.fire({
            title: "<?php echo app('translator')->get('messages.sweetAlertTitle'); ?>",
            text: isFolder 
                ? `Vous allez supprimer le dossier "${title}" et tout son contenu. <?php echo app('translator')->get('messages.recoverRecord'); ?>` 
                : "<?php echo app('translator')->get('messages.recoverRecord'); ?>",
            icon: 'warning',
            showCancelButton: true,
            focusConfirm: false,
            confirmButtonText: "<?php echo app('translator')->get('messages.confirmDelete'); ?>",
            cancelButtonText: "<?php echo app('translator')->get('app.cancel'); ?>",
            customClass: {
                confirmButton: 'btn btn-primary mr-3',
                cancelButton: 'btn btn-secondary'
            },
            showClass: {
                popup: 'swal2-noanimation',
                backdrop: 'swal2-noanimation'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                confirmDelete(id);
            }
        });
    }

    async function confirmDelete(id) {
      const url = DELETE_URL.replace(':id', id);
      try {
          const response = await fetch(url, { 
              method: 'DELETE', 
              headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' } 
          });
          const data = await response.json();
          if (data.status === "success") {
              location.reload(); 
          }
      } catch (e) { console.error("Delete failed", e); }
    }

    function renderSidebar() {
      const sidebarList = document.getElementById("sidebar-list");
      sidebarList.innerHTML = "";
      
      const renderNoteItem = (n, level = 0) => {
          if (searchQuery && !n.title.toLowerCase().includes(searchQuery.toLowerCase()) && (!n.children || !n.children.some(c => c.title.toLowerCase().includes(searchQuery.toLowerCase())))) return;

          const isActive = n.id == selectedId;
          const isFolder = n.is_folder;
          
          const el = document.createElement("div");
          el.className = "flex flex-col";
          
          const row = document.createElement("div");
          row.className = `flex items-center gap-2 rounded-lg px-3 py-1.5 text-sm transition-all cursor-pointer group ${isActive ? 'bg-slate-900 dark:bg-white text-white dark:text-slate-900 shadow-md font-medium' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-200/50 dark:hover:bg-slate-800 hover:translate-x-1'}`;
          if (level > 0) row.style.marginLeft = (level * 12) + "px";
          
          row.onclick = () => { selectedId = n.id; renderEditor(); renderSidebar(); };
          
          const iconName = getIconName(n.icon || (isFolder ? 'Folder' : 'FileText'));
          row.innerHTML = `
            ${isFolder ? `<i data-lucide="chevron-right" class="h-3 w-3 transition-transform ${n.expanded ? 'rotate-90' : ''}"></i>` : ''}
            <i data-lucide="${iconName}" class="h-4 w-4 shrink-0 ${isActive ? 'text-inherit' : (isFolder ? 'text-blue-400' : 'text-slate-400 dark:text-slate-500')}"></i>
            <span class="truncate flex-1">${n.title || (isFolder ? "Dossier sans titre" : "Sans titre")}</span>
          `;
          
          el.appendChild(row);
          sidebarList.appendChild(el);

          if (isFolder && (n.expanded || searchQuery)) {
              if (n.children) {
                  n.children.forEach(child => renderNoteItem(child, level + 1));
              }
          }

          if (isFolder) {
              const chevron = row.querySelector('[data-lucide="chevron-right"]');
              if (chevron) {
                chevron.onclick = (e) => {
                    e.stopPropagation();
                    n.expanded = !n.expanded;
                    renderSidebar();
                };
              }
          }
      };

      state.notes.forEach(n => renderNoteItem(n));
      lucide.createIcons({ root: sidebarList });
    }

    function renderEditor() {
      const container = document.getElementById("editor-container");
      const note = state.notes.find(n => n.id == selectedId);
      if (!note) { 
          container.innerHTML = `
            <div class="flex flex-col items-center justify-center h-full text-slate-400 dark:text-slate-500 space-y-4">
                <div class="h-16 w-16 rounded-2xl bg-slate-50 dark:bg-slate-900 flex items-center justify-center border border-border shadow-sm">
                    <i data-lucide="file-text" class="h-8 w-8 opacity-20"></i>
                </div>
                <div class="text-center">
                    <p class="text-base font-bold text-slate-900 dark:text-white">Aucune note sélectionnée</p>
                    <p class="text-sm text-slate-400 dark:text-slate-500">Choisissez une note dans la liste ou créez-en une nouvelle.</p>
                </div>
                <button onclick="createNote()" class="btn-notes btn-primary-notes px-6 py-2 rounded-xl bg-slate-900 dark:bg-white text-white dark:text-slate-900 font-bold shadow-lg hover:scale-105 transition-transform">
                    Commencer à rédiger
                </button>
            </div>
          `; 
          lucide.createIcons({ root: container });
          return; 
      }

      container.innerHTML = `
        <div class="max-w-4xl mx-auto px-20 py-16">
          <div class="flex items-center gap-4 mb-2">
            <div id="icon-picker-container"></div>
            <select id="parent-folder-select" class="text-[10px] font-bold uppercase tracking-widest text-slate-400 bg-slate-50 border border-slate-100 rounded-lg px-2 py-1 outline-none focus:bg-white transition-all">
                <option value="">📁 Racine</option>
                ${state.notes.filter(n => n.is_folder && n.id != note.id).map(f => `<option value="${f.id}" ${note.parent_id == f.id ? 'selected' : ''}>📁 ${f.title || 'Sans titre'}</option>`).join('')}
            </select>
          </div>
          <div class="flex items-center justify-between mb-8">
            <input type="text" id="note-title-input" value="${escapeHtml(note.title)}" placeholder="${note.is_folder ? 'Nom du dossier...' : 'Titre de la page...'}" class="flex-1 text-4xl font-black text-slate-900 dark:text-white bg-transparent border-none outline-none placeholder:text-slate-200 dark:placeholder:text-slate-800 focus:ring-0 tracking-tight">
            <div class="flex items-center gap-2">
                ${!note.is_folder ? `<button onclick="createNote('${note.id}')" class="h-9 px-3 flex items-center gap-2 rounded-xl text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-all text-xs font-bold"><i data-lucide="plus" class="h-4 w-4"></i> Ajouter sous-note</button>` : ''}
                <button onclick="showDeleteModal('${note.id}', ${note.is_folder}, '${escapeHtml(note.title || 'Sans titre')}')" class="h-9 w-9 flex items-center justify-center rounded-xl text-slate-300 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-all" title="Supprimer">
                    <i data-lucide="trash-2" class="h-5 w-5"></i>
                </button>
            </div>
          </div>
          ${note.is_folder ? `
            <div class="bg-slate-50 dark:bg-slate-900/50 rounded-2xl p-8 border border-border text-center">
                <i data-lucide="folder-open" class="h-12 w-12 text-slate-200 dark:text-slate-800 mx-auto mb-4"></i>
                <p class="text-slate-500 dark:text-slate-400 font-medium">Ceci est un dossier.</p>
                <p class="text-xs text-slate-400 dark:text-slate-500 mb-6">Utilisez la barre latérale pour organiser vos notes à l'intérieur.</p>
                <button onclick="createNote('${note.id}')" class="px-6 py-2 bg-slate-900 dark:bg-white text-white dark:text-slate-900 rounded-xl text-sm font-bold shadow-lg hover:scale-105 transition-transform flex items-center gap-2 mx-auto">
                    <i data-lucide="plus" class="h-4 w-4"></i> Créer une note ici
                </button>
            </div>
          ` : '<div id="blocks-container" class="space-y-1"></div>'}
        </div>
      `;

      const ipContainer = document.getElementById("icon-picker-container");
      ipContainer.appendChild(renderIconPicker(note.icon || (note.is_folder ? 'Folder' : 'FileText'), (newIcon) => {
          note.icon = newIcon;
          renderSidebar();
          renderEditor();
          fetch(UPDATE_URL.replace(':id', note.id), {
              method: 'PUT', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
              body: JSON.stringify({ icon: newIcon })
          });
      }));

      const folderSelect = document.getElementById("parent-folder-select");
      folderSelect.onchange = async (e) => {
          const newParentId = e.target.value || null;
          note.parent_id = newParentId;
          
          await fetch(UPDATE_URL.replace(':id', note.id), {
              method: 'PUT', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
              body: JSON.stringify({ parent_id: newParentId })
          });
          
          // Recharger les données pour reconstruire l'arborescence proprement
          location.reload(); 
      };

      const titleInput = document.getElementById("note-title-input");
      titleInput.oninput = (e) => { 
          note.title = e.target.value; 
          renderSidebar(); 
          if(saveTimeout) clearTimeout(saveTimeout);
          saveTimeout = setTimeout(() => {
              fetch(UPDATE_URL.replace(':id', note.id), {
                  method: 'PUT', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
                  body: JSON.stringify({ title: note.title })
              });
          }, 1000);
      };

      const blocksContainer = document.getElementById("blocks-container");
      if (!note.blocks) {
          fetch(SHOW_URL.replace(':id', note.id)).then(r => r.json()).then(data => {
              note.blocks = data.blocks;
              renderEditor();
          });
      } else {
          note.blocks.forEach((b, index) => blocksContainer.appendChild(createBlockElement(b, note, renderEditor, index)));
      }
      lucide.createIcons({ root: container });
    }

    function renderApp() { renderSidebar(); renderEditor(); }
    document.addEventListener("DOMContentLoaded", () => {
        document.getElementById("search-input").oninput = (e) => { searchQuery = e.target.value; renderSidebar(); };
        renderApp();
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/cp2255957p00/public_html/tacheflux/resources/views/notes/index.blade.php ENDPATH**/ ?>