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
 * Admin helpers for CSS examples pages.
 *
 * @package    mod_imagemap
 * @copyright  2026 Kelson C. M.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_once(__DIR__ . '/../lib.php');

require_login();

/**
 * Common page setup for admin pages.
 *
 * @param string $url
 */
function mod_imagemap_admin_setup_page(string $url): void {
    global $PAGE;

    $PAGE->set_url($url);
    $PAGE->set_context(context_system::instance());
    $PAGE->set_title(get_string('cssexamples', 'imagemap'));
    $PAGE->set_heading(get_string('cssexamples', 'imagemap'));
    $PAGE->set_pagelayout('admin');

    $PAGE->navbar->add(
        get_string('pluginadministration', 'imagemap'),
        new moodle_url('/admin/category.php', ['category' => 'modsettings'])
    );
    $PAGE->navbar->add(get_string('cssexamples', 'imagemap'));
}

/**
 * Normalize external/internal type identifiers to DB-safe values.
 *
 * @param string $type
 * @return string|null
 */
function mod_imagemap_admin_normalize_type(string $type): ?string {
    $map = [
        'active' => 'active',
        'inactive' => 'inactive',
        'activehover' => 'acthover',
        'inactivehover' => 'inahover',
        'acthover' => 'acthover',
        'inahover' => 'inahover',
    ];

    $key = core_text::strtolower(trim($type));
    return $map[$key] ?? null;
}

/**
 * Convert DB type to exported/public identifier.
 *
 * @param string $type
 * @return string
 */
function mod_imagemap_admin_export_type(string $type): string {
    $map = [
        'active' => 'active',
        'inactive' => 'inactive',
        'acthover' => 'activehover',
        'inahover' => 'inactivehover',
    ];

    return $map[$type] ?? $type;
}

/**
 * Allowed CSS example types.
 *
 * @return array<int, string>
 */
function mod_imagemap_admin_allowed_types(): array {
    return ['active', 'inactive', 'acthover', 'inahover'];
}

/**
 * Get the CSS examples of a type.
 *
 * The defaults are created on install and upgrade (\mod_imagemap\local\css_examples).
 *
 * @param string $type
 * @return array
 */
function mod_imagemap_admin_get_examples(string $type): array {
    $type = mod_imagemap_admin_normalize_type($type) ?? '';
    return \mod_imagemap\local\css_examples::get_by_type($type);
}
