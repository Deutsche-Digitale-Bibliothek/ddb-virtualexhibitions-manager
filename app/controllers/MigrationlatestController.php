<?php

class MigrationlatestController extends \BaseController {


    public $omimVersion = '1.0.0';
    public $msg = array();

    /**
     * Migrate to current version
     *
     * @return Response
     */
    public function getIndex()
    {

        // Only users with root privileges are allowed to migrate
        if (Auth::user()->isroot != 1) {
            return Redirect::to('admin')->with('error-message',
                'Sie haben keine Berechtigung, die Ressource \'admin/create\' zu verwenden.');
        }

        // Check if user has selected an instance
        $oid = (int) Input::get('oid');
        if (!isset($oid) || empty($oid)) {
            return Redirect::to('admin')->with('error-message',
                'Bitte wählen Sie eine Ausstellung, die Sie migrieren möchten.');
        }

        // Check if the instance is in the DB
        $va = OmimInstance::find($oid);
        if (!isset($va) || empty($va)) {
             return Redirect::to('admin')->with('error-message',
                 'Die von Ihnen gewählte Omeka Instanz existiert nicht in der Datenbank.');
        }

        // Check for necessary migrations
        $this->migrateOmekaUsers($va);
        $this->migrateExhibitId($va);

        // Manage migration messages
        if (empty($this->msg)) {
            $this->msg['Alle Datenbanktabellen'][] = '<span class="text-success glyphicon glyphicon-ok"></span> Keine Änderungen vorgenommen, Datenbank ist aktuell.';
        }
        $msg = $this->msg;
        return View::make('migratelatest.index', compact('va', 'msg'));
    }

    /**
     * migrate Omeka users db tbl
     *
     * @param $va object omim exhibition db data
     * @return void
     */
    public function migrateOmekaUsers($va)
    {
        $omekausersStructure = DB::select('DESCRIBE omeka_exh' . $va->id . '_users');
        if (!isset($omekausersStructure[8]) || $omekausersStructure[8]->Field !== 'confirm_use') {
            try {
                $result = DB::statement('ALTER TABLE `omeka_exh' . $va->id . '_users` ADD `confirm_use` tinyint(4) NULL DEFAULT \'0\' AFTER `role`');
            } catch (\Throwable $th) {
                $result = $th;
            }
            $resmsg = ($result === true)?
                '<span class="text-success glyphicon glyphicon-ok"></span>' :
                '<span class="text-danger glyphicon glyphicon-exclamation-sign"></span> ' .
                '<strong>Etwas stimmt nicht!</strong> Rückgabe Wert: <div>' . $result . '</div>';
            $this->msg['Tabelle omeka_users'][] = 'Feld "confirm_use" hinzufügen: ' . $resmsg;
        }
    }

    public function migrateExhibitId($va)
    {
        $exhibits = DB::select('SELECT id FROM omeka_exh' . $va->id . '_exhibits');
        if (count($exhibits) === 1 && $exhibits[0]->id !== 1) {
            try {
                $resultA = DB::statement('UPDATE omeka_exh' . $va->id .
                    '_exhibits SET id = 1 WHERE id = ' . $exhibits[0]->id);
                $resultB = DB::statement('UPDATE omeka_exh' . $va->id .
                    '_exhibit_pages SET `exhibit_id` = 1 WHERE `exhibit_id` = '. $exhibits[0]->id);
            } catch (\Throwable $th) {
                $result = $th;
            }
            $resmsg = ($resultA === true && $resultB === true)?
            '<span class="text-success glyphicon glyphicon-ok"></span>' :
            '<span class="text-danger glyphicon glyphicon-exclamation-sign"></span> ' .
            '<strong>Etwas stimmt nicht!</strong> Rückgabe Wert: <div>' . $result . '</div>';
            $this->msg['Tabelle exhibits und exhibit_pages'][] = 'Feld "id" auf 1 setzen: ' . $resmsg;
        }
    }


}