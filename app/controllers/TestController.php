<?php

class TestController extends \BaseController {

    /**
     * Publish omeka instances on production servers
     *
     * @return Response
     */
    public function getSsh()
    {

        // Only users with root privileges are allowed pubish
        if (Auth::user()->isroot != 1) {
            return Redirect::to('admin')->with('error-message',
                'Sie haben keine Berechtigung die Ressource \'test/ssh\' zu verwenden.');
        }


        // Gather configs and paths first
        $configOmim = Config::get('omim');
        $docRoot = rtrim($_SERVER['DOCUMENT_ROOT'], '\\/');


        /**
         * Loop through remote server configs and
         * init ssh connections.
         */
        foreach ($configOmim['remote'] as $remoteSrvNo => $remoteSrvConfig) {
            echo 'Initialisiere Verbindung zu Remote Server Nr. ' $remoteSrvNo . ', Host ' . $remoteSrvConfig['production']['ssh']['host'] . "<br>\n";
            $sshConnections[$remoteSrvNo] = $this->connectToProductionServer($remoteSrvConfig);
            if (!$sshConnections[$remoteSrvNo]) {
                die('Verbindung zum Productionsserver '
                    . $remoteSrvConfig['production']['ssh']['host']
                    . 'konnte nicht hergestellt werden.');
            }
        }

        foreach ($sshConnections as $remoteSrvNo => $ssh) {
            echo 'pwd Kommando auf remote server: ' . "<br>\n";
            echo $ssh->exec('pwd') . "<br>\n";
        }
    }


    /**
     * SSH connect to production server
     *
     * @param    array     production server config array
     * @return   object    ssh object or false on failure
     */
    protected function connectToProductionServer($configOmim)
    {
        if (!is_array($configOmim) || !array_key_exists('production', $configOmim) ||
            !array_key_exists('ssh', $configOmim['production'])) {
            echo 'Konfigurationsdatei enthält Fehler! - Abschnitt ssh nicht definiert' . "<br>\n";
            return false;
        }

        $ssh = new Net_SSH2($configOmim['production']['ssh']['host'], $configOmim['production']['ssh']['port']);

        if (array_key_exists('key', $configOmim['production']['ssh']) &&
            !empty($configOmim['production']['ssh']['key'])) {

            $key = new Crypt_RSA();

            if (array_key_exists('keyphrase', $configOmim['production']['ssh']) &&
            !empty($configOmim['production']['ssh']['keyphrase'])) {

                $key->setPassword($configOmim['production']['ssh']['keyphrase']);
            }

            if (!$key->loadKey(file_get_contents($configOmim['production']['ssh']['key']))) {
                 echo 'Fehler im RSA Schüssel! - Schlüssel konnte nicht geladen werden.' . "<br>\n";
                 return false;
            }

            if (!$ssh->login($configOmim['production']['ssh']['username'], $key)) {
                echo 'Login fehlgeschlagen.' . "<br>\n";
                return false;
            } else {
                echo 'Login erfolgreich.' . "<br>\n";
            }

            return $ssh;

        } else {
            echo 'Konfigurationsdatei enthält Fehler! - Pfad zum SSH Schlüssel nicht definiert.' . "<br>\n";
            return false;
        }
    }

}
