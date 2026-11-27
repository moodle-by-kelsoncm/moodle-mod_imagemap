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

namespace mod_imagemap\local;

/**
 * Tests for the default CSS examples.
 *
 * @package    mod_imagemap
 * @category   test
 * @copyright  2026 Kelson C. M.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \mod_imagemap\local\css_examples
 */
final class css_examples_test extends \advanced_testcase {
    /**
     * The install creates the defaults of every type, with English names.
     */
    public function test_defaults_created_on_install(): void {
        global $DB;

        foreach (css_examples::TYPES as $type) {
            $names = array_column(css_examples::get_by_type($type), 'name');
            $expected = array_column(
                array_filter(css_examples::defaults(), fn(array $e): bool => $e['type'] === $type),
                'name'
            );
            $this->assertSame(array_values($expected), array_values($names), $type);
        }
        $this->assertFalse($DB->record_exists('imagemap_css_examples', ['name' => 'Sem Efeito']));
    }

    /**
     * Creating the defaults again does not duplicate them nor touch types the administrator filled in.
     */
    public function test_create_defaults_only_fills_empty_types(): void {
        global $DB;
        $this->resetAfterTest();

        $total = $DB->count_records('imagemap_css_examples');
        css_examples::create_defaults();
        $this->assertEquals($total, $DB->count_records('imagemap_css_examples'));

        $DB->delete_records('imagemap_css_examples', ['type' => 'active']);
        $DB->insert_record('imagemap_css_examples', (object)[
            'type' => 'inactive', 'name' => 'Custom', 'css_text' => 'opacity: 0.5;', 'sortorder' => 99,
        ]);
        css_examples::create_defaults();

        $activecount = count(array_filter(css_examples::defaults(), fn(array $e): bool => $e['type'] === 'active'));
        $this->assertEquals($activecount, $DB->count_records('imagemap_css_examples', ['type' => 'active']));
        $this->assertEquals($total + 1, $DB->count_records('imagemap_css_examples'));
    }

    /**
     * Unknown types return no example.
     */
    public function test_get_by_type_unknown(): void {
        $this->assertSame([], css_examples::get_by_type('other'));
    }
}
