<?php

namespace App\Http\Controllers;

use App\Http\Resources\SurveyAnswerResource;
use App\Http\Resources\SurveyResourceDashboard;
use App\Models\Survey;
use App\Models\SurveyAnswer;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        // Tổng số Khảo sát
        $total  = Survey::query()->where('user_id', $user->id)->count();
        // Khảo sát gần nhất
        $latest = Survey::query()->where('user_id', $user->id)->latest('created_at')->first();

        // Tổng số câu trả lời
        $totalAnswers  = SurveyAnswer::query()
            ->join('surveys', 'survey_answers.survey_id', '=', 'surveys.id')
            ->where('surveys.user_id', $user->id)
            ->count();
        // 5 câu trả lời gần nhất
        $latestAnswers = SurveyAnswer::query()
            ->join('surveys', 'survey_answers.survey_id', '=', 'surveys.id')
            ->where('surveys.user_id', $user->id)
            ->orderBy('end_date', 'DESC')
            ->limit(5)
            ->getModels('survey_answers.*');

        return [
            'totalSurveys'  => $total,
            'latestSurvey'  => $latest ? new SurveyResourceDashboard($latest) : null,
            'totalAnswers'  => $totalAnswers,
            'latestAnswers' => SurveyAnswerResource::collection($latestAnswers),
        ];

    }
}
