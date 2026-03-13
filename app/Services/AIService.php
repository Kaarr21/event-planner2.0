<?php

namespace App\Services;

class AIService
{
    /**
     * Suggest tasks for an event.
     */
    public function suggestTasks(string $title, string $description = ''): array
    {
        // Placeholder logic: In a real app, this would call OpenAI/Gemini
        $suggestions = [
            'Basic' => [
                'Set budget',
                'Create guest list',
                'Pick a venue',
                'Send invitations',
            ],
            'Wedding' => [
                'Hire a photographer',
                'Select catering menu',
                'Choose flower arrangements',
                'Rehearsal dinner planning',
            ],
            'Conference' => [
                'Confirm keynote speakers',
                'Set up registration site',
                'Organize breakout sessions',
                'Arrange AV equipment',
            ],
        ];

        foreach ($suggestions as $key => $tasks) {
            if (stripos($title, $key) !== false || stripos($description, $key) !== false) {
                return $tasks;
            }
        }

        return $suggestions['Basic'];
    }

    /**
     * Generate event description.
     */
    public function generateDescription(string $title, string $location = ''): string
    {
        return "Join us for " . $title . ($location ? " at " . $location : "") . "! It's going to be an amazing event filled with great activities and networking opportunities. We look forward to seeing everyone there!";
    }
}
