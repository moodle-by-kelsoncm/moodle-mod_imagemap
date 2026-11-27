<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Upgrade script for mod_imagemap
 *
 * @package    mod_imagemap
 * @copyright  2026 Kelson C. M.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Execute imagemap upgrade from the given old version
 *
 * @param int $oldversion
 * @return bool
 */
function xmldb_imagemap_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    // Automatically generated Moodle v3.9.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2026013001) {
        // Define field activefilter to be changed to text.
        $table = new xmldb_table('imagemap_area');
        $field = new xmldb_field('activefilter', XMLDB_TYPE_TEXT, null, null, null, null, null, 'conditioncmid');

        // Launch change of type for field activefilter.
        $dbman->change_field_type($table, $field);

        // Define field inactivefilter to be changed to text.
        $field = new xmldb_field('inactivefilter', XMLDB_TYPE_TEXT, null, null, null, null, null, 'activefilter');

        // Launch change of type for field inactivefilter.
        $dbman->change_field_type($table, $field);

        // Imagemap savepoint reached.
        upgrade_mod_savepoint(true, 2026013001, 'imagemap');
    }

    if ($oldversion < 2026013101) {
        // Define table imagemap_css_examples to be created.
        $table = new xmldb_table('imagemap_css_examples');

        // Adding fields to table imagemap_css_examples.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('type', XMLDB_TYPE_CHAR, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('css_text', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('sortorder', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');

        // Adding keys to table imagemap_css_examples.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Adding indexes to table imagemap_css_examples.
        $table->add_index('type', XMLDB_INDEX_NOTUNIQUE, ['type']);
        $table->add_index('sortorder', XMLDB_INDEX_NOTUNIQUE, ['sortorder']);

        // Conditionally launch create table for imagemap_css_examples.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
            // The default examples are created by the 2026112700 step.
        }

        // Imagemap savepoint reached.
        upgrade_mod_savepoint(true, 2026013101, 'imagemap');
    }

    if ($oldversion < 2026021001) {
        // Define table imagemap_line to be created.
        $table = new xmldb_table('imagemap_line');

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('imagemapid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('from_areaid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('to_areaid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');

        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('imagemapid', XMLDB_KEY_FOREIGN, ['imagemapid'], 'imagemap', ['id']);
        $table->add_key('from_areaid', XMLDB_KEY_FOREIGN, ['from_areaid'], 'imagemap_area', ['id']);
        $table->add_key('to_areaid', XMLDB_KEY_FOREIGN, ['to_areaid'], 'imagemap_area', ['id']);

        $table->add_index('imagemapid_from_to', XMLDB_INDEX_UNIQUE, ['imagemapid', 'from_areaid', 'to_areaid']);

        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        upgrade_mod_savepoint(true, 2026021001, 'imagemap');
    }

    if ($oldversion < 2026021101) {
        $table = new xmldb_table('imagemap_area');

        $field = new xmldb_field('targettype', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, 'module', 'coords');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('targetid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'targettype');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Migrate existing data from linktype/linktarget.
        if ($dbman->field_exists($table, new xmldb_field('linktype'))) {
            $areas = $DB->get_records('imagemap_area', null, '', 'id, linktype, linktarget');
            foreach ($areas as $area) {
                if ($area->linktype === 'module' || $area->linktype === 'section') {
                    $DB->set_field('imagemap_area', 'targettype', $area->linktype, ['id' => $area->id]);
                    $DB->set_field('imagemap_area', 'targetid', (int)$area->linktarget, ['id' => $area->id]);
                }
            }
        }

        // Drop old fields and index.
        $index = new xmldb_index('conditioncmid', XMLDB_INDEX_NOTUNIQUE, ['conditioncmid']);
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }

        $field = new xmldb_field('linktype');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        $field = new xmldb_field('linktarget');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        $field = new xmldb_field('conditioncmid');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2026021101, 'imagemap');
    }

    if ($oldversion < 2026030901) {
        $table = new xmldb_table('imagemap_css_examples');
        $field = new xmldb_field('type', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, null, 'id');
        $index = new xmldb_index('type', XMLDB_INDEX_NOTUNIQUE, ['type']);

        if ($dbman->table_exists($table) && $dbman->field_exists($table, $field)) {
            // Some databases require dropping dependent indexes before changing column precision.
            if ($dbman->index_exists($table, $index)) {
                $dbman->drop_index($table, $index);
            }

            $dbman->change_field_precision($table, $field);

            if (!$dbman->index_exists($table, $index)) {
                $dbman->add_index($table, $index);
            }
        }

        upgrade_mod_savepoint(true, 2026030901, 'imagemap');
    }

    if ($oldversion < 2026112700) {
        // Default CSS examples: English names instead of the old Portuguese ones.
        $renames = [
            'Sem Efeito' => 'No effect',
            'Borda Azul' => 'Blue border',
            'Borda Cinza' => 'Gray border',
            'Brilho Leve' => 'Soft brightness',
            'Realce Azul' => 'Blue highlight',
            'Zoom Suave' => 'Soft zoom',
            'Contraste Leve' => 'Soft contrast',
            'Saturacao Leve' => 'Soft saturation',
            'Halo Verde' => 'Green halo',
            'Cinza + Escuro' => 'Gray and dark',
            'Desfoque Leve' => 'Soft blur',
            'Opacidade Reduzida' => 'Reduced opacity',
            'Baixo Contraste' => 'Low contrast',
            'Dessaturado' => 'Desaturated',
        ];
        foreach ($renames as $oldname => $newname) {
            $DB->set_field('imagemap_css_examples', 'name', $newname, ['name' => $oldname]);
        }

        // Create the defaults of the types that have none; pages no longer create them.
        \mod_imagemap\local\css_examples::create_defaults();

        upgrade_mod_savepoint(true, 2026112700, 'imagemap');
    }

    return true;
}
