<?php

class AdminController extends \BaseController {

    /**
     * Display a summary of omeka insatnces
     *
     * @return Response
     */
    public function getIndex()
    {

        switch (Input::get('sort-list')) {
            case 'title-asc':
                $orderBy = array('field' => 'title', 'direction' => 'ASC');
                break;
            case 'title-desc':
                $orderBy = array('field' => 'title', 'direction' => 'DESC');
                break;
            case 'date-asc':
                $orderBy = array('field' => 'created_at', 'direction' => 'ASC');
                break;
            case 'date-desc':
                $orderBy = array('field' => 'created_at', 'direction' => 'DESC');
                break;
            case 'slug-asc':
                $orderBy = array('field' => 'slug', 'direction' => 'ASC');
                break;
            case 'slug-desc':
                $orderBy = array('field' => 'slug', 'direction' => 'DESC');
                break;
            default:
                $orderBy = array('field' => 'created_at', 'direction' => 'ASC');
        }
        $omiminstance = OmimInstance::orderBy($orderBy['field'], $orderBy['direction'])->get();
        $configOmim = Config::get('omim');
        return View::make('admin.index', compact('omiminstance', 'configOmim'));
    }


    /**
     * Show the form for creating a new omeka instance
     *
     * @return Response
     */
    public function getCreate()
    {
        if (Auth::user()->isroot != 1) {
            return Redirect::to('admin')->with('error-message',
                'Sie haben keine Berechtigung die Ressource \'admin/create\' zu verwenden.');
        }
        return View::make('admin.create');
    }

    /**
     * Create an omeka instance
     *
     * @return Response
     */
    public function postCreate()
    {

        if (Auth::user()->isroot != 1) {
            return Redirect::to('admin')->with('error-message',
                'Sie haben keine Berechtigung die Ressource \'admin/create\' zu verwenden.');
        }


        $input = Input::all();
        $slugsInDb = DB::table('omim_instances')->lists('slug');
        $slugsInDbStr = implode(',', $slugsInDb);
        $addNotInExistingInstances = '';
        if (!empty($slugsInDbStr)) {
            if (in_array($input['slug'], $slugsInDb)) {
                return Redirect::to('admin/create')->withInput()->withErrors(
                    'Der gewählte Slug existiert bereits in der Datenbank, wählen Sie einen anderen!');
            }
            $addNotInExistingInstances = 'not_in:' . $slugsInDbStr;
        }
        $rules = array(
            'title' => 'required',
            'slug' => array(
                'required',
                'regex:/^[a-z0-9\-]*$/',
                'not_in:admin,user,login,assets,css,fonts,js,packages,downloads',
                'max:150',
                $addNotInExistingInstances
                ),
            'language' => array('required', 'regex:/^de|en$/')
        );
        $messages = array();
        $validator = Validator::make($input, $rules, $messages);
        if ($validator->fails()) {
            return Redirect::to('admin/create')->withInput()->withErrors($validator);
        } else {

            /**
             * Generate Omeka Instance
             */

            // Insert into omim db instance table
            $instance = new OmimInstance();
            $instance->title = $input['title'];
            $instance->subtitle = $input['subtitle'];
            $instance->slug = $input['slug'];
            $instance->language = $input['language'];
            $instance->langauge_fallback = 1;
            $instance->fk_root_instance_id = 0;
            $instance->state = 'active';
            $instance->save();

            // Gather some paths and vars
            $docRoot = rtrim($_SERVER['DOCUMENT_ROOT'], '\\/');
            $dbDataPath = realpath(base_path() . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'db');
            $deployArchive = base_path() . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'deploy.tar.gz';
            $configOmim = Config::get('omim');
            $configLocalDb = Config::get('database');

            // Make slug subdirectory in public folder
            $slug = escapeshellcmd($input['slug']);
            mkdir($docRoot . DIRECTORY_SEPARATOR . $slug, 0775);

            // Untar deploy archive
            exec('tar -xzf ' . $deployArchive . ' -C ' . $docRoot . DIRECTORY_SEPARATOR . $slug);
            // next is not neccessary as we are on user shell anyway
            // exec('chgrp -R ' . $configOmim['development']['user']['group'] . ' ' . $docRoot . DIRECTORY_SEPARATOR . $slug);

            // Set db.ini for new instance
            $dbIniContent =
                '[database]'                                                                     . "\n" .
                'host     = "' . $configLocalDb['connections']['mysql']['host']         . '"'    . "\n" .
                'username = "' . $configLocalDb['connections']['mysql']['username']     . '"'    . "\n" .
                'password = "' . $configLocalDb['connections']['mysql']['password']     . '"'    . "\n" .
                'dbname   = "' . $configLocalDb['connections']['mysql']['database']     . '"'    . "\n" .
                'prefix   = "' . $configOmim['common']['db']['prefix'] . $instance->id  . '_"'   . "\n" .
                'charset  = "' . $configLocalDb['connections']['mysql']['charset']      . '"'    . "\n" .
                'socket   = "' . $configLocalDb['connections']['mysql']['unix_socket']  . '"'    . "\n";

            file_put_contents($docRoot . DIRECTORY_SEPARATOR . $slug . DIRECTORY_SEPARATOR . 'db.ini', $dbIniContent);

            // Set .htaccess for new instance
            $htaccess = file_get_contents($docRoot . DIRECTORY_SEPARATOR . $slug . DIRECTORY_SEPARATOR . '.htaccess');
            $search = array(
                'RewriteBase /xxxx-exhibit-slug-xxxx'
            );
            $replace = array(
                'RewriteBase /' . $slug
            );
            $htaccessCurrent = str_replace($search, $replace, $htaccess);
            file_put_contents($docRoot . DIRECTORY_SEPARATOR . $slug . DIRECTORY_SEPARATOR . '.htaccess', $htaccessCurrent);

            // Generate and seed mysql tables with default contents
            $dbData = file_get_contents($dbDataPath . DIRECTORY_SEPARATOR . 'deploy-omeka-instance.sql');
            $exhibitSlug = $this->strToSafe($instance->title);
            $search = array(
                'xxxx-exhibit-number-xxxx',
                'xxxx-exhibit-title-xxxx',
                'xxxx-exhibit-description-xxxx',
                'xxxx-exhibit-slug-xxxx',
                'xxxx-instance-slug-xxxx'
            );
            $replace = array(
                $instance->id,
                $instance->title,
                $instance->subtitle,
                strtolower($exhibitSlug),
                $slug
            );
            $dbStatement = str_replace($search, $replace, $dbData);
            DB::connection()->getPdo()->exec($dbStatement);

            // Finished - redirect to admin index
            return Redirect::to('admin')->with('success-message', 'Instanz erfolgreich angelegt.');
        }

    }

