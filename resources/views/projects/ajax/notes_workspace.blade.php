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
          }
        }
      }
    }
</script>
@include('notes.block_menu')
<div class="flex flex-row h-[550px] overflow-hidden rounded-xl border border-border mt-4 bg-card text-card-foreground shadow-sm">
  <!-- Sidebar -->
  <aside class="w-60 border-r border-border flex flex-col bg-muted/50 shrink-0 h-full">
    <div class="p-4 border-b border-border bg-muted/30">
      <div class="flex items-center justify-between">
        <h3 class="text-[10px] font-black uppercase tracking-[0.1em] text-muted-foreground">Notes du Projet</h3>
        <button class="flex items-center justify-center h-6 w-6 rounded-md bg-card border border-border hover:bg-foreground hover:text-background hover:border-foreground text-muted-foreground transition-all shadow-sm" onclick="createProjectContextNote()" title="Nouvelle note">
          <i data-lucide="plus" class="h-3.5 w-3.5"></i>
        </button>
      </div>
    </div>
    <div id="project-context-sidebar-list" class="flex-1 overflow-y-auto p-2 space-y-1">
      <!-- Notes list populated here -->
    </div>
  </aside>

  <main class="flex-1 min-w-0 overflow-y-auto bg-card relative" id="project-context-editor-container">
    <div class="flex flex-col items-center justify-center h-full text-muted-foreground space-y-3">
        <div class="h-12 w-12 rounded-full bg-muted flex items-center justify-center border border-border">
            <i data-lucide="file-text" class="h-6 w-6 opacity-40"></i>
        </div>
        <p class="text-sm font-medium">Sélectionnez une note pour commencer</p>
    </div>
  </main>
</div>

