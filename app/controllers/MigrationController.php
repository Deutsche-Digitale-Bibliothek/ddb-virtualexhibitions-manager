<?php

class MigrationController extends \BaseController {


    public $omimVersion = '1.0.0';
    public $msg = array();

    /**
     * Migrate to current version
     *
     * @return Response
     */
    public function getIndex()
    {

        // die('here');
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

        // Check if migration is necessary (by custom title)
        $customTitle = DB::select('select * from omeka_exh' . $va->id . '_elements where id = ?', array(52));
        if (is_array($customTitle) && count($customTitle) == 1) {
            $customTitle = $customTitle[0];
        } else {
            return Redirect::to('admin')->with('error-message',
                 'Etwas stimmt nicht - Administrator kontaktieren (ERROR MIGRATE 001) .');
        }
        if (
            $customTitle->element_set_id == 3 &&
            $customTitle->order == 1 &&
            $customTitle->name == 'Titel' &&
            $customTitle->description == 'Beschreibung Metadatenfeld Titel' &&
            $customTitle->comment == 'Zusatzvermerk Metadatenfeld Titel'

            // && 1 != 1

            ) {

            if ($va->version == $this->omimVersion) {
                return Redirect::to('admin')->with('error-message',
                     'Die von Ihnen gewählte Omeka Instanz ist bereits migriert worden.');
            } else {
                DB::update('update omim_instances set version = ? where id = ?', array($this->omimVersion, $va->id));
                return Redirect::to('admin')->with('success-message',
                     'Die von Ihnen gewählte Omeka Instanz ist migriert.');
            }

        } else {
            $this->migrateElements($va);
            $this->migrateElementTexts($va);
            $this->migrateItemTypesElements($va);
            $this->migratePlugins($va);
            $this->migrateOptions($va);
            DB::update('update omim_instances set version = ? where id = ?', array($this->omimVersion, $va->id));

        }
        $msg = $this->msg;
        return View::make('migrate.index', compact('va', 'msg'));
    }