    /**
     * Publish omeka instances on production servers
     *
     * @return Response
     */
    public function getPublish()
    {

        // Only users with root privileges are allowed pubish
        if (Auth::user()->isroot != 1) {
            return Redirect::to('admin')->with('error-message',
                'Sie haben keine Berechtigung die Ressource \'admin/create\' zu verwenden.');
        }

        // Check if user has selected an instance
        $oid = (int) Input::get('oid');
        if (!isset($oid) || empty($oid)) {
            return Redirect::to('admin')->with('error-message',
                'Bitte wählen Sie eine Ausstellung, die Sie veröffentlichen möchten.');
        }

        // Check if the instance is in the DB
        $va = OmimInstance::find($oid);

        if (!isset($va) || empty($va)) {
             return Redirect::to('admin')->with('error-message',
                 'Die von Ihnen gewählte Omeka Instanz existiert nicht in der Datenbank.');
        }

        // Check user confirmation
        $confirm = Input::get('confirm');
        if (!isset($confirm) || empty($confirm) || $confirm !== 'ok') {

            return View::make('admin.publish-confirm', compact('va'));

        } else {

            /**
             * From here we are going to publish the instance
             * **********************************************
             */

            // Gather configs and paths first
            $startPublishTime = time();
            $configOmim = Config::get('omim');
            $configLocalDb = Config::get('database');

            $docRoot = rtrim($_SERVER['DOCUMENT_ROOT'], '\\/');
            $currentInstancePath = $docRoot . DIRECTORY_SEPARATOR . $va->slug;

            $packageBasePath = base_path() . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'publish';
            $packageDir = $va->slug . '_' . $startPublishTime;
            $packagePath = $packageBasePath . DIRECTORY_SEPARATOR . $packageDir;
            $packageFile = $packagePath . DIRECTORY_SEPARATOR . 'files-' . $startPublishTime . '.tar.gz';

            $dbDumpPath = $docRoot . DIRECTORY_SEPARATOR . $va->slug . DIRECTORY_SEPARATOR . 'db-dump';
            $dbDumpFile = $dbDumpPath . DIRECTORY_SEPARATOR . 'db-' . $va->slug . '-'
                . $startPublishTime . '.sql';

            $htaccessFile = $dbDumpPath . DIRECTORY_SEPARATOR . $va->slug . '-'
                . $startPublishTime . '.htaccess';

            $dbIniFile = $dbDumpPath . DIRECTORY_SEPARATOR . 'db-' . $va->slug . '-'
                . $startPublishTime . '.ini';

            foreach ($configOmim['common']['db']['tables'] as $dbtable) {
                $dbtables[] = 'omeka_exh' . $va->id . '_' . $dbtable;
            }

            /**
             * Loop through remote server configs and
             * init ssh connections before packaging, because we will not proceed
             * if a connection fails.
             */
            foreach ($configOmim['remote'] as $remoteSrvNo => $remoteSrvConfig) {
                $productionDocRoot[$remoteSrvNo] = rtrim($remoteSrvConfig['production']['ssh']['docroot'], '\\/');
                $productionInstancePath[$remoteSrvNo] = $productionDocRoot[$remoteSrvNo] . DIRECTORY_SEPARATOR . $va->slug;
                $productionDeployArchive[$remoteSrvNo] = $remoteSrvConfig['production']['ssh']['datadir'] . DIRECTORY_SEPARATOR . 'deploy.tar.gz';

                $sshConnections[$remoteSrvNo] = $this->connectToProductionServer($remoteSrvConfig);
                if (!$sshConnections[$remoteSrvNo]) {
                    return Redirect::to('admin')->with('error-message',
                        'Verbindung zum Productionsserver '
                        . $remoteSrvConfig['production']['ssh']['host']
                        . 'konnte nicht hergestellt werden.');
                }
            }

            /**
             * Perpare deployment package
             * **************************
             */

            // generate package dir and db-dump dir
            mkdir($packagePath, 0775);
            if (!is_dir($dbDumpPath)) {
                mkdir($dbDumpPath, 0775);
            }

            // generate dump with the DB tables
            exec('mysqldump -u' . $configLocalDb['connections']['mysql']['username']
                . ' -p' . $configLocalDb['connections']['mysql']['password']
                . ' --socket=' . $configLocalDb['connections']['mysql']['unix_socket'] . ' '
                . $configLocalDb['connections']['mysql']['database'] . ' '
                . implode(' ', $dbtables) . ' > ' . $dbDumpFile);
            chmod($dbDumpFile, 0664);

            // Generate db.ini files for production servers
            foreach ($configOmim['remote'] as $remoteSrvNo => $remoteSrvConfig) {
                $configProductionDb = $remoteSrvConfig['production']['db'];
                $remoteDbIniFiles[$remoteSrvNo] = $dbIniFile . $remoteSrvNo;
                $dbIniContent =
                    '[database]'                                                                      . "\n" .
                    'host     = "' . $configProductionDb['host']                             . '"'    . "\n" .
                    'username = "' . $configProductionDb['username']                         . '"'    . "\n" .
                    'password = "' . $configProductionDb['password']                         . '"'    . "\n" .
                    'dbname   = "' . $configProductionDb['database']                         . '"'    . "\n" .
                    'prefix   = "' . $configOmim['common']['db']['prefix'] . $va->id         . '_"'   . "\n" .
                    'charset  = "' . $configProductionDb['charset']                          . '"'    . "\n" .
                    'socket   = "' . $configProductionDb['unix_socket']                      . '"'    . "\n";

                file_put_contents($remoteDbIniFiles[$remoteSrvNo], $dbIniContent);
            }

            // Copy .htaccess and change its cache settings for production server
            copy($currentInstancePath . DIRECTORY_SEPARATOR . '.htaccess', $htaccessFile);
            $htaccess = file_get_contents($htaccessFile);
            $search = array(
                '# <IfModule mod_expires.c>',
                '#    <FilesMatch "\.(js|ico|gif|jpg|png|css)$">',
                '#        ExpiresActive on',
                '#        ExpiresDefault "access plus 10 day"',
                '#    </FilesMatch>',
                '# </IfModule>'
            );
            $replace = array(
                '<IfModule mod_expires.c>',
                '   <FilesMatch "\.(js|ico|gif|jpg|png|css)$">',
                '       ExpiresActive on',
                '       ExpiresDefault "access plus 10 day"',
                '   </FilesMatch>',
                '</IfModule>'
            );
            $htaccessCurrent = str_replace($search, $replace, $htaccess);
            file_put_contents($htaccessFile, $htaccessCurrent);

            // TAR files
            exec('tar -cvzf ' . $packageFile . ' -C '
                . $currentInstancePath . ' files db-dump');

            // Clean up db-dump subdirectory in public/slug directory
            unlink ($dbDumpFile);
            foreach ($remoteDbIniFiles as $file) {
                unlink ($file);
            }
            unlink ($htaccessFile);
            if (true === $this->isDirEmpty($dbDumpPath)) {
                rmdir($dbDumpPath);
            }

            /**
             * Deploy package on all remote Servers
             * ************************************
             */
            foreach ($sshConnections as $remoteSrvNo => $ssh) {

                // Make destination folder (slug) on remote server
                $ssh->exec('mkdir -m 775 ' . $productionInstancePath[$remoteSrvNo]);

                // Unpack deploy archive on remote server
                $ssh->exec('tar -xzf ' . $productionDeployArchive[$remoteSrvNo] . ' -C '
                    . $productionInstancePath[$remoteSrvNo]);
                $ssh->exec('chgrp -R ' . $configOmim['remote'][$remoteSrvNo]['production']['ssh']['group']
                    . ' ' . $productionInstancePath[$remoteSrvNo]);

                // Upload files to remote server
                exec('rsync --numeric-ids -ze "ssh -p'
                    . $configOmim['remote'][$remoteSrvNo]['production']['ssh']['port']
                    . ' -i ' . $configOmim['remote'][$remoteSrvNo]['production']['ssh']['key']
                    . '" ' . $packageFile . ' '
                    . $configOmim['remote'][$remoteSrvNo]['production']['ssh']['username'] . '@'
                    . $configOmim['remote'][$remoteSrvNo]['production']['ssh']['host']
                    . ':' . $productionInstancePath[$remoteSrvNo]);

                // Extract files on remote server
                $ssh->exec('tar -xzf ' . $productionInstancePath[$remoteSrvNo] . DIRECTORY_SEPARATOR
                    . 'files-' . $startPublishTime . '.tar.gz'
                    . ' -C ' . $productionInstancePath[$remoteSrvNo]);

                // Read db dump into remote DB
                $ssh->exec('mysql -u'
                    . $configOmim['remote'][$remoteSrvNo]['production']['db']['username'] . ' -p'
                    . $configOmim['remote'][$remoteSrvNo]['production']['db']['password']  . ' -h '
                    . $configOmim['remote'][$remoteSrvNo]['production']['db']['host']  . ' --socket='
                    . $configOmim['remote'][$remoteSrvNo]['production']['db']['unix_socket'] . ' '
                    . $configOmim['remote'][$remoteSrvNo]['production']['db']['database'] . ' < '
                    . $productionInstancePath[$remoteSrvNo] . DIRECTORY_SEPARATOR . 'db-dump'
                    . DIRECTORY_SEPARATOR . 'db-' . $va->slug . '-' . $startPublishTime . '.sql');

                // Move db.ini file on remote server
                $ssh->exec('mv ' . $productionInstancePath[$remoteSrvNo] . DIRECTORY_SEPARATOR
                    . 'db-dump' . DIRECTORY_SEPARATOR . 'db-' . $va->slug . '-'
                    . $startPublishTime . '.ini' . $remoteSrvNo . ' ' . $productionInstancePath[$remoteSrvNo]
                    . DIRECTORY_SEPARATOR . 'db.ini');

                // Move .htaccess file on remote server
                $ssh->exec('mv ' . $productionInstancePath[$remoteSrvNo] . DIRECTORY_SEPARATOR
                    . 'db-dump' . DIRECTORY_SEPARATOR . $va->slug . '-'
                    . $startPublishTime . '.htaccess ' . $productionInstancePath[$remoteSrvNo]
                    . DIRECTORY_SEPARATOR . '.htaccess');

                // Clean up remote server
                $ssh->exec('rm -rf ' . $productionInstancePath[$remoteSrvNo] . DIRECTORY_SEPARATOR . 'db-dump');

            }

            // Clean up lokal server
            exec('rm -rf ' . $packagePath);

            // Update db va instance
            $va->last_published_at = date('Y-m-d H:i:s');
            $va->save();

            return Redirect::to('admin')->with('success-message', 'Instanz erfolgreich veröffentlicht.');
        }

    }

