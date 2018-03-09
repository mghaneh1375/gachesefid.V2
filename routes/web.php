<?php

Route::get('salam', array('as' => 'getUpdates', 'uses' => 'HomeController@salam'));

Route::any('{token}/webhook', array('as' => 'webhook', 'uses' => 'TelegramController@getUpdates'));

Route::get('salam2', array('as' => 'getUpdates', 'uses' => 'TelegramController@postSendMessage'));

Route::post('doRegistration', 'RegistrationController@doRegistration')->name('doRegistration');

Route::post('get_exam_answer_sheet_template/{exam_id}', array('as' => 'get_exam_answer_sheet_template', 'uses' => 'AdminController@get_exam_answer_sheet_template'));

Route::group(array('middleware' => ['nothing', 'notLogin']), function () {
	
	Route::get('login', 'HomeController@login')->name('login');

	Route::post('doLogin', 'HomeController@doLogin')->name('doLogin');
});

Route::group(array('middleware' => 'nothing'), function (){

	Route::get('advisersList', array('as' => 'advisersList', 'uses' => 'ReportController@advisersList'));

	Route::get('studentsRanking/{page?}', array('as' => 'studentsRanking', 'uses' => 'ReportController@studentsRanking'));

	Route::get('aboutUs', array('as' => 'aboutUs', 'uses' => 'HomeController@aboutUs'));

	Route::get('schoolsList', array('as' => 'schoolsList', 'uses' => 'UserController@schoolsList'));

	Route::get('ranking/{quizId}', array('as' => 'ranking', 'uses' => 'QuizController@ranking'));
	
	Route::get('ranking1', array('as' => 'ranking1', 'uses' => 'QuizController@rankingSelectQuiz'));

	Route::post('getROQ', array('as' => 'getROQ', 'uses' => 'QuizController@getROQ'));

	Route::post('checkAuth', array('as' => 'checkAuth', 'uses' => 'HomeController@checkAuth'));

	Route::get('/', array('as' => 'home', 'uses' => 'HomeController@showHome'));

	Route::get('home', 'HomeController@showHome');

	Route::post('recoveryPassword', array('as' => 'doRecovery', 'uses' => 'HomeController@doRecoveryPas'));

	Route::get('resetPassword', array('as' => 'resetPas', 'uses' => 'HomeController@resetPas'));

	Route::post('doResetPassword', array('as' => 'doResetPas', 'uses' => 'HomeController@doResetPas'));

	Route::get('registration', array('as' => 'registration', 'uses' => 'RegistrationController@registration'));
	
	Route::get('getActivation', array('as' => 'getActivation', 'uses' => 'RegistrationController@getActivation'));

	Route::post('getActivation', array('as' => 'getActivation', 'uses' => 'RegistrationController@doGetActivation'));
});

Route::group(array('middleware' => ['nothing', 'auth']), function (){

	Route::get('messages', array('as' => 'message', 'uses' => 'MessageController@showMessages'));

	Route::get('sendMessage/{dest}', array('as' => 'sendMessage', 'uses' => 'MessageController@sendMessage'));

	Route::post('getListOfMsgs', array('as' => 'getListOfMsgs', 'uses' => 'MessageController@getListOfMsgs'));

	Route::post('getMessage', 'MessageController@getMessage');

	Route::post('opOnMsgs', array('as' => 'opOnMsgs', 'uses' => 'MessageController@opOnMsgs'));

	Route::post('sendMsg', array('as' => 'sendMsg', 'uses' => 'MessageController@sendMsg'));

});

Route::group(array('middleware' => ['nothing', 'auth', 'schoolLevel']), function () {

	Route::post('editStudent', array('as' => 'editStudent', 'uses' => 'UserController@editStudent'));

	Route::get('schoolStudent/{sId}', array('as' => 'schoolStudent', 'uses' => 'ReportController@schoolStudent'));

	Route::get('groupRegistration', array('as' => 'groupRegistration', 'uses' => 'RegistrationController@groupRegistration'));

	Route::post('doGroupRegistry', array('as' => 'doGroupRegistry', 'uses' => 'RegistrationController@doGroupRegistry'));

	Route::get('groupQuizRegistration', array('as' => 'groupQuizRegistration', 'uses' => 'RegistrationController@groupQuizRegistration'));

	Route::post('getRegularQuizesOfStd', array('as' => 'getRegularQuizesOfStd', 'uses' => 'RegistrationController@getRegularQuizesOfStd'));

	Route::post('registerableList', array('as' => 'registerableList', 'uses' => 'RegistrationController@registerableList'));

	Route::post('submitRegistry', array('as' => 'submitRegistry', 'uses' => 'RegistrationController@submitRegistry'));

	Route::post('getQueuedQuizes', array('as' => 'getQueuedQuizes', 'uses' => 'RegistrationController@getQueuedQuizes'));

	Route::post('getStdOfQuiz', array('as' => 'getStdOfQuiz', 'uses' => 'RegistrationController@getStdOfQuiz'));

	Route::post('deleteFromQueue', array('as' => 'deleteFromQueue', 'uses' => 'RegistrationController@deleteFromQueue'));
	
	Route::post('deleteStdFromSchool', array('as' => 'deleteStdFromSchool', 'uses' => 'RegistrationController@deleteStdFromSchool'));
});

