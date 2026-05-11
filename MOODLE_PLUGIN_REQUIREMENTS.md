# Moodle Plugin Requirements

This file tracks the Moodle plugin-directory and contribution rules that this plugin should continue to follow.

It is a maintenance checklist, not a runtime verification record.

## Naming and structure

- Use Frankenstyle names for plugin components, capabilities, classes, and database tables.
- Database tables should follow `plugintype_pluginname_tablename`.
- Keep the plugin root folder name aligned with the component name: `student_attendance` for `block_student_attendance`.
- Keep standard Moodle plugin files in place where needed:
  - `version.php`
  - `db/access.php`
  - `db/install.xml` when the plugin owns database tables
  - `db/tasks.php` when the plugin has scheduled tasks
  - `db/messages.php` when the plugin sends Moodle messages
  - `lang/en/block_student_attendance.php`

## Capabilities

- Define `block/student_attendance:addinstance`.
- Define `block/student_attendance:myaddinstance` for dashboard/self-add support.
- Define only the capabilities the plugin actually uses, and gate admin-only features with capability checks.

## Language strings

- Every referenced string key must exist in the language pack.
- Message providers declared in `db/messages.php` must have matching strings like `messageprovider:announcement`.
- Prefer sentence-style wording instead of title-style wording in English strings.

## Privacy API

- If the plugin stores personal data, provide a Privacy API provider in `classes/privacy/provider.php`.
- If the plugin only reads data owned by `mod_attendance`, use a null privacy provider and explain that no personal data is stored by the block.

## Documentation and submission metadata

- Keep a `README.md` at the plugin root.
- README should cover:
  - plugin purpose
  - installation
  - supported Moodle versions
  - configuration and usage summary
  - privacy notes
  - repository URL
  - issue tracker URL
  - support or maintenance notes

## File headers

- PHP files should include standard Moodle GPL headers.
- Add package metadata in docblocks.
- Include `@copyright`.
- Include `@license`.

## Database and cross-DB compatibility

- Use Moodle DB APIs instead of database-specific SQL where possible.
- Build `LIKE` conditions with Moodle helpers rather than hand-writing driver-specific SQL.
- Add explicit indexes for frequent lookup paths.
- If table names change, provide upgrade steps in `db/upgrade.php`.

## Messaging and scheduled tasks

- Declare message providers in `db/messages.php`.
- Use Moodle messaging APIs instead of custom channel logic when possible.
- In scheduled tasks, do not depend on the interactive `$USER` as sender context.
- Prefer a stable Moodle sender such as the noreply user for system-driven messages.
- Process queued delivery through scheduled tasks, not page loads.

## Forms and UI

- Prefer standard Moodle form patterns such as `hideIf` for conditional form sections.
- Use Moodle field lookups and standard filter-style operators where the UI mirrors core Moodle behavior.
- Keep access-controlled file delivery behind Moodle `pluginfile` handling.
- Keep business logic out of block rendering where practical.

## CSS and front-end output

- Put persistent plugin styling in `styles.css`.
- Avoid inline `<style>` output for normal component styling.
- Keep markup simple and Moodle-theme-friendly.

## Release packaging

- Package only the plugin folder contents.
- Do not include `.git`, macOS metadata files, local temp files, or editor junk.
- Keep the installable zip rooted at `student_attendance/`.

## Review discipline

- Do not claim runtime verification unless it was actually performed.
- When local Moodle execution is unavailable, document manual verification steps instead.
- Re-check Moodle plugin review guidance before submission:
  - frankenstyle naming
  - privacy metadata
  - capabilities
  - strings
  - CSS placement
  - README and metadata completeness
  - cross-database compatibility
