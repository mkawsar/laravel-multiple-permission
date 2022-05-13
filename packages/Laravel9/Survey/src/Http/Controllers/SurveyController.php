<?php

namespace Laravel9\Survey\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Laravel9\Survey\Http\Requests\StoreSurveyRequest;
use Laravel9\Survey\Http\Resources\SurveyResource;
use Laravel9\Survey\Models\Survey;
use Laravel9\Survey\Models\SurveyQuestion;

class SurveyController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        return Survey::query()
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'DESC')
            ->paginate(10);
    }

    public function store(StoreSurveyRequest $request)
    {
        $data = $request->validated();

        if (isset($data['image'])) {
            $relativePath = $this->saveImage($data['image']);
            $data['image'] = $relativePath;
        }

        $survey = Survey::create($data);

        // Create new questions
        foreach ($data['questions'] as $question) {
            $question['survey_id'] = $survey->id;
            $question['type'] = $request->type;
            $question['description'] = $request->description;
            $this->createQuestion($question);
        }
        return new SurveyResource($survey);
    }

    /**
     * Save image in local file system and return saved image path
     * @param $image
     * @throws \Exception
     */
    private function saveImage($image)
    {
        // Check if image is valid base64 string
        if (preg_match('/^data:image\/(\w+);base64,/', $image, $type)) {
            // Take out the base64 encoded text without mime type
            $image = substr($image, strpos($image, ',') + 1);
            // Get file extension
            $type = strtolower($type[1]); // jpg, png, gif

            // Check if file is an image
            if (!in_array($type, ['jpg', 'jpeg', 'gif', 'png'])) {
                throw new \Exception('invalid image type');
            }
            $image = str_replace(' ', '+', $image);
            $image = base64_decode($image);

            if ($image === false) {
                throw new \Exception('base64_decode failed');
            }
        } else {
            throw new \Exception('did not match data URI with image data');
        }

        $dir = 'images/';
        $file = Str::random() . '.' . $type;
        $absolutePath = public_path($dir);
        $relativePath = $dir . $file;
        if (!File::exists($absolutePath)) {
            File::makeDirectory($absolutePath, 0755, true);
        }
        file_put_contents($relativePath, $image);

        return $relativePath;
    }

    /**
     * Create a question and return
     *
     * @param $data
     * @return mixed
     * @throws \Illuminate\Validation\ValidationException
     */
    private function createQuestion($data)
    {
        if (is_array($data['question'])) {
            $data['data'] = json_encode($data);
        }

        $validator = Validator::make($data, [
            'question' => 'required|string',
            'type' => ['required', Rule::in([
                Survey::TYPE_TEXT,
                Survey::TYPE_TEXTAREA,
                Survey::TYPE_SELECT,
                Survey::TYPE_RADIO,
                Survey::TYPE_CHECKBOX,
            ])],
            'description' => 'nullable|string',
            'data' => 'present',
            'survey_id' => 'exists:Laravel9\Survey\Models\Survey,id'
        ]);

        return SurveyQuestion::create($validator->validated());
    }
}