Route::group(array('middleware' => ['nothing', 'auth', 'reportLevel']), function () {

	Route::get('getQuizReport/{quizId}', array('as' => 'getQuizReport', 'uses' => 'ReportController@getQuizReport'));

	Route::get('quizReports', array('as' => 'quizReports', 'uses' => 'ReportController@chooseQuiz'));

	Route::get('A5/{quizId}', array('as' => 'A5', 'uses' => 'ReportController@A5'));

	Route::get('printA5/{quizId}', array('as' => 'printA5', 'uses' => 'ReportController@printA5'));

	Route::get('A5Excel/{quizId}', array('as' => 'A5Excel', 'uses' => 'ReportController@A5Excel'));

	Route::get('A1/{quizId}', array('as' => 'A1', 'uses' => 'ReportController@A1'));

	Route::get('A1Excel/{quizId}', array('as' => 'A1Excel', 'uses' => 'ReportController@A1Excel'));

	Route::get('A2/{quizId}', array('as' => 'A2', 'uses' => 'ReportController@A2'));

	Route::get('A2Excel/{quizId}', array('as' => 'A2Excel', 'uses' => 'ReportController@A2Excel'));

	Route::get('A3/{quizId}', array('as' => 'preA3', 'uses' => 'ReportController@preA3'));

	Route::get('A3/{quizId}/{uId}/{backURL?}', array('as' => 'A3', 'uses' => 'ReportController@A3'));

	Route::get('printKarname/{quizId}/{uId}', array('as' => 'printKarnameMaster', 'uses' => 'ReportController@printKarname'));

	Route::get('A7/{quizId}', array('as' => 'A7', 'uses' => 'ReportController@A7'));

	Route::get('A7Excel/{quizId}', array('as' => 'A7Excel', 'uses' => 'ReportController@A7Excel'));

	Route::get('A4/{quizId}', array('as' => 'A4', 'uses' => 'ReportController@A4'));

	Route::get('A4Excel/{quizId}', array('as' => 'A4Excel', 'uses' => 'ReportController@A4Excel'));

	Route::get('A6/{quizId}', array('as' => 'A6', 'uses' => 'ReportController@A6'));

	Route::get('A6Excel/{quizId}', array('as' => 'A6Excel', 'uses' => 'ReportController@A6Excel'));

});

Route::post('showRSSGach', 'HomeController@showRSSGach')->name('showRSSGach');

Route::post('showRSSIrysc', 'HomeController@showRSSIrysc')->name('showRSSIrysc');

Route::group(array('middleware' => ['nothing', 'auth']), function () {

	Route::get('userInfo', array('as' => 'userInfo', 'uses' => 'HomeController@userInfo'));

	Route::get('userInfo/{selectedPart}', array('as' => 'userInfo2', 'uses' => 'HomeController@userInfo2'));

	Route::get('logout', array('as' => 'logout', 'uses' => 'HomeController@logout'));

	Route::post('editInfo', array('as' => 'editInfo', 'uses' => 'HomeController@editInfo'));

	Route::post('editRedundantInfo1', array('as' => 'editRedundantInfo1', 'uses' => 'HomeController@editRedundantInfo1'));

	Route::post('editRedundantInfo2', array('as' => 'editRedundantInfo2', 'uses' => 'HomeController@editRedundantInfo2'));

	Route::get('changePassword', array('as' => 'changePas', 'uses' => 'HomeController@changePas'));

	Route::post('doChangePassword', array('as' => 'doChangePas', 'uses' => 'HomeController@doChangePas'));
});

Route::group(array('middleware' => ['nothing', 'auth', 'phone']), function () {

	Route::get('profile', array('as' => 'profile', 'uses' => 'HomeController@profile'));

	Route::post('getSubjectQuestionNumsUser', array('as' => 'getSubjectQuestionNumsUser', 'uses' => 'QuestionController@getSubjectQuestionNumsUser'));

});

