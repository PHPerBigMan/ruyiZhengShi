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

Route::get('/', function () {
    return view('welcome');
});
Route::group(['namespace'=>'Page'],function(){
    Route::get('/register/{user_id?}/{type}','PageController@index');
    Route::post('/getCodetest','PageController@code');
    Route::post('/registerAdd','PageController@register');
    Route::get('/dowonloadpage','PageController@dowonloadpage');
    Route::get('/clientpage','PageController@clientpage');
    Route::get('/getError','DemoController@getErro');
    Route::get('/activity',function (){
        return view('/pages/activity');
    });
    Route::get('/clientactivity',function (){
        return view('/pages/clientactivity');
    });
});

Route::get('/success', function () {
    return view('/pages/success');
});
//  总后台路由
Route::group(['prefix'=>'back','namespace'=>'Back'],function(){
    Route::get('/login','LoginController@index');
    Route::post('/login/check','LoginController@check');
    Route::get('/loginout','LoginController@loginout');
});

Route::group(['prefix'=>'back','namespace'=>'Back','middleware'=>['IsLogin','userPermession']],function(){
    Route::get('/','IndexController@index');
    Route::get('/admin','AdminController@index');
    Route::post('/admin/del','AdminController@del');
    Route::post('/admin/add','AdminController@add');
    Route::post('/admin/edit','AdminController@edit');
    Route::get('dashboard','IndexController@detail');
    Route::get('/product/Plist','ProductController@Plist');
    Route::get('/product/SecCat/{pid}','ProductController@SecCat');
    Route::post('/product/cat_del','ProductController@cat_del');
    Route::post('/product/sec_cat_del','ProductController@sec_cat_del');
    Route::get('/product/edit','ProductController@edit');
    Route::post('/product/cat_pic','ProductController@cat_pic');
    Route::post('/product/save','ProductController@save');
    Route::post('/product/CatAdd','ProductController@CatAdd');
    Route::get('/product/product','ProductController@product');
    Route::get('/user/{type}','UserController@UserList');
    Route::get('/demo','UserController@demo');
    Route::get('user/detail/{id}', 'UserController@show')->name('user.detail');
    Route::get('company/detail/{id}', 'UserController@showCompany')->name('company.detail');

    Route::post('/user/changeStatus','UserController@changeStatus');
    Route::get('/order/{type}','OrderController@orderList');
    Route::post('/order/orderChange','OrderController@orderChange');
    Route::get('/setting','SettingController@SettingList');
    Route::get('/setting/serve','SettingController@Serve');
    Route::post('/setting/edit','SettingController@edit');
    Route::post('/setting/Sedit','SettingController@Sedit');
    Route::get('/black/{type}','BlackController@index');
    Route::post('/black/change','BlackController@change');
    Route::get('/integral','IntegralController@index');
    Route::get('product/checklist', 'ProductController@checkList');
    Route::get('product/check/{id}', 'ProductController@showCheck');
    Route::post('product/change/status', 'ProductController@editStatus');
    Route::get('/excel', 'ExcelController@excel');
    Route::get('/error', 'AdminController@PerMessionError');
    Route::get('/data', 'AdminController@dataTotal');
    Route::get('/todayTotal', 'DataTotalController@CatTodayTotal');
    Route::get('/typeChoose', 'DataTotalController@TotalType');
    Route::get('/areaList', 'DataTotalController@AreaList');

});

Route::group(['prefix'=>'back','namespace'=>'Back','middleware'=>['IsLogin']],function(){
    Route::get('/error', 'AdminController@PerMessionError');

});
// B端后台路由
Route::group(['prefix'=>'business','namespace'=>'Business'],function(){
    Route::get('/login','LoginController@index');
    Route::post('/check','LoginController@check');
    Route::get('/loginout','LoginController@loginout');
    Route::get('/settled','SettledController@index');
});


