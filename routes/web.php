<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/
$app->post('getPartList', [
    'as'=>'getPartList', 'uses'=> 'PartController@getPartList'
]);
$app->post('getSubPartList', [
    'as'=>'getSubPartList', 'uses'=> 'PartController@getSubPartList'
]);
$app->post('getSymptomListByPart', [
    'as'=>'getSymptomListByPart', 'uses'=> 'SymptomController@getSymptomListByPart'
]);
$app->post('getSubSymptomList', [
    'as'=>'getSubSymptomList', 'uses'=> 'SymptomController@getSubSymptomList'
]);
$app->get('foo', function () {
    return 'Hello World';
});
$app->get('/', function () use ($app) {
    return $app->version();
});