Route::group(array('middleware' => ['nothing', 'auth', 'adminLevel']), function () {

	Route::get('smsPanel', array('as' => 'smsPanel', 'uses' => 'SMSController@smsPanel'));

	Route::post('sendSMS', array('as' => 'sendSMS', 'uses' => 'SMSController@sendSMS'));

	Route::post('sendSMSStatus', array('as' => 'sendSMSStatus', 'uses' => 'SMSController@sendSMSStatus'));

	Route::get('slideBarManagement', array('as' => 'slideBarManagement', 'uses' => 'SlideBarController@slides'));

	Route::post('opOnSlides', array('as' => 'opOnSlides', 'uses' => 'SlideBarController@opOnSlides'));

	Route::get('reportsAccess', array('as' => 'reportsAccess', 'uses' => 'ReportController@reportsAccess'));

	Route::post('changeReportStatus', array('as' => 'changeReportStatus', 'uses' => 'ReportController@changeReportStatus'));

	Route::post('status', array('as' => 'status', 'uses' => 'QuizController@status'));

	Route::get('answer_sheet_templates', array('as' => 'answer_sheet_templates', 'uses' => 'AdminController@answer_sheet_templates'));

	Route::get('delete_answer_sheet_template/{answer_sheet_template}', array('as' => 'delete_answer_sheet_template', 'uses' => 'AdminController@delete_answer_sheet_template'));

	Route::post('add_answer_sheet_template', array('as' => 'add_answer_sheet_template', 'uses' => 'AdminController@add_answer_sheet_template'));
	
	Route::get('add_answer_sheet_template', array('as' => 'add_answer_sheet_template', 'uses' => 'AdminController@add_answer_sheet_template_form'));
	
	Route::get('edit_answer_sheet_template/{aId}', array('as' => 'edit_answer_sheet_template', 'uses' => 'AdminController@edit_answer_sheet_template'));
	
	Route::post('edit_answer_sheet_template/{aId}', array('as' => 'edit_answer_sheet_template', 'uses' => 'AdminController@update_answer_sheet_template'));

	Route::get('answer_sheet_template/{answer_sheet_template}/answers', array('as' => 'answer_answer_sheet_template', 'uses' => 'AdminController@answer_sheet_template_answers'));

	Route::post('answer_sheet_template/{answer_sheet_template}/add_answer_template', array('as' => 'add_answer_answer_sheet_template', 'uses' => 'AdminController@add_answer_template'));

	Route::get('answer_template/{answer_template}/delete', array('as' => 'delete_answer_answer_sheet_template', 'uses' => 'AdminController@delete_answer_template'));

	Route::post('edit_answer_answer_sheet_template', array('as' => 'edit_answer_answer_sheet_template', 'uses' => 'AdminController@edit_answer_template'));

	Route::get('groupQuizRegistrationController/{qId}', array('as' => 'groupQuizRegistrationController', 'uses' => 'AdminController@groupQuizRegistrationController'));

	Route::post('studentsOfAdviserInQuiz', array('as' => 'studentsOfAdviserInQuiz', 'uses' => 'AdminController@studentsOfAdviserInQuiz'));

	Route::post('totalRegister', array('as' => 'totalRegister', 'uses' => 'AdminController@totalRegister'));

	Route::get('subjectReport', array('as' => 'subjectReport', 'uses' => 'ReportController@subjectReport'));

	Route::get('subjectReportExcel', array('as' => 'subjectReportExcel', 'uses' => 'ReportController@subjectReportExcel'));

	Route::get('barcodeReport', array('as' => 'barcodeReport', 'uses' => 'ReportController@barcodeReport'));

	Route::get('getBarcodeReport/{quizId}', array('as' => 'getBarcodeReport', 'uses' => 'ReportController@getBarcodeReport'));

	Route::get('studentReport/{mode?}/{key?}/{page?}', array('as' => 'studentReport', 'uses' => 'ReportController@studentReport'));

	Route::post('doEditUser', array('as' => 'doEditUser', 'uses' => 'ReportController@doEditUser'));
	
	Route::post('doRemoveUser', array('as' => 'doRemoveUser', 'uses' => 'ReportController@doRemoveUser'));

	Route::get('studentReportPage/{page}', array('as' => 'studentReportPage', 'uses' => 'ReportController@studentReport'));

	Route::get('studentReportExcel', array('as' => 'studentReportExcel', 'uses' => 'ReportController@studentReportExcel'));

	Route::get('gradeReport', array('as' => 'gradeReport', 'uses' => 'ReportController@gradeReport'));

	Route::get('gradeReportExcel', array('as' => 'gradeReportExcel', 'uses' => 'ReportController@gradeReportExcel'));

	Route::get('quizReport', array('as' => 'quizReport', 'uses' => 'ReportController@quizReport'));
	
	Route::get('doublePartialQuizReport/{quizId}/{sId}/{online}', array('as' => 'doublePartialQuizReport', 'uses' => 'ReportController@doublePartialQuizReport'));

	Route::get('quizDoublePartialReportExcel/{quizId}/{sId}/{online}', array('as' => 'quizDoublePartialReportExcel', 'uses' => 'ReportController@quizDoublePartialReportExcel'));
	
	Route::get('quizPartialReportExcel/{quizId}', array('as' => 'quizPartialReportExcel', 'uses' => 'ReportController@quizPartialReportExcel'));

	Route::get('quizReportExcel', array('as' => 'quizReportExcel', 'uses' => 'ReportController@quizReportExcel'));

	Route::get('partialQuizReport/{quizId}', array('as' => 'partialQuizReport', 'uses' => 'ReportController@partialQuizReport'));

	Route::get('moneyReport', array('as' => 'moneyReport', 'uses' => 'ReportController@moneyReport'));

	Route::post('addBatchQToQ', array('as' => 'addBatchQToQ', 'uses' => 'QuizController@addBatchQToQ'));

});

Route::group(array('middleware' => ['nothing', 'auth', 'adminLevel']), function () {

	Route::post('testMethod', array('as' => 'testMethod', 'uses' => 'TestController@methodTest'));
	
	Route::get('test/{c}', array('as' => 'test', 'uses' => 'TestController@start'));
	
	Route::get('calenderManagement', array('as' => 'calenderManagement', 'uses' => 'CalenderController@calender'));

	Route::post('addEvent', array('as' => 'addEvent', 'uses' => 'CalenderController@addEvent'));

	Route::post('deleteEvent', array('as' => 'deleteEvent', 'uses' => 'CalenderController@deleteEvent'));

	Route::post('calcTaraz', array('as' => 'calcTaraz', 'uses' => 'AjaxController@calcTaraz'));

	Route::post('getRanksMoneyOfQuiz', array('as' => 'getRanksMoneyOfQuiz', 'uses' => 'TarazController@getRanksMoneyOfQuiz'));
});

Route::group(array('middleware' => ['nothing', 'auth', 'superAdminLevel']), function () {
	Route::get('admins', array('as' => 'admins', 'uses' => 'UserController@admins'));
});

