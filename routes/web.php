<?php

use App\Http\Controllers\Admin\CourseController as AdminCourseController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\{
    ContactMessageController,
    CourseContentController,
    PermissionController,
    CategoryController,
    LevelController,
    QuizController,
    UserController,
    RoleController,
    LogController,
};
use App\Http\Controllers\{
    NotificationController,
    QuizAttemptController,
    DashboardController,
    LiveClassController,
    ProfileController,
    SettingController,
    CourseController,
    LearnController,
    AuthController,
    HomeController,
    PageController,
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
    Route::post('/contact', 'contactSubmit')->name('contact.submit');
    Route::get('/categories', 'categories')->name('categories');
    Route::get('/instructors', 'instructors')->name('instructors');
    Route::get('/instructors/{instructor}', 'instructorShow')->name('instructors.show');
    Route::get('/blog', 'blog')->name('blog');
    Route::get('/blog/{post}', 'blogShow')->name('blog.show');
    Route::get('/faq', 'faq')->name('faq');
    Route::get('/pricing', 'pricing')->name('pricing');
    Route::get('/certificates', 'certificates')->name('certificates.index');
    Route::get('/verify-certificate', 'certificateLookup')->name('certificates.lookup');
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

    // In-app notifications
    Route::post('/notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.readAll');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'read'])->name('notifications.read');

    // Instructor area
    Route::get('/instructor/students', [DashboardController::class, 'students'])->name('instructor.students');
    Route::get('/instructor/earnings', [DashboardController::class, 'earnings'])->name('instructor.earnings');

    // Learning / Watch area
    Route::controller(LearnController::class)->group(function () {
        Route::get('/learn/{course}', 'start')->name('learn.start');
        Route::get('/learn/{course}/{lesson}', 'show')->name('learn.show');
        Route::post('/learn/{course}/{lesson}/complete', 'toggleComplete')->name('learn.complete');
        Route::post('/learn/{course}/{lesson}/notes', 'storeNote')->name('learn.notes.store');
        Route::delete('/learn/notes/{note}', 'destroyNote')->name('learn.notes.destroy');
        Route::get('/learn/resources/{resource}/download', 'downloadResource')->name('learn.resources.download');
        Route::post('/learn/{course}/{lesson}/questions', 'storeQuestion')->name('learn.questions.store');
        Route::delete('/learn/questions/{discussion}', 'destroyQuestion')->name('learn.questions.destroy');
        Route::post('/learn/questions/{discussion}/upvote', 'toggleUpvote')->name('learn.questions.upvote');
        Route::post('/learn/questions/{discussion}/answer', 'markAnswered')->name('learn.questions.answer');
        Route::post('/learn/{course}/{lesson}/video-progress', 'saveVideoProgress')->name('learn.video-progress.save');
        Route::get('/learn/{course}/{lesson}/video-progress', 'getVideoProgress')->name('learn.video-progress.get');
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
        Route::post('/checkout/apply-coupon', 'applyCoupon')->name('checkout.apply-coupon');
        Route::get('/checkout/remove-coupon', 'removeCoupon')->name('checkout.remove-coupon');
    });

    // Take Tests (enrolled students)
    Route::get('/courses/{course}/tests/{quiz}', [QuizAttemptController::class, 'show'])->name('courses.tests.show');
    Route::post('/courses/{course}/tests/{quiz}', [QuizAttemptController::class, 'submit'])->name('courses.tests.submit');
    Route::get('/quiz-history', [QuizAttemptController::class, 'history'])->name('quiz.history');

    // Assignments (enrolled students)
    Route::get('/courses/{course}/assignments/{quiz}', [\App\Http\Controllers\AssignmentController::class, 'show'])->name('courses.assignments.show');
    Route::post('/courses/{course}/assignments/{quiz}/submit', [\App\Http\Controllers\AssignmentController::class, 'submit'])->name('courses.assignments.submit');
    Route::get('/assignments/submissions/{submission}/download', [\App\Http\Controllers\AssignmentController::class, 'download'])->name('assignments.download');
    Route::get('/assignment-history', [\App\Http\Controllers\AssignmentController::class, 'history'])->name('assignment.history');

    // Live Classes (students)
    Route::get('/live-classes', [LiveClassController::class, 'index'])->name('live-classes.index');
    Route::post('/live-classes/{liveClass}/join', [LiveClassController::class, 'join'])->name('live-classes.join');
    Route::post('/live-classes/{liveClass}/leave', [LiveClassController::class, 'leave'])->name('live-classes.leave');

    // Gamification
    Route::get('/gamification', [\App\Http\Controllers\GamificationController::class, 'index'])->name('gamification.index');
    Route::get('/gamification/badges', [\App\Http\Controllers\GamificationController::class, 'badges'])->name('gamification.badges');
    Route::get('/gamification/leaderboard', [\App\Http\Controllers\GamificationController::class, 'leaderboard'])->name('gamification.leaderboard');
});

    // Shared Course Management (admins manage all, instructors manage their own)
