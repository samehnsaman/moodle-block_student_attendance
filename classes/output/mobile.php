<?php

declare(strict_types=1);

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
 * Moodle App output for block_student_attendance.
 *
 * @package    block_student_attendance
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_student_attendance\output;

defined('MOODLE_INTERNAL') || die();

use block_student_attendance\attendance_data;

/**
 * Mobile app renderer.
 */
class mobile {

    /**
     * Renders the block for the Moodle App.
     *
     * @param array|object $args Arguments supplied by the app.
     * @return array
     */
    public static function mobile_view($args): array {
        global $OUTPUT, $USER;

        $userid = self::get_requested_userid($args);
        if ($userid !== (int) $USER->id) {
            $userid = (int) $USER->id;
        }

        $cards = attendance_data::get_cards($userid);
        $data = [
            'hascards' => !empty($cards),
            'cards' => $cards,
        ];

        return [
            'templates' => [
                [
                    'id' => 'main',
                    'html' => $OUTPUT->render_from_template('block_student_attendance/mobileapp/carousel', $data),
                ],
            ],
        ];
    }

    /**
     * Gets the user id requested by the app, when supplied.
     *
     * @param array|object $args
     * @return int
     */
    protected static function get_requested_userid($args): int {
        if (is_array($args) && !empty($args['userid'])) {
            return (int) $args['userid'];
        }

        if (is_object($args) && !empty($args->userid)) {
            return (int) $args->userid;
        }

        global $USER;
        return (int) $USER->id;
    }
}