    /**
     * Show selection for delete - select production and or developmant server
     *
     * @return Response
     */
    public function getDelete()
    {

        /**
         * only users with root privileges are allowed pubish
         */
        if (Auth::user()->isroot != 1) {
            return Redirect::to('admin')->with('error-message',
                'Sie haben keine Berechtigung die Ressource \'admin/create\' zu verwenden.');
        }

        /**
         * Check if user has selected an instance
         */
        $oid = (int) Input::get('oid');
        if (!isset($oid) || empty($oid)) {
            return Redirect::to('admin')->with('error-message',
                'Bitte wählen Sie eine Ausstellung, die Sie löschen möchten.');
        }

        /**
         * Check if the instance is in the DB
         */
        $va = OmimInstance::find($oid);

        if (!isset($va) || empty($va)) {
             return Redirect::to('admin')->with('error-message',
                 'Die von Ihnen gewählte Omeka Instanz existiert nicht in der Datenbank.');
        }

        return View::make('admin.delete-select', compact('va'));

    }

    /**
     * Show selection for delete - select production and or developmant server
     *
     * @return Response
     */
    public function postDelete()
    {
        /**
         * only users with root privileges are allowed pubish
         */
        if (Auth::user()->isroot != 1) {
            return Redirect::to('admin')->with('error-message',
                'Sie haben keine Berechtigung die Ressource \'admin/create\' zu verwenden.');
        }

        /**
         * Check if user has selected an instance
         */
        $oid = (int) Input::get('oid');
        if (!isset($oid) || empty($oid)) {
            return Redirect::to('admin')->with('error-message',
                'Bitte wählen Sie eine Ausstellung, die Sie löschen möchten.');
        }

        /**
         * Check if the instance is in the DB
         */
        $va = OmimInstance::find($oid);

        if (!isset($va) || empty($va)) {
             return Redirect::to('admin')->with('error-message',
                 'Die von Ihnen gewählte Omeka Instanz existiert nicht in der Datenbank.');
        }

        /**
         * Check user selection
         */
        $delDevelopment = Input::get('del-development');
        $delProduktion = Input::get('del-produktion');
        if ((!isset($delDevelopment) || $delDevelopment !== 'ok') &&
            (!isset($delProduktion) || $delProduktion !== 'ok')) {

            return Redirect::to('admin/delete?oid=' . $va->id)->with('error-message',
                 'Bitte wählen Sie aus, was gelöscht werden soll.');

        } else {

            /**
             * From here we are going to delete the selected instances.
             * Gather some vars first.
             */
            $configOmim = Config::get('omim');
            $configLocalDb = Config::get('database');

            $docRoot = rtrim($_SERVER['DOCUMENT_ROOT'], '\\/');
            $currentInstancePath = $docRoot . DIRECTORY_SEPARATOR . $va->slug;

            foreach ($configOmim['common']['db']['tables'] as $dbtable) {
                $dbtables[] = 'DROP TABLE IF EXISTS \`omeka_exh' . $va->id . '_' . $dbtable . '\`; ';
            }
            $dbTablesStrg = implode('', $dbtables);

            // $configProductionDb = $configOmim['production']['db'];
            $msg = '';

            /**
             * Delete from production
             */
            if (isset($delProduktion) && $delProduktion == 'ok') {

                /**
                 * Loop through remote server configs and
                 * init ssh connections before packaging, because we will not proceed
                 * if a connection fails.
                 */
                foreach ($configOmim['remote'] as $remoteSrvNo => $remoteSrvConfig) {
                    $productionDocRoot[$remoteSrvNo] = rtrim($remoteSrvConfig['production']['ssh']['docroot'], '\\/');
                    $productionInstancePath[$remoteSrvNo] = $productionDocRoot[$remoteSrvNo] . DIRECTORY_SEPARATOR . $va->slug;
                    // $productionDeployArchive[$remoteSrvNo] = $remoteSrvConfig['production']['ssh']['datadir'] . DIRECTORY_SEPARATOR . 'deploy.tar.gz';

                    $sshConnections[$remoteSrvNo] = $this->connectToProductionServer($remoteSrvConfig);
                    if (!$sshConnections[$remoteSrvNo]) {
                        return Redirect::to('admin/delete?oid=' . $va->id)->with('error-message',
                        'Verbindung zum Productionsserver konnte nicht hergestellt werden.');
                    }
                }

                /* Delete from all Remote Servers */
                foreach ($sshConnections as $remoteSrvNo => $ssh) {

                    /**
                     * Delete db tables
                     */
                    $ssh->exec('echo "' . $dbTablesStrg
                        . '" | mysql -u'
                        . $configOmim['remote'][$remoteSrvNo]['production']['db']['username'] . ' -p'
                        . $configOmim['remote'][$remoteSrvNo]['production']['db']['password'] . ' --socket='
                        . $configOmim['remote'][$remoteSrvNo]['production']['db']['unix_socket'] . ' -v '
                        . $configOmim['remote'][$remoteSrvNo]['production']['db']['database']
                    );

                    /**
                     * Delete files
                     */
                    $ssh->exec('rm -rf ' . $productionInstancePath[$remoteSrvNo]);

                }

                /**
                 * Update db va instance
                 */
                if (isset($delDevelopment) || $delDevelopment != 'ok') {
                    $va->last_unpublished_at = date('Y-m-d H:i:s');
                    $va->save();
                }

                $msg .= 'Ausstellung vom Produktionsserver gelöscht.<br>';

            }

            /**
             * Delete from developmant
             */
            if (isset($delDevelopment) && $delDevelopment == 'ok') {

                /**
                 * Delete db tables
                 */
                exec('echo "' . $dbTablesStrg
                    . '" | mysql -u'
                    . $configLocalDb['connections']['mysql']['username'] . ' -p'
                    . $configLocalDb['connections']['mysql']['password'] . ' --socket='
                    . $configLocalDb['connections']['mysql']['unix_socket'] . ' -v '
                    . $configLocalDb['connections']['mysql']['database']
                );

                /**
                 * Delete files
                 */
                exec('rm -rf ' . $currentInstancePath);

                /**
                 * Delete instance row from omim db table
                 */
                $va->delete();

                $msg .= 'Ausstellung vom Entwicklungsserver gelöscht.<br>';

            }

            return Redirect::to('admin')->with('success-message', $msg);

        }
    }


