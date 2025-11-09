<?php
// This is our new AI Service, written in PHP.

function generateIcebreaker($studentName, $studentInterest, $seniorName, $seniorInterest) {

    // --- PASTE YOUR API KEY HERE ---
    $API_KEY = "AIzaSyCbj1MpzhMvN5DqOqklejhcgeFSUpgaGKA";
    // ---------------------------------

    if ($API_KEY === "AIzaSyCbj1MpzhMvN5DqOqklejhcgeFSUpgaGKA") {
        return "You're connected! A good first question: What's a piece of advice you wish you'd known at 20?";
    }

    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash-preview-09-2025:generateContent?key=" . $API_KEY;

    // The prompt is based on the ai-service.js file you sent
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
    if (curl_errno($ch)) {
        curl_close($ch);
        return "You're connected! A good first question: What's a piece of advice you wish you'd known at 20?";
    }
    curl_close($ch);

    $result = json_decode($response, true);

    if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
        return $result['candidates'][0]['content']['parts'][0]['text'];
    }

    // Fallback message
    return "You're connected with $seniorName! Feel free to start the chat.";
}
?>
