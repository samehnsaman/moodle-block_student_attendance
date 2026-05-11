# Moodle Plugins Directory Submission Text

## Plugin name

Student Attendance

## Component

block_student_attendance

## Plugin type

Block

## Supported Moodle versions

Moodle 4.0 and later.

## Dependencies

Requires the Attendance activity plugin (`mod_attendance`).

## Short description

Shows students their own Attendance activity records in a course-card carousel.

## Description

Student Attendance is a Moodle block that helps students track their recorded Attendance activity data across enrolled courses.

The block displays one card for each visible Attendance activity where the current student has recorded attendance. Each card includes the course name, Attendance activity name, recorded session count, attendance percentage when status grades are available, counts by configured attendance status, and the last five recorded sessions with status and remarks.

The plugin supports standard Moodle web output and Moodle App output. It does not create custom database tables and does not store personal data of its own.

## Privacy

The block does not store personal data. It only reads attendance records already stored by `mod_attendance` and displays them to the current logged-in student.

## Installation

Install the plugin into `blocks/student_attendance`, then visit Site administration > Notifications.

## Suggested tags

attendance, student, dashboard, block, mobile app

## Repository URL

To be provided.

## Issue tracker URL

To be provided.

## Release notes for 1.1.0

- Adds Moodle block implementation for student attendance summaries.
- Adds one card per visible Attendance activity.
- Shows recorded sessions, attendance percentage, status counts, and last five records.
- Adds Moodle App support.
- Adds a null Privacy API provider.
