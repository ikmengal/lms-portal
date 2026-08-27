<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{
    AssignmentSubmission,
    Certificate,
    Course,
    Enrollment,
    LiveClass,
    Payment,
    QuizAttempt,
    Review,
    Setting,
    User,
};
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\Response;

class ReportController extends Controller
{
    private const TABS = [
        'enrollments' => 'Enrollments',
        'payments' => 'Payments & Revenue',
        'students' => 'Students',
        'courses' => 'Course Performance',
        'quizzes' => 'Quiz Results',
        'assignments' => 'Assignment Submissions',
        'certificates' => 'Certificates',
        'live-classes' => 'Live Classes',
    ];

    public function index(Request $request)
    {
        $tab = $request->query('tab', 'enrollments');
        if (!array_key_exists($tab, self::TABS)) {
            $tab = 'enrollments';
        }

        $report = $this->buildReport($tab, $request);

        return view('pages.admin.reports', array_merge([
            'tabs' => self::TABS,
            'tab' => $tab,
            'reportTitle' => $report['title'],
            'reportDescription' => $report['description'],
            'columns' => $report['columns'],
            'rows' => $report['rows']->take(500),
            'stats' => $report['stats'],
            'courses' => Course::withTrashed()->orderBy('title')->get(['id', 'title']),
            'filters' => $this->filters($request),
        ], $this->filterOptions()));
    }

    public function export(Request $request)
    {
        $tab = $request->query('tab', 'enrollments');
        if (!array_key_exists($tab, self::TABS)) {
            $tab = 'enrollments';
        }

        $format = in_array($request->query('format', 'csv'), ['csv', 'pdf', 'xlsx'])
            ? $request->query('format')
            : 'csv';

        $report = $this->buildReport($tab, $request);
        $columns = $report['columns'];
        $rows = $report['rows'];
        $filename = 'report-' . $tab . '-' . now()->format('Y-m-d-His');
        $meta = $this->reportMeta($request, $tab, $report);

        return match ($format) {
            'pdf' => $this->pdfResponse($report, $columns, $rows, $filename, $meta),
            'xlsx' => $this->xlsxResponse($columns, $rows, $filename, $meta),
            default => $this->csvResponse($columns, $rows, $filename),
        };
    }

    private function reportMeta(Request $request, string $tab, array $report): array
    {
        $siteName = (string) Setting::get('site_name', config('app.name'));
        $siteTagline = (string) Setting::get('site_tagline', '');

        [$from, $to] = $this->dateRange($request);
        $fromLabel = $from->format('M j, Y');
        $toLabel = $to->format('M j, Y');

        $applied = [];
        if ($courseId = $request->query('course')) {
            $course = Course::withTrashed()->find($courseId);
            if ($course) {
                $applied[] = 'Course: ' . $course->title;
            }
        }
        if (($status = $request->query('status')) && $status !== 'all') {
            $label = $this->filterOptions()['statusOptions'][$tab][$status] ?? ucfirst($status);
            $applied[] = 'Status: ' . $label;
        }
        if (($role = $request->query('role')) && $role !== 'all') {
            $applied[] = 'Role: ' . ucfirst($role);
        }
        if (empty($applied)) {
            $applied[] = 'All records';
        }

        return [
            'siteName' => $siteName,
            'siteTagline' => $siteTagline,
            'title' => $report['title'],
            'dateRange' => $fromLabel . ' – ' . $toLabel,
            'filters' => implode(' · ', $applied),
            'generatedAt' => now()->format('M j, Y g:i A'),
        ];
    }

    private function csvResponse(array $columns, \Illuminate\Support\Collection $rows, string $filename): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        return response()->streamDownload(function () use ($columns, $rows) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, array_column($columns, 'label'));

            foreach ($rows as $row) {
                fputcsv($handle, array_map(function ($col) use ($row) {
                    return $row[$col['key']] ?? '';
                }, $columns));
            }