Route::group(array('middleware' => ['nothing', 'auth', 'adminLevel']), function () {

	Route::get('pointsConfig', array('as' => 'pointsConfig', 'uses' => 'ConfigController@pointsConfig'));

	Route::post('pointsConfig', array('as' => 'pointsConfig', 'uses' => 'ConfigController@doPointsConfig'));

	Route::get('adviserQuestions', array('as' => 'adviserQuestions', 'uses' => 'AdminController@adviserQuestions'));

	Route::post('deleteAdviserQuestion', array('as' => 'deleteAdviserQuestion', 'uses' => 'AdminController@deleteAdviserQuestion'));

	Route::post('addAdviserQuestion', array('as' => 'addAdviserQuestion', 'uses' => 'AdminController@addAdviserQuestion'));

	Route::post('editAdviserQuestion', array('as' => 'editAdviserQuestion', 'uses' => 'AdminController@editAdviserQuestion'));

});

Route::group(array('middleware' => ['nothing', 'auth', 'adminLevel']), function () {

	Route::get('config', array('as' => 'config', 'uses' => 'ConfigController@config'));

	Route::post('config', array('as' => 'config', 'uses' => 'ConfigController@doConfig'));

});

Route::group(array('middleware' => ['nothing', 'auth', 'adminLevel']), function () {

	Route::get('defineKarname', array('as' => 'defineKarname', 'uses' => 'ConfigController@defineKarname'));

	Route::post('defineKarname', array('as' => 'defineKarname', 'uses' => 'ConfigController@doDefineKarname'));

});

Route::group(array('middleware' => ['nothing', 'auth', 'namayandeLevel']), function () {

	Route::post('getStateCity', array('as' => 'getStateCity', 'uses' => 'AjaxController@getStateCity'));

	Route::post('editSchool', array('as' => 'editSchool', 'uses' => 'UserController@editSchool'));

	Route::get('namayandeStudent', array('as' => 'namayandeStudent', 'uses' => 'ReportController@namayandeStudent'));

	Route::get('namayandeSchool', array('as' => 'namayandeSchool', 'uses' => 'ReportController@namayandeSchool'));

	Route::post('addSchool', array('as' => 'addSchool', 'uses' => 'UserController@doAddSchool'));

	Route::get('addSchool', array('as' => 'addSchool', 'uses' => 'UserController@addSchool'));

	Route::post('removeSchool', array('as' => 'removeSchool', 'uses' => 'UserController@removeSchool'));

	Route::post('changeSchoolCode', array('as' => 'changeSchoolCode', 'uses' => 'UserController@changeSchoolCode'));
	
});

Route::group(array('middleware' => ['nothing', 'auth', 'phone', 'studentLevel']), function () {

	Route::get('myAdviser', array('as' => 'myAdviser', 'uses' => 'UserController@myAdviser'));
	
	Route::post('setAsMyAdviser', array('as' => 'setAsMyAdviser', 'uses' => 'UserController@setAsMyAdviser'));

	Route::post('submitRate', array('as' => 'submitRate', 'uses' => 'UserController@submitRate'));

	Route::get('showInboxSpecificMsgs/{selectedUser}', array('as' => 'showInboxSpecificMsgs', 'uses' => 'MessageController@showInboxSpecificMsgs'));

	Route::get('showOutboxSpecificMsgs/{selectedUser}', array('as' => 'showOutboxSpecificMsgs', 'uses' => 'MessageController@showOutboxSpecificMsgs'));

});

Route::group(array('middleware' => ['nothing', 'auth', 'phone']), function () {

	Route::get('questionList/subject/{sId}', array('as' => 'questionListSubject', 'uses' => 'QuestionController@showQuestionListSubject'));

	Route::post('questionInfo', array('as' => 'questionInfo', 'uses' => 'QuestionController@questionInfo'));

	Route::post('likeQuestion', array('as' => 'likeQuestion', 'uses' => 'QuestionController@likeQuestion'));

	Route::get('questionList/quiz/{quizMode}/{qId}', array('as' => 'questionListQuiz', 'uses' => 'QuestionController@showQuestionListQuiz'));
	
	Route::post('askQuestion', array('as' => 'askQuestion', 'uses' => 'QuestionController@askQuestion'));

	Route::post('getQuestions', array('as' => 'getQuestions', 'uses' => 'QuestionController@getQuestions'));

	Route::post('showAllAns', array('as' => 'showAllAns', 'uses' => 'QuestionController@showAllAns'));

	Route::post('sendAns', array('as' => 'sendAns', 'uses' => 'QuestionController@sendAns'));

	Route::post('opOnQuestion', array('as' => 'opOnQuestion', 'uses' => 'QuestionController@opOnQuestion'));

	Route::get('printKarname/{quizId}', array('as' => 'printKarname', 'uses' => 'ReportController@printKarname'));
	
});

