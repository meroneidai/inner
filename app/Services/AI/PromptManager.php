<?php

namespace App\Services\AI;

use App\Models\AnalysisPromptVersion;
use Illuminate\Support\Facades\File;

class PromptManager
{
    /**
     * Documented template variables available to admins.
     *
     * @return array<string, string>
     */
    public static function availableVariables(): array
    {
        return [
            '{{dream_text}}' => 'نص الحلم المؤكد',
            '{{dream_date}}' => 'تاريخ الحلم',
            '{{mood}}' => 'المزاج عند الاستيقاظ',
            '{{sleep_quality}}' => 'جودة النوم (1-10)',
            '{{emotional_intensity}}' => 'شدة المشاعر (1-10)',
            '{{personal_notes}}' => 'ملاحظات المستخدم',
            '{{preferred_language}}' => 'لغة المستخدم المفضلة (ar/en)',
            '{{user_context}}' => 'سياق الملف الداخلي ذي الصلة (JSON مضغوط)',
            '{{previous_dreams}}' => 'ملخص أحلام سابقة مصرّح بها',
            '{{preferred_name}}' => 'الاسم المفضل',
            '{{profile_summary}}' => 'ملخص لقطة الملف الداخلي إن وُجدت',
            '{{profile_goals}}' => 'أهداف النمو المعرفة من المستخدم',
            '{{interpretive_framework}}' => 'إطار التفسير المفضل (نفسي/ثقافي/روحي...)',
            '{{emotional_state}}' => 'الحالة العاطفية المصرّح بها',
            '{{current_concerns}}' => 'الاهتمامات الحالية من الملف',
            '{{profile_completeness}}' => 'نسبة اكتمال الملف %',
        ];
    }

    public function activeDreamAnalysisPrompt(): AnalysisPromptVersion
    {
        $active = AnalysisPromptVersion::query()->where('is_active', true)->latest('id')->first();

        if ($active) {
            return $active;
        }

        return $this->seedDefaultPrompt();
    }

    public function seedDefaultPrompt(): AnalysisPromptVersion
    {
        $system = File::get(resource_path('prompts/dream_analysis_system.txt'));

        return AnalysisPromptVersion::query()->updateOrCreate(
            ['slug' => 'dream-analysis-v1'],
            [
                'name' => 'InnerPath Dream Analysis',
                'version' => '1.0.0',
                'system_prompt' => $system,
                'user_prompt_template' => $this->defaultUserTemplate(),
                'is_active' => true,
                'metadata' => [
                    'schema_version' => '1.0',
                    'variables' => array_keys(self::availableVariables()),
                ],
            ]
        );
    }

    public function buildUserPrompt(array $context): string
    {
        $prompt = $this->activeDreamAnalysisPrompt();
        $template = $prompt->user_prompt_template ?: $this->defaultUserTemplate();

        $replacements = [];
        foreach (array_keys(self::availableVariables()) as $variable) {
            $key = trim($variable, '{}');
            $replacements[$variable] = (string) ($context[$key] ?? '');
        }

        // Always map core keys even if missing from docs sync
        $replacements = array_merge($replacements, [
            '{{dream_text}}' => $context['dream_text'] ?? '',
            '{{dream_date}}' => $context['dream_date'] ?? '',
            '{{mood}}' => $context['mood'] ?? '',
            '{{sleep_quality}}' => (string) ($context['sleep_quality'] ?? ''),
            '{{emotional_intensity}}' => (string) ($context['emotional_intensity'] ?? ''),
            '{{personal_notes}}' => $context['personal_notes'] ?? '',
            '{{preferred_language}}' => $context['preferred_language'] ?? 'ar',
            '{{user_context}}' => $context['user_context'] ?? '',
            '{{previous_dreams}}' => $context['previous_dreams'] ?? '',
            '{{preferred_name}}' => $context['preferred_name'] ?? '',
            '{{profile_summary}}' => $context['profile_summary'] ?? '',
            '{{profile_goals}}' => $context['profile_goals'] ?? '',
            '{{interpretive_framework}}' => $context['interpretive_framework'] ?? '',
            '{{emotional_state}}' => $context['emotional_state'] ?? '',
            '{{current_concerns}}' => $context['current_concerns'] ?? '',
            '{{profile_completeness}}' => (string) ($context['profile_completeness'] ?? ''),
        ]);

        return strtr($template, $replacements);
    }

    private function defaultUserTemplate(): string
    {
        return <<<'TXT'
Analyze the following dream. Respond ONLY with valid JSON matching the required schema.
Write textual fields in the user's preferred language: {{preferred_language}}.

Dreamer preferred name: {{preferred_name}}
Profile completeness: {{profile_completeness}}%
Emotional state (self-reported): {{emotional_state}}
Current concerns (self-reported): {{current_concerns}}
Interpretive framework preference: {{interpretive_framework}}
Profile goals: {{profile_goals}}
Latest profile summary: {{profile_summary}}

Dream date: {{dream_date}}
Mood upon waking: {{mood}}
Sleep quality (1-10): {{sleep_quality}}
Emotional intensity (1-10): {{emotional_intensity}}
Personal notes: {{personal_notes}}

Relevant Human Inner Profile context (authorized, filtered — prioritize user's own associations):
{{user_context}}

Previous dreams summary (authorized history only):
{{previous_dreams}}

Dream text:
{{dream_text}}

CRITICAL:
- For every main_symbol, fill personal_associations ONLY from the authorized profile/notes above; otherwise use [].
- Prefer the dreamer's lived meanings over generic symbol dictionaries.
- Add care_tags, support_focus, daily_encouragement, and step_by_step_growth for companion care.
TXT;
    }
}
