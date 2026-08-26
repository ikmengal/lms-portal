<?php

namespace Tests\Feature;

use App\Models\{Certificate, Course, CourseModule, Enrollment, Lesson, LiveClass, QuizAttempt, User};
use App\Services\Notifier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schedule;
use Tests\TestCase;

class NotificationsSystemTest extends TestCase
{
    use RefreshDatabase;

    private User $student;
    private User $instructor;
    private Course $course;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (['admin', 'student', 'instructor'] as $role) {
            \Spatie\Permission\Models\Role::findOrCreate($role, 'web');
        }

        $this->student = User::factory()->create();
        $this->student->assignRole('student');

        $this->instructor = User::factory()->create();
        $this->instructor->assignRole('instructor');

        $this->course = Course::create([
            'title' => 'Notification Test Course',
            'slug' => 'notif-test-' . uniqid(),
            'instructor_id' => $this->instructor->id,
        ]);
    }

    private function enrollStudent(): void
    {
        Enrollment::create(['user_id' => $this->student->id, 'course_id' => $this->course->id, 'status' => 'active']);
    }

    public function test_free_enrollment_notifies_student_and_instructor(): void
    {
        $this->actingAs($this->student)
            ->post(route('courses.enroll.free', $this->course))
            ->assertRedirect();

        // Student: welcome
        $welcome = $this->student->notifications()->latest()->first();
        $this->assertEquals('welcome', $welcome->data['type']);

        // Instructor: enrollment alert
        $enrollmentNote = $this->instructor->notifications()->latest()->first();
        $this->assertEquals('enrollment', $enrollmentNote->data['type']);
        $this->assertStringContainsString($this->student->name, $enrollmentNote->data['body']);
    }

    public function test_re_enrollment_does_not_duplicate_notifications(): void
    {
        $this->actingAs($this->student)->post(route('courses.enroll.free', $this->course));
        $count = $this->student->notifications()->count();
        $this->actingAs($this->student)->post(route('courses.enroll.free', $this->course));

        $this->assertEquals($count, $this->student->notifications()->count());
    }

    public function test_new_lesson_notifies_enrolled_students(): void
    {
        $module = CourseModule::create(['course_id' => $this->course->id, 'title' => 'M1']);
        $this->enrollStudent();

        $this->actingAs($this->instructor)
            ->post(route('admin.lessons.store', $module), [
                'title' => 'Brand New Lesson',
                'duration_minutes' => 10,
            ])
            ->assertRedirect();

        $note = $this->student->notifications()->where('type', \App\Notifications\InAppNotification::class)->latest()->first();
        $this->assertNotNull($note);
        $data = $note->data;
        $this->assertEquals('lesson', $data['type']);
        $this->assertStringContainsString('Brand New Lesson', $data['title'] . $data['body']);
    }

    private function buildQuiz(string $type = 'quiz'): \App\Models\Quiz
    {
        $quiz = $this->course->quizzes()->create([
            'title' => ($type === 'assignment' ? 'Assignment 1' : 'Quiz 1'),
            'type' => $type,
            'passing_score' => 60,
            'is_active' => true,
        ]);

        $q = $quiz->questions()->create(['question' => '2+2?', 'type' => 'multiple_choice', 'points' => 1]);
        $a = $q->options()->create(['option_text' => '4', 'is_correct' => true]);
        $q->options()->create(['option_text' => '5', 'is_correct' => false]);

        return $quiz;
    }

    public function test_quiz_submission_sends_result_notification_to_student(): void
    {
        $this->enrollStudent();
        $quiz = $this->buildQuiz();
        $correctId = $quiz->questions->first()->options->where('is_correct', true)->first()->id;

        $this->actingAs($this->student)
            ->post(route('courses.tests.submit', [$this->course, $quiz]), ['answers' => [$quiz->questions->first()->id => [$correctId]]])
            ->assertRedirect();

        $note = $this->student->notifications()->latest()->first();
        $this->assertEquals('quiz_result', $note->data['type']);
        $this->assertStringContainsString('Passed', $note->data['title']);
        $this->assertStringContainsString('100.0%', $note->data['body']);
    }

    public function test_assignment_submission_alerts_instructor(): void
    {
        $this->enrollStudent();
        $quiz = $this->buildQuiz('assignment');
        $correctId = $quiz->questions->first()->options->where('is_correct', true)->first()->id;

        $this->actingAs($this->student)
            ->post(route('courses.tests.submit', [$this->course, $quiz]), ['answers' => [$quiz->questions->first()->id => [$correctId]]])
            ->assertRedirect();

        // Student gets assignment result…
        $studentTypes = $this->student->notifications()->pluck('data')->map(fn ($d) => $d['type']);
        $this->assertTrue($studentTypes->contains('assignment_result'));

        // …and instructor gets the submission for feedback/review.
        $instructorNote = $this->instructor->notifications()->latest()->first();
        $this->assertEquals('assignment_submitted', $instructorNote->data['type']);
        $this->assertStringContainsString($this->student->name, $instructorNote->data['title']);
    }

    public function test_certificate_issuance_notifies_student(): void
    {
        $cert = Certificate::create([
            'code' => Certificate::generateCode(),
            'user_id' => $this->student->id,
            'course_id' => $this->course->id,
            'issued_at' => now(),
        ]);

        Notifier::certificateIssued($cert);

        $note = $this->student->notifications()->latest()->first();
        $this->assertEquals('certificate', $note->data['type']);
        $this->assertStringContainsString($cert->code, $note->data['body']);
    }

    public function test_live_class_schedule_and_reminders(): void
    {
        $this->enrollStudent();

        // Scheduled ~24h out: store() announces immediately; command sends "tomorrow" reminder in window
        $class = $this->course->liveClasses()->create([
            'title' => 'Live Q&A Session',
            'join_url' => 'https://zoom.us/j/test123',
            'scheduled_at' => now()->addHours(24),
            'duration_minutes' => 45,
        ]);

        Notifier::liveClassScheduled($class);
        $announce = $this->student->notifications()->latest()->first();
        $this->assertEquals('live_class', $announce->data['type']);
        $this->assertStringContainsString('Live Q&A Session', $announce->data['title']);

        // Reminder command inside the 24h window
        $this->artisan('lms:live-class-reminders')->assertSuccessful();
        $reminder = $this->student->notifications()
            ->where('data->type', 'live_class_reminder')->latest()->first();
        $this->assertNotNull($reminder);
        $this->assertEquals(now()->startOfDay()->format('Y-m-d'), $class->refresh()->reminder_24h_sent_at->startOfDay()->format('Y-m-d'));

        // No duplicate on second run (guard column set)
        $before = $this->student->notifications()->where('data->type', 'live_class_reminder')->count();
        $this->artisan('lms:live-class-reminders');
        $this->assertEquals($before, $this->student->notifications()->where('data->type', 'live_class_reminder')->count());

        // 15-minute window reminder
        $class2 = $this->course->liveClasses()->create([
            'title' => 'Starting Soon Class',
            'join_url' => 'https://zoom.us/j/soon',
            'scheduled_at' => now()->addMinutes(15),
        ]);
        $this->artisan('lms:live-class-reminders')->assertSuccessful();
        $soon = $this->student->notifications()->get()
            ->first(fn ($n) => str_contains($n->data['title'] ?? '', 'Starting Soon Class'));
        $this->assertNotNull($soon, '15-minute reminder notification missing');
        $this->assertStringContainsString('Starting Soon Class', $soon->data['title']);
    }

    public function test_bell_dropdown_renders_typed_notification(): void
    {
        $cert = Certificate::create([
            'code' => Certificate::generateCode(),
            'user_id' => $this->student->id,
            'course_id' => $this->course->id,
            'issued_at' => now(),
        ]);
        Notifier::certificateIssued($cert);

        // Student dashboard shows bell with the notification
        $res = $this->actingAs($this->student)->get(route('dashboard'));
        $res->assertOk()->assertSee('Certificate issued!');
    }

    public function test_mark_all_read_clears_unread_badge(): void
    {
        $this->actingAs($this->student)->post(route('courses.enroll.free', $this->course));
        $this->assertGreaterThan(0, $this->student->unreadNotifications()->count());

        $this->actingAs($this->student)->post(route('notifications.readAll'))->assertRedirect();

        $this->assertEquals(0, $this->student->unreadNotifications()->count());
    }
}