Route::group(array('middleware' => ['nothing', 'auth', 'phone', 'quiz']), function () {

	Route::get('doQuiz/{quizId}', array('as' => 'doQuiz', 'uses' => 'QuizController@doQuiz'));

	Route::get('doSelfQuiz/{quizId}', array('as' => 'doSelfQuiz', 'uses' => 'QuizController@doSelfQuiz'));

	Route::get('doRegularQuiz/{quizId}', array('as' => 'doRegularQuiz', 'uses' => 'QuizController@doRegularQuiz'));

	Route::get('showQuizWithOutTime/{quizId}/{quizMode}', array('as' => 'showQuizWithOutTime', 'uses' => 'QuizController@showQuizWithOutTime'));

	Route::any('seeResult', array('as' => 'seeResult', 'uses' => 'QuizController@seeResult'));

	Route::post('getQuizLessons', array('as' => 'getQuizLessons', 'uses' => 'AjaxController@getQuizLessons'));

});

Route::group(array('middleware' => ['nothing', 'auth', 'phone']), function () {

	Route::post('getLessons', array('as' => 'getLessons', 'uses' => 'ContentController@getLessons'));

	Route::post('getSubjects', array('as' => 'getSubjects', 'uses' => 'ContentController@getSubjects'));

	Route::post('getSubjects2', array('as' => 'getSubjects2', 'uses' => 'ContentController@getSubjects2'));

	Route::get('discussion/{qId}', array('as' => 'discussion', 'uses' => 'QuestionController@discussion'));

	Route::get('showSubjects', array('as' => 'showSubjects', 'uses' => 'ContentController@showSubjects'));

	Route::post('getCities', array('as' => 'getCities', 'uses' => 'StateController@getCities'));

	Route::get('chargeAccountWithStatus/{status}', array('as' => 'chargeAccountWithStatus', 'uses' => 'HomeController@chargeAccount'));

	Route::get('chargeAccount', array('as' => 'chargeAccount', 'uses' => 'HomeController@chargeAccount'));

	Route::post('doChargeAccount', array('as' => 'doChargeAccount', 'uses' => 'QuizController@doChargeAccount'));

	Route::post('chargeAccountPost/{additional}', array('as' => 'chargeAccountPost', 'uses' => 'QuizController@chargeAccountPost'));

	Route::post('chargeWithGiftCard', array('as' => 'chargeWithGiftCard', 'uses' => 'HomeController@chargeWithGiftCard'));

	Route::get('quizRegistry', array('as' => 'quizRegistry', 'uses' => 'QuizController@quizRegistry'));

	Route::get('regularQuizRegistry', array('as' => 'regularQuizRegistry', 'uses' => 'QuizController@regularQuizRegistry'));

	Route::get('quizEntry', array('as' => 'quizEntry', 'uses' => 'QuizController@quizEntry'));
	
	Route::get('myQuizes', array('as' => 'myQuizes', 'uses' => 'QuizController@myQuizes'));
	
	Route::get('doQuizRegistry/{quizId}/{mode}', array('as' => 'doQuizRegistry', 'uses' => 'QuizController@doQuizRegistry'));

	Route::get('doQuizRegistry/{quizId}/{mode}/{status}', array('as' => 'doQuizRegistryWithStatus', 'uses' => 'QuizController@doQuizRegistry'));

	Route::post('doQuizRegistryFromAccount/{mode}', array('as' => 'doQuizRegistryFromAccount', 'uses' => 'QuizController@doQuizRegistryFromAccount'));

	Route::post('paymentQuiz/{mode}', array('as' => 'paymentQuiz', 'uses' => 'QuizController@paymentQuiz'));

	Route::post('paymentPostQuiz/{quizId}/{mode}', array('as' => 'paymentPostQuiz', 'uses' => 'QuizController@paymentPostQuiz'));

	Route::post('paymentPostSelfQuiz/{quizId}', array('as' => 'paymentPostSelfQuiz', 'uses' => 'QuestionController@paymentPostSelfQuiz'));

	Route::post('useGiftCard', array('as' => 'useGiftCard', 'uses' => 'QuizController@useGiftCard'));

	Route::post('checkGiftCard', array('as' => 'checkGiftCard', 'uses' => 'QuizController@checkGiftCard'));

	Route::post('submitAnsSystemQuiz', array('as' => 'submitAnsSystemQuiz', 'uses' => 'QuizController@submitAnsSystemQuiz'));

	Route::post('submitAnsRegularQuiz', array('as' => 'submitAnsRegularQuiz', 'uses' => 'QuizController@submitAnsRegularQuiz'));

	Route::post('submitAnsSelfQuiz', array('as' => 'submitAnsSelfQuiz', 'uses' => 'QuizController@submitAnsSelfQuiz'));

	Route::post('getOnlineStanding', array('as' => 'getOnlineStanding', 'uses' => 'QuizController@getOnlineStanding'));

	Route::get('createCustomQuiz', array('as' => 'createCustomQuiz', 'uses' => 'QuizController@createCustomQuiz'));

	Route::post('preTransactionQuestion', array('as' => 'preTransactionQuestion', 'uses' => 'QuestionController@preTransactionQuestion'));

	Route::post('transactionQuestion', array('as' => 'transactionQuestion', 'uses' => 'QuestionController@transactionQuestion'));

	Route::get('preTransactionBuyQuestion/{quizId}', array('as' => 'preTransactionBuyQuestion', 'uses' => 'QuestionController@preTransactionBuyQuestion'));

	Route::get('doCreateCustomQuizWithStatus/{quizId}/{status}', array('as' => 'doCreateCustomQuizWithStatus', 'uses' => 'QuestionController@preTransactionBuyQuestion'));

	Route::post('doCreateCustomQuizFromAccount', array('as' => 'doCreateCustomQuizFromAccount', 'uses' => 'QuestionController@doCreateCustomQuizFromAccount'));

	Route::post('doCreateCustomQuizOnline', array('as' => 'doCreateCustomQuizOnline', 'uses' => 'QuestionController@doCreateCustomQuizOnline'));

	Route::post('getSuggestionQuestionsCount', array('as' => 'getSuggestionQuestionsCount', 'uses' => 'QuizController@getSuggestionQuestionsCount'));

	Route::get('myActivities', array('as' => 'myActivities', 'uses' => 'ReportController@myActivities'));

});

