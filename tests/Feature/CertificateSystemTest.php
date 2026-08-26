<?php

namespace Tests\Feature;

use App\Models\{Certificate, Course, CourseModule, Enrollment, Lesson, User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CertificateSystemTest extends TestCase
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
            'title' => 'Certificate Test Course',
            'slug' => 'cert-test-' . uniqid(),
            'duration_hours' => 5,
            'instructor_id' => $this->instructor->id,
        ]);

        // Two lessons across one module
        $module = CourseModule::create(['course_id' => $this->course->id, 'title' => 'M1', 'sort_order' => 0]);
        foreach ([1, 2] as $i) {
            Lesson::create([
                'course_module_id' => $module->id,
                'title' => "Lesson {$i}",
                'sort_order' => $i,
            ]);
        }

        Enrollment::create([
            'user_id' => $this->student->id,
            'course_id' => $this->course->id,
            'status' => 'active',
        ]);
    }

    public function test_generated_codes_match_documented_format_and_are_unique(): void
    {
        $codes = collect(range(1, 20))->map(fn () => Certificate::generateCode());

        $codes->each(
            fn ($c) => $this->assertMatchesRegularExpression('/^LMS-[23456789ABCDEFGHJKMNPQRSTUVWXYZ]{10}$/', $c)
        );
        $this->assertCount(20, $codes->unique());
    }

    private function completeAllLessons(): void
    {
        foreach ($this->course->modules()->with('lessons')->get()->flatMap->lessons as $lesson) {
            $this->actingAs($this->student)
                ->post(route('learn.complete', [$this->course, $lesson]))
                ->assertRedirect();
        }
    }

    public function test_certificate_auto_issued_when_course_reaches_100_percent(): void
    {
        $this->completeAllLessons();

        $enrollment = Enrollment::where('user_id', $this->student->id)->where('course_id', $this->course->id)->first();
        $this->assertEquals(100, $enrollment->progress);
        $this->assertNotNull($enrollment->completed_at);

        $this->assertDatabaseHas('certificates', [
            'user_id' => $this->student->id,
            'course_id' => $this->course->id,
        ]);

        $cert = Certificate::where('user_id', $this->student->id)->first();
        $this->assertStringStartsWith('LMS-', $cert->code);
        $this->assertNotNull($cert->issued_at);
    }

    public function test_only_one_certificate_per_course_even_after_undo_redo(): void
    {
        $this->completeAllLessons();

        // Undo one lesson then re-complete it
        $lesson = $this->course->modules()->with('lessons')->first()->lessons->first();
        $this->actingAs($this->student)->post(route('learn.complete', [$this->course, $lesson]), ['undo' => '1']);
        $this->actingAs($this->student)->post(route('learn.complete', [$this->course, $lesson]));

        $this->assertEquals(1, Certificate::where('user_id', $this->student->id)->where('course_id', $this->course->id)->count());
    }

    public function test_public_verification_page_shows_all_details_and_qr(): void
    {
        $this->completeAllLessons();

        $cert = Certificate::where('user_id', $this->student->id)->first();

        $res = $this->get(route('certificates.verify', $cert->code));

        $res->assertOk()
            ->assertSee('Certificate Verified')
            ->assertSee($cert->user->name)                       // student name
            ->assertSee($cert->course->title)                    // course
            ->assertSee($cert->course->instructor->name)         // instructor
            ->assertSee($cert->code)                             // unique number
            ->assertSee($cert->issued_at->format('M d, Y'))      // issue date
            ->assertSee('<svg', false);                          // QR code present
    }

    public function test_verification_fails_for_unknown_code(): void
    {
        $res = $this->get(route('certificates.verify', 'LMS-NOPE00000'));

        $res->assertOk()
            ->assertSee('Certificate Not Found')
            ->assertDontSee('Certificate Verified');
    }

    public function test_pdf_download_includes_certificate_data(): void
    {
        $this->completeAllLessons();
        $cert = Certificate::where('user_id', $this->student->id)->first();

        $res = $this->actingAs($this->student)->get(route('certificates.download', $cert));

        $res->assertOk()->assertHeader('Content-Type', 'application/pdf');
        $pdf = $res->getContent();
        $this->assertStringStartsWith('%PDF', $pdf);
        $this->assertGreaterThan(5000, strlen($pdf)); // includes QR vector data

        // Owner-only access
        $other = User::factory()->create();
        $other->assignRole('student');
        $this->actingAs($other)->get(route('certificates.download', $cert))->assertForbidden();
    }
}