@include('notes.editor_scripts')
<script>
        window.CSRF_TOKEN = "{{ csrf_token() }}";
        const SYNC_URL = "{{ route('notes.sync_blocks', ':id') }}";
        const STORE_URL = "{{ route('notes.store') }}";
        const UPDATE_URL = "{{ route('notes.update', ':id') }}";
        const SHOW_URL = "{{ route('notes.show', ':id') }}";
        const DELETE_URL = "{{ route('notes.destroy', ':id') }}";

        const PROJECT_ID = "{{ $project->id }}";
        const PROJECT_TYPE = "App\\Models\\Project";
        
        window.contextState = { notes: @json($notes) };
        window.currentNoteId = null;
        window.contextSaveTimeout = null;

        function renderContextSidebar() {
            const list = document.getElementById("project-context-sidebar-list");
            list.innerHTML = "";
            contextState.notes.forEach(n => {
                const isActive = n.id == currentNoteId;
                const el = document.createElement("div");
                el.className = `flex items-center gap-2 rounded-lg px-3 py-2 text-sm transition-all cursor-pointer group ${isActive ? 'bg-foreground text-background shadow-md shadow-border/50 font-medium' : 'text-muted-foreground hover:bg-muted/50 hover:translate-x-1'}`;
                el.onclick = () => { currentNoteId = n.id; renderContextEditor(); renderContextSidebar(); };
                el.innerHTML = `<i data-lucide="${getIconName(n.icon)}" class="h-4 w-4 shrink-0 ${isActive ? 'text-background' : 'text-muted-foreground'}"></i><span class="truncate flex-1">${n.title || "Sans titre"}</span>`;
                list.appendChild(el);
            });
            lucide.createIcons({ root: list });
        }

        window.createProjectContextNote = async function() {
            try {
                const response = await fetch(STORE_URL, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
                    body: JSON.stringify({ title: "", icon: "FileText", noteable_id: PROJECT_ID, noteable_type: PROJECT_TYPE })
                });
                const data = await response.json();
                if (data.status === "success") {
                    const note = data.note;
                    note.blocks = [{ id: null, type: "text", content: "" }];
                    contextState.notes.unshift(note);
                    currentNoteId = note.id;
                    renderContextSidebar();
                    renderContextEditor();
                }
            } catch (e) { console.error("Create failed", e); }
        };

        window.deleteProjectNote = function(id) {
            Swal.fire({
                title: "@lang('messages.sweetAlertTitle')",
                text: "@lang('messages.recoverRecord')",
                icon: 'warning',
                showCancelButton: true,
                focusConfirm: false,
                confirmButtonText: "@lang('messages.confirmDelete')",
                cancelButtonText: "@lang('app.cancel')",
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
                    executeDeleteNote(id);
                }
            });
        };

        async function executeDeleteNote(id) {
            try {
                await fetch(DELETE_URL.replace(':id', id), {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': CSRF_TOKEN }
                });
                contextState.notes = contextState.notes.filter(n => n.id != id);
                currentNoteId = contextState.notes.length > 0 ? contextState.notes[0].id : null;
                renderContextSidebar();
                renderContextEditor();
            } catch (e) { console.error("Delete failed", e); }
        }

        function renderContextEditor() {
            const container = document.getElementById("project-context-editor-container");
            const note = contextState.notes.find(n => n.id == currentNoteId);
            if (!note) {
                container.innerHTML = `
                    <div class="flex flex-col items-center justify-center h-full text-muted-foreground space-y-3">
                        <div class="h-12 w-12 rounded-full bg-muted flex items-center justify-center border border-border">
                            <i data-lucide="file-text" class="h-6 w-6 opacity-40"></i>
                        </div>
                        <p class="text-sm font-medium">Sélectionnez une note pour commencer</p>
                    </div>
                `;
                lucide.createIcons({ root: container });
                return;
            }

            container.innerHTML = `
                <div class="px-16 py-10 max-w-4xl mx-auto">
                    <div id="pctx-icon-picker-container"></div>
                    <div class="flex items-center justify-between mb-8">
                        <input type="text" id="pctx-note-title" value="${escapeHtml(note.title)}" placeholder="Titre de la note du projet..." class="flex-1 text-3xl font-extrabold text-foreground bg-transparent border-none outline-none placeholder:text-muted-foreground/20 focus:ring-0 tracking-tight">
                        <button onclick="deleteProjectNote('${note.id}')" class="h-9 w-9 flex items-center justify-center rounded-xl text-muted-foreground hover:text-red-500 hover:bg-red-500/10 transition-all" title="Supprimer la note">
                            <i data-lucide="trash-2" class="h-5 w-5"></i>
                        </button>
                    </div>
                    <div id="pctx-blocks-container" class="space-y-1"></div>
                    <div class="mt-10 pt-6 border-t border-border/50 flex items-center justify-between text-[10px] text-muted-foreground/50 uppercase tracking-widest font-bold">
                        <span>Auto-sauvegarde activée</span>
                        <span>ID: #${note.id}</span>
                    </div>
                </div>
            `;

            const ipContainer = document.getElementById("pctx-icon-picker-container");
            ipContainer.appendChild(renderIconPicker(note.icon || 'FileText', (newIcon) => {
                note.icon = newIcon;
                renderContextSidebar();
                renderContextEditor();
                fetch(UPDATE_URL.replace(':id', note.id), {
                    method: 'PUT', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
                    body: JSON.stringify({ icon: newIcon })
                });
            }));

            const titleInput = document.getElementById("pctx-note-title");
            titleInput.oninput = (e) => {
                note.title = e.target.value;
                renderContextSidebar();
                if(contextSaveTimeout) clearTimeout(contextSaveTimeout);
                contextSaveTimeout = setTimeout(() => {
                    fetch(UPDATE_URL.replace(':id', note.id), {
                        method: 'PUT', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
                        body: JSON.stringify({ title: note.title })
                    });
                }, 1000);
            };

            const blocksContainer = document.getElementById("pctx-blocks-container");
            if (!note.blocks) {
                fetch(SHOW_URL.replace(':id', note.id)).then(r => r.json()).then(data => {
                    note.blocks = data.blocks;
                    renderContextEditor();
                });
            } else {
                note.blocks.forEach((b, index) => {
                    const blockEl = createBlockElement(b, note, renderContextEditor, index);
                    blocksContainer.appendChild(blockEl);
                });
            }
            lucide.createIcons({ root: container });
        }

        renderContextSidebar();
        lucide.createIcons();
</script>
