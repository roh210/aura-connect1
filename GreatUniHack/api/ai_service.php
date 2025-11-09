<?php
// This is our new AI Service, with better error checking.

function generateIcebreaker($studentName, $studentInterest, $seniorName, $seniorInterest) {

    // --- PASTE YOUR API KEY HERE ---
    $API_KEY = "AIzaSyCbj1MpzhMvN5DqOqklejhcgeFSUpgaGKA";

    // We just check if you forgot to paste the key.
    if (empty($API_KEY) || $API_KEY === "YOUR_GEMINI_API_KEY_GOES_HERE") {
        return "<strong>AI Error:</strong> API Key is missing or is still the placeholder in api/ai_service.php";
    }
  


    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash-preview-09-2025:generateContent?key=" . $API_KEY;

    $prompt = "You are Aura, an AI wellness agent. A student and senior are connecting.
Student: $studentName (interested in $studentInterest)
Senior: $seniorName (interested in $seniorInterest)

Generate ONE warm, specific question the student could ask the senior.
- Relate their interests if possible.
- Be conversational and open-ended.
- Keep it to 1-2 sentences.
- Wrap the most important part in <strong> tags.
Example: 'Ask $seniorName about a challenge they faced when they were interested in $seniorInterest.'";

    $payload = json_encode([
        "contents" => [
            ["parts" => [
                ["text" => $prompt]
            ]]
        ]
    ]);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);

    $response = curl_exec($ch);

    // This will catch if curl is disabled on your XAMPP
    if (curl_errno($ch)) {
        $error_msg = curl_error($ch);
        curl_close($ch);
        return "<strong>AI (curl) Error:</strong> " . $error_msg;
    }

    curl_close($ch);

    $result = json_decode($response, true);

    // This is a successful response
    if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
        return $result['candidates'][0]['content']['parts'][0]['text'];
    }

    // This will catch if your API key is invalid
    if (isset($result['error']['message'])) {
        return "<strong>AI (Google) Error:</strong> " . $result['error']['message'];
    }

    // A final fallback
    //return "<strong>AI Error:</strong> Unknown error. Response: "a . $response;
}
?>