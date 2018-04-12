<?php

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
use Illuminate\Support\Facades\Mail;

Route::get('/', function () {
    return redirect('events');
});
Route::get('/events/{id}/manager',['as'=>'events.showTomanager','uses'=>'EventsController@showTomanager']);
Route::get('/events/{id}/student',['as'=>'events.showToStudent','uses'=>'EventsController@showToStudent']);
Route::get('/events/{id}/host',['as'=>'events.showToHost','uses'=>'EventsController@showToHost']);
Route::get('/book','BookingController@book');
Route::get('/cancel','BookingController@cancel');
Route::get('/enqueue','BookingController@enqueue');
Route::get('/dequeue','BookingController@dequeue');
Route::get('/bookmark/store','BookmarkController@store');
Route::get('/bookmark/remove','BookmarkController@remove');
Route::post('/mail/bookingNotify','MailController@bookingNotify');
Route::post('/mail/cancelingNotify','MailController@cancelingNotify');
Route::post('/mail/joinQueueNotify','MailController@joinQueueNotify');
Route::post('/mail/reminder','MailController@reminderNotify');
Route::get('/events/search',['as'=>'events.search','uses'=>'EventsController@search']);
Route::post('/submitAbsenceList',['as'=>'events.checkAttendance','uses'=>'AbsenceController@store']);
Route::resource('/events','EventsController');
Route::post('/submitFollowingUpMail','MailController@sendFollowingUpMail');
Route::post('/subscribe','SubscribingController@store');
Route::get('/statistics',function(){
	return view('statistics.statistics');
});
Route::get('/subscription','SubscribingController@show');
Route::post('/events/{id}/enterPromo','PromoController@applyPromoCode');
Route::post('/chart/attendanceByCategory','StatisticsController@getAttendanceDataByCategory');
Route::post('/chart/attendanceByTime','StatisticsController@getAttendanceDataByTime');
Route::post('/chart/attendanceByUndergraduate','StatisticsController@getAttendanceDataByUndergraduate');
Route::post('/chart/attendanceByPostgraduate','StatisticsController@getAttendanceDataByPostgraduate');
Route::post('/events/{id}/checkout','BuyTicketController@checkout');
Route::post('/events/{id}/paym','BuyTicketController@payt');
Route::post('/chart/profitByCategory','StatisticsController@getProfitDataByCategory');
Route::post('/chart/profitByTime','StatisticsController@getProfitDataByTime');
Auth::routes();

Route::get('/dashboard', 'DashboardController@index')->name('home');