    /**
     * Migrate elements db table
     * @param $va object omim exhibition db data
     * @return void
     */
    public function migrateElements($va)
    {
        $this->msg['elements'] = array();

        // Prepare unique ids - important!
        $customElements = DB::select('select * from omeka_exh' . $va->id . '_elements where element_set_id = ?', array(3));
        $counter = 0;
        foreach ($customElements as $customElement) {
            $currentOrder = 1000 + $counter;
            DB::update('update omeka_exh' . $va->id . '_elements set `order` = ? where id = ?',
                array($currentOrder, $customElement->id)
            );
            $counter++;
        }
        $this->msg['elements'][] = 'UNIQUE Index "element_set_id, order" vorbereitet.';

        $result = DB::update('update omeka_exh' . $va->id . '_elements set `order` = ?, description = ?, comment = ? where id = ?',
            array(
                1,
                'Beschreibung Metadatenfeld Titel',
                'Zusatzvermerk Metadatenfeld Titel',
                52
            )
        );
        $this->msg['elements'][] = 'Feld "Titel" - Anzahl der Änderungen ' . $result;

        $result = DB::delete('delete from omeka_exh' . $va->id . '_elements where id = ?', array(53));
        $this->msg['elements'][] = 'Feld "Alternativer Titel" - Anzahl der Änderungen ' . $result;

        $result = DB::update('update omeka_exh' . $va->id . '_elements set `order` = ?, description = ?, comment = ? where id = ?',
            array(
                17,
                'Beschreibung Metadatenfeld Material/Technik',
                'Zusatzvermerk Metadatenfeld Material/Technik',
                54
            )
        );
        $this->msg['elements'][] = 'Feld "Material/Technik" - Anzahl der Änderungen ' . $result;

        $result = DB::update('update omeka_exh' . $va->id . '_elements set `order` = ?, description = ?, comment = ? where id = ?',
            array(
                16,
                'Beschreibung Metadatenfeld Maße/Umfang',
                'Zusatzvermerk Metadatenfeld Maße/Umfang',
                55
            )
        );
        $this->msg['elements'][] = 'Feld "Maße/Umfang" - Anzahl der Änderungen ' . $result;

        $result = DB::update('update omeka_exh' . $va->id . '_elements set `order` = ?, description = ?, comment = ? where id = ?',
            array(
                15,
                'Beschreibung Metadatenfeld Ort',
                'Zusatzvermerk Metadatenfeld Ort',
                56
            )
        );
        $this->msg['elements'][] = 'Feld "Ort" - Anzahl der Änderungen ' . $result;

        $result = DB::update('update omeka_exh' . $va->id . '_elements set `order` = ?, description = ?, comment = ? where id = ?',
            array(
                19,
                'Beschreibung Metadatenfeld Identifikator',
                'Zusatzvermerk Metadatenfeld Identifikator',
                57
            )
        );
        $this->msg['elements'][] = 'Feld "Identifikator" - Anzahl der Änderungen ' . $result;

        $result = DB::delete('delete from omeka_exh' . $va->id . '_elements where id = ?', array(58));
        $this->msg['elements'][] = 'Feld "Rechteinformation" - Anzahl der Änderungen ' . $result;

        $result = DB::update('update omeka_exh' . $va->id . '_elements set `order` = ?, description = ?, comment = ? where id = ?',
            array(
                3,
                'Beschreibung Metadatenfeld Beschreibung',
                'Zusatzvermerk Metadatenfeld Beschreibung',
                59
            )
        );
        $this->msg['elements'][] = 'Feld "Beschreibung" - Anzahl der Änderungen ' . $result;

        $result = DB::delete('delete from omeka_exh' . $va->id . '_elements where id = ?', array(60));
        $this->msg['elements'][] = 'Feld "Aufnahmejahr" - Anzahl der Änderungen ' . $result;

        $result = DB::delete('delete from omeka_exh' . $va->id . '_elements where id = ?', array(61));
        $this->msg['elements'][] = 'Feld "Aufnahmeort" - Anzahl der Änderungen ' . $result;

        $result = DB::delete('delete from omeka_exh' . $va->id . '_elements where id = ?', array(62));
        $this->msg['elements'][] = 'Feld "Hergestellt (wann und wo)" - Anzahl der Änderungen ' . $result;

        $result = DB::delete('delete from omeka_exh' . $va->id . '_elements where id = ?', array(63));
        $this->msg['elements'][] = 'Feld "Fotograf" - Anzahl der Änderungen ' . $result;

        $result = DB::update('update omeka_exh' . $va->id . '_elements set `order` = ?, description = ?, comment = ? where id = ?',
            array(
                10,
                'Beschreibung Metadatenfeld Typ',
                'Zusatzvermerk Metadatenfeld Typ',
                64
            )
        );
        $this->msg['elements'][] = 'Feld "Typ" - Anzahl der Änderungen ' . $result;

        $result = DB::update('update omeka_exh' . $va->id . '_elements set `order` = ?, description = ?, comment = ? where id = ?',
            array(
                12,
                'Beschreibung Metadatenfeld Thema',
                'Zusatzvermerk Metadatenfeld Thema',
                65
            )
        );
        $this->msg['elements'][] = 'Feld "Thema" - Anzahl der Änderungen ' . $result;

        $result = DB::delete('delete from omeka_exh' . $va->id . '_elements where id = ?', array(66));
        $this->msg['elements'][] = 'Feld "Hersteller" - Anzahl der Änderungen ' . $result;

        $result = DB::update('update omeka_exh' . $va->id . '_elements set `order` = ?, description = ?, comment = ? where id = ?',
            array(
                14,
                'Beschreibung Metadatenfeld Zeit',
                'Zusatzvermerk Metadatenfeld Zeit',
                67
            )
        );
        $this->msg['elements'][] = 'Feld "Zeit" - Anzahl der Änderungen ' . $result;

        $result = DB::delete('delete from omeka_exh' . $va->id . '_elements where id = ?', array(70));
        $this->msg['elements'][] = 'Feld "Material" - Anzahl der Änderungen ' . $result;

        $result = DB::update('update omeka_exh' . $va->id . '_elements set `order` = ?, description = ?, comment = ? where id = ?',
            array(
                18,
                'Beschreibung Metadatenfeld Sprache',
                'Zusatzvermerk Metadatenfeld Sprache',
                71
            )
        );
        $this->msg['elements'][] = 'Feld "Sprache" - Anzahl der Änderungen ' . $result;

        $result = DB::update('update omeka_exh' . $va->id . '_elements set `order` = ?, description = ?, comment = ? where id = ?',
            array(
                9,
                'Beschreibung Metadatenfeld Rechtsstatus',
                'Zusatzvermerk Metadatenfeld Rechtsstatus',
                72
            )
        );
        $this->msg['elements'][] = 'Feld "Rechtsstatus" - Anzahl der Änderungen ' . $result;

        $result = DB::delete('delete from omeka_exh' . $va->id . '_elements where id = ?', array(73));
        $this->msg['elements'][] = 'Feld "Rechtestatus" - Anzahl der Änderungen ' . $result;

        $result = DB::update('update omeka_exh' . $va->id . '_elements set `order` = ?, description = ?, comment = ? where id = ?',
            array(
                22,
                'Beschreibung Metadatenfeld Videoquelle',
                'Zusatzvermerk Metadatenfeld Videoquelle',
                74
            )
        );
        $this->msg['elements'][] = 'Feld "Videoquelle" - Anzahl der Änderungen ' . $result;

        $result = DB::update('update omeka_exh' . $va->id . '_elements set `order` = ?, description = ?, comment = ? where id = ?',
            array(
                2,
                'Beschreibung Metadatenfeld Weiterer Titel',
                'Zusatzvermerk Metadatenfeld Weiterer Titel',
                75
            )
        );
        $this->msg['elements'][] = 'Feld "Weiterer Titel" - Anzahl der Änderungen ' . $result;

        // Chanmge name
        $result = DB::update('update omeka_exh' . $va->id . '_elements set name = ?, `order` = ?, description = ?, comment = ? where id = ?',
            array(
                'Name der Institution',
                5,
                'Beschreibung Metadatenfeld Name der Institution',
                'Zusatzvermerk Metadatenfeld Name der Institution',
                76
            )
        );
        $this->msg['elements'][] = 'Feld "Name der Institution" - Anzahl der Änderungen ' . $result;

        // Chanmge name
        $result = DB::update('update omeka_exh' . $va->id . '_elements set name = ?, `order` = ?, description = ?, comment = ? where id = ?',
            array(
                'Link zum Objekt in der DDB',
                7,
                'Beschreibung Metadatenfeld Link zum Objekt in der DDB',
                'Zusatzvermerk Metadatenfeld Link zum Objekt in der DDB',
                77
            )
        );
        $this->msg['elements'][] = 'Feld "Link zum Objekt in der DDB" - Anzahl der Änderungen ' . $result;

        // Chanmge name
        $result = DB::update('update omeka_exh' . $va->id . '_elements set name = ?, `order` = ?, description = ?, comment = ? where id = ?',
            array(
                'Link zum Objekt bei der datengebenden Institution',
                8,
                'Beschreibung Metadatenfeld Link zum Objekt bei der datengebenden Institution',
                'Zusatzvermerk Metadatenfeld Link zum Objekt bei der datengebenden Institution',
                78
            )
        );
        $this->msg['elements'][] = 'Feld "Link zum Objekt bei der datengebenden Institution" - Anzahl der Änderungen ' . $result;

        $result = DB::update('update omeka_exh' . $va->id . '_elements set `order` = ?, description = ?, comment = ? where id = ?',
            array(
                11,
                'Beschreibung Metadatenfeld Teil von',
                'Zusatzvermerk Metadatenfeld Teil von',
                79
            )
        );
        $this->msg['elements'][] = 'Feld "Teil von" - Anzahl der Änderungen ' . $result;

        $result = DB::update('update omeka_exh' . $va->id . '_elements set `order` = ?, description = ?, comment = ? where id = ?',
            array(
                4,
                'Beschreibung Metadatenfeld Kurzbeschreibung',
                'Zusatzvermerk Metadatenfeld Kurzbeschreibung',
                80
            )
        );
        $this->msg['elements'][] = 'Feld "Kurzbeschreibung" - Anzahl der Änderungen ' . $result;

        $result = DB::update('update omeka_exh' . $va->id . '_elements set `order` = ?, description = ?, comment = ? where id = ?',
            array(
                13,
                'Beschreibung Metadatenfeld Beteiligte Personen und Organisationen',
                'Zusatzvermerk Metadatenfeld Beteiligte Personen und Organisationen',
                81
            )
        );
        $this->msg['elements'][] = 'Feld "Beteiligte Personen und Organisationen" - Anzahl der Änderungen ' . $result;

        $result = DB::update('update omeka_exh' . $va->id . '_elements set `order` = ?, description = ?, comment = ? where id = ?',
            array(
                20,
                'Beschreibung Metadatenfeld Anmerkungen',
                'Zusatzvermerk Metadatenfeld Anmerkungen',
                82
            )
        );
        $this->msg['elements'][] = 'Feld "Anmerkungen" - Anzahl der Änderungen ' . $result;

        $result = DB::update('update omeka_exh' . $va->id . '_elements set `order` = ?, description = ?, comment = ? where id = ?',
            array(
                21,
                'Beschreibung Metadatenfeld Förderung',
                'Zusatzvermerk Metadatenfeld Förderung',
                83
            )
        );
        $this->msg['elements'][] = 'Feld "Förderung" - Anzahl der Änderungen ' . $result;

        $result = DB::update('update omeka_exh' . $va->id . '_elements set `order` = ?, description = ?, comment = ? where id = ?',
            array(
                23,
                'Beschreibung Metadatenfeld Imagemap',
                'Zusatzvermerk Metadatenfeld Imagemap',
                84
            )
        );
        $this->msg['elements'][] = 'Feld "Imagemap" - Anzahl der Änderungen ' . $result;

        // Create new
        $result = DB::insert('insert into omeka_exh' . $va->id . '_elements (element_set_id, `order`, name, description, comment) values (?, ?, ?, ?, ?)',
            array(
                3,
                6,
                'URL der Institution',
                'Beschreibung Metadatenfeld URL der Institution',
                'Zusatzvermerk Metadatenfeld URL der Institution'
            )
        );
        $this->msg['elements'][] = 'Feld "URL der Institution" hinzugefügt - Anzahl der Änderungen ' . $result;
        $newField = DB::select('select * from omeka_exh' . $va->id . '_elements where name = ? AND element_set_id = ?', array('URL der Institution', 3));
        if (count($newField) > 0) {
            $newField = $newField[0];
            $result = DB::insert('insert into omeka_exh' . $va->id . '_item_types_elements (item_type_id, element_id, `order`) values (?, ?, ?)',
                array(
                    18,
                    $newField->id,
                    $newField->order
                )
            );
            $this->msg['item_types_elements'][] = 'Feld "URL der Institution" eingetragen - Anzahl der Änderungen ' . $result;

        }

    }

