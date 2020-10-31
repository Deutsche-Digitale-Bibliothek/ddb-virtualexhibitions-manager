<?php

class AdminController extends \BaseController {


    public $omimVersion = '1.0.0';

    /**
     * Display a summary of omeka insatnces
     *
     * @return Response
     */
    public function getIndex()
    {
        $this->checkOmimInstanceTbl();
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
        $exhibitTypes = array(
            'leporello' => 'Leporello (klassische Ausstellung)',
            'litfass' => 'Litfaß Partner Standard (Single Page Ausstellung)',
            'litfass_featured' => 'Litfaß Partner Featured (Single Page Ausstellung)',
            'litfass_ddb' => 'Litfaß DDB Exhibition (Single Page Ausstellung)'
        );
        return View::make('admin.index', compact('omiminstance', 'configOmim', 'exhibitTypes'));
    }

    public function checkOmimInstanceTbl()
    {
        $omiminstancesStructure = DB::select('DESCRIBE omim_instances');
        if (!array_key_exists(12, $omiminstancesStructure)
            || (isset($omiminstancesStructure[12]) && $omiminstancesStructure{12}->Field !== 'version')) {

            DB::statement("ALTER TABLE `omim_instances` ADD `version` varchar(255) COLLATE 'utf8_unicode_ci' NULL;");
        }
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
                'Sie haben keine Berechtigung, die Ressource \'admin/create\' zu verwenden.');
        }
        $users = OmimOmekaUser::all();
        $colorPalettes = OmimExhibitColorPalette::all();
        $showNames = array();
        foreach ($colorPalettes as $colorPaletteKey => $colorPalette) {
            preg_match('/custom_([^_]+_)(.*)/', $colorPalette['palette'], $matches);
            if (count($matches) === 3) {
                $showNames[$colorPaletteKey] = $matches[2];
            } else {
                $showNames[$colorPaletteKey] = $colorPalette['palette'];
            }
        }
        // var_dump($colorPalettes);
        return View::make('admin.create', compact('users', 'colorPalettes', 'showNames'));
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
                'Sie haben keine Berechtigung, die Ressource \'admin/create\' zu verwenden.');
        }


        $input = Input::all();
        // var_dump($input);
        // return;

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
            $instance->version = $this->omimVersion;
            $instance->exhibit_type	= $input['exhibit_type'];
            if ($input['exhibit_type'] === 'litfass_ddb') {
                $instance->color_palette = 'ddb';
            } elseif (isset($input['color_palette']) && !empty($input['color_palette'])) {
                $instance->color_palette = $input['color_palette'];
            } else {
                $instance->color_palette = 'a';
            }
            $instance->save();

            // Gather some paths and vars
            $docRoot = rtrim($_SERVER['DOCUMENT_ROOT'], '\\/');
            $dbDataPath = realpath(base_path() . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'db');
            if (isset($input['exhibit_type']) && $input['exhibit_type'] !== 'leporello') {
                $deployArchive = base_path() . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'deploy_lf.tar.gz';
            } else {
                $deployArchive = base_path() . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'deploy.tar.gz';
            }
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
            $exhibitSlug = $this->strToSafe($instance->title);
            if (isset($input['exhibit_type']) && $input['exhibit_type'] !== 'leporello') {
                $dbData = file_get_contents($dbDataPath . DIRECTORY_SEPARATOR . 'deploy-omeka-instance-lf.sql');
                $search = array(
                    'xxxx-exhibit-number-xxxx',
                    'xxxx-exhibit-title-xxxx',
                    'xxxx-exhibit-description-xxxx',
                    'xxxx-exhibit-slug-xxxx',
                    'xxxx-instance-slug-xxxx',
                    'xxxx-exhibit-date-xxxx',
                    'xxxx-exhibit-colorpalette-xxxx',
                    'xxxx-exhibit-type-xxxx'
                );
                $replace = array(
                    $instance->id,
                    $instance->title,
                    $instance->subtitle,
                    strtolower($exhibitSlug),
                    $slug,
                    date('Y-m-d H:i:s'),
                    $instance->color_palette,
                    $input['exhibit_type']
                );
            } else {
                $dbData = file_get_contents($dbDataPath . DIRECTORY_SEPARATOR . 'deploy-omeka-instance.sql');
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
            }
            $dbStatement = str_replace($search, $replace, $dbData);
            DB::connection()->getPdo()->exec($dbStatement);

            // insert selected Omeka Base users
            if (isset($input['user']) && !empty($input['user']) && is_array($input['user'])) {
                foreach ($input['user'] as $userId => $userActive) {
                    $omimOmekaUser = OmimOmekaUser::find($userId);
                    if (isset($omimOmekaUser) && !empty($omimOmekaUser)) {
                        DB::insert('insert into omeka_exh' . $instance->id . '_users '
                            . '(username, name, email, password, salt, active, role) '
                            . 'values (?, ?, ?, ?, ?, ?, ?)',
                            array(
                                $omimOmekaUser->username,
                                $omimOmekaUser->name,
                                $omimOmekaUser->email,
                                $omimOmekaUser->password,
                                $omimOmekaUser->salt,
                                1,
                                $omimOmekaUser->role
                            )
                        );
                    }
                }
            }

            // insert selected color palette
            if (isset($input['exhibit_type']) && $input['exhibit_type'] !== 'leporello') {
                $colorPalettes = OmimExhibitColorPalette::where('palette', $instance->color_palette)->get()->toArray();
                foreach ($colorPalettes as $colorPalette) {
                    DB::insert('insert into omeka_exh' . $instance->id . '_exhibit_color_palettes '
                        . '(`palette`, `color`, `hex`, `type`, `menu`) '
                        . 'values (?, ?, ?, ?, ?)',
                        array(
                            $colorPalette['palette'],
                            $colorPalette['color'],
                            $colorPalette['hex'],
                            $colorPalette['type'],
                            $colorPalette['menu']
                        )
                    );
                }
            }

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
                'Sie haben keine Berechtigung, die Ressource \'admin/create\' zu verwenden.');
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

            // Get user input
            $input = Input::all();

            // Save publish date into db
            $vaPublished = DB::table('omeka_exh' . $oid . '_options')->where('name', 'publish-date')->first();
            // $publishDate = DB::connection()->getPdo()->quote($input['publish-date']);
            if ($vaPublished) {
                DB::table('omeka_exh' . $oid . '_options')
                    ->where('name', 'publish-date')
                    ->update(array('value' => $input['publish-date']));

            } else {
                DB::table('omeka_exh' . $oid . '_options')
                    ->insert(array(
                        'name' => 'publish-date',
                        'value' => $input['publish-date']
                    )
                );
            }

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
                if ($dbtable === 'exhibit_color_palettes'
                    && (!isset($va->exhibit_type) || $va->exhibit_type === 'leporello')) {
                    continue;
                }
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
                $productionDeployArchive[$remoteSrvNo] = rtrim($remoteSrvConfig['production']['ssh']['datadir'], '\\/') . DIRECTORY_SEPARATOR . 'deploy.tar.gz';
                $productionDeployArchiveLf[$remoteSrvNo] = rtrim($remoteSrvConfig['production']['ssh']['datadir'], '\\/') . DIRECTORY_SEPARATOR . 'deploy_lf.tar.gz';

                $sshConnections[$remoteSrvNo] = $this->connectToProductionServer($remoteSrvConfig);
                if (!$sshConnections[$remoteSrvNo]) {
                    return Redirect::to('admin')->with('error-message',
                        'Verbindung zum Productionsserver '
                        . $remoteSrvConfig['production']['ssh']['host']
                        . 'konnte nicht hergestellt werden.');
                }
                $sftpConnections[$remoteSrvNo] = $this->sftpConnectToProductionServer($remoteSrvConfig);
            }

            /**
             * Check deploy.tar.gz skeleton package
             * ************************************
             */
            $localDeploymentSkeleton = base_path() . DIRECTORY_SEPARATOR
                . 'data' . DIRECTORY_SEPARATOR
                . 'production'. DIRECTORY_SEPARATOR
                . 'deploy.tar.gz';
            $localDeploymentSkeletonLf = base_path() . DIRECTORY_SEPARATOR
                . 'data' . DIRECTORY_SEPARATOR
                . 'production'. DIRECTORY_SEPARATOR
                . 'deploy_lf.tar.gz';
             $localDeploymentSkeletonMd5 = md5_file($localDeploymentSkeleton);
             $localDeploymentSkeletonLfMd5 = md5_file($localDeploymentSkeletonLf);
             foreach ($sshConnections as $remoteSrvNo => $ssh) {
                $productionDeploymentSkeletonMd5 = $ssh->exec('md5sum ' . $productionDeployArchive[$remoteSrvNo]);
                $productionDeploymentSkeletonMd5 = substr(
                    $productionDeploymentSkeletonMd5, 0, strpos($productionDeploymentSkeletonMd5, ' ')
                );
                $productionDeploymentSkeletonLfMd5 = $ssh->exec('md5sum ' . $productionDeployArchiveLf[$remoteSrvNo]);
                $productionDeploymentSkeletonLfMd5 = substr(
                    $productionDeploymentSkeletonLfMd5, 0, strpos($productionDeploymentSkeletonLfMd5, ' ')
                );

                if ($productionDeploymentSkeletonMd5 !== $localDeploymentSkeletonMd5) {
                    $updateSkeleton = $sftpConnections[$remoteSrvNo]->put(
                        $productionDeployArchive[$remoteSrvNo],
                        $localDeploymentSkeleton,
                        1
                    );
                    if ($updateSkeleton != true) {
                        return Redirect::to('admin')->with('error-message',
                            'Es gab Probleme der Aktualisierung des Standard-Archivs ' .
                            'auf Ausspielungsserver Nr.' . $remoteSrvNo . '.');
                    }
                }

                if ($productionDeploymentSkeletonLfMd5 !== $localDeploymentSkeletonLfMd5) {
                    $updateSkeleton = $sftpConnections[$remoteSrvNo]->put(
                        $productionDeployArchiveLf[$remoteSrvNo],
                        $localDeploymentSkeletonLf,
                        1
                    );
                    if ($updateSkeleton != true) {
                        return Redirect::to('admin')->with('error-message',
                            'Es gab Probleme der Aktualisierung des Standard-Archivs ' .
                            'auf Ausspielungsserver Nr.' . $remoteSrvNo . '.');
                    }
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
            $localDbSocket = ' ';
            if (isset($configLocalDb['connections']['mysql']['unix_socket'])
                && !empty($configLocalDb['connections']['mysql']['unix_socket'])) {
                $localDbSocket = ' --socket='
                    . $configLocalDb['connections']['mysql']['unix_socket']
                    . ' ';
            }
            exec('mysqldump -u' . $configLocalDb['connections']['mysql']['username']
                . ' -p' . $configLocalDb['connections']['mysql']['password']
                . $localDbSocket
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
                '   <FilesMatch "\.(?i:js|ico|gif|jpg|jpeg|png|css|woff|woff2|ttf|eot|svg)$">',
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

                // Delete destination folder (slug) on remote server if it exists.
                $checkTargetDir = $ssh->exec('if test -d ' . $productionInstancePath[$remoteSrvNo] . '; then echo "ok"; else echo "no"; fi');
                $checkTargetDir = str_replace(array("\r", "\n"), '', $checkTargetDir);
                if ($checkTargetDir == 'ok') {
                    $ssh->exec('rm -rf ' . $productionInstancePath[$remoteSrvNo]);
                }

                // Make destination folder (slug) on remote server
                $ssh->exec('mkdir -m 775 ' . $productionInstancePath[$remoteSrvNo]);

                // Unpack deploy archive on remote server
                if (!isset($va->exhibit_type) || empty($va->exhibit_type) || $va->exhibit_type === 'leporello') {
                    $ssh->exec('tar -xzf ' . $productionDeployArchive[$remoteSrvNo] . ' -C '
                        . $productionInstancePath[$remoteSrvNo]);
                } else {
                    $ssh->exec('tar -xzf ' . $productionDeployArchiveLf[$remoteSrvNo] . ' -C '
                        . $productionInstancePath[$remoteSrvNo]);
                }
                $ssh->exec('chgrp -R ' . $configOmim['remote'][$remoteSrvNo]['production']['ssh']['group']
                    . ' ' . $productionInstancePath[$remoteSrvNo]);

                // Upload files to remote server
                // exec('rsync --numeric-ids -ze "ssh -p'
                //     . $configOmim['remote'][$remoteSrvNo]['production']['ssh']['port']
                //     . ' -i ' . $configOmim['remote'][$remoteSrvNo]['production']['ssh']['key']
                //     . '" ' . $packageFile . ' '
                //     . $configOmim['remote'][$remoteSrvNo]['production']['ssh']['username'] . '@'
                //     . $configOmim['remote'][$remoteSrvNo]['production']['ssh']['host']
                //     . ':' . $productionInstancePath[$remoteSrvNo]);

                // We rather use SFTP than direct rsync, as user privileges are easier to handle with SFTP.
                // With rsync the PHP/Apache user would need the rights to do rsync ...
                $uploadMainFile = $sftpConnections[$remoteSrvNo]->put(
                    $productionInstancePath[$remoteSrvNo] . DIRECTORY_SEPARATOR . 'files-' . $startPublishTime . '.tar.gz',
                    $packageFile,
                    1
                );

                if ($uploadMainFile != true) {
                    return Redirect::to('admin')->with('error-message',
                        'Es gab Probleme beim Upload der Instanz auf den Ausspielungsserver Nr.' . $remoteSrvNo . '.');
                } else {

                    // Extract files on remote server
                    $ssh->exec('tar -xzf ' . $productionInstancePath[$remoteSrvNo] . DIRECTORY_SEPARATOR
                        . 'files-' . $startPublishTime . '.tar.gz'
                        . ' -C ' . $productionInstancePath[$remoteSrvNo]);

                    // Read db dump into remote DB
                    $remoteDbSocket = ' ';
                    if (isset($configOmim['remote'][$remoteSrvNo]['production']['db']['unix_socket'])
                        && !empty($configOmim['remote'][$remoteSrvNo]['production']['db']['unix_socket'])) {
                        $remoteDbSocket = ' --socket=' . $configOmim['remote'][$remoteSrvNo]['production']['db']['unix_socket'] . ' ';
                    }
                    $ssh->exec('mysql -u'
                        . $configOmim['remote'][$remoteSrvNo]['production']['db']['username'] . ' -p'
                        . $configOmim['remote'][$remoteSrvNo]['production']['db']['password']  . ' -h '
                        . $configOmim['remote'][$remoteSrvNo]['production']['db']['host']
                        . $remoteDbSocket
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
                    $ssh->exec('rm ' . $productionInstancePath[$remoteSrvNo] . DIRECTORY_SEPARATOR . 'files-' . $startPublishTime . '.tar.gz');

                }


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
                'Sie haben keine Berechtigung, die Ressource \'admin/create\' zu verwenden.');
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
                'Sie haben keine Berechtigung, die Ressource \'admin/create\' zu verwenden.');
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
             * Also delete from production (Ausspielungsserver) if
             * delete from development (Redaktionsserver) == ok
             */
            if (isset($delDevelopment) && $delDevelopment == 'ok' &&
                $va->last_published_at && $va->last_unpublished_at < $va->last_published_at) {
                $delProduktion = 'ok';
            }

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

                $msg .= 'Ausstellung vom Ausspielungsserver gelöscht.<br>';

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

                $msg .= 'Ausstellung vom Redaktionsserver gelöscht.<br>';

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
                'Sie haben keine Berechtigung, die Ressource \'admin/download\' zu verwenden.');
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

        $tables = array();
        $retval = false;
        $cmd = 'mysql -N' .
            ' -u' . $configLocalDb['connections']['mysql']['username'] .
            ' -p' . $configLocalDb['connections']['mysql']['password'] .
            ' --socket=' . $configLocalDb['connections']['mysql']['unix_socket'] .
            ' information_schema -e "' .
            'SELECT TABLE_NAME FROM TABLES WHERE TABLE_SCHEMA = \'' .
            $configLocalDb['connections']['mysql']['database'] . '\' ' .
            'AND TABLE_NAME LIKE \'omeka\_exh' . $va->id . '\_%\'"';
        exec($cmd, $tables, $retval);
        if ($retval === 0 && !empty($tables)) {
            $dbtables = $tables;
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

    public function getImprint()
    {
        $contents = array(
            'litfass' => $this->readImprintFile('litfass'),
            'litfass_featured' => $this->readImprintFile('litfass_featured'),
            'litfass_ddb' => $this->readImprintFile('litfass_ddb'),
        );
        return View::make('admin.imprint', compact('contents'));
    }

    public function postImprint()
    {
        $contents = array(
            'litfass' => Input::get('imprint_litfass'),
            'litfass_featured' => Input::get('imprint_litfass_featured'),
            'litfass_ddb' => Input::get('imprint_litfass_ddb'),
        );
        $this->writeImprintFile($contents);
        return View::make('admin.imprint', compact('contents'));
    }

    protected function readImprintFile($name)
    {
        $contents = false;
        if (is_file(base_path('data/imprint_' . $name . '.html'))) {
            $contents = file_get_contents(base_path('data/imprint_' . $name . '.html'));
        }
        if ($contents === false) {
            $contents = '';
        }
        return $contents;
    }

    protected function writeImprintFile($contents)
    {
        foreach ($contents as $name => $content) {
            file_put_contents(base_path('data/imprint_' . $name . '.html'), $content);
        }
    }

    public function getPublishimprint()
    {
        // Only users with root privileges are allowed pubish
        if (Auth::user()->isroot != 1) {
            return Redirect::to('admin')->with('error-message',
                'Sie haben keine Berechtigung, die Ressource \'admin/publishimprint\' zu verwenden.');
        }

        // Gather configs and paths first
        $configOmim = Config::get('omim');
        $dataPath = base_path() . DIRECTORY_SEPARATOR . 'data';
        $files = array(
            'imprint_litfass.html' => $dataPath . DIRECTORY_SEPARATOR . 'imprint_litfass.html',
            'imprint_litfass_ddb.html' => $dataPath . DIRECTORY_SEPARATOR . 'imprint_litfass_ddb.html',
            'imprint_litfass_featured.html' => $dataPath . DIRECTORY_SEPARATOR . 'imprint_litfass_featured.html',
        );

        /**
         * Loop through remote server configs and
         * init sftp connections first, because we will not proceed
         * if a connection fails.
         */
        foreach ($configOmim['remote'] as $remoteSrvNo => $remoteSrvConfig) {
            $sftpConnections[$remoteSrvNo] = $this->sftpConnectToProductionServer($remoteSrvConfig);
            if (!$sftpConnections[$remoteSrvNo]) {
                return Redirect::to('admin/imprint')->with('error-message',
                    'Verbindung zum Server '
                    . $remoteSrvConfig['production']['ssh']['host']
                    . 'konnte nicht hergestellt werden.');
            }
        }

        /**
         * Deploy files to all remote Servers
         * ************************************
         */
        foreach ($sftpConnections as $remoteSrvNo => $sftp) {
            foreach ($files as $fileName => $filePath) {
                $remoteFile = $configOmim['remote'][$remoteSrvNo]['production']['ssh']['datadir'] . DIRECTORY_SEPARATOR . $fileName;
                $upload = $sftp->put($remoteFile, $filePath, 1);
                if ($upload != true) {
                    return Redirect::to('admin/imprint')->with('error-message',
                        'Es gab Probleme beim Upload der Impressen auf den Server '
                        . $configOmim['remote'][$remoteSrvNo]['production']['ssh']['host'] . '.');
                } else {
                    // chgrp will return false if user does not have privileges to change (owner and) group (i.e. is root) - but we do not care.
                    $checkChangeGroup[] = $sftp->chgrp($remoteFile, $configOmim['remote'][$remoteSrvNo]['production']['ssh']['group']);
                }
            }
        }

        return Redirect::to('admin/imprint')->with('success-message', 'Impressen erfolgreich veröffentlicht.');
    }

    public function getConsent()
    {
        $contents = array(
            'termsofuse' => $this->readConsentFile('termsofuse'),
            'privacypolicy' => $this->readConsentFile('privacypolicy'),
        );
        return View::make('admin.consent', compact('contents'));
    }

    public function postConsent()
    {
        $contents = array(
            'termsofuse' => Input::get('consent_termsofuse'),
            'privacypolicy' => Input::get('consent_privacypolicy'),
        );
        $this->writeConsentFile($contents);
        return View::make('admin.consent', compact('contents'));
    }

    protected function readConsentFile($name)
    {
        $contents = false;
        if (is_file(base_path('data/consent_' . $name . '.html'))) {
            $contents = file_get_contents(base_path('data/consent_' . $name . '.html'));
        }
        if ($contents === false) {
            $contents = '';
        }
        return $contents;
    }

    protected function writeConsentFile($contents)
    {
        foreach ($contents as $name => $content) {
            file_put_contents(base_path('data/consent_' . $name . '.html'), $content);
        }
    }

    public function getPublishconsent()
    {
        // Only users with root privileges are allowed pubish
        if (Auth::user()->isroot != 1) {
            return Redirect::to('admin')->with('error-message',
                'Sie haben keine Berechtigung, die Ressource \'admin/publishconsent\' zu verwenden.');
        }

        // Actualy there is no need to put this on production servers
        // return Redirect::to('admin')->with('error-message',
        //         'Einverständniserklärungen müssen nicht veröffentlicht werden.');

        // Gather configs and paths first
        $configOmim = Config::get('omim');
        $dataPath = base_path() . DIRECTORY_SEPARATOR . 'data';
        $files = array(
            'consent_privacypolicy.html' => $dataPath . DIRECTORY_SEPARATOR . 'consent_privacypolicy.html',
            'consent_termsofuse.html' => $dataPath . DIRECTORY_SEPARATOR . 'consent_termsofuse.html',
        );

        /**
         * Loop through remote server configs and
         * init sftp connections first, because we will not proceed
         * if a connection fails.
         */
        foreach ($configOmim['remote'] as $remoteSrvNo => $remoteSrvConfig) {
            $sftpConnections[$remoteSrvNo] = $this->sftpConnectToProductionServer($remoteSrvConfig);
            if (!$sftpConnections[$remoteSrvNo]) {
                return Redirect::to('admin/consent')->with('error-message',
                    'Verbindung zum Server '
                    . $remoteSrvConfig['production']['ssh']['host']
                    . 'konnte nicht hergestellt werden.');
            }
        }

        /**
         * Deploy files to all remote Servers
         * ************************************
         */
        foreach ($sftpConnections as $remoteSrvNo => $sftp) {
            foreach ($files as $fileName => $filePath) {
                $remoteFile = $configOmim['remote'][$remoteSrvNo]['production']['ssh']['datadir'] . DIRECTORY_SEPARATOR . $fileName;
                $upload = $sftp->put($remoteFile, $filePath, 1);
                if ($upload != true) {
                    return Redirect::to('admin/consent')->with('error-message',
                        'Es gab Probleme beim Upload der Einverständniserklärung auf den Server '
                        . $configOmim['remote'][$remoteSrvNo]['production']['ssh']['host'] . '.');
                } else {
                    // chgrp will return false if user does not have privileges to change (owner and) group (i.e. is root) - but we do not care.
                    $checkChangeGroup[] = $sftp->chgrp($remoteFile, $configOmim['remote'][$remoteSrvNo]['production']['ssh']['group']);
                }
            }
        }

        return Redirect::to('admin/consent')->with('success-message', 'Einverständniserklärung erfolgreich veröffentlicht.');
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

        // $ssh = new Net_SSH2($configOmim['production']['ssh']['host'], $configOmim['production']['ssh']['port']);
        $ssh = new phpseclib\Net\SSH2($configOmim['production']['ssh']['host'], $configOmim['production']['ssh']['port']);


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

            // $key = new Crypt_RSA();
            $key = new phpseclib\Crypt\RSA();

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
     * SFTP connect to production server
     *
     * @param    array     production server config array
     * @return   object    ssh object or false on failure
     */
    protected function sftpConnectToProductionServer($configOmim)
    {
        if (!is_array($configOmim) || !array_key_exists('production', $configOmim) ||
            !array_key_exists('ssh', $configOmim['production'])) {

            return false;
        }

        // $ssh = new Net_SFTP($configOmim['production']['ssh']['host'], $configOmim['production']['ssh']['port']);
        $ssh = new phpseclib\Net\SFTP($configOmim['production']['ssh']['host'], $configOmim['production']['ssh']['port']);

        if (array_key_exists('key', $configOmim['production']['ssh']) &&
            !empty($configOmim['production']['ssh']['key'])) {

            // $key = new Crypt_RSA();
            $key = new phpseclib\Crypt\RSA();

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
                'Sie haben keine Berechtigung, die Ressource \'test/ssh\' zu verwenden.');
        }

        // Gather configs and paths first
        $configOmim = Config::get('omim');

        /**
         * Loop through remote server configs and
         * init ssh connections.
         */
        foreach ($configOmim['remote'] as $remoteSrvNo => $remoteSrvConfig) {
            echo 'Initialisiere Verbindung zu Remote Server Nr. '  . $remoteSrvNo
                . ', Host ' . $remoteSrvConfig['production']['ssh']['host'] . "<br>\n";

            $sshConnections[$remoteSrvNo] = $this->connectTestToProductionServer($remoteSrvConfig);

            if (!$sshConnections[$remoteSrvNo]) {
                echo 'Verbindung zum Productionsserver '
                    . $remoteSrvConfig['production']['ssh']['host']
                    . ' konnte nicht hergestellt werden.';
            } else {
                echo 'pwd auf remote server: ' . "<br>\n";
                echo $sshConnections{$remoteSrvNo}->exec('pwd') . "<br>\n";
                echo 'ls ' . $remoteSrvConfig['production']['ssh']['docroot'] . ' auf remote server: ' . "<br>\n";
                echo $sshConnections{$remoteSrvNo}->exec('ls ' . $remoteSrvConfig['production']['ssh']['docroot']) . "<br>\n";
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

        // $ssh = new Net_SSH2($configOmim['production']['ssh']['host'], $configOmim['production']['ssh']['port']);
        $ssh = new phpseclib\Net\SSH2($configOmim['production']['ssh']['host'], $configOmim['production']['ssh']['port']);

        if (array_key_exists('key', $configOmim['production']['ssh']) &&
            !empty($configOmim['production']['ssh']['key'])) {

            // $key = new Crypt_RSA();
            $key = new phpseclib\Crypt\RSA();

            if (array_key_exists('keyphrase', $configOmim['production']['ssh']) &&
            !empty($configOmim['production']['ssh']['keyphrase'])) {

                $key->setPassword($configOmim['production']['ssh']['keyphrase']);
            }

            if (!$key->loadKey(file_get_contents($configOmim['production']['ssh']['key']))) {
                 echo 'Fehler im RSA Schüssel! - Schlüssel konnte nicht geladen werden.' . "<br>\n";
                 return false;
            }

            echo 'Versuche Login ...' . "<br>\n";

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

    public function getColorpalettesList()
    {
        if (Auth::user()->isroot != 1) {
            return Redirect::to('admin')->with('error-message',
                'Sie haben keine Berechtigung, die Ressource \'admin/colorpalettes-list\' zu verwenden.');
        }
        // $colorPalettes = OmimExhibitColorPalette::all();
        $colorPalettes = OmimExhibitColorPalette::where('palette', 'like', 'custom_%')->orderBy('palette', 'DESC')->get()->toArray();
        foreach ($colorPalettes as $colorPaletteKey => $colorPalette) {
            preg_match('/custom_([^_]+_)(.*)/', $colorPalette['palette'], $matches);
            $colorPalettes[$colorPaletteKey]['show_name'] = $matches[2];
        }
        return View::make('admin.colorpalettes-list', compact('colorPalettes'));
    }

    public function getColorpalettesCreate()
    {
        if (Auth::user()->isroot != 1) {
            return Redirect::to('admin')->with('error-message',
            'Sie haben keine Berechtigung, die Ressource \'admin/colorpalettes-create\' zu verwenden.');
        }
        // $colorPalettes = OmimExhibitColorPalette::all();
        $paletteName = uniqid();
        return View::make('admin.colorpalettes-create', compact('paletteName'));
    }

    public function postColorpalettesCreate()
    {
        if (Auth::user()->isroot != 1) {
            return Redirect::to('admin')->with('error-message',
            'Sie haben keine Berechtigung, die Ressource \'admin/colorpalettes-create\' zu verwenden.');
        }
        $input = Input::all();
        if (!isset($input['palette_palette']) || empty($input['palette_palette']) ||
            !isset($input['palette_palette_showname']) || empty($input['palette_palette_showname']) ||
            !isset($input['palette']) || empty($input['palette'])) {
            return Redirect::to('admin/colorpalettes-list')->with('error-message',
            'Es wurde kein Name oder keine Farbe für die Palette übermittelt.');
        }
        $input['palette_palette_showname'] = preg_replace('/[^a-zA-Z0-9]/', '', $input['palette_palette_showname']);
        if (!isset($input['palette_palette_showname']) || empty($input['palette_palette_showname'])) {
            return Redirect::to('admin/colorpalettes-list')->with('error-message',
            'Es wurde kein Name für die Palette übermittelt.');
        }
        $paletteName = 'custom_' . $input['palette_palette'] . '_' . $input['palette_palette_showname'];
        $test = OmimExhibitColorPalette::where('palette', $paletteName)->get();
        if (count($test) > 0) {
            $paletteName = 'custom_' . uniqid() . '_' . $input['palette_palette_showname'];
        }
        // var_dump($input, $paletteName);
        // return;
        $uniquePalette = $this->makeUniqueColors($input['palette']);
        foreach ($uniquePalette as $colorKey => $color) {
            $color['color'] = $this->filterColorName($color['color']);
            if (isset($color['hex']) && !empty($color['hex']) &&
                isset($color['color']) && !empty($color['color']) &&
                isset($color['type']) && !empty($color['type'])) {

                    $exhibitColorPalette = new OmimExhibitColorPalette;
                    $exhibitColorPalette->palette = $paletteName;
                    $exhibitColorPalette->color = $color['color'];
                    $exhibitColorPalette->hex = $color['hex'];
                    $exhibitColorPalette->type = $color['type'];
                    if (!isset($input['palette_menu'])) {
                        $input['palette_menu'] = $colorKey;
                    }
                    if ($colorKey == $input['palette_menu']) {
                        $exhibitColorPalette->menu = 1;
                    } else {
                        $exhibitColorPalette->menu = 0;
                    }
                    $exhibitColorPalette->save();

            }
        }

        return Redirect::to('admin/colorpalettes-list')
            ->with('success-message', 'Farbpalette erfolgreich gespeichert.');
    }

    public function getColorpalettesEdit()
    {
        if (Auth::user()->isroot != 1) {
            return Redirect::to('admin')->with('error-message',
            'Sie haben keine Berechtigung, die Ressource \'admin/colorpalettes-create\' zu verwenden.');
        }

        // Check if user has selected a palette
        $palette = Input::get('palette');
        if (!isset($palette) || empty($palette)) {
            return Redirect::to('admin/colorpalettes-list')->with('error-message',
                'Bitte wählen Sie eine Farbplatte, die Sie bearbeiten möchten.');
        }

        preg_match('/custom_([^_]+_)(.*)/', $palette, $matches);
        if (!isset($matches[2]) || empty($matches[2])) {
            return Redirect::to('admin/colorpalettes-list')->with('error-message',
                'Die von Ihnen gewählte Farbplatte kann nicht editiert werden.');
        }
        $paletteShowName = $matches[2];

        // Check if the palette is in the DB
        $dbPalette = OmimExhibitColorPalette::where('palette', $palette)->get();
        if (!isset($dbPalette) || empty($dbPalette) || count($dbPalette) == 0) {
            return Redirect::to('admin/colorpalettes-list')->with('error-message',
            'Die von Ihnen gewählte Farbplatte existiert nicht in der Datenbank.');
        }
        $dbPalette->toArray();
        $paletteCounter = count($dbPalette);

        // $colorPalettes = OmimExhibitColorPalette::all();
        return View::make('admin.colorpalettes-edit', compact('palette', 'dbPalette', 'paletteCounter', 'paletteShowName'));
    }

    public function postColorpalettesEdit()
    {
        if (Auth::user()->isroot != 1) {
            return Redirect::to('admin')->with('error-message',
            'Sie haben keine Berechtigung, die Ressource \'admin/colorpalettes-create\' zu verwenden.');
        }

        $input = Input::all();

        // delete
        if (isset($input['palette_oldpalette']) && isset($input['delete_palette']) &&
            $input['palette_oldpalette'] === $input['delete_palette']) {
                $deletedColors = OmimExhibitColorPalette::where('palette', $input['palette_oldpalette'])->delete();
                return Redirect::to('admin/colorpalettes-list')->with(
                    'success-message',
                    'Farbpalette erfolgreich gelöscht. Es wurden ' . $deletedColors . ' Farben entfernt.');
        }

        if (!isset($input['palette_oldpalette'])|| empty($input['palette_oldpalette']) ||
            !isset($input['palette_palette_showname']) || empty($input['palette_palette_showname'])) {
            return Redirect::to('admin/colorpalettes-list')->with('error-message',
            'Name der Palette wurde nicht übermittelt.');
        }

        // palette name
        preg_match('/custom_([^_]+_)(.*)/', $input['palette_oldpalette'], $matches);
        if (isset($matches[2]) && $matches[2] == $input['palette_palette_showname']) {
            $paletteName = $input['palette_oldpalette'];
        } else {
            $input['palette_palette_showname'] = preg_replace('/[^a-zA-Z0-9]/', '', $input['palette_palette_showname']);
            if (!isset($input['palette_palette_showname']) || empty($input['palette_palette_showname'])) {
                return Redirect::to('admin/colorpalettes-list')->with('error-message',
                'Es wurde kein Name für die Palette übermittelt.');
            }
            if (isset($matches[1])) {
                $paletteName = 'custom_' . $matches[1] . '_' . $input['palette_palette_showname'];
                $test = OmimExhibitColorPalette::where('palette', $paletteName)->get();
                if (count($test) > 0) {
                    $paletteName = 'custom_' . uniqid() . '_' . $input['palette_palette_showname'];
                }
            } else {
                $paletteName = 'custom_' . uniqid() . '_' . $input['palette_palette_showname'];
            }
        }

        $deletedColors = OmimExhibitColorPalette::where('palette', $input['palette_oldpalette'])->delete();

        $uniquePalette = $this->makeUniqueColors($input['palette']);
        foreach ($uniquePalette as $colorKey => $color) {
            $color['color'] = $this->filterColorName($color['color']);
            if (isset($color['hex']) && !empty($color['hex']) &&
                isset($color['color']) && !empty($color['color']) &&
                isset($color['type']) && !empty($color['type'])) {

                    $exhibitColorPalette = new OmimExhibitColorPalette;
                    $exhibitColorPalette->palette = $paletteName;
                    $exhibitColorPalette->color = $color['color'];
                    $exhibitColorPalette->hex = $color['hex'];
                    $exhibitColorPalette->type = $color['type'];
                    if (!isset($input['palette_menu'])) {
                        $input['palette_menu'] = $colorKey;
                    }
                    if ($colorKey == $input['palette_menu']) {
                        $exhibitColorPalette->menu = 1;
                    } else {
                        $exhibitColorPalette->menu = 0;
                    }
                    $exhibitColorPalette->save();
            }
        }

        return Redirect::to('admin/colorpalettes-list')
            ->with('success-message', 'Farbpalette erfolgreich gespeichert.');
    }

    public function filterColorName($color)
    {
        $color = preg_replace('/[^a-z0-9_\-]/', '', strtolower($color));
        if (preg_match('/^[0-9]+/', $color)) {
            $color = 'c' . $color;
        }
        return $color;
    }

    public function makeUniqueColors($palette)
    {
        $paletteCopy = $palette;
        foreach ($palette as $colorKey => $color) {
            foreach ($paletteCopy as $copyKey => $copyColor) {
                if ($color['color'] == $copyColor['color'] && $colorKey != $copyKey) {
                    $palette[$colorKey]['color'] = $color['color'] . '_' . uniqid();
                    $color['color'] = $palette[$colorKey]['color'];
                }
            }
        }
        return $palette;
    }

}
