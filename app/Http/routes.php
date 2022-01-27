<?php

use App\Team;
use App\Http\RoutesTrait;

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

// Route::get('/fundamentals', ['as' => 'fundamentals', function () {
//  $project_id = 1;
//     $losses = App\Losses::where('project_id', $project_id)->get();
//     return view('pages.home', compact("losses", "project_id"));
// }]);

Route::get('/', ['as' => 'home', function () {
    return view('home');
}]);

Route::match(array('GET', 'POST'), '{slug}/stepone', ['as' => 'stepone', function ($slug) {
    if (Auth::check()) {
        $project_id = App\Project::select("id")->where('URL', $slug)->first()->id;
        $project_type = App\Project::select("type")->where('URL', $slug)->first()->type;
        $project_name = App\Project::select("name")->where('URL', $slug)->first()->name;
        $losses = App\Losses::where('project_id', $project_id)->orderBy('id')->get();
        $hazards = App\Hazards::where('project_id', $project_id)->orderBy('id')->get();
        $belongsToProject = Team::where('project_id', $project_id)->where('user_id', Auth::user()->id)->first() != null;
        $loss_map = mapLoss($losses);
        $hazard_map = mapHazard($project_id);
        $sysconstraints_map = mapConstraints($project_id);
        $goals_map = mapGoals($project_id);
        $assumptions_map = mapAssumptions($project_id);
        return view(
            'pages.stepone',
            compact(
                "losses",
                "hazards",
                "project_id",
                "project_name",
                "project_type",
                "slug",
                "loss_map",
                "hazard_map",
                "goals_map",
                "assumptions_map",
                "sysconstraints_map"
            )
        );
    }
}]);

Route::match(array('GET', 'POST'), '{slug}/steptwo', ['as' => 'steptwo', function ($slug) {
    if (Auth::check()) {
        $project_id = App\Project::select("id")->where('URL', $slug)->first()->id;
        $project_type = App\Project::select("type")->where('URL', $slug)->first()->type;
        $project_name = App\Project::select("name")->where('URL', $slug)->first()->name;
        return view('pages.steptwo', compact("project_id", "project_name", "project_type", "slug"));
    }
}]);

Route::match(array('GET', 'POST'), '{slug}/stepthree', ['as' => 'stepthree', function ($slug) {
    if (Auth::check()) {
        $project_id = App\Project::select("id")->where('URL', $slug)->first()->id;
        $project_type = App\Project::select("type")->where('URL', $slug)->first()->type;
        $belongsToProject = Team::where('project_id', $project_id)->where('user_id', Auth::user()->id)->first() != null;
        $hazard_map = mapHazard($project_id);
        if ($belongsToProject) {
            return view('pages.stepthree', compact("project_id", "project_type", "slug", "hazard_map"));
        }
    } else {
        return view('home');
    }
}]);

Route::match(array('GET', 'POST'), '{slug}/stepfour', ['as' => 'stepfour', function ($slug) {
    if (Auth::check()) {
        $project_id = App\Project::select("id")->where('URL', $slug)->first()->id;
        $project_type = App\Project::select("type")->where('URL', $slug)->first()->type;
        $belongsToProject = Team::where('project_id', $project_id)->where('user_id', Auth::user()->id)->first() != null;
        if ($belongsToProject) {
            return view('pages.stepfour', compact("project_id", "project_type", "slug"));
        }
    } else {
        return view('home');
    }
}]);

Route::get('/login', ['as' => 'login', function () {
    return view('auth.login');
}]);

Route::get('/register', ['as' => 'login', function () {
    return view('auth.register');
}]);

Route::get('/projects', ['as' => 'projects', function () {
    return view('pages.project');
}]);

Route::post('/getteam', 'TeamController@get');

Route::post('/addproject', 'ProjectController@add');
Route::post('/editproject', 'ProjectController@edit');
Route::post('/deleteproject', 'ProjectController@delete');

Route::post('/addsystemgoal', 'SystemGoalController@add');
Route::post('/editsystemgoal', 'SystemGoalController@edit');
Route::post('/deletesystemgoal', 'SystemGoalController@delete');
Route::post('/textsystemgoal', 'SystemGoalController@getText');