    /**
     * Migrate elements db table
     * @param $va object omim exhibition db data
     * @return void
     */
    public function migrateItemTypesElements($va)
    {
        $results = 0;
        $result = DB::update('update omeka_exh' . $va->id . '_item_types_elements set `order` = ? where item_type_id = ? AND element_id = ?',
            array(1, 18, 52)
        );
        $results = $results + $result;
        $result = DB::update('update omeka_exh' . $va->id . '_item_types_elements set `order` = ? where item_type_id = ? AND element_id = ?',
            array(17, 18, 54)
        );
        $results = $results + $result;
        $result = DB::update('update omeka_exh' . $va->id . '_item_types_elements set `order` = ? where item_type_id = ? AND element_id = ?',
            array(16, 18, 55)
        );
        $results = $results + $result;
        $result = DB::update('update omeka_exh' . $va->id . '_item_types_elements set `order` = ? where item_type_id = ? AND element_id = ?',
            array(15, 18, 56)
        );
        $results = $results + $result;
        $result = DB::update('update omeka_exh' . $va->id . '_item_types_elements set `order` = ? where item_type_id = ? AND element_id = ?',
            array(19, 18, 57)
        );
        $results = $results + $result;
        $result = DB::update('update omeka_exh' . $va->id . '_item_types_elements set `order` = ? where item_type_id = ? AND element_id = ?',
            array(3, 18, 59)
        );
        $results = $results + $result;
        $result = DB::update('update omeka_exh' . $va->id . '_item_types_elements set `order` = ? where item_type_id = ? AND element_id = ?',
            array(10, 18, 64)
        );
        $results = $results + $result;
        $result = DB::update('update omeka_exh' . $va->id . '_item_types_elements set `order` = ? where item_type_id = ? AND element_id = ?',
            array(12, 18, 65)
        );
        $results = $results + $result;
        $result = DB::update('update omeka_exh' . $va->id . '_item_types_elements set `order` = ? where item_type_id = ? AND element_id = ?',
            array(14, 18, 67)
        );
        $results = $results + $result;
        $result = DB::update('update omeka_exh' . $va->id . '_item_types_elements set `order` = ? where item_type_id = ? AND element_id = ?',
            array(18, 18, 71)
        );
        $results = $results + $result;
        $result = DB::update('update omeka_exh' . $va->id . '_item_types_elements set `order` = ? where item_type_id = ? AND element_id = ?',
            array(22, 18, 74)
        );
        $results = $results + $result;
        $result = DB::update('update omeka_exh' . $va->id . '_item_types_elements set `order` = ? where item_type_id = ? AND element_id = ?',
            array(2, 18, 75)
        );
        $results = $results + $result;
        $result = DB::update('update omeka_exh' . $va->id . '_item_types_elements set `order` = ? where item_type_id = ? AND element_id = ?',
            array(5, 18, 76)
        );
        $results = $results + $result;
        $result = DB::update('update omeka_exh' . $va->id . '_item_types_elements set `order` = ? where item_type_id = ? AND element_id = ?',
            array(7, 18, 77)
        );
        $results = $results + $result;
        $result = DB::update('update omeka_exh' . $va->id . '_item_types_elements set `order` = ? where item_type_id = ? AND element_id = ?',
            array(8, 18, 78)
        );
        $results = $results + $result;
        $result = DB::update('update omeka_exh' . $va->id . '_item_types_elements set `order` = ? where item_type_id = ? AND element_id = ?',
            array(11, 18, 79)
        );
        $results = $results + $result;
        $result = DB::update('update omeka_exh' . $va->id . '_item_types_elements set `order` = ? where item_type_id = ? AND element_id = ?',
            array(4, 18, 80)
        );
        $results = $results + $result;
        $result = DB::update('update omeka_exh' . $va->id . '_item_types_elements set `order` = ? where item_type_id = ? AND element_id = ?',
            array(13, 18, 81)
        );
        $results = $results + $result;
        $result = DB::update('update omeka_exh' . $va->id . '_item_types_elements set `order` = ? where item_type_id = ? AND element_id = ?',
            array(20, 18, 82)
        );
        $results = $results + $result;
        $result = DB::update('update omeka_exh' . $va->id . '_item_types_elements set `order` = ? where item_type_id = ? AND element_id = ?',
            array(21, 18, 83)
        );
        $results = $results + $result;
        $result = DB::update('update omeka_exh' . $va->id . '_item_types_elements set `order` = ? where item_type_id = ? AND element_id = ?',
            array(9, 18, 72)
        );
        $results = $results + $result;
        $result = DB::update('update omeka_exh' . $va->id . '_item_types_elements set `order` = ? where item_type_id = ? AND element_id = ?',
            array(23, 18, 84)
        );
        $results = $results + $result;
        $result = DB::update('update omeka_exh' . $va->id . '_item_types_elements set `order` = ? where item_type_id = ? AND element_id = ?',
            array(6, 18, 85)
        );
        $results = $results + $result;
        $this->msg['item_types_elements'][] = 'Reiehnfolge für Elemente des Objekttyps angepasst - Anzahl der Änderungen ' . $results;
    }