    /**
     * Handle Downloads
     *
     * @return Response
     */
    public function getDownload()
    {

        /**
         * only users with root privileges are allowed to download
         */
        if (Auth::user()->isroot != 1) {
            return Redirect::to('admin')->with('error-message',
                'Sie haben keine Berechtigung die Ressource \'admin/download\' zu verwenden.');
        }

        /**
         * Check if user has selected an instance
         */
        $oid = (int) Input::get('oid');
        if (!isset($oid) || empty($oid)) {
            return Redirect::to('admin')->with('error-message',
                'Bitte wählen Sie eine Ausstellung, die Sie herunterladen möchten.');
        }

        /**
         * Check if the instance is in the DB
         */
        $va = OmimInstance::find($oid);

        if (!isset($va) || empty($va)) {
             return Redirect::to('admin')->with('error-message',
                 'Die von Ihnen gewählte Omeka Instanz existiert nicht in der Datenbank.');
        }

        /**
         * Generate download links
         * Get configs and paths first
         */
        $startPublishTime = time();
        $configOmim = Config::get('omim');
        $configLocalDb = Config::get('database');

        $docRoot = rtrim($_SERVER['DOCUMENT_ROOT'], '\\/');
        $currentInstancePath = $docRoot . DIRECTORY_SEPARATOR . $va->slug;

        $packageBasePath = $docRoot . DIRECTORY_SEPARATOR . 'downloads';
        $packageFile = $packageBasePath . DIRECTORY_SEPARATOR . 'files-' . $va->slug . '-' . $startPublishTime . '.tar.gz';

        $dbDumpFile = $packageBasePath . DIRECTORY_SEPARATOR . 'db-' . $va->slug . '-'
            . $startPublishTime . '.sql';
        $dbDumpFileTar = $packageBasePath . DIRECTORY_SEPARATOR . 'db-' . $va->slug . '-'
            . $startPublishTime . '.tar.gz';
        $dbIniFile = $packageBasePath . DIRECTORY_SEPARATOR . 'db-' . $va->slug . '-'
            . $startPublishTime . '.ini';

        foreach ($configOmim['common']['db']['tables'] as $dbtable) {
            $dbtables[] = 'omeka_exh' . $va->id . '_' . $dbtable;
        }

        /**
         * generate dump with the DB tables
         */
        exec('mysqldump -u' . $configLocalDb['connections']['mysql']['username']
            . ' -p' . $configLocalDb['connections']['mysql']['password']
            . ' --socket=' . $configLocalDb['connections']['mysql']['unix_socket'] . ' '
            . $configLocalDb['connections']['mysql']['database'] . ' '
            . implode(' ', $dbtables) . ' > ' . $dbDumpFile);
        chmod($dbDumpFile, 0664);

        /**
         * Generate db.ini for production server
         */
        $dbIniContent =
            '[database]'       . "\n" .
            'host     = ""'    . "\n" .
            'username = ""'    . "\n" .
            'password = ""'    . "\n" .
            'dbname   = ""'    . "\n" .
            'prefix   = "' . $configOmim['common']['db']['prefix'] . $va->id          . '_"'   . "\n" .
            'charset  = "' . $configLocalDb['connections']['mysql']['charset']        . '"'    . "\n" .
            'socket   = "' . $configLocalDb['connections']['mysql']['unix_socket']    . '"'    . "\n";

        file_put_contents($dbIniFile, $dbIniContent);

        /**
         * TAR files
         */
        exec('tar -cvzf ' . $packageFile . ' -C '
            . $currentInstancePath . ' files');
        exec('tar -cvzf ' . $dbDumpFileTar . ' -C '
            . $packageBasePath . ' '
            . 'db-' . $va->slug . '-' . $startPublishTime . '.sql'
            .' '
            . 'db-' . $va->slug . '-' . $startPublishTime . '.ini');

        /**
         * Clean up db-dump subdirectory in public/slug directory
         */
        unlink ($dbDumpFile);
        unlink ($dbIniFile);

        $offsettime = $startPublishTime - (60 * 60 * 24);
        $handle = opendir($packageBasePath);
        while (false !== ($file = readdir($handle))) {
            $currentFilePath = $packageBasePath . DIRECTORY_SEPARATOR . $file;
            if ($file != "." && $file != ".." &&
                is_file($currentFilePath) &&
                filemtime($currentFilePath) < $offsettime
            ) {
                unlink($currentFilePath);
            }
        }
        closedir($handle);

        return View::make('admin.download', compact('va', 'startPublishTime'));

    }

