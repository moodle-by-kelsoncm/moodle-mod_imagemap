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
 * Site-wide CSS examples (presets) offered in the area editor.
 *
 * The defaults are created once, on install and on upgrade; pages only read them.
 *
 * @package    mod_imagemap
 * @copyright  2026 Kelson C. M.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class css_examples {
    /** @var string[] Example types: active, inactive and the hover variants. */
    public const TYPES = ['active', 'inactive', 'acthover', 'inahover'];

    /**
     * Default examples, with English source names.
     *
     * @return array<int, array{type: string, name: string, css_text: string, sortorder: int}>
     */
    public static function defaults(): array {
        $defaults = [
            'active' => [
                ['No effect', 'none'],
                ['Grayscale', 'filter: grayscale(100%);'],
                ['Opacity 50%', 'filter: opacity(0.5);'],
                ['Blur', 'filter: blur(2px);'],
                ['Blue border', 'border: 2px solid #0073e6;'],
            ],
            'inactive' => [
                ['No effect', 'none'],
                ['Grayed out', 'filter: grayscale(100%);'],
                ['Blurred', 'filter: blur(3px);'],
                ['Gray border', 'border: 2px solid #9e9e9e;'],
            ],
            'acthover' => [
                ['No effect', 'none'],
                ['Soft brightness', 'filter: brightness(1.15);'],
                ['Blue highlight', 'box-shadow: 0 0 0 3px rgba(0,115,230,0.4);'],
                ['Soft zoom', 'transform: scale(1.03);'],
                ['Soft contrast', 'filter: contrast(1.15);'],
                ['Soft saturation', 'filter: saturate(1.25);'],
                ['Green halo', 'box-shadow: 0 0 0 3px rgba(40,167,69,0.35);'],
            ],
            'inahover' => [
                ['No effect', 'none'],
                ['Gray and dark', 'filter: grayscale(1) brightness(0.75);'],
                ['Soft blur', 'filter: blur(1px) grayscale(0.7);'],
                ['Reduced opacity', 'opacity: 0.75;'],
                ['Low contrast', 'filter: contrast(0.75) brightness(0.8);'],
                ['Desaturated', 'filter: saturate(0.35) brightness(0.85);'],
            ],
        ];

        $examples = [];
        foreach ($defaults as $type => $items) {
            foreach ($items as $sortorder => [$name, $csstext]) {
                $examples[] = ['type' => $type, 'name' => $name, 'css_text' => $csstext, 'sortorder' => $sortorder];
            }
        }
        return $examples;
    }

    /**
     * Creates the default examples of every type that has no example yet.
     *
     * Types the administrator already filled in are left untouched.
     */
    public static function create_defaults(): void {
        global $DB;

        $now = time();
        foreach (self::TYPES as $type) {
            if ($DB->record_exists('imagemap_css_examples', ['type' => $type])) {
                continue;
            }
            foreach (self::defaults() as $example) {
                if ($example['type'] !== $type) {
                    continue;
                }
                $DB->insert_record('imagemap_css_examples', (object)($example + [
                    'timecreated' => $now,
                    'timemodified' => $now,
                ]));
            }
        }
    }

    /**
     * Returns the examples of a type, in display order.
     *
     * @param string $type One of self::TYPES.
     * @return \stdClass[]
     */
    public static function get_by_type(string $type): array {
        global $DB;

        if (!in_array($type, self::TYPES, true)) {
            return [];
        }
        return $DB->get_records('imagemap_css_examples', ['type' => $type], 'sortorder ASC, name ASC');
    }
}
