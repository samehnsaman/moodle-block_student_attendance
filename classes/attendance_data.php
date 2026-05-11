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
 * Attendance data builder shared by the web block and Moodle App view.
 *
 * @package    block_student_attendance
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_student_attendance;

defined('MOODLE_INTERNAL') || die();

/**
 * Builds current-user attendance summaries.
 */
class attendance_data {

    /**
     * Builds attendance cards for all visible attendance activities with records.
     *
     * @param int $userid
     * @return array
     */
    public static function get_cards(int $userid): array {
        global $CFG;

        require_once($CFG->libdir . '/enrollib.php');

        $cards = [];
        $courses = enrol_get_users_courses($userid, true, 'id, fullname, shortname, visible');

        foreach ($courses as $course) {
            if (empty($course->visible)) {
                continue;
            }

            $coursecontext = \context_course::instance($course->id, IGNORE_MISSING);
            if (!$coursecontext) {
                continue;
            }

            $modinfo = get_fast_modinfo($course, $userid);
            if (empty($modinfo->instances['attendance'])) {
                continue;
            }

            foreach ($modinfo->instances['attendance'] as $cm) {
                if (empty($cm->uservisible)) {
                    continue;
                }

                $attendanceid = (int) $cm->instance;
                $records = self::get_attendance_records($attendanceid, $userid);
                if (empty($records)) {
                    continue;
                }

                $modulecontext = \context_module::instance($cm->id, IGNORE_MISSING);
                if (!$modulecontext) {
                    continue;
                }

                $percentage = self::get_attendance_percentage($attendanceid, $records);

                $cards[] = [
                    'course' => format_string($course->fullname, true, ['context' => $coursecontext]),
                    'activity' => format_string($cm->name, true, ['context' => $modulecontext]),
                    'url' => (new \moodle_url('/mod/attendance/view.php', ['id' => $cm->id]))->out(false),
                    'recorded' => count($records),
                    'haspercentage' => $percentage !== null,
                    'percentage' => $percentage,
                    'percentagefraction' => $percentage !== null ? round($percentage / 100, 2) : 0,
                    'statuscounts' => array_values(self::get_status_counts($records)),
                    'lastrecords' => array_map([self::class, 'format_last_record'], array_slice($records, 0, 5)),
                ];
            }
        }

        return $cards;
    }

    /**
     * Gets recorded attendance rows for one attendance activity and one student.
     *
     * @param int $attendanceid
     * @param int $userid
     * @return array
     */
    protected static function get_attendance_records(int $attendanceid, int $userid): array {
        global $DB;

        $sql = "SELECT al.id,
                       al.statusid,
                       al.remarks,
                       al.timetaken,
                       ats.sessdate,
                       ast.acronym,
                       ast.description,
                       ast.grade
                  FROM {attendance_log} al
                  JOIN {attendance_sessions} ats ON ats.id = al.sessionid
             LEFT JOIN {attendance_statuses} ast ON ast.id = al.statusid
                 WHERE al.studentid = :userid
                   AND ats.attendanceid = :attendanceid
              ORDER BY ats.sessdate DESC, al.timetaken DESC, al.id DESC";

        return array_values($DB->get_records_sql($sql, [
            'userid' => $userid,
            'attendanceid' => $attendanceid,
        ]));
    }

    /**
     * Groups attendance records by their configured status.
     *
     * @param array $records
     * @return array
     */
    protected static function get_status_counts(array $records): array {
        $counts = [];

        foreach ($records as $record) {
            $statusid = (int) $record->statusid;
            if (!isset($counts[$statusid])) {
                $label = self::get_status_label($record);
                $counts[$statusid] = [
                    'label' => $label,
                    'count' => 0,
                    'class' => self::get_status_class($label, $record->acronym ?? ''),
                ];
            }
            $counts[$statusid]['count']++;
        }

        uasort($counts, static function(array $a, array $b): int {
            return strnatcasecmp($a['label'], $b['label']);
        });

        return $counts;
    }

    /**
     * Calculates a percentage using attendance status grades.
     *
     * @param int $attendanceid
     * @param array $records
     * @return int|null
     */
    protected static function get_attendance_percentage(int $attendanceid, array $records): ?int {
        global $DB;

        $maxgrade = (float) $DB->get_field_sql(
            "SELECT MAX(grade)
               FROM {attendance_statuses}
              WHERE attendanceid = :attendanceid
                AND visible = 1
                AND deleted = 0",
            ['attendanceid' => $attendanceid]
        );

        if ($maxgrade <= 0) {
            return null;
        }

        $earned = 0.0;
        foreach ($records as $record) {
            $earned += isset($record->grade) ? (float) $record->grade : 0.0;
        }

        $percentage = (int) round(($earned / (count($records) * $maxgrade)) * 100);
        return max(0, min(100, $percentage));
    }

    /**
     * Formats a recent attendance row for templates.
     *
     * @param \stdClass $record
     * @return array
     */
    protected static function format_last_record(\stdClass $record): array {
        $remarks = trim((string) ($record->remarks ?? ''));

        return [
            'date' => userdate((int) $record->sessdate, get_string('strftimedatetimeshort', 'langconfig')),
            'timestamp' => (int) $record->sessdate,
            'status' => self::get_status_label($record),
            'class' => self::get_status_class($record->description ?? '', $record->acronym ?? ''),
            'hasremarks' => $remarks !== '',
            'remarks' => shorten_text($remarks, 80),
        ];
    }

    /**
     * Gets the best available status label.
     *
     * @param \stdClass $record
     * @return string
     */
    protected static function get_status_label(\stdClass $record): string {
        $description = trim((string) ($record->description ?? ''));
        if ($description !== '') {
            return $description;
        }

        $acronym = trim((string) ($record->acronym ?? ''));
        if ($acronym !== '') {
            return $acronym;
        }

        return get_string('unknownstatus', 'block_student_attendance');
    }

    /**
     * Maps common Attendance status names to badge classes.
     *
     * @param string $label
     * @param string $acronym
     * @return string
     */
    protected static function get_status_class(string $label, string $acronym): string {
        $value = \core_text::strtolower(trim($label));
        $short = \core_text::strtolower(trim($acronym));

        if ($value === 'present' || $short === 'p') {
            return 'att-badge-present';
        }
        if ($value === 'absent' || $short === 'a') {
            return 'att-badge-absent';
        }
        if ($value === 'late' || $short === 'l') {
            return 'att-badge-late';
        }
        if ($value === 'excused' || $short === 'e') {
            return 'att-badge-excused';
        }

        return 'att-badge-other';
    }
}