Route::group(array('middleware' => ['nothing', 'auth', 'adminLevel']), function () {

	Route::get('operators_2', array('as' => 'operators_2', 'uses' => 'UserController@operators_2'));

	Route::get('addOperator2', array('as' => 'addOperator2', 'uses' => 'UserController@addOperator2'));

	Route::post('addOperator2', array('as' => 'addOperator2', 'uses' => 'UserController@doAddOperator2'));

	Route::get('operators_1', array('as' => 'operators_1', 'uses' => 'UserController@operators_1'));

	Route::get('addOperator1', array('as' => 'addOperator1', 'uses' => 'UserController@addOperator1'));

	Route::post('addOperator1', array('as' => 'addOperator1', 'uses' => 'UserController@doAddOperator1'));

	Route::get('controllers', array('as' => 'controllers', 'uses' => 'UserController@controllers'));

	Route::get('addControllers', array('as' => 'addControllers', 'uses' => 'UserController@addControllers'));

	Route::post('addControllers', array('as' => 'addControllers', 'uses' => 'UserController@doAddControllers'));

	Route::get('advisers', array('as' => 'advisers', 'uses' => 'UserController@advisers'));

	Route::get('namayandeha', array('as' => 'namayandeha', 'uses' => 'UserController@namayandeha'));

	Route::post('addNamayande', array('as' => 'addNamayande', 'uses' => 'UserController@doAddNamayande'));

	Route::get('addNamayande', array('as' => 'addNamayande', 'uses' => 'UserController@addNamayande'));

	Route::get('schools', array('as' => 'schools', 'uses' => 'UserController@schools'));

	Route::post('confirmAdviser', array('as' => 'confirmAdviser', 'uses' => 'UserController@confirmAdviser'));

	Route::post('removeUser/{mode}', array('as' => 'removeUser', 'uses' => 'UserController@removeUser'));

	Route::get('assignControllers', array('as' => 'assignControllers', 'uses' => 'UserController@assignControllers'));

	Route::post('doAssignToController', array('as' => 'doAssignToController', 'uses' => 'UserController@doAssignToController'));

	Route::post('getControllerLevelsDir', array('as' => 'getControllerLevelsDir', 'uses' => 'UserController@getControllerLevelsDir'));

});

Route::group(array('middleware' => ['nothing', 'auth', 'adminLevel']), function () {

	Route::get('states', array('as' => 'states', 'uses' => 'StateController@states'));

	Route::get('cities', array('as' => 'cities', 'uses' => 'StateController@cities'));

	Route::post('addCity', array('as' => 'addCity', 'uses' => 'StateController@addCity'));

	Route::post('deleteCity', array('as' => 'deleteCity', 'uses' => 'StateController@deleteCity'));

	Route::post('addCityBatch', array('as' => 'addCityBatch', 'uses' => 'StateController@addCityBatch'));

	Route::post('addState', array('as' => 'addState', 'uses' => 'StateController@addState'));

	Route::post('deleteState', array('as' => 'deleteState', 'uses' => 'StateController@deleteState'));

	Route::post('addStateBatch', array('as' => 'addStateBatch', 'uses' => 'StateController@addStateBatch'));

	Route::post('getStates', array('as' => 'getStates', 'uses' => 'StateController@getStates'));

});

Route::group(array('middleware' => ['nothing', 'auth', 'adminLevel']), function () {

	Route::get('grades', array('as' => 'grades', 'uses' => 'ContentController@grades'));

	Route::post('addGrade', array('as' => 'addGrade', 'uses' => 'ContentController@addGrade'));

	Route::post('deleteGrade', array('as' => 'deleteGrade', 'uses' => 'ContentController@deleteGrade'));

	Route::post('editGrade', array('as' => 'editGrade', 'uses' => 'ContentController@editGrade'));

	Route::get('lessons', array('as' => 'lessons', 'uses' => 'ContentController@lessons'));

	Route::post('addLesson', array('as' => 'addLesson', 'uses' => 'ContentController@addLesson'));

	Route::post('addLessonBatch', array('as' => 'addLessonBatch', 'uses' => 'ContentController@addLessonBatch'));

	Route::post('deleteLesson', array('as' => 'deleteLesson', 'uses' => 'ContentController@deleteLesson'));

	Route::post('editLesson', array('as' => 'editLesson', 'uses' => 'ContentController@editLesson'));

	Route::get('subjects', array('as' => 'subjects', 'uses' => 'ContentController@subjects'));

	Route::post('addSubject', array('as' => 'addSubject', 'uses' => 'ContentController@addSubject'));

	Route::post('addSubjectBatch', array('as' => 'addSubjectBatch', 'uses' => 'ContentController@addSubjectBatch'));

	Route::post('deleteSubject', array('as' => 'deleteSubject', 'uses' => 'ContentController@deleteSubject'));

	Route::post('editSubject', array('as' => 'editSubject', 'uses' => 'ContentController@editSubject'));
});

