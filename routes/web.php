<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GoogleClassroomController;
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

Route::get('/', function () {
    return view('welcome');
});
// Route::delete('/excluir-alias/{cursoId}/{alias}',
// [GoogleClassroomController::class, 'excluirAlias'])->name('excluir-alias');


$router->group(['prefix' => 'api/v1'], function () use ($router) {
    $router->post('/courses', [GoogleClassroomController::class, 'createCourse'])->name('create-course');
    //$router->get('/courses/{id}', 'GoogleClassroomController@getCourse');
    //$router->get('/courses', 'GoogleClassroomController@listCourses');
    //$router->put('/courses/{id}', 'GoogleClassroomController@updateCourse');
    //$router->patch('/courses/{id}', 'GoogleClassroomController@patchCourse');
});