Route::group(['prefix'=>'business','namespace'=>'Business','middleware'=>'BusinessIsLogin'],function(){
    Route::get('/demo','ApplyController@demo');
    Route::get('/back','ProductController@index');
    Route::post('/productList','ProductController@productList');
    Route::post('/ProductSecondCat','ProductController@ProductSecondCat');
    Route::post('/productSave','ProductController@productSave');
    Route::post('/productDel','ProductController@productDel');
    Route::post('/shelves','ProductController@shelves');
    Route::post('/city','ProductController@city');
    Route::post('/area','ProductController@area');
    Route::get('/productRead/{id}/{type}/{cat_id?}','ProductController@productRead');
    Route::get('/evaluate','EvaluateController@index');
    Route::post('/evaluateList','EvaluateController@evaluateList');
    Route::post('/evaluateSave','EvaluateController@evaluateSave');
    Route::get('/evaluateAdd/{id}/{type?}','EvaluateController@evaluateAdd');
    Route::get('/apply','ApplyController@index');
    Route::post('/applyList','ApplyController@applyList');
    Route::get('/success/{type}','ApplyController@Success');
    Route::post('/OrderCancel','ApplyController@OrderCancel');
    Route::post('/successList','ApplyController@successList');
    Route::get('/yinlian/{id}/{type}','ApplyController@Yinlian');
    Route::post('/yinlianSave','ApplyController@yinlianSave');
    Route::get('/admin','AdminController@index');
    Route::get('/adminAdd','AdminController@Add');
    Route::any('/adminSave','AdminController@adminSave');
    Route::post('/isSave','AdminController@isSave');
    Route::post('/adminImg','AdminController@adminImg');
    Route::post('/PayList','AdminController@PayList');
    Route::post('/ChildList','AdminController@ChildList');
    Route::post('/ChildSave','AdminController@ChildSave');
    Route::post('/ChildDel','AdminController@ChildDel');
    Route::get('/ChildShow/{id?}','AdminController@ChildShow');
    Route::post('/ChildDel','AdminController@ChildDel');
    Route::post('/changeOrder','ApplyController@changeOrder');
    Route::get('/feedback','FeedbackController@index');
    Route::post('/FeedbackSave','FeedbackController@FeedbackSave');
    Route::get('/black','BlackController@index');
    Route::get('/blackAdd/{id}/{type}','BlackController@blackAdd');
    Route::post('/blackList','BlackController@BlackList');
    Route::post('/blackImg','BlackController@blackImg');
    Route::post('/blackDel','BlackController@blackDel');
    Route::post('/blackSave','BlackController@blackSave');
    Route::post('/blackSearch','BlackController@blackSearch');
    Route::get('/integral','IntegralController@index');
    Route::post('/integralList','IntegralController@integralList');
    Route::get('/share/{cat_id?}/{sec?}','ShareController@index');
    Route::post('/shareList','ShareController@shareList');
    Route::post('/shareImg','ShareController@shareImg');
    Route::post('/getProperty','ShareController@getProperty');
    Route::post('/SearchData','ShareController@SearchData');
    Route::post('/sort','ShareController@sort');
    Route::get('/shareRead/{id}','ShareController@ShareRead');
    Route::get('/getData/{cat_id}','ShareController@getData');
    Route::get('/sharePay','ShareController@sharePay');
    Route::get('/shareContent/{id}/{data}','ShareController@shareContent');
    Route::get('/message','MessageController@index');
    Route::get('/message/read/{id}','MessageController@read');
    Route::get('/Qrcode','ProductController@QrCode');


});

