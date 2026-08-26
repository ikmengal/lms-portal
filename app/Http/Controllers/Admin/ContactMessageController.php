<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ContactMessageController extends Controller
{
    public function index(Request $request)
    {
        $filter = in_array($request->get('filter'), ['unread', 'read']) ? $request->get('filter') : 'all';
        $search = trim((string) $request->get('search'));

        $query = ContactMessage::query()->latest();

        match ($filter) {
            'unread' => $query->where('is_read', false),
            'read' => $query->where('is_read', true),
            default => null,
        };

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%")
                    ->orWhere('message', 'like', "%{$search}%");
            });
        }

        $messages = $query->paginate(12)->withQueryString();

        return view('pages.admin.messages', [
            'messages' => $messages,
            'filter' => $filter,
            'search' => $search,
            'unreadCount' => ContactMessage::where('is_read', false)->count(),
        ]);
    }

    public function toggleRead(ContactMessage $message)
    {
        $message->update(['is_read' => !$message->is_read]);

        return back()->with('success', $message->is_read ? 'Message marked as read.' : 'Message marked as unread.');
    }

    public function destroy(ContactMessage $message)
    {
        $message->delete();

        return back()->with('success', "Message from '{$message->name}' deleted.");
    }
}
