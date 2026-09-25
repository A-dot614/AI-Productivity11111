<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client as HttpClient;
use Exception;

class GeminiController extends Controller
{
    public function index()
    {
        return view('gemini');
    }

    public function generate(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string|max:2000',
        ]);

        $prompt = $request->input('prompt');

        // Define primary and backup fallback models
        $models = ['gemini-3.6-flash', 'gemini-3.8-flash'];

        $client = \Gemini::factory()
            ->withApiKey(env('GEMINI_API_KEY'))
            ->withHttpClient(new HttpClient([
                'timeout' => 120.0,
                'connect_timeout' => 30.0,
            ]))
            ->make();

        $lastException = null;

        foreach ($models as $modelName) {
            // Try up to 2 attempts per model with a short pause
            for ($attempt = 1; $attempt <= 2; $attempt++) {
                try {
                    $response = $client->generativeModel(model: $modelName)
                        ->generateContent($prompt);

                    return back()
                        ->with('response', $response->text())
                        ->with('prompt', $prompt);

                } catch (Exception $e) {
                    $lastException = $e;
                    $errorText = strtolower($e->getMessage());

                    // If it's a high demand / overload error, wait 2 seconds and retry
                    if (str_contains($errorText, 'high demand') || str_contains($errorText, '503') || str_contains($errorText, 'overloaded')) {
                        sleep(2);
                        continue;
                    }

                    // If it's a rate limit (quota), no point retrying immediately
                    if (str_contains($errorText, 'quota') || str_contains($errorText, '429')) {
                        return back()
                            ->withInput()
                            ->withErrors(['error' => 'API rate limit reached. Please wait 20–30 seconds before submitting another request.']);
                    }

                    // For other errors, break out to try the fallback model
                    break;
                }
            }
        }

        // If all retries and fallback models failed
        return back()
            ->withInput()
            ->withErrors(['error' => 'Google servers are currently overloaded. Please wait 10–15 seconds and try again.']);
    }
}