/*C端接口*/
Route::group(['prefix'=>'api','namespace'=>'Api','middleware'=>['VerifyCsrfToken']],function(){
    Route::any('/proCat','IndexController@proCat');
    Route::any('/img','IndexController@img');
    Route::any('/city','IndexController@City');
    Route::any('/about','IndexController@About');
    Route::any('/getCity','IndexController@getCity');
    Route::any('/cityList','IndexController@CityList');
    Route::any('/FenXiangImg','IndexController@FenXiangImg');
    //B端和C端共用注册登录获取验证码接口
    Route::any('/register','RegisterController@register');
    Route::any('/getCode','RegisterController@code');
    Route::any('/login','RegisterController@login');
    Route::any('/userInfo','UserController@UserInfo');
    Route::any('/userSave','UserController@UserSave');
    Route::any('/userApply','UserController@UserApply');
    Route::any('/userUnEvaluate','UserController@Evaluate');
    Route::any('/userQuestion','UserController@QA');
    Route::any('/userService','UserController@ServicePhone');
    Route::any('/userFeedback','UserController@Feedback');
    Route::any('/userChangeApply','UserController@ChangeApply');
    Route::any('/userSaveEvaluate','UserController@SaveEvaluate');
    Route::any('/userGetEvaluate','UserController@GetEvaluate');
    Route::any('/userPayList','UserController@PayList');
    Route::any('/article','UserController@Atricle');
    Route::any('/Integral','UserController@Integral');
    Route::any('/getApk','UserController@Apk');
    Route::any('/getApkC','UserController@Apkc');

    Route::any('/apply','ApplyController@apply');
    Route::any('/companyApply','ApplyController@CompanyApply');
    Route::any('/applyData','ApplyController@applyData');
    Route::any('/saveApply','ApplyController@SaveApplyData');
    Route::any('/yinLian','ApplyController@YinlianPay');
    Route::any('/getIntegral','ApplyController@GetIntegral');
    Route::any('/abnormalSave','ApplyController@AbnormalSave');
    Route::any('/orderInform','ApplyController@ReutrnOrderInform');
    Route::any('/refluseReason','ApplyController@Reason');
    Route::any('/Lian','ApplyController@Lian');
    Route::any('/demand','ApplyController@demand');
    Route::any('/isShen','ApplyController@isShen');
    Route::any('/Sort','ApplyController@Sort');
    Route::any('/evaluate','ProductController@evaluate');
    Route::any('/productRead','ProductController@read');
    Route::any('/property','ProductController@Property');
    Route::any('/json','ProductController@json');
    Route::any('/saveData','BusinessSettledController@SaveData');
    Route::any('/changePhone','BusinessSettledController@SaveCompanyPhone');
    Route::any('/userData','BusinessSettledController@UserData');
    Route::any('/demo','RegisterController@demo');
    Route::any('/messageList','MessageController@MessageList');
    Route::any('/moreMessage','MessageController@MoreMessageList');
    Route::any('/messageRead','MessageController@MessageRead');
    Route::any('/pay','PayController@pay');
    Route::any('/cardBin','PayController@cardBin');
    Route::any('/IosApkNew','UserController@ApkInformation');
    Route::any('/HasUserInformation','UserController@HasInformation');
});


Route::group(['prefix'=>'pay','namespace'=>'Api','middleware'=>'VerifyCsrfToken'],function(){
    Route::any('/callback','PayController@getInfo');
    Route::any('/setSign','PayController@setSign');
});

Route::group(['prefix'=>'bapi','namespace'=>'Bapi','middleware'=>'VerifyCsrfToken'],function(){
    Route::any('/Accept','OrderController@Accept');
    Route::any('/Accepted','OrderController@Accepted');
    Route::any('/orderChange','OrderController@OrderChange');
    Route::any('/getEvaluateList','OrderController@GetEvaluate');
    Route::any('/getDesc','OrderController@getDesc');
    Route::any('/getDescB','OrderController@getDescB');
    Route::any('/saveEvaluate','OrderController@SaveEvaluate');
    Route::any('/percentage','OrderController@Server');
    Route::any('/ta','OrderController@ta');
    Route::any('/orderBasic','OrderController@OrderBasic');
    Route::any('/getbasic','OrderController@getbasic');
    Route::any('/getImgs','OrderController@getImgs');
    Route::any('/orderData','OrderController@OrderData');
    Route::any('/Order','OperateController@Order');
    Route::any('/blackSearch','OperateController@BlackList');
    Route::any('/blackRead','OperateController@BlackRead');
    Route::any('/myBlack','OperateController@MyBlackList');
    Route::any('/blackChange','OperateController@BlackChange');
    Route::any('/blackSave','OperateController@BlackSave');
    Route::any('/evaluateList','OperateController@EvaluateList');
    Route::any('/userInfo','UserController@UserInfo');
    Route::any('/userUnEvaluate','UserController@Evaluate');
    Route::any('/SaveHeadImg','UserController@SaveHeadImg');
    Route::any('/managementList','UserController@ManagementList');
    Route::any('/Anquan','UserController@PasswordCheck');
    Route::any('/moneyList','UserController@MoneyList');
    Route::any('/saveInfo','UserController@SaveInfo');
    Route::any('/protect','UserController@Protect');
    Route::any('/pingTai','UserController@PingTai');
    Route::any('/productList','ProductController@ProductList');
    Route::any('/productChange','ProductController@ProductChange');
    Route::any('/productSave','ProductController@ProductSave');
    Route::any('/productNo','ProductController@ProductNo');
});