            fclose($handle);
        }, $filename . '.csv', ['Content-Type' => 'text/csv']);
    }

    private function pdfResponse(
        array $report,
        array $columns,
        \Illuminate\Support\Collection $rows,
        string $filename,
        array $meta
    ): Response {
        $th = collect($columns)->map(fn ($c) => '<th>' . e($c['label']) . '</th>')->implode('');
        $tds = $rows->map(function ($row) use ($columns) {
            $cells = collect($columns)->map(fn ($c) => '<td>' . e((string) ($row[$c['key']] ?? '')) . '</td>');
            return '<tr>' . $cells->implode('') . '</tr>';
        })->implode('');

        $statsHtml = collect($report['stats'])->map(fn ($s) =>
            '<div class="stat"><span class="stat-value">' . e($s['value']) . '</span><span class="stat-label">' . e($s['label']) . '</span></div>'
        )->implode('');

        $html = <<<HTML
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="utf-8">
            <style>
                body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #111827; }
                .brand { font-size: 20px; font-weight: bold; color: #111827; letter-spacing: 0.02em; }
                .brand small { display: block; font-size: 9px; font-weight: normal; color: #6b7280; margin-top: 1px; }
                h1 { font-size: 16px; margin: 14px 0 2px; border-bottom: 2px solid #111827; padding-bottom: 8px; }
                .meta { color: #6b7280; font-size: 10px; line-height: 1.7; }
                .meta b { color: #374151; }
                .stats { display: flex; gap: 8px; margin: 14px 0; flex-wrap: wrap; }
                .stat { border: 1px solid #e5e7eb; border-radius: 6px; padding: 8px 12px; min-width: 120px; }
                .stat-value { font-size: 15px; font-weight: bold; color: #111827; }
                .stat-label { display: block; font-size: 9px; color: #6b7280; margin-top: 2px; }
                table { width: 100%; border-collapse: collapse; }
                th { background: #f3f4f6; text-align: left; padding: 6px 8px; font-size: 9px; text-transform: uppercase; letter-spacing: 0.04em; border: 1px solid #e5e7eb; }
                td { padding: 5px 8px; border: 1px solid #e5e7eb; }
                tr:nth-child(even) td { background: #fafafa; }
                .footer { margin-top: 16px; font-size: 9px; color: #9ca3af; text-align: center; }
            </style>
        </head>
        <body>
            <div class="brand">{$meta['siteName']}<small>{$meta['siteTagline']}</small></div>
            <h1>{$report['title']}</h1>
            <p class="meta">
                <b>Date Range:</b> {$meta['dateRange']} &nbsp;·&nbsp;
                <b>Filters:</b> {$meta['filters']} &nbsp;·&nbsp;
                <b>Generated:</b> {$meta['generatedAt']} &nbsp;·&nbsp;
                <b>Records:</b> {$rows->count()}
            </p>
            <div class="stats">{$statsHtml}</div>
            <table>
                <thead><tr>{$th}</tr></thead>
                <tbody>{$tds}</tbody>
            </table>
            <p class="footer">{$meta['siteName']} · {$report['title']} · Generated on {$meta['generatedAt']}</p>
        </body>
        </html>
        HTML;

        $dompdf = new \Dompdf\Dompdf();
        $dompdf->setPaper('a4', 'landscape');
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->render();

        return response()->streamDownload(fn () => print($dompdf->output()), $filename . '.pdf', [
            'Content-Type' => 'application/pdf',
        ]);
    }

    private function xlsxResponse(array $columns, \Illuminate\Support\Collection $rows, string $filename, array $meta): Response
    {
        return response()->streamDownload(function () use ($columns, $rows, $meta) {
            $headers = collect($columns)->map(fn ($c) => $c['label'])->values();
            $cells = function (array $values, bool $bold = false): string {
                $xml = '';
                foreach ($values as $value) {
                    $xml .= '<c' . ($bold ? ' s="1"' : '') . ' t="inlineStr"><is><t xml:space="preserve">'
                        . htmlspecialchars((string) $value, ENT_QUOTES | ENT_XML1, 'UTF-8')
                        . '</t></is></c>';
                }
                return $xml;
            };

            $row = 1;
            $sheetRows = '<row r="' . $row++ . '">' . $cells([$meta['siteName']], true) . '</row>';
            $sheetRows .= '<row r="' . $row++ . '">' . $cells(['Report: ' . $meta['title']]) . '</row>';
            $sheetRows .= '<row r="' . $row++ . '">' . $cells(['Date Range: ' . $meta['dateRange']]) . '</row>';
            $sheetRows .= '<row r="' . $row++ . '">' . $cells(['Filters: ' . $meta['filters'] . '   |   Generated: ' . $meta['generatedAt']]) . '</row>';
            $sheetRows .= '<row r="' . $row++ . '">' . $cells([$meta['siteName'] . ' — ' . $meta['title'] . ' · ' . $meta['dateRange']]) . '</row>';
            $row++; // blank spacer
            $sheetRows .= '<row r="' . $row++ . '">' . $cells($headers->all(), true) . '</row>';

            foreach ($rows as $dataRow) {
                $vals = collect($columns)->map(fn ($c) => $dataRow[$c['key']] ?? '');
                $sheetRows .= '<row r="' . $row++ . '">' . $cells($vals->all()) . '</row>';
            }

            $sheet = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
                . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
                . '<sheetData>' . $sheetRows . '</sheetData></worksheet>';

            $workbook = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
                . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" '
                . 'xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
                . '<sheets><sheet name="Report" sheetId="1" r:id="rId1"/></sheets></workbook>';

            $contentTypes = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
                . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
                . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
                . '<Default Extension="xml" ContentType="application/xml"/>'
                . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
                . '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
                . '</Types>';

            $rootRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
                . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
                . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
                . '</Relationships>';

            $workbookRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
                . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
                . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
                . '</Relationships>';

            $styles = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
                . '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
                . '<fonts count="2"><font><sz val="11"/><name val="Calibri"/></font><font><b/><sz val="11"/><name val="Calibri"/></font></fonts>'
                . '<fills count="2"><fill><patternFill patternType="none"/></fill><fill><patternFill patternType="gray125"/></fill></fills>'
                . '<borders count="1"><border><left/><right/><top/><bottom/><diagonal/></border></borders>'
                . '<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
                . '<cellXfs count="2"><xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>'
                . '<xf numFmtId="0" fontId="1" fillId="0" borderId="0" xfId="0" applyFont="1"/></cellXfs>'
                . '</styleSheet>';

            $tmp = tempnam(sys_get_temp_dir(), 'rpt');
            try {
                $zip = new \ZipArchive();
                $zip->open($tmp, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
                $zip->addFromString('[Content_Types].xml', $contentTypes);
                $zip->addFromString('_rels/.rels', $rootRels);
                $zip->addFromString('xl/workbook.xml', $workbook);
                $zip->addFromString('xl/_rels/workbook.xml.rels', $workbookRels);
                $zip->addFromString('xl/styles.xml', $styles);
                $zip->addFromString('xl/worksheets/sheet1.xml', $sheet);
                $zip->close();
                echo file_get_contents($tmp);
            } finally {
                @unlink($tmp);
            }
        }, $filename . '.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    private function buildReport(string $tab, Request $request): array
    {
        return match ($tab) {
            'payments' => $this->paymentsReport($request),
            'students' => $this->studentsReport($request),
            'courses' => $this->coursesReport($request),
            'quizzes' => $this->quizzesReport($request),
            'assignments' => $this->assignmentsReport($request),
            'certificates' => $this->certificatesReport($request),
            'live-classes' => $this->liveClassesReport($request),
            default => $this->enrollmentsReport($request),
        };
    }

    private function dateRange(Request $request): array
    {
        $to = $request->query('to') ? Carbon::parse($request->query('to'))->endOfDay() : now();
        $from = $request->query('from')
            ? Carbon::parse($request->query('from'))->startOfDay()
            : (clone $to)->subMonths(12)->startOfDay();

        return [$from, $to];
    }

    private function filters(Request $request): array
    {
        return [
            'from' => $request->query('from'),
            'to' => $request->query('to'),
            'course' => $request->query('course'),
            'status' => $request->query('status'),
            'role' => $request->query('role'),
            'tab' => $request->query('tab', 'enrollments'),
        ];
    }

    private function filterOptions(): array
    {
        return [
            'statusOptions' => [
                'enrollments' => ['all' => 'All statuses', 'active' => 'In progress', 'completed' => 'Completed'],
                'payments' => ['all' => 'All statuses', 'paid' => 'Paid', 'pending' => 'Pending', 'failed' => 'Failed'],
                'assignments' => ['all' => 'All statuses', 'submitted' => 'Submitted', 'graded' => 'Graded'],
                'live-classes' => ['all' => 'All classes', 'past' => 'Past', 'upcoming' => 'Upcoming'],
            ],
            'roleOptions' => ['all' => 'All roles', 'student' => 'Student', 'instructor' => 'Instructor', 'admin' => 'Admin'],
        ];
    }

    private function enrollmentsReport(Request $request): array
    {
        [$from, $to] = $this->dateRange($request);

        $query = Enrollment::with(['user', 'course.instructor'])
            ->whereBetween('enrollments.created_at', [$from, $to]);

        if ($courseId = $request->query('course')) {
            $query->where('course_id', $courseId);
        }

        if ($status = $request->query('status')) {
            if ($status === 'completed') {
                $query->whereNotNull('completed_at');
            } elseif ($status === 'active') {
                $query->whereNull('completed_at');
            }
        }

        $rows = $query->orderByDesc('created_at')->get()->map(fn (Enrollment $e) => [
            'student' => $e->user?->name ?? '—',
            'course' => $e->course?->title ?? '—',
            'instructor' => $e->course?->instructor?->name ?? '—',
            'progress' => number_format($e->progress) . '%',
            'status' => $e->completed_at ? 'Completed' : 'In progress',
            'enrolled' => $e->created_at?->format('M j, Y'),
            'completed' => $e->completed_at?->format('M j, Y') ?? '—',
        ]);

        $totalEnrollments = Enrollment::count();
        $completed = Enrollment::whereNotNull('completed_at')->count();

        $stats = [
            ['label' => 'Enrollments in range', 'value' => number_format($rows->count()), 'sub' => 'filtered period'],
            ['label' => 'Total enrollments', 'value' => number_format($totalEnrollments), 'sub' => 'all time'],
            ['label' => 'Completed', 'value' => number_format($completed), 'sub' => round($totalEnrollments > 0 ? $completed / $totalEnrollments * 100 : 0) . '% completion rate'],
            ['label' => 'Avg. progress', 'value' => number_format((float) Enrollment::avg('progress'), 1) . '%', 'sub' => 'across all enrollments'],
        ];

        return [
            'columns' => [
                ['key' => 'student', 'label' => 'Student'],
                ['key' => 'course', 'label' => 'Course'],
                ['key' => 'instructor', 'label' => 'Instructor'],
                ['key' => 'progress', 'label' => 'Progress'],
                ['key' => 'status', 'label' => 'Status'],
                ['key' => 'enrolled', 'label' => 'Enrolled'],
                ['key' => 'completed', 'label' => 'Completed'],
            ],
            'rows' => $rows,
            'stats' => $stats,
            'title' => 'Enrollment Report',
            'description' => 'Course sign-ups, progress and completion across every enrolled student.',
        ];
    }

    private function paymentsReport(Request $request): array
    {
        [$from, $to] = $this->dateRange($request);

        $query = Payment::with(['user', 'course', 'coupon'])
            ->whereBetween('payments.created_at', [$from, $to]);

        if ($courseId = $request->query('course')) {
            $query->where('course_id', $courseId);
        }

        if (($status = $request->query('status')) && $status !== 'all') {
            $query->where('status', $status);
        }

        $rows = $query->orderByDesc('created_at')->get()->map(fn (Payment $p) => [
            'receipt' => $p->receipt_no,
            'student' => $p->user?->name ?? '—',
            'course' => $p->course?->title ?? '—',
            'method' => $p->methodLabel(),
            'amount' => '$' . number_format((float) $p->amount, 2),
            'discount' => $p->discount_amount > 0 ? '$' . number_format((float) $p->discount_amount, 2) : '—',
            'net' => '$' . number_format((float) $p->final_amount, 2),
            'coupon' => $p->coupon?->code ?? '—',
            'status' => ucfirst($p->status),
            'date' => $p->paid_at?->format('M j, Y g:i A') ?? $p->created_at?->format('M j, Y g:i A'),
        ]);

        $paid = (clone $query)->where('status', 'paid');
        $revenue = (float) (clone $paid)->sum('final_amount');
        $transactions = (clone $paid)->count();
        $discounts = (float) (clone $paid)->sum('discount_amount');
        $avgOrder = $transactions > 0 ? $revenue / $transactions : 0;

        $stats = [
            ['label' => 'Revenue in range', 'value' => '$' . number_format($revenue, 2), 'sub' => number_format($transactions) . ' paid transactions'],
            ['label' => 'Avg. order value', 'value' => '$' . number_format($avgOrder, 2), 'sub' => 'paid transactions'],
            ['label' => 'Coupon savings', 'value' => '$' . number_format($discounts, 2), 'sub' => 'discounts applied'],
            ['label' => 'Total revenue', 'value' => '$' . number_format((float) Payment::where('status', 'paid')->sum('final_amount'), 2), 'sub' => 'all time'],
        ];

        return [
            'columns' => [
                ['key' => 'receipt', 'label' => 'Receipt'],
                ['key' => 'student', 'label' => 'Student'],
                ['key' => 'course', 'label' => 'Course'],
                ['key' => 'method', 'label' => 'Method'],
                ['key' => 'amount', 'label' => 'Amount'],
                ['key' => 'discount', 'label' => 'Discount'],
                ['key' => 'net', 'label' => 'Net Paid'],
                ['key' => 'coupon', 'label' => 'Coupon'],
                ['key' => 'status', 'label' => 'Status'],
                ['key' => 'date', 'label' => 'Date'],
            ],
            'rows' => $rows,
            'stats' => $stats,
            'title' => 'Payments & Revenue Report',
            'description' => 'Every transaction with coupon usage, discounts and payment status.',
        ];
    }

    private function studentsReport(Request $request): array
    {
        [$from, $to] = $this->dateRange($request);

        $query = User::with(['roles', 'enrollments', 'certificates'])
            ->whereBetween('users.created_at', [$from, $to]);

        if (($role = $request->query('role')) && $role !== 'all') {
            $query->whereHas('roles', fn ($q) => $q->where('name', $role));
        }

        $rows = $query->orderByDesc('created_at')->get()->map(fn (User $u) => [
            'name' => $u->name,
            'email' => $u->email,
            'role' => ucfirst($u->roles->first()?->name ?? 'User'),
            'xp' => number_format($u->xp),
            'enrollments' => number_format($u->enrollments->count()),
            'certificates' => number_format($u->certificates->count()),
            'joined' => $u->created_at?->format('M j, Y'),
            'status' => $u->email_verified_at ? 'Verified' : 'Unverified',
        ]);

        $students = User::role('student')->count();
        $stats = [
            ['label' => 'Accounts created', 'value' => number_format($rows->count()), 'sub' => 'in filtered period'],
            ['label' => 'Total students', 'value' => number_format($students), 'sub' => 'all time'],
            ['label' => 'Total users', 'value' => number_format(User::count()), 'sub' => 'including admins & instructors'],
            ['label' => 'Total XP earned', 'value' => number_format(User::sum('xp')), 'sub' => 'across all members'],
        ];

        return [
            'columns' => [
                ['key' => 'name', 'label' => 'Name'],
                ['key' => 'email', 'label' => 'Email'],
                ['key' => 'role', 'label' => 'Role'],
                ['key' => 'xp', 'label' => 'XP'],
                ['key' => 'enrollments', 'label' => 'Enrollments'],
                ['key' => 'certificates', 'label' => 'Certificates'],
                ['key' => 'joined', 'label' => 'Joined'],
                ['key' => 'status', 'label' => 'Status'],
            ],
            'rows' => $rows,
            'stats' => $stats,
            'title' => 'Students & Members Report',
            'description' => 'Member accounts, roles, XP and learning outcomes.',
        ];
    }

    private function coursesReport(Request $request): array
    {
        [$from, $to] = $this->dateRange($request);

        $revenueByCourse = Payment::where('status', 'paid')
            ->selectRaw('course_id, SUM(final_amount) as revenue')
            ->groupBy('course_id')
            ->pluck('revenue', 'course_id');

        $rows = Course::withTrashed()
            ->with(['instructor', 'categoryTerm', 'levelTerm'])
            ->withCount('enrollments')
            ->withCount(['enrollments as completed_count' => fn ($q) => $q->whereNotNull('completed_at')])
            ->withAvg('reviews as course_avg_rating', 'rating')
            ->whereBetween('courses.created_at', [$from, $to])
            ->orderByDesc('enrollments_count')
            ->get()
            ->map(function (Course $c) use ($revenueByCourse) {
                $completed = (int) $c->completed_count;
                $avgRating = (float) $c->course_avg_rating;
                $revenue = (float) ($revenueByCourse[$c->id] ?? 0);

                return [
                    'course' => $c->title,
                    'instructor' => $c->instructor?->name ?? '—',
                    'category' => $c->category ?? '—',
                    'level' => $c->level ?? '—',
                    'price' => '$' . number_format((float) $c->price, 2),
                    'enrollments' => number_format((int) $c->enrollments_count),
                    'completed' => number_format($completed),
                    'rating' => $avgRating > 0 ? number_format($avgRating, 1) . '★' : '—',
                    'revenue' => '$' . number_format($revenue, 2),
                ];
            });

        $all = Course::withTrashed();
        $totalCourses = (clone $all)->count();

        $stats = [
            ['label' => 'Courses created', 'value' => number_format($rows->count()), 'sub' => 'in filtered period'],
            ['label' => 'Total courses', 'value' => number_format($totalCourses), 'sub' => 'including trashed'],
            ['label' => 'Total enrollments', 'value' => number_format(Enrollment::count()), 'sub' => 'all time'],
            ['label' => 'Avg. course rating', 'value' => number_format((float) Review::avg('rating'), 1) . '★', 'sub' => 'from ' . number_format(Review::count()) . ' reviews'],
        ];

        return [
            'columns' => [
                ['key' => 'course', 'label' => 'Course'],
                ['key' => 'instructor', 'label' => 'Instructor'],
                ['key' => 'category', 'label' => 'Category'],
                ['key' => 'level', 'label' => 'Level'],
                ['key' => 'price', 'label' => 'Price'],
                ['key' => 'enrollments', 'label' => 'Enrollments'],
                ['key' => 'completed', 'label' => 'Completed'],
                ['key' => 'rating', 'label' => 'Rating'],
                ['key' => 'revenue', 'label' => 'Revenue'],
            ],
            'rows' => $rows,
            'stats' => $stats,
            'title' => 'Course Performance Report',
            'description' => 'How every course is performing — enrollments, completions, ratings and estimated revenue.',
        ];
    }

    private function quizzesReport(Request $request): array
    {
        [$from, $to] = $this->dateRange($request);

        $query = QuizAttempt::with(['user', 'quiz.course'])
            ->whereNotNull('completed_at')
            ->whereBetween('quiz_attempts.completed_at', [$from, $to]);

        if ($courseId = $request->query('course')) {
            $query->whereHas('quiz', fn ($q) => $q->where('course_id', $courseId));
        }

        $rows = $query->orderByDesc('completed_at')->get()->map(fn (QuizAttempt $a) => [
            'quiz' => $a->quiz?->title ?? '—',
            'course' => $a->quiz?->course?->title ?? '—',
            'student' => $a->user?->name ?? '—',
            'score' => number_format((float) $a->score, 1) . '%',
            'result' => $a->passed ? 'Pass' : 'Fail',
            'attempts' => number_format($a->quiz?->attempts()->where('user_id', $a->user_id)->count() ?? 0),
            'completed' => $a->completed_at?->format('M j, Y g:i A'),
        ]);

        $attempts = QuizAttempt::whereNotNull('completed_at');
        $total = (clone $attempts)->count();
        $passed = (clone $attempts)->where('passed', true)->count();
        $avg = (float) (clone $attempts)->avg('score');
        $inRange = (clone $attempts)->whereBetween('completed_at', [$from, $to])->count();

        $stats = [
            ['label' => 'Attempts in range', 'value' => number_format($inRange), 'sub' => 'filtered period'],
            ['label' => 'Total attempts', 'value' => number_format($total), 'sub' => 'all time'],
            ['label' => 'Pass rate', 'value' => round($total > 0 ? $passed / $total * 100 : 0) . '%', 'sub' => number_format($passed) . ' of ' . number_format($total) . ' passed'],
            ['label' => 'Average score', 'value' => number_format($avg, 1) . '%', 'sub' => 'across all attempts'],
        ];

        return [
            'columns' => [
                ['key' => 'quiz', 'label' => 'Quiz'],
                ['key' => 'course', 'label' => 'Course'],
                ['key' => 'student', 'label' => 'Student'],
                ['key' => 'score', 'label' => 'Score'],
                ['key' => 'result', 'label' => 'Result'],
                ['key' => 'attempts', 'label' => 'Total Attempts'],
                ['key' => 'completed', 'label' => 'Completed'],
            ],
            'rows' => $rows,
            'stats' => $stats,
            'title' => 'Quiz Results Report',
            'description' => 'Every completed quiz attempt with scores, pass/fail and attempt counts.',
        ];
    }

    private function assignmentsReport(Request $request): array
    {
        [$from, $to] = $this->dateRange($request);

        $query = AssignmentSubmission::with(['user', 'quiz.course'])
            ->whereBetween('assignment_submissions.submitted_at', [$from, $to]);

        if ($courseId = $request->query('course')) {
            $query->whereHas('quiz', fn ($q) => $q->where('course_id', $courseId));
        }

        if (($status = $request->query('status')) && $status !== 'all') {
            $query->where('status', $status);
        }

        $rows = $query->orderByDesc('submitted_at')->get()->map(fn (AssignmentSubmission $s) => [
            'assignment' => $s->quiz?->title ?? '—',
            'course' => $s->quiz?->course?->title ?? '—',
            'student' => $s->user?->name ?? '—',
            'submitted' => $s->submitted_at?->format('M j, Y g:i A'),
            'due' => $s->quiz?->due_date?->format('M j, Y') ?? '—',
            'late' => $s->isLate() ? 'Late' : 'On time',
            'status' => $s->isGraded() ? 'Graded' : 'Submitted',
            'marks' => $s->marks !== null ? number_format((float) $s->marks) . '%' : '—',
            'feedback' => $s->feedback ?: '—',
        ]);

        $total = AssignmentSubmission::count();
        $graded = AssignmentSubmission::whereNotNull('marks')->count();
        $submittedInRange = AssignmentSubmission::whereBetween('submitted_at', [$from, $to])->count();

        $stats = [
            ['label' => 'Submissions in range', 'value' => number_format($submittedInRange), 'sub' => 'filtered period'],
            ['label' => 'Total submissions', 'value' => number_format($total), 'sub' => 'all time'],
            ['label' => 'Graded', 'value' => number_format($graded), 'sub' => round($total > 0 ? $graded / $total * 100 : 0) . '% of all submissions'],
            ['label' => 'Pending grading', 'value' => number_format($total - $graded), 'sub' => 'awaiting review'],
        ];

        return [
            'columns' => [
                ['key' => 'assignment', 'label' => 'Assignment'],
                ['key' => 'course', 'label' => 'Course'],
                ['key' => 'student', 'label' => 'Student'],
                ['key' => 'submitted', 'label' => 'Submitted'],
                ['key' => 'due', 'label' => 'Due Date'],
                ['key' => 'late', 'label' => 'Submission'],
                ['key' => 'status', 'label' => 'Status'],
                ['key' => 'marks', 'label' => 'Marks'],
                ['key' => 'feedback', 'label' => 'Feedback'],
            ],
            'rows' => $rows,
            'stats' => $stats,
            'title' => 'Assignment Submission Report',
            'description' => 'Assignment submissions with timeliness, grading status and feedback.',
        ];
    }

    private function certificatesReport(Request $request): array
    {
        [$from, $to] = $this->dateRange($request);

        $query = Certificate::with(['user', 'course'])
            ->whereBetween('certificates.issued_at', [$from, $to]);

        if ($courseId = $request->query('course')) {
            $query->where('course_id', $courseId);
        }

        $rows = $query->orderByDesc('issued_at')->get()->map(fn (Certificate $c) => [
            'code' => $c->code,
            'student' => $c->user?->name ?? '—',
            'course' => $c->course?->title ?? '—',
            'issued' => $c->issued_at?->format('M j, Y'),
            'link' => $c->verificationUrl(),
        ]);

        $total = Certificate::count();
        $inRange = Certificate::whereBetween('issued_at', [$from, $to])->count();

        $stats = [
            ['label' => 'Issued in range', 'value' => number_format($inRange), 'sub' => 'filtered period'],
            ['label' => 'Total issued', 'value' => number_format($total), 'sub' => 'all time'],
            ['label' => 'Students certified', 'value' => number_format(Certificate::distinct('user_id')->count()), 'sub' => 'unique recipients'],
            ['label' => 'Courses completed', 'value' => number_format(Enrollment::whereNotNull('completed_at')->count()), 'sub' => 'completed enrollments'],
        ];

        return [
            'columns' => [
                ['key' => 'code', 'label' => 'Code'],
                ['key' => 'student', 'label' => 'Student'],
                ['key' => 'course', 'label' => 'Course'],
                ['key' => 'issued', 'label' => 'Issued'],
                ['key' => 'link', 'label' => 'Verification URL'],
            ],
            'rows' => $rows,
            'stats' => $stats,
            'title' => 'Certificates Report',
            'description' => 'Every certificate issued with its public verification URL.',
        ];
    }

    private function liveClassesReport(Request $request): array
    {
        [$from, $to] = $this->dateRange($request);

        $query = LiveClass::with(['course', 'attendances'])
            ->whereBetween('live_classes.scheduled_at', [$from, $to]);

        if ($courseId = $request->query('course')) {
            $query->where('course_id', $courseId);
        }

        if (($status = $request->query('status')) && $status !== 'all') {
            $query->where($status === 'past' ? 'scheduled_at' : 'scheduled_at', $status === 'past' ? '<' : '>=', now());
        }

        $rows = $query->orderByDesc('scheduled_at')->get()->map(fn (LiveClass $lc) => [
            'class' => $lc->title,
            'course' => $lc->course?->title ?? '—',
            'scheduled' => $lc->scheduled_at?->format('M j, Y g:i A'),
            'duration' => $lc->duration_minutes . ' min',
            'attendance' => number_format($lc->attendances->count()),
            'status' => $lc->isUpcoming() ? 'Upcoming' : 'Past',
        ]);

        $total = LiveClass::count();
        $upcoming = LiveClass::where('scheduled_at', '>=', now())->count();
        $attendedSessions = LiveClass::withCount('attendances')->get()->sum('attendances_count');

        $stats = [
            ['label' => 'Classes in range', 'value' => number_format($rows->count()), 'sub' => 'filtered period'],
            ['label' => 'Total classes', 'value' => number_format($total), 'sub' => number_format($upcoming) . ' upcoming'],
            ['label' => 'Attendance sessions', 'value' => number_format($attendedSessions), 'sub' => 'student joins recorded'],
            ['label' => 'Avg. attendance', 'value' => number_format($total > 0 ? $attendedSessions / $total : 0, 1), 'sub' => 'students per class'],
        ];

        return [
            'columns' => [
                ['key' => 'class', 'label' => 'Live Class'],
                ['key' => 'course', 'label' => 'Course'],
                ['key' => 'scheduled', 'label' => 'Scheduled'],
                ['key' => 'duration', 'label' => 'Duration'],
                ['key' => 'attendance', 'label' => 'Attendance'],
                ['key' => 'status', 'label' => 'Status'],
            ],
            'rows' => $rows,
            'stats' => $stats,
            'title' => 'Live Classes Report',
            'description' => 'Scheduled live sessions and student attendance.',
        ];
    }
}