    /**
     * Migrate elements db table
     * @param $va object omim exhibition db data
     * @return void
     */
    public function migrateElementTexts($va)
    {
        $this->msg['element_texts'] = array();

        $institutionNames = DB::select('select * from omeka_exh' . $va->id . '_element_texts where element_id = ?', array(76));
        $results = 0;
        $resultsAdd = 0;
        foreach ($institutionNames as $institutionName) {
            $result =  DB::update('update omeka_exh' . $va->id . '_element_texts set text = ?, html = ? where id = ?',
                array(
                    strip_tags($institutionName->text),
                    0,
                    $institutionName->id
                )
            );
            $results = $results + $result;

            $dom = new DOMDocument();
            $dom->loadHTML($institutionName->text);
            $tags = $dom->getElementsByTagName('a');
            $href = '';
            foreach ($tags as $tag) {
                $href = $tag->getAttribute('href');
                break;
            }
            if (!empty($href)) {
                $result = DB::insert('insert into omeka_exh' . $va->id . '_element_texts (record_id, record_type, element_id, html, text) values (?, ?, ?, ?, ?)',
                    array(
                        $institutionName->record_id,
                        'Item',
                        85,
                        0,
                        $href
                    )
                );
                $resultsAdd = $resultsAdd + $result;
            }
        }
        $this->msg['element_texts'][] = 'Texte für "Name der Institution" angepasst - Anzahl der Änderungen ' . $results;
        $this->msg['element_texts'][] = 'Felder "URL der Institution" gesetzt - Anzahl der Änderungen ' . $resultsAdd;

        $linkDDBs = DB::select('select * from omeka_exh' . $va->id . '_element_texts where element_id = ?', array(77));
        $results = 0;
        foreach ($linkDDBs as $linkDDB) {
            $dom = new DOMDocument();
            $dom->loadHTML($linkDDB->text);
            $tags = $dom->getElementsByTagName('a');
            $href = $linkDDB->text;
            foreach ($tags as $tag) {
                $href = $tag->getAttribute('href');
                break;
            }
            $result =  DB::update('update omeka_exh' . $va->id . '_element_texts set text = ?, html = ? where id = ?',
                array(
                    $href,
                    0,
                    $linkDDB->id
                )
            );
            $results = $results + $result;
        }
        $this->msg['element_texts'][] = 'Texte für "Link zum Objekt in der DDB" angepasst - Anzahl der Änderungen ' . $results;

        $linkInsts = DB::select('select * from omeka_exh' . $va->id . '_element_texts where element_id = ?', array(78));
        $results = 0;
        foreach ($linkInsts as $linkInst) {
            if ($linkInst->text !== strip_tags($linkInst->text)) {
                $dom = new DOMDocument();
                $dom->loadHTML($linkInst->text);
                $tags = $dom->getElementsByTagName('a');
                $href = '';
                foreach ($tags as $tag) {
                    $href = $tag->getAttribute('href');
                    break;
                }
                if (!empty($href)) {
                    $result =  DB::update('update omeka_exh' . $va->id . '_element_texts set text = ?, html = ? where id = ?',
                        array(
                            $href,
                            0,
                            $linkInst->id
                        )
                    );
                    $results = $results + $result;
                }
            }
        }
        $this->msg['element_texts'][] = 'Texte für "Link zum Objekt bei der datengebenden Institution" angepasst - Anzahl der Änderungen ' . $results;
    }

