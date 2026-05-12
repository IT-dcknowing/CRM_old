<?php

namespace App\Http\Controllers;

use App\Helper\Reply;
use App\Models\Note;
use App\Models\NoteBlock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotesController extends AccountBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'Notes Workspace';
    }

    public function index()
    {
        $this->notes = Note::with('children')
            ->where(function($q) {
                $q->where('user_id', $this->user->id)
                  ->orWhere('is_private', false);
            })
            ->whereNull('parent_id')
            ->orderBy('updated_at', 'desc')
            ->get();
            
        return view('notes.index', $this->data);
    }

    public function store(Request $request)
    {
        $note = Note::create([
            'title' => $request->title ?? '',
            'icon' => $request->icon ?? ($request->is_folder ? 'Folder' : 'FileText'),
            'user_id' => $this->user->id,
            'noteable_id' => $request->noteable_id,
            'noteable_type' => $request->noteable_type,
            'parent_id' => $request->parent_id,
            'is_folder' => $request->is_folder ?? false,
            'is_private' => $request->is_private ?? false,
        ]);

        if (!$request->is_folder) {
            NoteBlock::create([
                'note_id' => $note->id,
                'type' => 'text',
                'content' => '',
                'order' => 0
            ]);
        }

        return response()->json(['status' => 'success', 'note' => $note]);
    }

    public function show($id)
    {
        $note = Note::with('blocks')->findOrFail($id);
        return response()->json($note);
    }

    public function update(Request $request, $id)
    {
        $note = Note::findOrFail($id);
        $note->update($request->only(['title', 'icon', 'is_private', 'parent_id']));
        return Reply::success(__('messages.updateSuccess'));
    }

    public function destroy($id)
    {
        Note::destroy($id);
        return Reply::success(__('messages.deleteSuccess'));
    }

    /**
     * Synchronise les blocs d'une note (Auto-save)
     */
    public function syncBlocks(Request $request, $id)
    {
        $note = Note::findOrFail($id);
        
        DB::transaction(function () use ($note, $request) {
            $incomingBlockIds = collect($request->blocks)->pluck('id')->filter();
            $note->blocks()->whereNotIn('id', $incomingBlockIds)->delete();

            foreach ($request->blocks as $index => $blockData) {
                NoteBlock::updateOrCreate(
                    ['id' => $blockData['id'] ?? null, 'note_id' => $note->id],
                    [
                        'type' => $blockData['type'],
                        'content' => $blockData['content'] ?? '',
                        'data' => $blockData['data'] ?? null,
                        'reminder_at' => $blockData['reminder_at'] ?? null,
                        'order' => $index
                    ]
                );
            }
        });

        $note->touch();
        return response()->json(['status' => 'success']);
    }

    public function upload(Request $request)
    {
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store('notes', 'public');
            return Reply::success('File uploaded', [
                'path' => asset('storage/' . $path),
                'name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'type' => $file->getMimeType()
            ]);
        }
        return Reply::error('No file uploaded');
    }
}
