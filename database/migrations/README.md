# SITech – Database Setup Guide

**Project:** Student Information System with QR-Based Attendance and Academic Services  
**Client:** Salus Institute of Technology  
**Stack:** Laravel 12 · PHP 8.4 · MySQL · Spatie Packages

---

## Migration order

| # | File | Creates |
|---|------|---------|
| 1 | `000001_create_users_table` | users |
| 2 | `000002_create_courses_table` | courses |
| 3 | `000003_create_year_levels_table` | year_levels |
| 4 | `000004_create_sections_table` | sections |
| 5 | `000005_create_students_table` | students (approval workflow) |
| 6 | `000006_create_subjects_table` | subjects |
| 7 | `000007_create_class_schedules_table` | class_schedules |
| 8 | `000008_create_study_loads_table` | study_loads |
| 9 | `000009_create_grading_tables` | grading_components, score_items, student_scores, final_grades |
| 10 | `000010_create_announcements_table` | announcements |
| 11 | `000011_create_school_events_table` | school_events |
| 12 | `000012_create_ssg_tables` | ssg_events, ssg_event_attendances |
| — | Spatie auto-generates | roles, permissions, model_has_roles, … |

---

## Setup steps

```bash
# 1. Create your Laravel project (skip if already done)
composer create-project laravel/laravel sitech
cd sitech

# 2. Install required packages
composer require spatie/laravel-permission
composer require spatie/laravel-activitylog
composer require spatie/laravel-pdf
composer require simplesoftwareio/simple-qrcode

# 3. Publish Spatie migrations
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan vendor:publish --provider="Spatie\Activitylog\ActivitylogServiceProvider" --tag="activitylog-migrations"

# 4. Copy all migration files from this folder into:
#    database/migrations/

# 5. Copy DatabaseSeeder.php into:
#    database/seeders/DatabaseSeeder.php

# 6. Set your .env database credentials
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sitech_db
DB_USERNAME=root
DB_PASSWORD=

# 7. Run migrations + seed
php artisan migrate --seed
```

---

## Table relationships

```
users ──────────────────────────────────────────────┐
  │                                                  │
  ├─ has many → students (via encoded_by)            │
  ├─ has many → students (via approved_by)           │
  ├─ has many → students (via user_id)               │
  ├─ has many → class_schedules (as teacher)         │
  ├─ has many → announcements (as posted_by)         │
  ├─ has many → school_events (as created_by)        │
  └─ has many → ssg_events (as created_by)           │
                                                     │
courses ─────────────────────────────────────────────┤
  └─ has many → sections                             │
                                                     │
year_levels ─────────────────────────────────────────┤
  └─ has many → sections                             │
                                                     │
sections                                             │
  └─ has many → students                             │
  └─ has many → class_schedules                      │
                                                     │
students                                             │
  ├─ belongs to → users, courses, year_levels, sections
  ├─ has many → study_loads                          │
  ├─ has many → student_scores (via score_items)     │
  ├─ has many → final_grades                         │
  └─ has many → ssg_event_attendances                │
                                                     │
subjects                                             │
  └─ has many → class_schedules                      │
                                                     │
class_schedules                                      │
  ├─ belongs to → subjects, users (teacher), sections│
  ├─ has many → study_loads                          │
  ├─ has many → grading_components                   │
  └─ has many → final_grades                         │
                                                     │
grading_components                                   │
  └─ has many → score_items                          │
                                                     │
score_items                                          │
  └─ has many → student_scores                       │
                                                     │
ssg_events                                           │
  └─ has many → ssg_event_attendances                │
```

---

## Key business rules encoded in the schema

| Rule | How it's stored |
|------|-----------------|
| Two-step student approval | `is_approved`, `encoded_by`, `approved_by`, `approved_at` on `students` |
| Auto-compute final grade | `final_grades` is populated by Laravel after teacher saves scores |
| Fine = 0 if present | `actual_fine` on `ssg_event_attendances` is set to 0 when `is_present = true` |
| One score per item per student | `UNIQUE(student_id, score_item_id)` on `student_scores` |
| One grade per subject per student | `UNIQUE(student_id, class_schedule_id)` on `final_grades` |
| Weights must total 100% | Validated at the application layer (Laravel FormRequest) |
| QR generated after approval | `qr_code_path` populated only after `is_approved = true` |
