# Student Attendance

Student Attendance is a Moodle block plugin that helps students track attendance records from the Moodle Attendance activity (`mod_attendance`).

The block shows one card for each visible Attendance activity where the current logged-in student has recorded attendance data. Each card includes the course name, attendance activity name, recorded session count, attendance percentage when status grades allow it, counts by configured attendance status, and the last five recorded sessions.

## Requirements

- Moodle 4.0 or later.
- The Attendance activity plugin (`mod_attendance`).

## Installation

1. Copy the plugin folder to `blocks/student_attendance`.
2. Visit Site administration > Notifications to complete installation.
3. Add the block to Dashboard, course pages, or other supported block regions.

The installable ZIP package should be rooted at `student_attendance/`.

## Usage

Students see only their own attendance records. The block automatically finds enrolled, visible courses that contain visible Attendance activities with recorded attendance rows for the current student.

Each card shows:

- Course and Attendance activity names.
- Recorded session count.
- Attendance percentage based on the configured Attendance status grades, when available.
- Counts by actual configured Attendance statuses.
- The last five recorded sessions with date, status, and remarks when available.
- A link to the Attendance activity.

The block also includes Moodle App support using Moodle's mobile plugin handler system.

## Privacy

This block does not store personal data. It reads and displays attendance data already stored by `mod_attendance` for the current logged-in student.

## Support

Repository URL: To be provided.

Issue tracker URL: To be provided.

Maintainer: To be provided.

## License

GNU GPL v3 or later.
