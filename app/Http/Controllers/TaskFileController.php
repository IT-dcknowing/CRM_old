<?php

namespace App\Http\Controllers;

use App\Helper\Files;
use App\Helper\Reply;
use App\Models\Task;
use App\Models\TaskFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TaskFileController extends AccountBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageIcon = 'icon-layers';
        $this->pageTitle = 'app.menu.taskFiles';
    }

    /**
     * @param Request $request
     * @return mixed|void
     * @throws \Froiden\RestAPI\Exceptions\RelatedResourceNotFoundException
     */
    public function store(Request $request)
    {
        $this->addPermission = user()->permission('add_task_files');
        $task = Task::with('users', 'project')->findOrFail($request->task_id);
        $assigne = est_assigne_tache($task);
        $createur = $task->added_by == user()->id;

        /* L'ancienne condition testait deux fois « added » (au lieu de
           « both ») et appelait abort_403(false), qui ne refuse jamais :
           le controle etait sans effet. Le formulaire de creation et celui
           d'edition proposent le depot des que le droit vaut « all » ou
           « added » ; toute personne qui peut modifier la tache peut donc
           y joindre un fichier, en plus de la regle propre au droit. */
        $isAllowed = $this->addPermission == 'all'
            || ($this->addPermission != 'none' && (
                ($this->addPermission == 'added' && $createur)
                || ($this->addPermission == 'owned' && $assigne)
                || ($this->addPermission == 'both' && ($assigne || $createur))
                || peut_modifier_tache($task)
            ));

        abort_403(!$isAllowed);

        if ($request->hasFile('file')) {
            // Dropzone envoie « file[] » (uploadMultiple) ; un envoi isole
            // arrive sous « file » : on accepte les deux formes.
            $fichiers = is_array($request->file('file')) ? $request->file('file') : [$request->file('file')];

            foreach ($fichiers as $fileData) {
                try {
                    $filename = Files::uploadLocalOrS3($fileData, TaskFile::FILE_PATH . '/' . $task->id);
                }
                catch (\Exception $e) {
                    Log::error('Echec du depot d\'un fichier de tache', [
                        'task_id' => $task->id,
                        'fichier' => $fileData->getClientOriginalName(),
                        'erreur' => $e->getMessage(),
                    ]);

                    // Message lisible par Dropzone au lieu d'une page 500.
                    return response()->json([
                        'status' => 'fail',
                        'message' => $fileData->getClientOriginalName() . ' : ' . $e->getMessage(),
                    ], 422);
                }

                $file = new TaskFile();
                $file->task_id = $task->id;
                $file->user_id = $this->user->id;
                $file->filename = $fileData->getClientOriginalName();
                $file->hashname = $filename;
                $file->size = $fileData->getSize();
                $file->save();

                $this->logTaskActivity($task->id, $this->user->id, 'fileActivity', $task->board_column_id);
            }

            $this->files = TaskFile::where('task_id', $request->task_id)->orderByDesc('id');
            $viewTaskFilePermission = user()->permission('view_task_files');

            if ($viewTaskFilePermission == 'added') {
                $this->files = $this->files->where('added_by', user()->id);
            }

            $this->files = $this->files->get();
            $view = view('tasks.files.show', $this->data)->render();

            return Reply::dataOnly(['status' => 'success', 'view' => $view]);
        }
    }

    /**
     * @param Request $request
     * @param int $id
     * @return array
     * @throws \Throwable
     */
    public function destroy(Request $request, $id)
    {
        $file = TaskFile::findOrFail($id);
        $this->deletePermission = user()->permission('delete_task_files');
        abort_403(!($this->deletePermission == 'all' || ($this->deletePermission == 'added' && $file->added_by == user()->id)));

        TaskFile::destroy($id);

        $this->files = TaskFile::where('task_id', $file->task_id)->orderByDesc('id')->get();

        $view = view('tasks.files.show', $this->data)->render();

        return Reply::successWithData(__('messages.deleteSuccess'), ['view' => $view]);

    }

    /**
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function download($id)
    {
        $file = TaskFile::whereRaw('md5(id) = ?', $id)->firstOrFail();
        $this->viewPermission = user()->permission('view_task_files');
        abort_403(!($this->viewPermission == 'all' || ($this->viewPermission == 'added' && $file->added_by == user()->id)));

        return download_local_s3($file, 'task-files/' . $file->task_id . '/' . $file->hashname);

    }

}