// Access is ownership-checked inside the controllers.
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {    Route::get('/courses', [AdminCourseController::class, 'index'])->name('courses.index');
    Route::get('/courses/create', [AdminCourseController::class, 'create'])->name('courses.create');
    Route::post('/courses', [AdminCourseController::class, 'store'])->name('courses.store');
    Route::get('/courses/{course}', [AdminCourseController::class, 'show'])->name('courses.show');
    Route::get('/courses/{course}/edit', [AdminCourseController::class, 'edit'])->name('courses.edit');
    Route::put('/courses/{course}', [AdminCourseController::class, 'update'])->name('courses.update');
    Route::delete('/courses/{course}', [AdminCourseController::class, 'destroy'])->name('courses.destroy');

    // Course Curriculum (chapters & lessons)
    Route::get('/courses/{course}/curriculum', [CourseContentController::class, 'index'])->name('courses.curriculum');

    // Live Classes
    Route::get('/courses/{course}/live-classes', [\App\Http\Controllers\Admin\LiveClassController::class, 'index'])->name('courses.live-classes.index');
    Route::post('/courses/{course}/live-classes', [\App\Http\Controllers\Admin\LiveClassController::class, 'store'])->name('courses.live-classes.store');
    Route::put('/courses/{course}/live-classes/{liveClass}', [\App\Http\Controllers\Admin\LiveClassController::class, 'update'])->name('courses.live-classes.update');
    Route::delete('/courses/{course}/live-classes/{liveClass}', [\App\Http\Controllers\Admin\LiveClassController::class, 'destroy'])->name('courses.live-classes.destroy');
    Route::get('/courses/{course}/live-classes/{liveClass}/attendance', [\App\Http\Controllers\Admin\LiveClassController::class, 'attendance'])->name('courses.live-classes.attendance');

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

    // Assignments
    Route::get('/courses/{course}/assignments', [\App\Http\Controllers\Admin\AssignmentController::class, 'index'])->name('courses.assignments.index');
    Route::get('/courses/{course}/assignments/create', [\App\Http\Controllers\Admin\AssignmentController::class, 'create'])->name('courses.assignments.create');
    Route::post('/courses/{course}/assignments', [\App\Http\Controllers\Admin\AssignmentController::class, 'store'])->name('courses.assignments.store');
    Route::get('/courses/{course}/assignments/{quiz}/edit', [\App\Http\Controllers\Admin\AssignmentController::class, 'edit'])->name('courses.assignments.edit');
    Route::put('/courses/{course}/assignments/{quiz}', [\App\Http\Controllers\Admin\AssignmentController::class, 'update'])->name('courses.assignments.update');
    Route::delete('/courses/{course}/assignments/{quiz}', [\App\Http\Controllers\Admin\AssignmentController::class, 'destroy'])->name('courses.assignments.destroy');
    Route::get('/courses/{course}/assignments/{quiz}/submissions', [\App\Http\Controllers\Admin\AssignmentController::class, 'submissions'])->name('courses.assignments.submissions');
    Route::get('/courses/{course}/assignments/{quiz}/submissions/{submission}/grade', [\App\Http\Controllers\Admin\AssignmentController::class, 'showGrade'])->name('courses.assignments.submissions.grade');
    Route::post('/courses/{course}/assignments/{quiz}/submissions/{submission}/grade', [\App\Http\Controllers\Admin\AssignmentController::class, 'grade'])->name('courses.assignments.submissions.grade.store');
});

// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Analytics Dashboard
    Route::get('/analytics', [\App\Http\Controllers\Admin\AnalyticsController::class, 'index'])->name('analytics');

    // Reports & Analysis
    Route::controller(\App\Http\Controllers\Admin\ReportController::class)->prefix('reports')->name('reports.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/export', 'export')->name('export');
    });

    // Contact Messages Inbox
    Route::controller(ContactMessageController::class)->prefix('messages')->name('messages.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/{message}/toggle-read', 'toggleRead')->name('toggle');
        Route::delete('/{message}', 'destroy')->name('destroy');
    });

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

    // Activity Logs
    Route::controller(LogController::class)->prefix('activity-logs')->name('activity-logs.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{activity}', 'show')->name('show');
        Route::delete('/{id}', 'destroy')->name('destroy');
        Route::delete('/', 'destroyAll')->name('destroyAll');
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

    // Coupons
    Route::controller(\App\Http\Controllers\Admin\CouponController::class)->prefix('coupons')->name('coupons.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::put('/{coupon}', 'update')->name('update');
        Route::delete('/{coupon}', 'destroy')->name('destroy');
        Route::post('/{coupon}/toggle', 'toggleActive')->name('toggle');
    });

    // Website Settings
    Route::get('/settings', [SettingController::class, 'edit'])->name('settings');
    Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
    Route::delete('/settings/images/{key}', [SettingController::class, 'removeImage'])->name('settings.images.remove');
});
