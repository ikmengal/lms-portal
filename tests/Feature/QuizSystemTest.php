<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuizSystemTest extends TestCase
{
    use RefreshDatabase;

    private User $student;
    private Course $course;
    private Quiz $quiz;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (['admin', 'instructor', 'student'] as $role) {
            \Spatie\Permission\Models\Role::findOrCreate($role, 'web');
        }

        $this->student = User::factory()->create();
        $this->student->assignRole('student');

        $instructor = User::factory()->create();
        $instructor->assignRole('admin');

        $this->course = Course::create([
            'title' => 'Test Course',
            'slug' => 'test-course-' . uniqid(),
            'instructor_id' => $instructor->id,
        ]);

        Enrollment::create([
            'user_id' => $this->student->id,
            'course_id' => $this->course->id,
            'status' => 'active',
        ]);

        $this->quiz = $this->course->quizzes()->create([
            'title' => 'Feature Test Quiz',
            'type' => 'quiz',
            'passing_score' => 50,
            'duration_minutes' => 10,
            'max_attempts' => 2,
            'shuffle_questions' => false,
            'shuffle_options' => false,
            'is_active' => true,
        ]);

        // MCQ (1pt) — option A correct
        $mcq = $this->quiz->questions()->create(['question' => '2+2?', 'type' => 'multiple_choice', 'points' => 1, 'sort_order' => 0]);
        $a = $mcq->options()->create(['option_text' => '4', 'is_correct' => true]);
        $mcq->options()->create(['option_text' => '5', 'is_correct' => false]);

        // True/False (1pt) — False correct
        $tf = $this->quiz->questions()->create(['question' => 'Sky is green.', 'type' => 'true_false', 'points' => 1, 'sort_order' => 1]);
        $tf->options()->create(['option_text' => 'True', 'is_correct' => false]);
        $f = $tf->options()->create(['option_text' => 'False', 'is_correct' => true]);

        // Multiple answers (2pts) — 1 & 2 correct
        $ma = $this->quiz->questions()->create(['question' => 'Primes?', 'type' => 'multiple_answers', 'points' => 2, 'sort_order' 	=> 2]);
        $p1 = $ma->options()->create(['option_text' => '2', 'is_correct' => true]);
        $p2 = $ma->options()->create(['option_text' => '3', 'is_correct' => true]);
        $ma->options()->create(['option_text' => '4', 'is_correct' => false]);

        $this->correctAnswers = [$mcq->id => [(string) $a->id], $tf->id => [(string) $f->id], $ma->id => [(string) $p1->id, (string) $p2->id]];
    }

    public function test_intro_screen_shows_before_start(): void
    {
        $res = $this->actingAs($this->student)->get(route('courses.tests.show', [$this->course, $this->quiz]));
        $res->assertOk()->assertSee('Start Test')->assertDontSee('Submit Test');
    }

    public function test_starting_creates_attempt_with_timer(): void
    {
        $res = $this->actingAs($this->student)->get(route('courses.tests.show', [$this->course, $this->quiz]) . '?start=1');
        $res->assertOk()->assertSee('Submit Test')->assertSee('Time Remaining');

        $this->assertDatabaseHas('quiz_attempts', ['user_id' => $this->student->id, 'quiz_id' => $this->quiz->id]);
    }

    public function test_submit_grades_all_question_types_and_passes(): void
    {
        $answers = array_map(fn ($v) => array_map('intval', $v), $this->correctAnswers);

        $res = $this->actingAs($this->student)->post(route('courses.tests.submit', [$this->course, $this->quiz]), [
            'answers' => $answers,
        ]);

        $res->assertRedirect();
        $attempt = QuizAttempt::where('user_id', $this->student->id)->whereNotNull('completed_at')->first();
        $this->assertNotNull($attempt);
        $this->assertEquals(100.0, (float) $attempt->score);
        $this->assertTrue($attempt->passed);
        $this->assertNotNull($attempt->completed_at);
    }

    public function test_partial_multiple_answers_is_wrong(): void
    {
        $answers = array_map(fn ($v) => array_map('intval', $v), $this->correctAnswers);
        // Remove one of the two primes → multi-answer wrong → 50% exactly
        $maQuestion = $this->quiz->questions()->where('type', 'multiple_answers')->first();
        $answers[$maQuestion->id] = [intval(array_values($answers[$maQuestion->id])[0])];

        $res = $this->actingAs($this->student)->post(route('courses.tests.submit', [$this->course, $this->quiz]), [
            'answers' => $answers,
        ]);

        $attempt = QuizAttempt::whereNotNull('completed_at')->first();
        // MCQ(1pt) + TF(1pt) correct; selecting only ONE of two primes earns 0 of 2pts.
        // If partial selection counted as correct the score would be 100% — it must be exactly 50%.
        $this->assertEquals(50.0, (float) $attempt->score);
        $this->assertTrue($attempt->passed); // 50 >= passing_score of 50
    }

    public function test_attempts_limit_is_enforced(): void
    {
        foreach (range(1, 2) as $i) {
            $this->actingAs($this->student)
                ->post(route('courses.tests.submit', [$this->course, $this->quiz]), ['answers' => []])
                ->assertRedirect();
        }

        // Third attempt must be blocked
        $res = $this->actingAs($this->student)->post(route('courses.tests.submit', [$this->course, $this->quiz]), ['answers' => []]);
        $res->assertRedirect();
        $res->assertSessionHas('error');

        $this->assertEquals(0, $this->quiz->attemptsLeftFor($this->student->id));
    }

    public function test_expired_hard_deadline_discards_attempt(): void
    {
        // Create an attempt started way in the past
        $attempt = QuizAttempt::create([
            'user_id' => $this->student->id,
            'quiz_id' => $this->quiz->id,
            'started_at' => now()->subMinutes(30),
            'question_ids' => $this->quiz->questions->pluck('id')->all(),
            'answers' => [],
        ]);

        $res = $this->actingAs($this->student)->post(route('courses.tests.submit', [$this->course, $this->quiz]), ['answers' => []]);

        $res->assertRedirect();
        $res->assertSessionHas('error');
        $this->assertSoftDeleted('quiz_attempts', ['id' => $attempt->id]); // discarded
    }

    public function test_quiz_history_lists_attempts(): void
    {
        QuizAttempt::create([
            'user_id' => $this->student->id,
            'quiz_id' => $this->quiz->id,
            'answers' => [],
            'score' => 75,
            'passed' => true,
            'started_at' => now()->subMinutes(5),
            'time_spent_seconds' => 95,
            'completed_at' => now(),
        ]);

        $res = $this->actingAs($this->student)->get(route('quiz.history'));
        $res->assertOk()
            ->assertSee('My Quiz History')
            ->assertSee('75%')
            ->assertSee('Passed');
    }

    public function test_shuffle_produces_stored_order_for_attempt(): void
    {
        $this->quiz->update(['shuffle_questions' => true]);

        $this->actingAs($this->student)->get(route('courses.tests.show', [$this->course, $this->quiz]) . '?start=1');

        $attempt = QuizAttempt::whereNull('completed_at')->latest()->first();
        $this->assertCount(3, $attempt->question_ids);
        $this->assertEqualsCanonicalizing($this->quiz->questions->pluck('id')->all(), $attempt->question_ids);
    }
}
