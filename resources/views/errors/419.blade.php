@extends('errors.minimal', [
    'code' => '419',
    'title' => 'Session Expired',
    'message' => 'Your session has expired for security reasons. Please refresh the page or go back and try again.',
    'icon' => 'M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z',
])