Route::post('/addassumption', 'AssumptionsController@add');
Route::post('/editassumption', 'AssumptionsController@edit');
Route::post('/deleteassumption', 'AssumptionsController@delete');
Route::post('/textassumption', 'AssumptionsController@getText');

Route::post('/addloss', 'LossController@add');
Route::post('/editloss', 'LossController@edit');
Route::post('/deleteloss', 'LossController@delete');
Route::post('/textloss', 'LossController@getText');

Route::post('/addactuator', 'ActuatorController@add');
Route::post('/editactuator', 'ActuatorController@edit');
Route::post('/deleteactuator', 'ActuatorController@delete');

Route::post('/addcontroller', 'ControllerController@add');
Route::post('/editcontroller', 'ControllerController@edit');
Route::post('/deletecontroller', 'ControllerController@delete');

Route::post('/addcontrolledprocess', 'ControlledProcessController@add');
Route::post('/editcontrolledprocess', 'ControlledProcessController@edit');
Route::post('/deletecontrolledprocess', 'ControlledProcessController@delete');

Route::post('/addsensor', 'SensorController@add');
Route::post('/editsensor', 'SensorController@edit');
Route::post('/deletesensor', 'SensorController@delete');

Route::post('/addmission', 'MissionController@add');
Route::post('/editmission', 'MissionController@edit');
Route::post('/deletemission', 'MissionController@delete');

Route::post('/addcontrolaction', 'ControlActionController@add');
Route::post('/editcontrolaction', 'ControlActionController@edit');
Route::post('/deletecontrolaction', 'ControlActionController@delete');

Route::post('/addhazard', 'HazardController@add');
Route::post('/edithazard', 'HazardController@edit');
Route::post('/deletehazard', 'HazardController@delete');
Route::post('/texthazard', 'HazardController@getText');
Route::post('/deletehazardLossAssociation', 'HazardController@deleteAssociatedLoss');

Route::post('/addvariable', 'VariableController@add');
Route::post('/editvariable', 'VariableController@edit');
Route::post('/deletevariable', 'VariableController@delete');

Route::post('/addsystemsafetyconstraint', 'SystemSafetyConstraintController@add');
Route::post('/editsystemsafetyconstraint', 'SystemSafetyConstraintController@edit');
Route::post('/deletesystemsafetyconstraint', 'SystemSafetyConstraintController@delete');
Route::post('/textsystemsafetyconstraint', 'SystemSafetyConstraintController@getText');
Route::post(
    '/deletesystemSafetyConstraintHazardAssociation',
    'SystemSafetyConstraintController@deleteAssociatedHazard'
);

Route::post('/addconnections', 'ConnectionController@add');
Route::post('/deleteconnections', 'ConnectionController@delete');

Route::post('/addstate', 'StateController@add');
Route::post('/deletestate', 'StateController@delete');

Route::post('/adduca', 'SafetyConstraintsController@add');
Route::post('/edituca', 'SafetyConstraintsController@edit');
Route::post('/scdata', 'SafetyConstraintsController@getSafetyConstraint');
Route::post('/editucaByRule', 'SafetyConstraintsController@editByRule');
Route::post('/refreshUcasByRule', 'SafetyConstraintsController@refreshUcasWithRules');
Route::post('/deleteuca', 'SafetyConstraintsController@delete');
Route::post('/deletealluca', 'SafetyConstraintsController@deleteAll');
// Route::post('/addsuggesteduca', 'SystemSafetyConstraintController@save');

Route::post('/addtuple', 'CausalAnalysisController@add');
Route::post('/edittuple', 'CausalAnalysisController@edit');
Route::post('/deletetuple', 'CausalAnalysisController@delete');
Route::post('/deletealltuple', 'CausalAnalysisController@deleteAll');


Route::post('/deletelossassociated', 'LossHazardController@delete');

Route::post('/addrule', 'RuleController@add');
Route::post('/editrule', 'RuleController@edit');
Route::post('/deleterule', 'RuleController@delete');
Route::post('/deleteallrules', 'RuleController@deleteAll');

Route::post('/savecontexttable', 'ContextTableController@save');
Route::post('/deletecontexttable', 'ContextTableController@delete');
Route::post('/generateUCA', 'ContextTableController@generateUCA');

Route::auth();

Route::get('/home', 'HomeController@index');

Route::get('/tutorial', function () {
    return response()->file('files/WebSTAMP Full Tutorial.pdf');
});
