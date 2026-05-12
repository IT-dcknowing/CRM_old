<style>
    .block-row:hover .handle-trigger { opacity: 1 !important; }
</style>
<script>
    window.CSRF_TOKEN = window.CSRF_TOKEN || "{{ csrf_token() }}";
    window.SYNC_URL = window.SYNC_URL || "{{ route('notes.sync_blocks', ':id') }}";

    if (typeof generateId !== 'function') {
        window.generateId = function() { return Math.random().toString(36).slice(2, 10) + Date.now().toString(36); };
        window.escapeHtml = function(str) { const p = document.createElement('p'); p.innerText = str; return p.innerHTML; };

        window.getIconName = function(icon) {
          const map = {
            FileText: 'file-text', Target: 'target', Lightbulb: 'lightbulb',
            Folder: 'folder', Star: 'star', Clock: 'clock',
            Heading1: 'heading-1', TextIcon: 'type', ListTodo: 'list-todo',
            List: 'list', Quote: 'quote', Minus: 'minus', Plus: 'plus',
            Trash2: 'trash-2', MoreHorizontal: 'more-horizontal',
            GripVertical: 'grip-vertical', ChevronRight: 'chevron-right', Search: 'search',
            Table: 'table'
          };
          return map[icon] || icon;
        };

        window.renderIconPicker = function(currentIcon, onSelect) {
            const icons = ['FileText', 'Target', 'Lightbulb', 'Folder', 'Star', 'Clock'];
            const wrapper = document.createElement("div");
            wrapper.className = "relative inline-block mb-4";
            
            wrapper.innerHTML = `
                <button id="icon-picker-trigger" class="h-10 w-10 flex items-center justify-center rounded-xl bg-muted border border-border hover:bg-card hover:shadow-md transition-all text-foreground">
                    <i data-lucide="${getIconName(currentIcon)}" class="h-5 w-5"></i>
                </button>
                <div id="icon-picker-menu" class="hidden absolute top-full left-0 mt-2 p-2 bg-card border border-border rounded-2xl shadow-xl z-50 min-w-[160px]">
                    <p class="text-[10px] font-bold text-muted-foreground uppercase tracking-widest px-2 mb-2">Choisir une icône</p>
                    <div class="grid grid-cols-3 gap-1">
                        ${icons.map(icon => `
                            <button class="icon-option p-3 rounded-xl hover:bg-muted transition-all ${icon === currentIcon ? 'bg-muted text-foreground' : 'text-muted-foreground'}" data-icon="${icon}">
                                <i data-lucide="${getIconName(icon)}" class="h-5 w-5"></i>
                            </button>
                        `).join('')}
                    </div>
                </div>
            `;

            const trigger = wrapper.querySelector('#icon-picker-trigger');
            const menu = wrapper.querySelector('#icon-picker-menu');
            
            trigger.onclick = () => menu.classList.toggle('hidden');
            
            wrapper.querySelectorAll('.icon-option').forEach(btn => {
                btn.onclick = () => {
                    const icon = btn.getAttribute('data-icon');
                    onSelect(icon);
                    menu.classList.add('hidden');
                };
            });

            // Close on click outside
            document.addEventListener('click', (e) => {
                if (!wrapper.contains(e.target)) menu.classList.add('hidden');
            });

            lucide.createIcons({ root: wrapper });
            return wrapper;
        };

        window.activeBlockIndex = null;
        window.showBlockMenu = function(e, index) {
            e.preventDefault();
            e.stopPropagation();
            window.activeBlockIndex = index;
            const menu = document.getElementById('block-menu');
            menu.classList.remove('hidden');
            
            // Positionnement intelligent
            const rect = e.currentTarget.getBoundingClientRect();
            menu.style.top = `${rect.top}px`;
            menu.style.left = `${rect.right + 10}px`;

            const closeMenu = (ev) => {
                if (!menu.contains(ev.target)) {
                    menu.classList.add('hidden');
                    document.removeEventListener('mousedown', closeMenu);
                }
            };
            document.addEventListener('mousedown', closeMenu);
            lucide.createIcons({ root: menu });
        };

        window.executeBlockAction = function(action, type = null) {
            const notesSource = (typeof window.contextState !== 'undefined' && window.contextState !== null) ? window.contextState.notes : (typeof window.state !== 'undefined' ? window.state.notes : []);
            const currentId = (typeof window.contextState !== 'undefined' && window.contextState !== null) ? window.currentNoteId : (typeof window.selectedId !== 'undefined' ? window.selectedId : null);
            
            const note = notesSource.find(n => n.id == currentId);
            if (!note || window.activeBlockIndex === null) return;

            if (action === 'transform') {
                const block = note.blocks[window.activeBlockIndex];
                block.type = type;
                if (type === 'table' && !block.data) {
                    block.data = { rows: [{ desc: "", priority: "Normal", date: "", done: false }] };
                }
                if (typeof renderEditor !== 'undefined') renderEditor();
                if (typeof renderContextEditor !== 'undefined') renderContextEditor();
            } else if (action === 'insert') {
                if (type === 'file') {
                    const input = document.createElement('input');
                    input.type = 'file';
                    input.onchange = async (e) => {
                        const file = e.target.files[0];
                        if (!file) return;
                        const formData = new FormData();
                        formData.append('file', file);
                        formData.append('_token', window.CSRF_TOKEN);
                        try {
                            const res = await fetch("{{ route('notes.upload') }}", { method: 'POST', body: formData });
                            const data = await res.json();
                            if (data.status === 'success') {
                                const newBlock = { 
                                    id: null, 
                                    tempId: 'temp_' + Date.now(),
                                    type: file.type.startsWith('image/') ? 'image' : 'file', 
                                    content: data.name,
                                    data: { path: data.path, size: data.size, mime: data.type }
                                };
                                note.blocks.splice(window.activeBlockIndex + 1, 0, newBlock);
                                if (typeof renderEditor !== 'undefined') renderEditor();
                                if (typeof renderContextEditor !== 'undefined') renderContextEditor();
                                debouncedSync(note);
                            }
                        } catch (err) { console.error("Upload failed", err); }
                    };
                    input.click();
                    document.getElementById('block-menu').classList.add('hidden');
                    return;
                }

                const newBlock = { id: null, tempId: 'temp_' + Date.now(), type: type, content: "", data: {} };
                if (type === 'table') newBlock.data = { rows: [{ desc: "", priority: "Normal", date: "", done: false }] };
                
                note.blocks.splice(window.activeBlockIndex + 1, 0, newBlock);
                if (typeof renderEditor !== 'undefined') renderEditor();
                if (typeof renderContextEditor !== 'undefined') renderContextEditor();
                
                setTimeout(() => {
                    const textareas = document.querySelectorAll('textarea');
                    const allBlocks = document.querySelectorAll('.block-row');
                    const targetBlock = allBlocks[window.activeBlockIndex + 1];
                    if (targetBlock) {
                        const ta = targetBlock.querySelector('textarea');
                        if (ta) ta.focus();
                    }
                }, 50);
            } else if (action === 'delete') {
                note.blocks.splice(window.activeBlockIndex, 1);
                if (note.blocks.length === 0) note.blocks.push({ id: null, type: "text", content: "" });
                if (typeof renderEditor !== 'undefined') renderEditor();
                if (typeof renderContextEditor !== 'undefined') renderContextEditor();
            }

            document.getElementById('block-menu').classList.add('hidden');
            debouncedSync(note);
        };

        window.addTableRow = function(blockId) {
            const notesSource = (typeof window.contextState !== 'undefined' && window.contextState !== null) ? window.contextState.notes : (typeof window.state !== 'undefined' ? window.state.notes : []);
            const currentId = (typeof window.contextState !== 'undefined' && window.contextState !== null) ? window.currentNoteId : (typeof window.selectedId !== 'undefined' ? window.selectedId : null);
            
            const note = notesSource.find(n => n.id == currentId);
            if (!note) return;
            const block = note.blocks.find(b => b.id == blockId || (b.tempId && b.tempId == blockId));
            if (!block) return;
            if (!block.data.rows) block.data.rows = [];
            block.data.rows.push({ desc: "", priority: "Normal", date: "", done: false });
            if (typeof renderEditor !== 'undefined') renderEditor();
            if (typeof renderContextEditor !== 'undefined') renderContextEditor();
            debouncedSync(note);
        };

        window.removeTableRow = function(blockId, rowIndex) {
            const notesSource = (typeof window.contextState !== 'undefined' && window.contextState !== null) ? window.contextState.notes : (typeof window.state !== 'undefined' ? window.state.notes : []);
            const currentId = (typeof window.contextState !== 'undefined' && window.contextState !== null) ? window.currentNoteId : (typeof window.selectedId !== 'undefined' ? window.selectedId : null);
            
            const note = notesSource.find(n => n.id == currentId);
            if (!note) return;
            const block = note.blocks.find(b => b.id == blockId || (b.tempId && b.tempId == blockId));
            if (!block) return;
            block.data.rows.splice(rowIndex, 1);
            if (typeof renderEditor !== 'undefined') renderEditor();
            if (typeof renderContextEditor !== 'undefined') renderContextEditor();
            debouncedSync(note);
        };

        window.setTableFilter = function(blockId, filter) {
            const notesSource = (typeof window.contextState !== 'undefined' && window.contextState !== null) ? window.contextState.notes : (typeof window.state !== 'undefined' ? window.state.notes : []);
            const currentId = (typeof window.contextState !== 'undefined' && window.contextState !== null) ? window.currentNoteId : (typeof window.selectedId !== 'undefined' ? window.selectedId : null);
            
            const note = notesSource.find(n => n.id == currentId);
            if (!note) return;
            const block = note.blocks.find(b => b.id == blockId || (b.tempId && b.tempId == blockId));
            if (!block) return;
            block.data.filter = filter;
            if (typeof renderEditor !== 'undefined') renderEditor();
            if (typeof renderContextEditor !== 'undefined') renderContextEditor();
        };

        window.setReminder = function(index) {
            const note = state.notes.find(n => n.id == selectedId);
            if (!note || !note.blocks[index]) return;
            const block = note.blocks[index];
            const date = prompt("Définir un rappel (AAAA-MM-JJ HH:MM) :", block.reminder_at || "");
            if (date !== null) {
                block.reminder_at = date;
                renderEditor();
                syncBlocks();
            }
        };

        window.createBlockElement = function(block, note, renderCallback, index) {
          const row = document.createElement("div");
          row.className = "group flex items-start gap-1 py-0.5 relative block-row";
          
          row.innerHTML = `
                <div class="absolute -left-7 top-0 bottom-0 flex items-center opacity-0 handle-trigger group-hover:opacity-100 transition-opacity">
                  <div class="flex items-center gap-1">
                      <button onclick="showBlockMenu(event, ${index})" class="p-1 hover:bg-muted rounded text-muted-foreground hover:text-foreground transition-all cursor-pointer" title="Options du bloc">
                        <i data-lucide="grip-vertical" class="h-4 w-4"></i>
                      </button>
                      <button onclick="setReminder(${index})" class="p-1 hover:bg-muted rounded ${block.reminder_at ? 'text-primary bg-primary/10' : 'text-muted-foreground'} hover:text-primary transition-all" title="Définir un rappel">
                        <i data-lucide="clock" class="h-3.5 w-3.5"></i>
                      </button>
                  </div>
                </div>
          `;

          let contentContainer = document.createElement("div");
          contentContainer.className = "flex-1 flex items-start"; // Restored to horizontal alignment

          // Bloc Type: TABLE
          if (block.type === 'table') {
              contentContainer.className = "flex-1 flex flex-col"; // Only tables need column layout
              if (!block.data) block.data = { rows: [{ desc: "", priority: "Normal", date: "" }] };
              
              const tableWrapper = document.createElement("div");
              tableWrapper.className = "w-full my-3 border border-border rounded-xl overflow-hidden shadow-sm bg-card";
              
              let tableHtml = `
                <table class="w-full text-left border-collapse">
                  <thead>
                    <tr>
                      <td colspan="5" class="px-4 py-2 bg-card border-b border-border/50">
                        <div class="flex items-center gap-1 text-[11px] font-bold" id="filters-${block.id}">
                            <!-- Filters populated by JS -->
                        </div>
                      </td>
                    </tr>
                    <tr class="bg-muted/50 border-b border-border/50">
                      <th class="px-2 py-2 w-8"></th>
                      <th class="px-4 py-2 text-[10px] font-bold text-muted-foreground uppercase tracking-wider w-1/2">Description</th>
                      <th class="px-4 py-2 text-[10px] font-bold text-muted-foreground uppercase tracking-wider">Priorité</th>
                      <th class="px-4 py-2 text-[10px] font-bold text-muted-foreground uppercase tracking-wider">Date</th>
                      <th class="px-4 py-2 w-10"></th>
                    </tr>
                  </thead>
                  <tbody id="table-body-${block.id}">
                  </tbody>
                </table>
                <button class="w-full py-2 text-xs text-muted-foreground hover:text-foreground hover:bg-muted/50 transition-colors border-t border-border/50 flex items-center justify-center gap-1" onclick="addTableRow('${block.id || block.tempId}')">
                  <i data-lucide="plus" class="h-3 w-3"></i> Ajouter une ligne
                </button>
              `;
              tableWrapper.innerHTML = tableHtml;
              contentContainer.appendChild(tableWrapper);

              const renderRows = () => {
                  const tbody = tableWrapper.querySelector('tbody');
                  const filterContainer = tableWrapper.querySelector(`#filters-${block.id}`);
                  const currentFilter = block.data.filter || 'Tous';
                  
                  // Calcul des compteurs
                  const counts = {
                      Tous: block.data.rows.length,
                      Urgent: block.data.rows.filter(r => r.priority === 'Urgent').length,
                      Haut: block.data.rows.filter(r => r.priority === 'Haut').length,
                      Normal: block.data.rows.filter(r => r.priority === 'Normal').length
                  };

                  // Rendu des filtres
                  filterContainer.innerHTML = ['Tous', 'Urgent', 'Haut', 'Normal'].map(f => `
                      <button onclick="setTableFilter('${block.id || block.tempId}', '${f}')" 
                              class="px-3 py-1.5 rounded-lg transition-all ${currentFilter === f ? 'bg-foreground text-background shadow-sm' : 'text-muted-foreground hover:bg-muted hover:text-foreground'}">
                          ${f} <span class="ml-1 opacity-60">${counts[f]}</span>
                      </button>
                  `).join('');

                  tbody.innerHTML = "";
                  block.data.rows.forEach((row, rIdx) => {
                      if (currentFilter !== 'Tous' && row.priority !== currentFilter) return;

                      const tr = document.createElement("tr");
                      tr.className = `border-b border-border/50 last:border-0 hover:bg-muted/30 transition-colors ${row.done ? 'bg-muted/20' : ''}`;
                      
                      const pClass = row.priority === 'Urgent' ? 'bg-red-500/10 text-red-500' : (row.priority === 'Haut' ? 'bg-orange-500/10 text-orange-500' : 'bg-muted text-muted-foreground');
                      
                      tr.innerHTML = `
                        <td class="px-2 py-1 text-center">
                          <input type="checkbox" ${row.done ? 'checked' : ''} class="h-3.5 w-3.5 rounded border-border bg-card text-primary focus:ring-0 cursor-pointer">
                        </td>
                         <td class="px-3 py-1">
                           <input type="text" value="${escapeHtml(row.desc)}" placeholder="Détails..." class="w-full bg-transparent border-none outline-none text-sm ${row.done ? 'line-through text-muted-foreground' : 'text-slate-900 dark:text-white'} placeholder:text-muted-foreground/30 focus:ring-0 py-1">
                         </td>
                        <td class="px-3 py-1">
                          <select class="bg-transparent border-none outline-none text-[11px] font-bold px-2 py-0.5 rounded-full ${pClass} cursor-pointer appearance-none focus:ring-0 ${row.done ? 'opacity-50' : ''}">
                            <option value="Normal" ${row.priority === 'Normal' ? 'selected' : ''}>Normal</option>
                            <option value="Haut" ${row.priority === 'Haut' ? 'selected' : ''}>Haut</option>
                            <option value="Urgent" ${row.priority === 'Urgent' ? 'selected' : ''}>Urgent</option>
                          </select>
                        </td>
                        <td class="px-3 py-1">
                          <input type="date" value="${row.date || ''}" class="bg-transparent border-none outline-none text-xs ${row.done ? 'text-muted-foreground/40' : 'text-muted-foreground'} focus:ring-0 cursor-pointer [color-scheme:dark]">
                        </td>
                        <td class="px-3 py-1 text-right">
                          <button class="opacity-0 group-hover:opacity-100 p-1 text-muted-foreground hover:text-red-500 transition-all" onclick="removeTableRow('${block.id || block.tempId}', ${rIdx})">
                            <i data-lucide="trash-2" class="h-3 w-3"></i>
                          </button>
                        </td>
                      `;
                      
                      const cb = tr.querySelector('input[type="checkbox"]');
                      cb.onchange = (e) => { row.done = e.target.checked; renderRows(); debouncedSync(note); };

                      const descInput = tr.querySelector('input[type="text"]');
                      descInput.oninput = (e) => { row.desc = e.target.value; debouncedSync(note); };
                      
                      const pSelect = tr.querySelector('select');
                      pSelect.onchange = (e) => { row.priority = e.target.value; renderRows(); debouncedSync(note); };
                      
                      const dInput = tr.querySelector('input[type="date"]');
                      dInput.onchange = (e) => { row.date = e.target.value; debouncedSync(note); };

                      tbody.appendChild(tr);
                  });
                  lucide.createIcons({ root: tbody });
              };

              setTimeout(renderRows, 0);
              row.appendChild(contentContainer);
              lucide.createIcons({ root: row });
              return row;
          }

          // Bloc Type: FILE / IMAGE
          if (block.type === 'file' || block.type === 'image') {
              const link = document.createElement("a");
              link.href = block.data.path;
              link.target = "_blank";
              link.className = "flex items-center gap-2 text-primary hover:text-primary/80 font-medium text-sm py-1 my-1 decoration-2 underline-offset-4 hover:underline transition-all";
              
              const icon = block.type === 'image' ? 'image' : 'paperclip';
              link.innerHTML = `
                  <i data-lucide="${icon}" class="h-4 w-4 shrink-0"></i>
                  <span>${block.content}</span>
                  <span class="text-[10px] text-muted-foreground font-normal ml-1">(${(block.data.size / 1024).toFixed(1)} KB)</span>
              `;
              contentContainer.appendChild(link);
          }

          // Bloc Type: TODO
          if (block.type === 'todo') {
              const cb = document.createElement("input");
              cb.type = "checkbox";
              cb.checked = block.data?.checked || false;
              cb.className = "mt-2 mr-2 h-4 w-4 cursor-pointer accent-foreground";
              cb.onchange = (e) => {
                  block.data = { checked: e.target.checked };
                  textarea.classList.toggle('line-through', e.target.checked);
                  textarea.classList.toggle('text-muted-foreground', e.target.checked);
                  textarea.classList.toggle('opacity-50', e.target.checked);
                  debouncedSync(note);
              };
              contentContainer.appendChild(cb);
          }

          // Bloc Type: BULLET
          if (block.type === 'bullet') {
              const dot = document.createElement("span");
              dot.className = "mt-1.5 mr-2 text-muted-foreground font-bold shrink-0";
              dot.innerHTML = "&bull;";
              contentContainer.appendChild(dot);
          }

          // Bloc Type: HR (Separator)
          if (block.type === 'hr') {
              const hr = document.createElement("div");
              hr.className = "w-full h-px bg-border my-4 flex-1";
              contentContainer.appendChild(hr);
              row.appendChild(contentContainer);
              return row;
          }

          let textarea = document.createElement("textarea");
          textarea.value = block.content || "";
          textarea.placeholder = block.type === 'heading' ? "Titre..." : "Tapez quelque chose...";
          textarea.className = "flex-1 resize-none bg-transparent border-none outline-none py-1.5 px-1 text-slate-900 dark:text-white placeholder:text-muted-foreground/30 focus:ring-0 leading-relaxed overflow-hidden block";
          
          if (block.type === 'h2') textarea.className += " text-3xl font-black text-slate-900 dark:text-white mt-4 mb-2 tracking-tight";
          if (block.type === 'heading') textarea.className += " text-2xl font-bold text-slate-900 dark:text-white";
          if (block.type === 'quote') textarea.className += " italic border-l-4 border-border pl-4 text-muted-foreground py-2 my-2";
          if (block.type === 'todo' && block.data?.checked) textarea.className += " line-through text-muted-foreground opacity-60";
          
          textarea.oninput = (e) => { 
              block.content = e.target.value; 
              
              // Slash command detection
              if (block.content === "/file" || block.content === "/image") {
                  const input = document.createElement('input');
                  input.type = 'file';
                  input.onchange = async (e) => {
                      const file = e.target.files[0];
                      if (!file) return;
                      const formData = new FormData();
                      formData.append('file', file);
                      formData.append('_token', window.CSRF_TOKEN);
                      
                      try {
                          const res = await fetch("{{ route('notes.upload') }}", { method: 'POST', body: formData });
                          const data = await res.json();
                          if (data.status === 'success') {
                              block.type = file.type.startsWith('image/') ? 'image' : 'file';
                              block.content = data.name;
                              block.data = { path: data.path, size: data.size, mime: data.type };
                              if (renderCallback) renderCallback();
                          }
                      } catch (err) { console.error("Upload failed", err); }
                  };
                  input.click();
                  return;
              }
              if (block.content === "/table") {
                  block.type = "table";
                  block.content = "";
                  block.data = { rows: [{ desc: "", priority: "Normal", date: "", done: false }] };
                  if (renderCallback) renderCallback();
                  return;
              }
              if (block.content === "/todo") {
                  block.type = "todo";
                  block.content = "";
                  block.data = { checked: false };
                  if (renderCallback) renderCallback();
                  return;
              }

              textarea.style.height = "auto"; textarea.style.height = textarea.scrollHeight + "px";
              debouncedSync(note);
          };
          
          textarea.onkeydown = (e) => {
              if (e.key === "Enter" && !e.shiftKey) { 
                  e.preventDefault(); 
                  addBlockAfter(note, block.id, block.type === 'heading' ? 'text' : block.type, renderCallback);
              }
              if (e.key === "Backspace" && !block.content) { 
                  e.preventDefault(); 
                  deleteBlock(note, block.id, renderCallback);
              }
              // Slash commands (simple)
              if (e.key === "/") {
                  // Optionnel: implémenter un menu flottant ici
              }
          };

          contentContainer.appendChild(textarea);
          row.appendChild(contentContainer);

          setTimeout(() => { 
              textarea.style.height = textarea.scrollHeight + "px"; 
              if(window.activeBlockId == block.id) textarea.focus(); 
          }, 0);
          
          lucide.createIcons({ root: row });
          return row;
        };

        window.addBlockAfter = function(note, id, type = 'text', renderCallback) {
            const idx = note.blocks.findIndex(b => b.id == id);
            const newBlock = { id: 'temp-' + generateId(), type, content: "", data: {} };
            if (idx === -1) note.blocks.push(newBlock);
            else note.blocks.splice(idx + 1, 0, newBlock);
            window.activeBlockId = newBlock.id;
            if (renderCallback) renderCallback();
            debouncedSync(note);
        };

        window.deleteBlock = function(note, id, renderCallback) {
            if (note.blocks.length <= 1) return;
            const idx = note.blocks.findIndex(b => b.id == id);
            note.blocks = note.blocks.filter(b => b.id != id);
            window.activeBlockId = note.blocks[idx - 1]?.id || note.blocks[0].id;
            if (renderCallback) renderCallback();
            debouncedSync(note);
        };

        window.debouncedSync = function(note) {
            if (window.saveTimeout) clearTimeout(window.saveTimeout);
            window.saveTimeout = setTimeout(async () => {
                const url = window.SYNC_URL.replace(':id', note.id);
                try {
                    await fetch(url, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': window.CSRF_TOKEN },
                        body: JSON.stringify({ blocks: note.blocks })
                    });
                } catch (e) { console.error("Sync failed", e); }
            }, 1000);
        };
    }
</script>
