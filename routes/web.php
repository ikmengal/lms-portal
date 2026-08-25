<?php

use App\Http\Controllers\Admin\CourseController as AdminCourseController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\{
    CourseContentController,
    PermissionController,
    CategoryController,
    LevelController,
    QuizController,
    UserController,
    RoleController
};
use App\Http\Controllers\{
    DashboardController,
    ProfileController,
    QuizAttemptController,
    SettingController,
    CourseController,
    AuthController,
    HomeController,
    PageController
};

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::controller(CourseController::class)->group(function () {
    Route::get('/courses', 'index')->name('courses.index');
    Route::get('/courses/{slug}', 'show')->name('courses.show');
    Route::post('/courses/{course}/wishlist', 'toggleWishlist')->middleware('auth')->name('courses.wishlist');
});

Route::controller(PageController::class)->group(function () {
    Route::get('/about', 'about')->name('about');
    Route::get('/contact', 'contact')->name('contact');
});

// Public Certificate Verification
Route::get('/verify-certificate/{code}', [DashboardController::class, 'verify'])->name('certificates.verify');

// Auth Routes (guest only)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    // Password Reset
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

    // Account Activation (welcome email)
    Route::get('/activate/resend', [AuthController::class, 'showResendForm'])->name('activation.resend.form');
    Route::post('/activate/resend', [AuthController::class, 'resendActivation'])
        ->middleware('throttle:5,1')->name('activation.resend');
    Route::get('/activate/{user}', [AuthController::class, 'activate'])
        ->middleware('signed')->name('activation.verify');
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Instructor area
    Route::get('/instructor/students', [DashboardController::class, 'students'])->name('instructor.students');
    Route::get('/instructor/earnings', [DashboardController::class, 'earnings'])->name('instructor.earnings');

    // Learning / Watch area
    Route::controller(\App\Http\Controllers\LearnController::class)->group(function () {
        Route::get('/learn/{course}', 'start')->name('learn.start');
        Route::get('/learn/{course}/{lesson}', 'show')->name('learn.show');
        Route::post('/learn/{course}/{lesson}/complete', 'toggleComplete')->name('learn.complete');
        Route::post('/learn/{course}/{lesson}/notes', 'storeNote')->name('learn.notes.store');
        Route::delete('/learn/notes/{note}', 'destroyNote')->name('learn.notes.destroy');
        Route::get('/learn/resources/{resource}/download', 'downloadResource')->name('learn.resources.download');
        Route::post('/learn/{course}/{lesson}/questions', 'storeQuestion')->name('learn.questions.store');
        Route::delete('/learn/questions/{discussion}', 'destroyQuestion')->name('learn.questions.destroy');
    });

    // Certificates
    Route::get('/certificates/{certificate}', [DashboardController::class, 'certificate'])->name('certificates.show');
    Route::get('/certificates/{certificate}/download', [DashboardController::class, 'downloadCertificate'])->name('certificates.download');

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar.update');
    Route::delete('/profile/avatar', [ProfileController::class, 'removeAvatar'])->name('profile.avatar.remove');
    Route::post('/profile/banner', [ProfileController::class, 'updateBanner'])->name('profile.banner.update');
    Route::delete('/profile/banner', [ProfileController::class, 'removeBanner'])->name('profile.banner.remove');

    // Cart & Checkout & Receipts
    Route::controller(\App\Http\Controllers\CheckoutController::class)->group(function () {
        Route::get('/cart', 'cartIndex')->name('cart.index');
        Route::post('/cart/add/{course}', 'addToCart')->name('cart.add');
        Route::post('/cart/remove/{course}', 'removeFromCart')->name('cart.remove');
        Route::get('/checkout/cart', 'checkoutCart')->name('checkout.cart');
        Route::get('/checkout/{course}', 'checkoutCourse')->name('checkout.course');
        Route::post('/checkout/process', 'process')->name('checkout.process');
        Route::get('/receipts/{payment}', 'showReceipt')->name('receipts.show');
        Route::post('/courses/{course}/enroll-free', 'enrollFree')->name('courses.enroll.free');
    });

    // Take Tests (enrolled students)
    Route::get('/courses/{course}/tests/{quiz}', [QuizAttemptController::class, 'show'])->name('courses.tests.show');
    Route::post('/courses/{course}/tests/{quiz}', [QuizAttemptController::class, 'submit'])->name('courses.tests.submit');
});