    /**
     * Format a string to be safe e.g. for URL or filename
     *
     * @param    string     $name   The string name
     * @param    integer    $maxlen Maximun permited string lenght
     * @param    bool       $remdot Remove dots from string
     * @return   string     safe string
     */
    protected function strToSafe($name, $maxlen = '', $remdot = TRUE)
    {
        $name = trim($name);
        $quotes = array(
            '&quot;'  => '',
            '&acute;' => '',
            '&bdquo;' => '',
            '&ldquo;' => '',
            '&rdquo;' => '',
            '&lsquo;' => '',
            '&rsquo;' => '',
            '&rsquo;' => '',
            '&#34;'   => '',
            '&#034;'   => '',
            '&#39;'   => '',
            '&#039;'   => '',
            '&#96;'   => '',
            '&#096;'   => '',
            '&#128;'  => '',
            '&#132;'  => '',
            '&#147;'  => '',
            '&#148;'  => '',
            '&#145;'  => '',
            '&#146;'  => '',
            '&#158;'  => '',
            '&#180;'  => '',
            '&#194;'  => '',
            '&#226;'  => ''
        );
        $name = strtr($name, $quotes);
        $name = html_entity_decode($name, ENT_NOQUOTES, 'UTF-8');
        $break = array(
            "\n" => '',
            "\r" => ''
        );
        $german = array(
            'Ä' => 'Ae',
            'Ö' => 'Oe',
            'Ü' => 'Ue',
            'ä' => 'ae',
            'ö' => 'oe',
            'ü' => 'ue',
            'ß' => 'ss',
            '@' => '-bei-',
            '&' => '-und-'
        );
        $noalpha = 'ÁÉÍÓÚÝáéíóúýÂÊÎÔÛâêîôûÀÈÌÒÙàèìòùÄËÏÖÜäëïöüÿÃãÕõÅåÑñÇç@°ºªß&';
        $alpha   = 'AEIOUYaeiouyAEIOUaeiouAEIOUaeiouAEIOUaeiouyAaOoAaNnCcaooas-';
        if (!empty($maxlen)) {
            $name = substr($name, 0, $maxlen);
        }
        $name = strtr($name, $break);
        $name = strtr($name, $german);
        $name = strtr($name, $noalpha, $alpha);
        // not permitted chars are replaced with "-"
        if ($remdot === TRUE) {
            $name = preg_replace('/[^a-zA-Z0-9\-]/', '-', $name);
        } else {
            $name = preg_replace('/[^a-zA-Z0-9\-\.]/', '-', $name);
        }
        return preg_replace(array('/^[\-]+(.*)$/', '/[\-]{2,}/'), array('$1', '-'), $name);
    }

