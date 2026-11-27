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

namespace mod_imagemap;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/imagemap/lib.php');

/**
 * Tests for the area deletion and the instance list viewed event.
 *
 * @package    mod_imagemap
 * @category   test
 * @copyright  2026 Kelson C. M.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     ::imagemap_delete_area
 * @covers     \mod_imagemap\event\course_module_instance_list_viewed
 */
final class lib_test extends \advanced_testcase {
    /**
     * Create an image map with two areas joined by a line.
     *
     * @param int $courseid Course id.
     * @return array [image map record, area ids, line id]
     */
    private function create_imagemap_with_line(int $courseid): array {
        global $DB;

        $imagemap = $this->getDataGenerator()->create_module('imagemap', ['course' => $courseid]);
        $areaids = [];
        foreach ([0, 1] as $sortorder) {
            $areaids[] = (int)$DB->insert_record('imagemap_area', (object)[
                'imagemapid' => $imagemap->id,
                'shape' => 'rect',
                'coords' => '0,0,10,10',
                'targettype' => 'section',
                'targetid' => 0,
                'title' => 'Area ' . $sortorder,
                'sortorder' => $sortorder,
            ]);
        }
        $lineid = (int)$DB->insert_record('imagemap_line', (object)[
            'imagemapid' => $imagemap->id,
            'from_areaid' => $areaids[0],
            'to_areaid' => $areaids[1],
            'timecreated' => time(),
        ]);
        return [$imagemap, $areaids, $lineid];
    }

    /**
     * Deleting an area removes the area and its lines.
     */
    public function test_delete_area_removes_area_and_lines(): void {
        global $DB;
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        [$imagemap, $areaids, $lineid] = $this->create_imagemap_with_line($course->id);

        imagemap_delete_area($imagemap, $areaids[0]);

        $this->assertFalse($DB->record_exists('imagemap_area', ['id' => $areaids[0]]));
        $this->assertTrue($DB->record_exists('imagemap_area', ['id' => $areaids[1]]));
        $this->assertFalse($DB->record_exists('imagemap_line', ['id' => $lineid]));
    }

    /**
     * An area of another image map is not deleted, nor are its lines.
     */
    public function test_delete_area_of_other_imagemap_changes_nothing(): void {
        global $DB;
        $this->resetAfterTest();

        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        [$imagemap1] = $this->create_imagemap_with_line($course1->id);
        [, $otherareaids, $otherlineid] = $this->create_imagemap_with_line($course2->id);

        try {
            imagemap_delete_area($imagemap1, $otherareaids[0]);
            $this->fail('Expected dml_missing_record_exception');
        } catch (\dml_missing_record_exception $e) {
            $this->assertTrue($DB->record_exists('imagemap_area', ['id' => $otherareaids[0]]));
            $this->assertTrue($DB->record_exists('imagemap_line', ['id' => $otherlineid]));
        }
    }

    /**
     * The instance list viewed event can be created and triggered.
     */
    public function test_instance_list_viewed_event(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $event = \mod_imagemap\event\course_module_instance_list_viewed::create([
            'context' => \context_course::instance($course->id),
        ]);
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $sink->close();

        $this->assertCount(1, $events);
        $this->assertInstanceOf(\mod_imagemap\event\course_module_instance_list_viewed::class, $events[0]);
        $this->assertEquals('r', $events[0]->crud);
        $this->assertEquals(\core\event\base::LEVEL_OTHER, $events[0]->edulevel);
    }
}
