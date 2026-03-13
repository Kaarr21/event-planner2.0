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
                'Plan food and drinks menu',
                'Book entertainment or a DJ',
                'Send thank you notes',
                'Arrange transportation',
                'Create an event timeline',
                'Rent equipment or furniture',
            ],
            'Wedding' => [
                'Hire a photographer',
                'Select catering menu',
                'Choose flower arrangements',
                'Rehearsal dinner planning',
                'Book the officiant',
                'Send save-the-dates',
                'Order the wedding cake',
                'Buy wedding rings',
                'Plan the honeymoon',
            ],
            'Conference' => [
                'Confirm keynote speakers',
                'Set up registration site',
                'Organize breakout sessions',
                'Arrange AV equipment',
                'Design name badgers',
                'Book hotel blocks for attendees',
                'Prepare welcome packets',
                'Hire event staff',
            ],
        ];

        $matchedTasks = $suggestions['Basic'];
        foreach ($suggestions as $key => $tasks) {
            if (stripos($title, $key) !== false || stripos($description, $key) !== false) {
                $matchedTasks = $tasks;
                break;
            }
        }

        shuffle($matchedTasks);
        return array_slice($matchedTasks, 0, 4);
    }

    /**
     * Generate event description.
     */
    public function generateDescription(string $title, string $location = ''): string
    {
        return "Join us for " . $title . ($location ? " at " . $location : "") . "! It's going to be an amazing event filled with great activities and networking opportunities. We look forward to seeing everyone there!";
    }
}