Route::group(array('middleware' => ['nothing', 'auth', 'adminLevel']), function () {

	Route::get('addQuestion', array('as' => 'addQuestion', 'uses' => 'QuestionController@addQuestion'));

	Route::get('totalQuestions/{qId?}', array('as' => 'totalQuestions', 'uses' => 'QuestionController@totalQuestions'));

	Route::post('getTotalQuestions', array('as' => 'getTotalQuestions', 'uses' => 'QuestionController@getTotalQuestions'));

	Route::post('doAddQuestionPic', array('as' => 'doAddQuestionPic', 'uses' => 'QuestionController@doAddQuestionPic'));

	Route::post('doChangeQuestionPic/{qId}', array('as' => 'doChangeQuestionPic', 'uses' => 'QuestionController@doChangeQuestionPic'));
	
	Route::post('addAnsToQuestion/{qId}', array('as' => 'addAnsToQuestion', 'uses' => 'QuestionController@addAnsToQuestion'));

	Route::post('doChangeAnsPic/{qId}', array('as' => 'doChangeAnsPic', 'uses' => 'QuestionController@doChangeAnsPic'));

	Route::post('addDetailToQuestion/{qId}', array('as' => 'addDetailToQuestion', 'uses' => 'QuestionController@addDetailToQuestion'));
	
	Route::post('addQuestionBatch', array('as' => 'addQuestionBatch', 'uses' => 'QuestionController@addQuestionBatch'));

	Route::get('tags', array('as' => 'tags', 'uses' => 'TagController@tags'));

	Route::post('addTag', array('as' => 'addTag', 'uses' => 'TagController@addTag'));

	Route::post('editTag', array('as' => 'editTag', 'uses' => 'TagController@editTag'));

	Route::post('deleteTag', array('as' => 'deleteTag', 'uses' => 'TagController@deleteTag'));
});

Route::group(array('middleware' => ['nothing', 'auth', 'controllerLevel']), function () {

	Route::post('getGrades', array('as' => 'getGrades', 'uses' => 'ContentController@getGrades'));
	
	Route::get('unConfirmedQuestions', array('as' => 'unConfirmedQuestions', 'uses' => 'QuestionController@unConfirmedQuestions'));

	Route::post('getLessonsController', array('as' => 'getLessonsController', 'uses' => 'QuestionController@getLessonsController'));

	Route::post('getControllerQuestions', array('as' => 'getControllerQuestions', 'uses' => 'QuestionController@getControllerQuestions'));

	Route::post('getQuestionSubjects', array('as' => 'getQuestionSubjects', 'uses' => 'QuestionController@getQuestionSubjects'));

	Route::post('editDetailQuestion/{qId}', array('as' => 'editDetailQuestion', 'uses' => 'QuestionController@editDetailQuestion'));

	Route::post('rejectQuestion', array('as' => 'rejectQuestion', 'uses' => 'QuestionController@rejectQuestion'));
	
});

