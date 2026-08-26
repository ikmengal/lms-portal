@extends('errors.minimal', [
    'code' => '403',
    'title' => 'Access Forbidden',
    'message' => "Sorry, you don't have permission to access this page. If you think this is a mistake, please contact support or sign in with a different account.",
    'icon' => 'M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z',
])