    /**
     * Migrate elements db table
     * @param $va object omim exhibition db data
     * @return void
     */
    public function migratePlugins($va)
    {
        $this->msg['plugins'] = array();
        $result = DB::delete('delete from omeka_exh' . $va->id . '_plugins where name = ?', array('SimplePages'));
        $this->msg['plugins'][] = 'Entferne veraltete Plugins - Anzahl der Änderungen ' . $result;

        $results = 0;
        $result = DB::insert('insert into omeka_exh' . $va->id . '_plugins (name, active, version) values (?, ?, ?)',
            array(
                'GinaImageConvert',
                1,
                '1.0.0'
            )
        );
        $results = $results + $result;

        $result = DB::insert('insert into omeka_exh' . $va->id . '_plugins (name, active, version) values (?, ?, ?)',
            array(
                'GinaAdminMod',
                1,
                '1.0.0'
            )
        );
        $results = $results + $result;

        $result = DB::insert('insert into omeka_exh' . $va->id . '_plugins (name, active, version) values (?, ?, ?)',
            array(
                'SimpleVocab',
                1,
                '2.1'
            )
        );
        $results = $results + $result;
        $this->msg['plugins'][] = 'Füge neue Plugins ein - Anzahl der Änderungen ' . $results;

        // add simple vocab DB table and data
        $drop = 'DROP TABLE IF EXISTS `omeka_exh' . $va->id . '_simple_vocab_terms`;';
        $result = DB::statement($drop);

        $simpleVocabTbl = 'CREATE TABLE `omeka_exh' . $va->id . '_simple_vocab_terms` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `element_id` int(10) unsigned NOT NULL,
          `terms` text COLLATE utf8_unicode_ci NOT NULL,
          PRIMARY KEY (`id`),
          UNIQUE KEY `element_id` (`element_id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;';
        $result = DB::statement($simpleVocabTbl);
        $this->msg['plugins'][] = 'Datenbank Tabellen anlegen für Plug "SimpleVocab" - Anzahl der Änderungen ' . $result;

        $simpleVocabData = "INSERT INTO `omeka_exh" . $va->id . "_simple_vocab_terms` (`id`, `element_id`, `terms`) VALUES
        (1, 72, '[[license:CC-PD-M1]]|||Public Domain Mark 1.0\n[[license:CC-PD-U1]]|||CC0 1.0 Universell - Public Domain Dedication\n[[license:G-RR-AF]]|||Rechte vorbehalten - Freier Zugang\n[[license:G-RR-AA]]|||Rechte vorbehalten - Zugang nach Autorisierung\n[[license:CC-BY-3.0-DEU]]|||Namensnennung 3.0 Deutschland\n[[license:CC-BY-4.0-INT]]|||Namensnennung 4.0 International\n[[license:CC-BY-SA-3.0-DEU]]|||Namensnennung - Weitergabe unter gleichen Bedingungen 3.0 Deutschland\n[[license:CC-BY-SA-4.0-INT]]|||Namensnennung - Weitergabe unter gleichen Bedingungen 4.0 International\n[[license:CC-BY-ND-3.0-DEU]]|||Namensnennung - Keine Bearbeitung 3.0 Deutschland\n[[license:CC-BY-ND-4.0-INT]]|||Namensnennung - Keine Bearbeitung 4.0 International\n[[license:CC-BY-NC-3.0-DEU]]|||Namensnennung - Nicht kommerziell 3.0 Deutschland\n[[license:CC-BY-NC-4.0-INT]]|||Namensnennung - Nicht kommerziell 4.0 International\n[[license:CC-BY-NC-SA-3.0-DEU]]|||Namensnennung - Nicht kommerziell - Weitergabe unter gleichen Bedingungen 3.0 Deutschland\n[[license:CC-BY-NC-SA-4.0-INT]]|||Namensnennung - Nicht kommerziell - Weitergabe unter gleichen Bedingungen 4.0 International\n[[license:CC-BY-NC-ND-3.0-DEU]]|||Namensnennung - Nicht kommerziell - Keine Bearbeitung 3.0 Deutschland\n[[license:CC-BY-NC-ND-4.0-INT]]|||Namensnennung - Nicht kommerziell - Keine Bearbeitung 4.0 International\n[[license:G-VW]]|||Verwaistes Werk\n[[license:G-NUG-KKN]]|||Nicht urheberrechtlich geschützt - Keine kommerzielle Nachnutzung');";
        $result = DB::statement($simpleVocabData);
        $this->msg['plugins'][] = 'Datenbank Inhalte anlegen für Plug "SimpleVocab" - Anzahl der Änderungen ' . $result;

    }
    /**
     * Migrate elements db table
     * @param $va object omim exhibition db data
     * @return void
     */
    public function migrateOptions($va)
    {
        $this->msg['options'] = array();
        $result =  DB::update('update omeka_exh' . $va->id . '_options set value = ? where name = ?',
            array(
                $va->title,
                'site_title',
            )
        );
        $this->msg['options'][] = 'Setze Seitentitel (Blogtitel) - Anzahl der Änderungen ' . $result;

        $result =  DB::update('update omeka_exh' . $va->id . '_options set value = ? where name = ?',
            array(
                'service@deutsche-digitale-bibliothek.de',
                'administrator_email',
            )
        );
        $this->msg['options'][] = 'Setze E-Mail-Adresse des Administrators - Anzahl der Änderungen ' . $result;

        $result = DB::insert('insert into omeka_exh' . $va->id . '_options (name, value) values (?, ?)',
            array(
                'simple_vocab_files',
                0,
            )
        );
        $this->msg['options'][] = 'Setze Option für Simple Vocab PLugin - Anzahl der Änderungen ' . $result;

        $results = 0;
        $result = DB::insert('insert into omeka_exh' . $va->id . '_options (name, value) values (?, ?)',
            array(
                'gina_admin_mod_dashboard_panel_title',
                'Wenn Sie Unterstützung benötigen: ',
            )
        );
        $results = $results + $result;
        $result = DB::insert('insert into omeka_exh' . $va->id . '_options (name, value) values (?, ?)',
            array(
                'gina_admin_mod_dashboard_panel_content',
                '<p><a title="Kuratoren-Handbuch online" href="https://deutsche-digitale-bibliothek.github.io/ddb-virtualexhibitions-docs/" target="_blank">Benutzungs-Handbuch online</a></p>
<h4>Ansprechpersonen:</h4>
<p>Laura Schr&ouml;der<br /><a href="mailto:L.Schroeder@dnb.de">L.Schroeder@dnb.de</a><br />Tel.:&nbsp;<span>+49 69 1525-1793<br /></span></p>
<p>Lisa Landes<br /><a href="mailto:L.Landes@dnb.de" target="_self">L.Landes@dnb.de</a><br />Tel.:&nbsp;<span>+49 69 1525-1797<br /><br /></span></p>',
            )
        );
        $results = $results + $result;
        $this->msg['options'][] = 'Setze Optionen für Admin Modifications Plugin - Anzahl der Änderungen ' . $results;

    }

    public function getMigrateomekaversion()
    {
        // Only users with root privileges are allowed to migrate
        if (Auth::user()->isroot != 1) {
            return Redirect::to('admin')->with('error-message',
                'Sie haben keine Berechtigung, die Ressource \'admin/create\' zu verwenden.');
        }

        // extract bootstrap from delpoy file
        $datapath = realpath(base_path() . DIRECTORY_SEPARATOR . 'data');
        $tarfile = realpath(base_path() . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'deploy_lf.tar.gz');
        exec('tar -xzf ' . $tarfile . ' -C ' . $datapath . ' bootstrap.php');

        // Just a notice: regenerate deploy_lf.tar.gz:
        // tar -cvzf deploy_lf.tar.gz -C /absulte/path/to/files * .htaccess

        $publicpath = realpath(base_path() . DIRECTORY_SEPARATOR . 'public');
        $updates = array();

        $vas = OmimInstance::all();
        foreach ($vas as $va) {
            $bootstrap = $publicpath . DIRECTORY_SEPARATOR . $va->slug . DIRECTORY_SEPARATOR . 'bootstrap.php';
            $contents = file_get_contents($bootstrap);
            preg_match("/define\('OMEKA_VERSION'\, '2\.[0-6]{1}\.[0-9]{1}'\)\;/", $contents, $matches);
            if (!empty($matches)) {
                copy(
                    $datapath . DIRECTORY_SEPARATOR . 'bootstrap.php',
                    $publicpath . DIRECTORY_SEPARATOR . $va->slug . DIRECTORY_SEPARATOR . 'bootstrap.php'
                );
                $updates[] = $va;
            }

        }

        unlink($datapath . DIRECTORY_SEPARATOR . 'bootstrap.php');
        return View::make('migrate.migrateomekaversion', compact('updates'));
    }

}