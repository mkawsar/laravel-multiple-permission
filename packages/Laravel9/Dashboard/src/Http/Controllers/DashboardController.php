<?php

namespace Laravel9\Dashboard\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel9\Dashboard\Http\Resources\SurveyResourceDashboard;
use Laravel9\Survey\Http\Resources\SurveyAnswerResource;
use Laravel9\Survey\Models\Survey;
use Laravel9\Survey\Models\SurveyAnswer;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        // Total number of survey
        $total = Survey::query()->where('user_id', '=', $user->id)->count();

        // Latest Survey
        $latest = Survey::query()->where('user_id', '=', $user->id)->latest('created_at')->first();

        // Total Number of answers
        $totalAnswers = SurveyAnswer::query()
            ->join('surveys', 'survey_answers.survey_id', '=', 'surveys.id')
            ->where('surveys.user_id', '=', $user->id)
            ->orderBy('end_date', 'DESC')
            ->limit(5)
            ->getModels('survey_answers.*');

        // Latest 5 answer
        $latestAnswers = SurveyAnswer::query()
            ->join('surveys', 'survey_answers.survey_id', '=', 'surveys.id')
            ->where('surveys.user_id', $user->id)
            ->orderBy('end_date', 'DESC')
            ->limit(5)
            ->getModels('survey_answers.*');

        return [
            'total_surveys' => $total,
            'latest_survey' => $latest ? new SurveyResourceDashboard($latest) : null,
            'total_answers' => $totalAnswers,
            'latest_answers' => SurveyAnswerResource::collection($latestAnswers)
        ];
    }
}