Route::group(array('middleware' => ['nothing', 'auth', 'adminLevel']), function () {

	Route::any('quizStatus', array('as' => 'quizStatus', 'uses' => 'QuizController@quizStatus'));

	Route::get('onlineQuizes', array('as' => 'onlineQuizes', 'uses' => 'QuizController@onlineQuizes'));

	Route::get('regularQuizes', array('as' => 'regularQuizes', 'uses' => 'QuizController@regularQuizes'));

	Route::post('elseQuiz', array('as' => 'elseQuiz', 'uses' => 'QuizController@elseQuiz'));

	Route::post('elseSystemQuiz', array('as' => 'elseSystemQuiz', 'uses' => 'QuizController@elseSystemQuiz'));

	Route::post('deleteQFromQ', array('as' => 'deleteQFromQ', 'uses' => 'QuizController@deleteQFromQ'));

	Route::post('deleteDeletedQFromQ', array('as' => 'deleteDeletedQFromQ', 'uses' => 'QuizController@deleteDeletedQFromQ'));

	Route::post('deleteDeletedQFromSystemQ', array('as' => 'deleteDeletedQFromSystemQ', 'uses' => 'QuizController@deleteDeletedQFromSystemQ'));

	Route::post('changeRankingCount', array('as' => 'changeRankingCount', 'uses' => 'QuizController@changeRankingCount'));

	Route::post('addQuiz', array('as' => 'addQuiz', 'uses' => 'QuizController@addQuiz'));

	Route::post('addQuizRegular', array('as' => 'addQuizRegular', 'uses' => 'QuizController@addQuizRegular'));

	Route::post('getQuizQuestions', array('as' => 'getQuizQuestions', 'uses' => 'QuizController@getQuizQuestions'));

	Route::post('getRegularQuizQuestions', array('as' => 'getRegularQuizQuestions', 'uses' => 'QuizController@getRegularQuizQuestions'));

	Route::post('getSubjectQuestions', array('as' => 'getSubjectQuestions', 'uses' => 'QuizController@getSubjectQuestions'));

	Route::post('fetchQuestionByOrganizationId', array('as' => 'fetchQuestionByOrganizationId', 'uses' => 'QuizController@fetchQuestionByOrganizationId'));

	Route::post('deleteQuiz', array('as' => 'deleteQuiz', 'uses' => 'QuizController@deleteQuiz'));

	Route::post('deleteRegularQuiz', array('as' => 'deleteRegularQuiz', 'uses' => 'QuizController@deleteRegularQuiz'));

	Route::post('doAddQuestionToQuiz', array('as' => 'doAddQuestionToQuiz', 'uses' => 'QuizController@doAddQuestionToQuiz'));

	Route::post('doAddQuestionToRegularQuiz', array('as' => 'doAddQuestionToRegularQuiz', 'uses' => 'QuizController@doAddQuestionToRegularQuiz'));

	Route::post('removeQFromSystemQ', array('as' => 'removeQFromSystemQ', 'uses' => 'QuizController@removeQFromSystemQ'));

	Route::post('removeQFromRegularQ', array('as' => 'removeQFromRegularQ', 'uses' => 'QuizController@removeQFromRegularQ'));

	Route::post('getSystemQuizDetails', array('as' => 'getSystemQuizDetails', 'uses' => 'QuizController@getSystemQuizDetails'));

	Route::post('getRegularQuizDetails', array('as' => 'getRegularQuizDetails', 'uses' => 'QuizController@getRegularQuizDetails'));

	Route::post('editQuiz', array('as' => 'editQuiz', 'uses' => 'QuizController@editQuiz'));

	Route::post('editQuizRegular', array('as' => 'editQuizRegular', 'uses' => 'QuizController@editQuizRegular'));

	Route::post('changeMarkQ', array('as' => 'changeMarkQ', 'uses' => 'QuizController@changeMarkQ'));

	Route::post('changeQNo', array('as' => 'changeQNo', 'uses' => 'QuizController@changeQNo'));

	Route::post('changeQNoRegularQuiz', array('as' => 'changeQNoRegularQuiz', 'uses' => 'QuizController@changeQNoRegularQuiz'));

	Route::post('createTarazTable', array('as' => 'createTarazTable', 'uses' => 'TarazController@createTarazTable'));

	Route::get('createTarazTable', array('as' => 'createTarazTable', 'uses' => 'TarazController@seeQuizes'));

	Route::get('createTarazTable/{mode}', array('as' => 'createTarazTable2', 'uses' => 'TarazController@seeQuizes'));

	Route::get('deleteTarazTable', array('as' => 'deleteTarazTable', 'uses' => 'TarazController@seeQuizes2'));

	Route::post('deleteTarazTable', array('as' => 'deleteTarazTable', 'uses' => 'TarazController@deleteTarazTable'));

	Route::post('getEnherafMeyar', array('as' => 'getEnherafMeyar', 'uses' => 'AjaxController@getEnherafMeyar'));
	
	Route::get('finishQuiz', array('as' => 'finishQuiz', 'uses' => 'QuizController@finishQuiz'));

	Route::post('doFinishQuiz', array('as' => 'doFinishQuiz', 'uses' => 'QuizController@doFinishQuiz'));

});

Route::group(array('middleware' => ['nothing', 'auth', 'operator2Level']), function () {

	Route::get('unConfirmedDiscussionQ', array('as' => 'unConfirmedDiscussionQ', 'uses' => 'QuestionController@unConfirmedDiscussionQ'));

	Route::get('unConfirmedDiscussionAns', array('as' => 'unConfirmedDiscussionAns', 'uses' => 'QuestionController@unConfirmedDiscussionAns'));

	Route::post('getUnConfirmedQuestions', array('as' => 'getUnConfirmedQuestions', 'uses' => 'QuestionController@getUnConfirmedQuestions'));

	Route::post('getUnConfirmedAnses', array('as' => 'getUnConfirmedAnses', 'uses' => 'QuestionController@getUnConfirmedAnses'));
	
	Route::post('getConfirmedQuestions', array('as' => 'getConfirmedQuestions', 'uses' => 'QuestionController@getConfirmedQuestions'));

	Route::post('getConfirmedAnses', array('as' => 'getConfirmedAnses', 'uses' => 'QuestionController@getConfirmedAnses'));

	Route::post('getConfirmedAndUnConfirmedQuestions', array('as' => 'getConfirmedAndUnConfirmedQuestions', 'uses' => 'QuestionController@getConfirmedAndUnConfirmedQuestions'));

	Route::post('getConfirmedAndUnConfirmedAnses', array('as' => 'getConfirmedAndUnConfirmedAnses', 'uses' => 'QuestionController@getConfirmedAndUnConfirmedAnses'));

	Route::post('changeQuestionStatus', array('as' => 'changeQuestionStatus', 'uses' => 'QuestionController@changeQuestionStatus'));

	Route::get('controlMsg', array('as' => 'controlMsg', 'uses' => 'MessageController@controlMsg'));

	Route::post('acceptedMsgs', array('as' => 'acceptedMsgs', 'uses' => 'MessageController@acceptedMsgs'));

	Route::post('rejectedMsgs', array('as' => 'rejectedMsgs', 'uses' => 'MessageController@rejectedMsgs'));

	Route::post('pendingMsgs', array('as' => 'pendingMsgs', 'uses' => 'MessageController@pendingMsgs'));

	Route::post('acceptMsgs', array('as' => 'acceptMsgs', 'uses' => 'MessageController@acceptMsgs'));

	Route::post('rejectMsgs', array('as' => 'rejectMsgs', 'uses' => 'MessageController@rejectMsgs'));
	
});

Route::post('getEvents', array('as' => 'getEvents', 'uses' => 'CalenderController@getEvents'));

Auth::routes();
