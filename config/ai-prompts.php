<?php

return [

    /*
    |--------------------------------------------------------------------------
    | AI Prompt Templates
    |--------------------------------------------------------------------------
    |
    | Every prompt sent to the AI providers lives here so templates can be
    | tuned without touching application code. Placeholders use the
    | {placeholder} syntax and are replaced by the PromptBuilder service.
    |
    */

    'natural_language_task' => [
        'system' => 'You are a productivity assistant that converts free-text task requests into structured JSON. Only respond with valid JSON, nothing else.',
        'user' => <<<'EOT'
            Convert the following user request into a task definition.
            Infer the title, description, priority (low|medium|high|urgent), due date (ISO 8601 or null), estimated duration in minutes, and an optional category name.

            Request:
            "{request}"

            Respond with JSON in exactly this shape:
            {"title": "...", "description": "...", "priority": "...", "due_date": "..." or null, "estimated_minutes": number or null, "category": "..." or null}
            EOT,
    ],

    'task_breakdown' => [
        'system' => 'You are an expert project planner. Break large tasks into small, actionable, ordered subtasks.',
        'user' => <<<'EOT'
            Break the following task into concrete subtasks that can each be completed independently.

            Task title: "{title}"
            Description: "{description}"
            Estimated duration: {estimated_minutes} minutes

            Return exactly a JSON array of strings, maximum 8 items, ordered logically:
            ["First step", "Second step", ...]
            EOT,
    ],

    'task_prioritization' => [
        'system' => 'You are an expert in time management and prioritization (Eisenhower matrix, weighted scoring).',
        'user' => <<<'EOT'
            Prioritize the following tasks and recommend an order for the day. Consider urgency, importance, due dates, and estimated effort.

            Tasks:
            {tasks}

            Return exactly JSON in this shape:
            [{"id": "<original task id>", "priority": "low|medium|high|urgent", "suggested_order": 1, "reason": "one short sentence"}]
            EOT,
    ],

    'task_rewrite' => [
        'system' => 'You are a writing assistant that rewrites tasks to be clear, specific and actionable (following SMART criteria).',
        'user' => <<<'EOT'
            Rewrite the following task to be clearer and more actionable while keeping its intent.

            Current title: "{title}"
            Current description: "{description}"

            Respond with exactly JSON in this shape:
            {"title": "...", "description": "..."}
            EOT,
    ],

    'daily_planning' => [
        'system' => 'You are a personal productivity coach helping the user plan their day realistically.',
        'user' => <<<'EOT'
            Today's available focus minutes: {available_minutes}
            Today's tasks:
            {tasks}

            Create a realistic daily plan. Order tasks by priority and due time, keep the total under the available minutes, and mark unplannable tasks clearly.

            Return exactly JSON in this shape:
            {"plan": [{"id": "<task id>", "start_time": "HH:MM", "title": "..."}], "note": "one sentence explanation", "unplannable_ids": ["..."]}
            EOT,
    ],

    'weekly_planning' => [
        'system' => 'You are a personal productivity coach helping the user plan their week ahead.',
        'user' => <<<'EOT'
            Distribute the following tasks across the next 7 days (Monday to Sunday). Respect due dates and priorities. Aim for a balanced workload of at most {daily_capacity} minutes per day.

            Upcoming tasks:
            {tasks}

            Return exactly JSON in this shape:
            {"days": [{"date": "YYYY-MM-DD", "task_ids": ["..."]}], "note": "one sentence summary"}
            EOT,
    ],

    'productivity_suggestions' => [
        'system' => 'You are an AI productivity coach that gives concise, actionable improvement suggestions based on real user data.',
        'user' => <<<'EOT'
            Analyze the following productivity metrics and give 3 specific, actionable suggestions to improve. Be concise and encouraging.

            Metrics (last 7 days):
            {metrics}

            Return exactly JSON in this shape:
            {"suggestions": ["...", "...", "..."]}
            EOT,
    ],

    'note_summarization' => [
        'system' => 'You are a note summarization assistant. Summarize notes into concise, structured summaries.',
        'user' => <<<'EOT'
            Summarize the following note in at most 5 bullet points, keeping the most important information.

            Note title: "{title}"
            Note content:
            {content}
            EOT,
    ],

    'note_to_tasks' => [
        'system' => 'You convert notes into actionable task lists.',
        'user' => <<<'EOT'
            Extract actionable tasks from the following note. Each task must be self-contained and specific.

            Note:
            {content}

            Return exactly JSON in this shape:
            [{"title": "...", "description": "...", "priority": "low|medium|high|urgent", "estimated_minutes": number or null}]
            EOT,
    ],

    'estimate_duration' => [
        'system' => 'You estimate how many minutes a task will realistically take based on its description.',
        'user' => <<<'EOT'
            Estimate the time (in minutes) needed to complete this task.

            Title: "{title}"
            Description: "{description}"

            Return exactly JSON: {"estimated_minutes": number, "confidence": "low|medium|high", "reason": "one short sentence"}
            EOT,
    ],

    'daily_digest' => [
        'system' => 'You are a personal productivity coach writing a short morning digest email.',
        'user' => <<<'EOT'
            Write a short, warm morning digest (max 120 words) that summarizes today's most important tasks and one encouraging tip.

            Today's tasks:
            {tasks}
            EOT,
    ],

];