// Shared Course Management (admins manage all, instructors manage their own)
// Access is ownership-checked inside the controllers.
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/courses', [AdminCourseController::class, 'index'])->name('courses.index');
    Route::get('/courses/create', [AdminCourseController::class, 'create'])->name('courses.create');
    Route::post('/courses', [AdminCourseController::class, 'store'])->name('courses.store');
    Route::get('/courses/{course}', [AdminCourseController::class, 'show'])->name('courses.show');
    Route::get('/courses/{course}/edit', [AdminCourseController::class, 'edit'])->name('courses.edit');
    Route::put('/courses/{course}', [AdminCourseController::class, 'update'])->name('courses.update');
    Route::delete('/courses/{course}', [AdminCourseController::class, 'destroy'])->name('courses.destroy');

    // Course Curriculum (chapters & lessons)
    Route::get('/courses/{course}/curriculum', [CourseContentController::class, 'index'])->name('courses.curriculum');
    Route::post('/courses/{course}/modules', [CourseContentController::class, 'storeModule'])->name('modules.store');
    Route::put('/modules/{module}', [CourseContentController::class, 'updateModule'])->name('modules.update');
    Route::post('/modules/{module}/move', [CourseContentController::class, 'moveModule'])->name('modules.move');
    Route::delete('/modules/{module}', [CourseContentController::class, 'destroyModule'])->name('modules.destroy');
    Route::post('/modules/{module}/lessons', [CourseContentController::class, 'storeLesson'])->name('lessons.store');
    Route::put('/lessons/{lesson}', [CourseContentController::class, 'updateLesson'])->name('lessons.update');
    Route::delete('/lessons/{lesson}', [CourseContentController::class, 'destroyLesson'])->name('lessons.destroy');
    Route::post('/lessons/{lesson}/resources', [CourseContentController::class, 'storeResource'])->name('lessons.resources.store');
    Route::delete('/lessons/resources/{resource}', [CourseContentController::class, 'destroyResource'])->name('lessons.resources.destroy');

    // Tests / Assignments / Final Exams
    Route::get('/courses/{course}/tests', [QuizController::class, 'index'])->name('courses.tests.index');
    Route::get('/courses/{course}/tests/create', [QuizController::class, 'create'])->name('courses.tests.create');
    Route::post('/courses/{course}/tests', [QuizController::class, 'store'])->name('courses.tests.store');
    Route::get('/courses/{course}/tests/{quiz}/edit', [QuizController::class, 'edit'])->name('courses.tests.edit');
    Route::put('/courses/{course}/tests/{quiz}', [QuizController::class, 'update'])->name('courses.tests.update');
    Route::delete('/courses/{course}/tests/{quiz}', [QuizController::class, 'destroy'])->name('courses.tests.destroy');
});

// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Manage Users
    Route::controller(UserController::class)->group(function () {
        Route::get('/users', 'index')->name('users');
        Route::get('/users/create', 'create')->name('users.create');
        Route::post('/users', 'store')->name('users.store');
        Route::get('/users/{user}/edit', 'edit')->name('users.edit');
        Route::put('/users/{user}', 'update')->name('users.update');
        Route::delete('/users/{user}', 'destroy')->name('users.destroy');
    });

    // Roles & Permissions Management
    Route::controller(RoleController::class)->prefix('roles')->name('roles.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{role}/edit', 'edit')->name('edit');
        Route::put('/{role}', 'update')->name('update');
        Route::delete('/{role}', 'destroy')->name('destroy');
    });

    Route::controller(PermissionController::class)->prefix('permissions')->name('permissions.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::put('{id}', 'update')->name('update');
        Route::delete('{id}', 'destroy')->name('destroy');
    });

    // Categories Management
    Route::controller(CategoryController::class)->prefix('categories')->name('categories.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::put('{id}', 'update')->name('update');
        Route::post('{id}/move', 'move')->name('move');
        Route::post('{id}/toggle', 'toggleActive')->name('toggle');
        Route::delete('{id}', 'destroy')->name('destroy');
        Route::post('{id}/restore', 'restore')->name('restore');
        Route::delete('{id}/force', 'forceDelete')->name('force-delete');
    });

    // Levels Management
    Route::controller(LevelController::class)->prefix('levels')->name('levels.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::put('{id}', 'update')->name('update');
        Route::post('{id}/move', 'move')->name('move');
        Route::post('{id}/toggle', 'toggleActive')->name('toggle');
        Route::delete('{id}', 'destroy')->name('destroy');
        Route::post('{id}/restore', 'restore')->name('restore');
        Route::delete('{id}/force', 'forceDelete')->name('force-delete');
    });

    Route::get('/courses/trash', [AdminCourseController::class, 'trash'])->name('courses.trash');
    Route::post('/courses/{id}/restore', [AdminCourseController::class, 'restore'])->name('courses.restore');
    Route::delete('/courses/{id}/force-delete', [AdminCourseController::class, 'forceDelete'])->name('courses.force-delete');

    // Website Settings
    Route::get('/settings', [SettingController::class, 'edit'])->name('settings');
    Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
    Route::delete('/settings/images/{key}', [SettingController::class, 'removeImage'])->name('settings.images.remove');
});
