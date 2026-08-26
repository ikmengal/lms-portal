<?php

namespace Tests\Feature;

use App\Models\{Certificate, Course, Enrollment, Payment, QuizAttempt, User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnalyticsDashboardTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $student;
    private User $instructor;
    private Course $course;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (['admin', 'student', 'instructor'] as $role) {
            \Spatie\Permission\Models\Role::findOrCreate($role, 'web');
        }

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->student = User::factory()->create();
        $this->student->assignRole('student');

        $this->instructor = User::factory()->create(['name' => 'Earny Instructor']);
        $this->instructor->assignRole('instructor');

        $this->course = Course::create([
            'title' => 'Analytics Course',
            'slug' => 'analytics-' . uniqid(),
            'price' => 49.99,
            'instructor_id' => $this->instructor->id,
        ]);

        Enrollment::create(['user_id' => $this->student->id, 'course_id' => $this->course->id, 'status' => 'active']);

        Payment::create([
            'receipt_no' => 'RCP-' . uniqid(),
            'user_id' => $this->student->id,
            'course_id' => $this->course->id,
            'amount' => 49.99,
            'method' => 'stripe',
            'transaction_ref' => 'TXN-' . uniqid(),
            'status' => 'paid',
            'paid_at' => now(),
        ]);
    }

    public function test_admin_can_view_analytics_dashboard_with_all_metrics(): void
    {
        $res = $this->actingAs($this->admin)->get(route('admin.analytics'));

        $res->assertOk()
            ->assertSee('Analytics Dashboard')
            ->assertSee('Total Students')
            ->assertSee('Active Students')
            ->assertSee('Course Enrollments')
            ->assertSee('Completion Rate')
            ->assertSee('Total Revenue')
            ->assertSee('$49.99')                    // revenue reflects payment
            ->assertSee('Most Popular Courses')
            ->assertSee('Analytics Course')
            ->assertSee('Top Instructor Earnings')
            ->assertSee('Earny Instructor')
            ->assertSee('Quiz Performance')
            ->assertSee('Student Engagement');
    }

    public function test_non_admin_cannot_view_analytics(): void
    {
        $this->actingAs($this->student)->get(route('admin.analytics'))->assertForbidden();
    }

    public function test_metrics_reflect_data_changes(): void
    {
        // Complete the enrollment → completion rate becomes 100%
        Enrollment::where('user_id', $this->student->id)->update(['progress' => 100, 'completed_at' => now()]);

        // Add a quiz attempt
        $quiz = $this->course->quizzes()->create(['title' => 'A1 Quiz', 'type' => 'quiz', 'passing_score' => 50, 'is_active' => true]);
        $q = $quiz->questions()->create(['question' => 'Q?', 'type' => 'true_false', 'points' => 1]);
        $q->options()->create(['option_text' => 'True', 'is_correct' => false]);
        $f = $q->options()->create(['option_text' => 'False', 'is_correct' => true]);
        QuizAttempt::create([
            'user_id' => $this->student->id,
            'quiz_id' => $quiz->id,
            'answers' => [],
            'score' => 100,
            'passed' => true,
            'started_at' => now(),
            'time_spent_seconds' => 30,
            'completed_at' => now(),
        ]);

        Certificate::create([
            'code' => \App\Models\Certificate::generateCode(),
            'user_id' => $this->student->id,
            'course_id' => $this->course->id,
            'issued_at' => now(),
        ]);

        $res = $this->actingAs($this->admin)->get(route('admin.analytics'));
        $res->assertOk()
            ->assertSee('A1 Quiz')          // quiz appears in best-performing list
            ->assertSee('100%')             // completion rate / pass rate / avg score
            ->assertSee('$49.99');          // revenue unchanged
    }

    public function test_analytics_link_visible_on_admin_dashboard(): void
    {
        $res = $this->actingAs($this->admin)->get(route('dashboard'));
        $res->assertOk()->assertSee('View Analytics')->assertSee(route('admin.analytics'));
    }
}