    /**
     * Check if a directory is empty
     *
     * @param    string     $dir   Directory to search within
     * @return   mixed      void or boolean true or false
     */
    protected function isDirEmpty($dir)
    {
        if (!is_readable($dir)) {
            return null;
        }
        $handle = opendir($dir);
        while (false !== ($entry = readdir($handle))) {
            if ($entry != "." && $entry != "..") {
                closedir($handle);
                return false;
            }
        }
        closedir($handle);
        return true;
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

            return false;
        }

        $ssh = new Net_SSH2($configOmim['production']['ssh']['host'], $configOmim['production']['ssh']['port']);


        /**
         * Password Authentification is not supported by rsync in scripting mode,
         * so we will disable it here too.
         */
        // if (array_key_exists('password', $configOmim['production']['ssh']) &&
        //     !empty($configOmim['production']['ssh']['password'])) {

        //     if (!$ssh->login($configOmim['production']['ssh']['username'],
        //         $configOmim['production']['ssh']['password'])) {

        //         return false;
        //     }

        //     return $ssh;

        // } else

        if (array_key_exists('key', $configOmim['production']['ssh']) &&
            !empty($configOmim['production']['ssh']['key'])) {

            $key = new Crypt_RSA();

            if (array_key_exists('keyphrase', $configOmim['production']['ssh']) &&
            !empty($configOmim['production']['ssh']['keyphrase'])) {

                $key->setPassword($configOmim['production']['ssh']['keyphrase']);
            }

            $key->loadKey(file_get_contents($configOmim['production']['ssh']['key']));

            if (!$ssh->login($configOmim['production']['ssh']['username'], $key)) {

                return false;
            }

            return $ssh;

        } else {
            return false;
        }
    }

    /**
     * Publish omeka instances on production servers
     *
     * @return Response
     */
    public function getTestssh()
    {

        // Only users with root privileges are allowed
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
            echo 'Initialisiere Verbindung zu Remote Server Nr. '  . $remoteSrvNo . ', Host ' . $remoteSrvConfig['production']['ssh']['host'] . "<br>\n";
            $sshConnections[$remoteSrvNo] = $this->connectTestToProductionServer($remoteSrvConfig);
            if (!$sshConnections[$remoteSrvNo]) {
                die('Verbindung zum Productionsserver '
                    . $remoteSrvConfig['production']['ssh']['host']
                    . 'konnte nicht hergestellt werden.');
            } else {
                echo 'pwd Kommando auf remote server: ' . "<br>\n";
                echo $sshConnections{$remoteSrvNo}->exec('pwd') . "<br>\n";
            }
        }
    }


    /**
     * SSH connect to production server
     *
     * @param    array     production server config array
     * @return   object    ssh object or false on failure
     */
    protected function connectTestToProductionServer($configOmim)
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