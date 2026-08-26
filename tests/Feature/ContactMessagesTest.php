<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification as NotificationFacade;
use Tests\TestCase;

class ContactMessagesTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $student;

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
    }

    public function test_contact_submission_saves_message_and_notifies_admins(): void
    {
        NotificationFacade::fake();

        $res = $this->post(route('contact.submit'), [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'subject' => 'Question about courses',
            'message' => 'Hi, I would like to know more about your pricing plans.',
        ]);

        $res->assertRedirect()->assertSessionHas('success');

        $this->assertDatabaseHas('contact_messages', ['email' => 'john@example.com', 'is_read' => false]);

        // Admin received an in-app (database) notification
        NotificationFacade::assertSentTo($this->admin, \App\Notifications\NewContactMessageNotification::class);
    }

    public function test_admin_can_view_messages_page(): void
    {
        ContactMessage::create(['name' => 'A', 'email' => 'a@x.com', 'subject' => 'S1', 'message' => 'M1']);

        $res = $this->actingAs($this->admin)->get(route('admin.messages.index'));

        $res->assertOk()
            ->assertSee('Contact Messages')
            ->assertSee('S1')
            ->assertSee('1 unread');
    }

    public function test_non_admin_cannot_access_messages(): void
    {
        $this->actingAs($this->student)->get(route('admin.messages.index'))->assertForbidden();
    }

    public function test_admin_can_toggle_read_status(): void
    {
        $msg = ContactMessage::create(['name' => 'B', 'email' => 'b@x.com', 'subject' => 'S2', 'message' => 'M2']);
        $this->assertFalse((bool) $msg->fresh()->is_read);

        $this->actingAs($this->admin)->post(route('admin.messages.toggle', $msg))->assertRedirect();
        $this->assertTrue((bool) $msg->fresh()->is_read);
    }

    public function test_admin_can_delete_message(): void
    {
        $msg = ContactMessage::create(['name' => 'C', 'email' => 'c@x.com', 'subject' => 'S3', 'message' => 'M3']);

        $this->actingAs($this->admin)->delete(route('admin.messages.destroy', $msg))->assertRedirect();

        // ContactMessage hard-deletes (no soft deletes on this model)
        $this->assertDatabaseMissing('contact_messages', ['id' => $msg->id]);
    }

    public function test_bell_shows_unread_and_mark_all_read_works(): void
    {
        ContactMessage::create(['name' => 'D', 'email' => 'd@x.com', 'subject' => 'Bell subject', 'message' => 'Body text here.']);
        NotificationFacade::send($this->admin, new \App\Notifications\NewContactMessageNotification(
            ContactMessage::latest()->first()
        ));

        // Dashboard shows the notification in the bell with unread count badge
        $res = $this->actingAs($this->admin)->get(route('dashboard'));
        $res->assertOk()->assertSee('Bell subject')->assertSee('>1</span>', false);

        // Mark all read clears it
        $this->actingAs($this->admin)->post(route('notifications.readAll'))->assertRedirect();
        $this->assertEquals(0, $this->admin->unreadNotifications()->count());
    }
}
