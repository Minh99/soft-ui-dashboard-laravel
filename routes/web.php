<?php

use App\Http\Controllers\ChangePasswordController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InfoUserController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ResetController;
use App\Http\Controllers\SessionsController;
use App\Http\Controllers\StoryController;
use App\Http\Controllers\Test2Controller;
use App\Http\Controllers\TopicController;
use App\Http\Controllers\UserWithChatController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::group(['middleware' => ['auth', 'day']], function () {

    Route::get('/', [HomeController::class, 'home']);
	Route::get('dashboard', [HomeController::class, 'index'])->name('dashboard');

	// Route::get('billing', function () {
	// 	return view('billing');
	// })->name('billing');

	// Route::get('profile', function () {
	// 	return view('profile');
	// })->name('profile');

	// Route::get('rtl', function () {
	// 	return view('rtl');
	// })->name('rtl');

	Route::get('user-management', [InfoUserController::class, 'index'])->name('user-management');
	// id is optional
	Route::match(['get', 'post'], 'user-management-store/{id?}', [InfoUserController::class, 'store'])->name('user-management-store');
	Route::delete('users/{id}', [InfoUserController::class, 'delete'])->name('user-management-delete');

	
	Route::get('user-report', [InfoUserController::class, 'report'])->name('user-report');
	Route::get('user-report/{id}', [InfoUserController::class, 'reportDetail'])->name('user-report-detail');

	Route::get('vocabularies', [InfoUserController::class, 'vocabularies'])->name('vocabularies');
	Route::POST('vocabularies-store', [InfoUserController::class, 'createVocabulary'])->name('vocabularies-store');

	Route::get('topics-management', [InfoUserController::class, 'topicsManagement'])->name('topicsManagement');
	Route::POST('topics-management-create', [InfoUserController::class, 'createTopicsManagement'])->name('createTopicsManagement');
	Route::post('topics-management-delete', [InfoUserController::class, 'deleteTopicsManagement'])->name('deleteTopicsManagement');


	// Route::get('tables', function () {
	// 	return view('tables');
	// })->name('tables');

    // Route::get('virtual-reality', function () {
	// 	return view('virtual-reality');
	// })->name('virtual-reality');

    // Route::get('static-sign-in', function () {
	// 	return view('static-sign-in');
	// })->name('sign-in');

    // Route::get('static-sign-up', function () {
	// 	return view('static-sign-up');
	// })->name('sign-up');

    Route::get('/logout', [SessionsController::class, 'destroy']);

	Route::get('/chat', [UserWithChatController::class, 'index'])->name('chat');
	Route::get('/exchange-start-word/{word}', [UserWithChatController::class, 'exchangeStartWord'])->name('exchange-start-word');
	Route::get('/exchange-question/{id}', [UserWithChatController::class, 'exchangeQuestion'])->name('exchange-question');

    Route::get('/login', function () {
		return view('dashboard');
	})->name('sign-up');

	Route::get('test-ui', function () {
		return view('test-ui');
	})->name('test-ui');


	Route::get('quiz-first',[HomeController::class, 'QuizFirst'])->name('quiz-first');
	Route::post('quiz-first',[HomeController::class, 'QuizFirstSubmit'])->name('quiz-first-submit');

	Route::get('final-test-by-day',[Test2Controller::class, 'test2'])->name('test2');
	Route::post('final-test-by-day',[Test2Controller::class, 'test2Submit'])->name('test2-submit');
	
	Route::get('topics',[TopicController::class, 'index'])->name('topics');

	// by user topic
	Route::get('topics/{id}/{typeQuiz}',[TopicController::class, 'getTopicByUser'])->name('topics.detail');
	Route::get('quiz-for/{topicUserId}',[HomeController::class, 'QuizFor'])->name('quiz-for');
	Route::post('quiz-for',[HomeController::class, 'QuizForSubmit'])->name('quiz-for-submit');
	Route::get('test',[HomeController::class, 'Quiz'])->name('quiz');

	// mark-done
	Route::post('mark-done/{topicUserId}',[HomeController::class, 'markDone'])->name('mark-done');
	Route::get('mark-done-test-2',[HomeController::class, 'markDoneTest2'])->name('mark-done-test-2');

	// API
	Route::get('generate-stories/{topicId}',[StoryController::class, 'generate'])->name('generate-stories');
	Route::get('generate-question/{topicUserId}',[StoryController::class, 'generateQuestion'])->name('generate-question');
	Route::get('generate-answer/{topicUserId}',[StoryController::class, 'generateAnswer'])->name('generate-answer');

});


Route::group(['middleware' => 'guest'], function () {
    Route::get('/register', [RegisterController::class, 'create']);
    Route::post('/register', [RegisterController::class, 'store']);
    Route::get('/login', [SessionsController::class, 'create']);
    Route::post('/session', [SessionsController::class, 'store']);
	Route::get('/login/forgot-password', [ResetController::class, 'create']);
	Route::post('/forgot-password', [ResetController::class, 'sendEmail']);
	Route::get('/reset-password/{token}', [ResetController::class, 'resetPass'])->name('password.reset');
	Route::post('/reset-password', [ChangePasswordController::class, 'changePassword'])->name('password.update');

});

Route::get('/login', function () {
    return view('session/login-session');
})->name('login');


Route::get('/init-base-command/{id}', function ($id) {
	if ($id == 'migrate') {
		Artisan::call('migrate');
		return 'migrate success';
	}

	if ($id == 'seed') {
		Artisan::call('db:seed');
		return 'seed success';
	}

	if ($id == 'migrate-seed') {
		Artisan::call('migrate:refresh');
		Artisan::call('db:seed');

		Log::info(Artisan::output());
		return 'migrate seed success';
	}

	if ($id == 'migrate-refresh') {
		Artisan::call('migrate:refresh');
		return 'migrate refresh success';
	}

	if ($id == 'migrate-fresh') {
		Artisan::call('migrate:fresh');
		return 'migrate fresh success';
	}

	if ($id == 'optimize') {
		Artisan::call('optimize');
		Artisan::call('cache:clear');
		Artisan::call('config:clear');

		
		return 'optimize success:'. date('Y-m-d H:i:s');
	}
});