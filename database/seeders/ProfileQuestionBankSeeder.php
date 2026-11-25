<?php

namespace Database\Seeders;

use App\Models\ProfileQuestion;
use App\Models\ProfileQuestionTranslation;
use App\Models\ProfileSection;
use Illuminate\Database\Seeder;

class ProfileQuestionBankSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->sections() as $sectionData) {
            $questions = $sectionData['questions'];
            unset($sectionData['questions']);

            $section = ProfileSection::query()->updateOrCreate(
                ['slug' => $sectionData['slug']],
                $sectionData
            );

            foreach ($questions as $index => $q) {
                $question = ProfileQuestion::query()->updateOrCreate(
                    ['question_key' => $q['key']],
                    [
                        'section_id' => $section->id,
                        'answer_type' => $q['type'],
                        'options_json' => $q['options'] ?? null,
                        'conditional_rules_json' => $q['conditional'] ?? null,
                        'is_required' => $q['required'] ?? false,
                        'is_sensitive' => $q['sensitive'] ?? $section->is_sensitive,
                        'is_active' => true,
                        'include_in_stage1' => $q['stage1'] ?? false,
                        'display_order' => $index + 1,
                        'profile_dimension' => $q['dimension'] ?? $section->slug,
                        'ai_context_relevance' => $q['relevance'] ?? 'medium',
                        'version' => '1.0',
                    ]
                );

                ProfileQuestionTranslation::query()->updateOrCreate(
                    ['question_id' => $question->id, 'locale' => 'ar'],
                    [
                        'question_text' => $q['ar'],
                        'description' => $q['ar_desc'] ?? null,
                        'options_json' => $q['options_ar'] ?? $q['options'] ?? null,
                    ]
                );

                ProfileQuestionTranslation::query()->updateOrCreate(
                    ['question_id' => $question->id, 'locale' => 'en'],
                    [
                        'question_text' => $q['en'],
                        'description' => $q['en_desc'] ?? null,
                        'options_json' => $q['options_en'] ?? $q['options'] ?? null,
                    ]
                );
            }
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function sections(): array
    {
        $age = [
            ['value' => 'under_18', 'emoji' => '🧒', 'label_ar' => 'أقل من 18', 'label_en' => 'Under 18'],
            ['value' => '18-24', 'emoji' => '🧑', 'label_ar' => '18–24', 'label_en' => '18–24'],
            ['value' => '25-34', 'emoji' => '👤', 'label_ar' => '25–34', 'label_en' => '25–34'],
            ['value' => '35-44', 'emoji' => '👥', 'label_ar' => '35–44', 'label_en' => '35–44'],
            ['value' => '45-54', 'emoji' => '🧔', 'label_ar' => '45–54', 'label_en' => '45–54'],
            ['value' => '55-64', 'emoji' => '🧓', 'label_ar' => '55–64', 'label_en' => '55–64'],
            ['value' => '65+', 'emoji' => '🌟', 'label_ar' => '65+', 'label_en' => '65+'],
            ['value' => 'prefer_not', 'emoji' => '🤐', 'label_ar' => 'أفضل عدم الإجابة', 'label_en' => 'Prefer not to say'],
        ];
        $emotions = [
            ['value' => 'happiness', 'emoji' => '😊', 'label_ar' => 'سعادة', 'label_en' => 'Happiness'],
            ['value' => 'sadness', 'emoji' => '😢', 'label_ar' => 'حزن', 'label_en' => 'Sadness'],
            ['value' => 'anxiety', 'emoji' => '😰', 'label_ar' => 'قلق', 'label_en' => 'Anxiety'],
            ['value' => 'fear', 'emoji' => '😨', 'label_ar' => 'خوف', 'label_en' => 'Fear'],
            ['value' => 'anger', 'emoji' => '😠', 'label_ar' => 'غضب', 'label_en' => 'Anger'],
            ['value' => 'calmness', 'emoji' => '😌', 'label_ar' => 'هدوء', 'label_en' => 'Calmness'],
            ['value' => 'loneliness', 'emoji' => '🥀', 'label_ar' => 'وحدة', 'label_en' => 'Loneliness'],
            ['value' => 'hope', 'emoji' => '🌅', 'label_ar' => 'أمل', 'label_en' => 'Hope'],
            ['value' => 'frustration', 'emoji' => '😤', 'label_ar' => 'إحباط', 'label_en' => 'Frustration'],
            ['value' => 'confusion', 'emoji' => '😕', 'label_ar' => 'حيرة', 'label_en' => 'Confusion'],
            ['value' => 'excitement', 'emoji' => '🤩', 'label_ar' => 'حماس', 'label_en' => 'Excitement'],
            ['value' => 'other', 'emoji' => '🌀', 'label_ar' => 'أخرى', 'label_en' => 'Other'],
        ];
        $scale = [
            ['value' => 1, 'emoji' => '😔', 'label_en' => 'Not at all', 'label_ar' => 'أبداً'],
            ['value' => 2, 'emoji' => '😐', 'label_en' => 'Slightly', 'label_ar' => 'قليلاً'],
            ['value' => 3, 'emoji' => '🙂', 'label_en' => 'Sometimes', 'label_ar' => 'أحياناً'],
            ['value' => 4, 'emoji' => '😊', 'label_en' => 'Often', 'label_ar' => 'غالباً'],
            ['value' => 5, 'emoji' => '🌟', 'label_en' => 'Very strongly', 'label_ar' => 'بقوة كبيرة'],
        ];
        $yesNo = [
            ['value' => 'yes', 'emoji' => '✅', 'label_ar' => 'نعم', 'label_en' => 'Yes'],
            ['value' => 'no', 'emoji' => '❌', 'label_ar' => 'لا', 'label_en' => 'No'],
            ['value' => 'prefer_not', 'emoji' => '🤐', 'label_ar' => 'أفضل التخطي', 'label_en' => 'Prefer not'],
        ];
        $perspectives = [
            ['value' => 'psychological_only', 'emoji' => '🧠', 'label_ar' => 'نفسي فقط', 'label_en' => 'Psychological only'],
            ['value' => 'historical_cultural', 'emoji' => '🏛️', 'label_ar' => 'تاريخي/ثقافي', 'label_en' => 'Historical/cultural'],
            ['value' => 'religious_spiritual', 'emoji' => '🕊️', 'label_ar' => 'ديني/روحي', 'label_en' => 'Religious/spiritual'],
            ['value' => 'combination', 'emoji' => '🔀', 'label_ar' => 'مزيج', 'label_en' => 'Combination'],
            ['value' => 'choose_per_dream', 'emoji' => '🎛️', 'label_ar' => 'حسب كل حلم', 'label_en' => 'Choose per dream'],
        ];
        $emotionalStates = [
            ['value' => 'calm', 'emoji' => '🌿', 'label_ar' => 'هادئ', 'label_en' => 'Calm'],
            ['value' => 'anxious', 'emoji' => '😰', 'label_ar' => 'قلق', 'label_en' => 'Anxious'],
            ['value' => 'tired', 'emoji' => '😮‍💨', 'label_ar' => 'متعب', 'label_en' => 'Tired'],
            ['value' => 'hopeful', 'emoji' => '🌅', 'label_ar' => 'متفائل', 'label_en' => 'Hopeful'],
            ['value' => 'sad', 'emoji' => '😢', 'label_ar' => 'حزين', 'label_en' => 'Sad'],
            ['value' => 'stressed', 'emoji' => '💥', 'label_ar' => 'مضغوط', 'label_en' => 'Stressed'],
            ['value' => 'mixed', 'emoji' => '🎭', 'label_ar' => 'مزيج', 'label_en' => 'Mixed'],
        ];
        $concerns = [
            ['value' => 'work', 'emoji' => '💼', 'label_ar' => 'العمل', 'label_en' => 'Work'],
            ['value' => 'family', 'emoji' => '👨‍👩‍👧', 'label_ar' => 'العائلة', 'label_en' => 'Family'],
            ['value' => 'health', 'emoji' => '🩺', 'label_ar' => 'الصحة', 'label_en' => 'Health'],
            ['value' => 'money', 'emoji' => '💰', 'label_ar' => 'المال', 'label_en' => 'Money'],
            ['value' => 'relationships', 'emoji' => '💞', 'label_ar' => 'العلاقات', 'label_en' => 'Relationships'],
            ['value' => 'future', 'emoji' => '🔮', 'label_ar' => 'المستقبل', 'label_en' => 'Future'],
            ['value' => 'self', 'emoji' => '🪞', 'label_ar' => 'الذات', 'label_en' => 'Self'],
            ['value' => 'other', 'emoji' => '🌀', 'label_ar' => 'أخرى', 'label_en' => 'Other'],
        ];
        $fears = [
            ['value' => 'love', 'emoji' => '💗', 'label_ar' => 'الحب/القرب', 'label_en' => 'Love/closeness'],
            ['value' => 'security', 'emoji' => '🏠', 'label_ar' => 'الأمان', 'label_en' => 'Security'],
            ['value' => 'health', 'emoji' => '🩺', 'label_ar' => 'الصحة', 'label_en' => 'Health'],
            ['value' => 'status', 'emoji' => '🏅', 'label_ar' => 'المكانة', 'label_en' => 'Status'],
            ['value' => 'control', 'emoji' => '🎛️', 'label_ar' => 'السيطرة', 'label_en' => 'Control'],
            ['value' => 'belonging', 'emoji' => '🤝', 'label_ar' => 'الانتماء', 'label_en' => 'Belonging'],
            ['value' => 'other', 'emoji' => '🌀', 'label_ar' => 'أخرى', 'label_en' => 'Other'],
        ];
        $lifeStages = [
            ['value' => 'starting', 'emoji' => '🌱', 'label_ar' => 'بداية جديدة', 'label_en' => 'New beginning'],
            ['value' => 'building', 'emoji' => '🧱', 'label_ar' => 'بناء واستقرار', 'label_en' => 'Building'],
            ['value' => 'transition', 'emoji' => '🌉', 'label_ar' => 'مرحلة انتقال', 'label_en' => 'Transition'],
            ['value' => 'pressure', 'emoji' => '🌪️', 'label_ar' => 'ضغط كثيف', 'label_en' => 'High pressure'],
            ['value' => 'healing', 'emoji' => '🩹', 'label_ar' => 'تعافٍ', 'label_en' => 'Healing'],
            ['value' => 'searching', 'emoji' => '🧭', 'label_ar' => 'بحث عن معنى', 'label_en' => 'Searching'],
        ];
        $thoughtTopics = [
            ['value' => 'work', 'emoji' => '💼', 'label_ar' => 'العمل', 'label_en' => 'Work'],
            ['value' => 'relationships', 'emoji' => '💞', 'label_ar' => 'العلاقات', 'label_en' => 'Relationships'],
            ['value' => 'self_growth', 'emoji' => '🌱', 'label_ar' => 'تطوير الذات', 'label_en' => 'Self-growth'],
            ['value' => 'health', 'emoji' => '🩺', 'label_ar' => 'الصحة', 'label_en' => 'Health'],
            ['value' => 'money', 'emoji' => '💰', 'label_ar' => 'المال', 'label_en' => 'Money'],
            ['value' => 'faith', 'emoji' => '🕊️', 'label_ar' => 'المعنى/الإيمان', 'label_en' => 'Meaning/faith'],
            ['value' => 'family', 'emoji' => '👨‍👩‍👧', 'label_ar' => 'العائلة', 'label_en' => 'Family'],
        ];
        $goals = [
            ['value' => 'calm', 'emoji' => '🧘', 'label_ar' => 'المزيد من الهدوء', 'label_en' => 'More calm'],
            ['value' => 'confidence', 'emoji' => '💪', 'label_ar' => 'ثقة بالنفس', 'label_en' => 'Confidence'],
            ['value' => 'relationships', 'emoji' => '💞', 'label_ar' => 'علاقات أفضل', 'label_en' => 'Better relationships'],
            ['value' => 'health', 'emoji' => '🏃', 'label_ar' => 'صحة أفضل', 'label_en' => 'Better health'],
            ['value' => 'career', 'emoji' => '🚀', 'label_ar' => 'تطور مهني', 'label_en' => 'Career growth'],
            ['value' => 'purpose', 'emoji' => '🎯', 'label_ar' => 'وضوح الهدف', 'label_en' => 'Clear purpose'],
            ['value' => 'creativity', 'emoji' => '🎨', 'label_ar' => 'إبداع', 'label_en' => 'Creativity'],
        ];
        $values = [
            ['value' => 'honesty', 'emoji' => '🪞', 'label_ar' => 'الصدق', 'label_en' => 'Honesty'],
            ['value' => 'family', 'emoji' => '👨‍👩‍👧', 'label_ar' => 'العائلة', 'label_en' => 'Family'],
            ['value' => 'freedom', 'emoji' => '🕊️', 'label_ar' => 'الحرية', 'label_en' => 'Freedom'],
            ['value' => 'growth', 'emoji' => '🌱', 'label_ar' => 'النمو', 'label_en' => 'Growth'],
            ['value' => 'kindness', 'emoji' => '💛', 'label_ar' => 'اللطف', 'label_en' => 'Kindness'],
            ['value' => 'faith', 'emoji' => '🕌', 'label_ar' => 'الإيمان', 'label_en' => 'Faith'],
            ['value' => 'achievement', 'emoji' => '🏆', 'label_ar' => 'الإنجاز', 'label_en' => 'Achievement'],
            ['value' => 'balance', 'emoji' => '⚖️', 'label_ar' => 'التوازن', 'label_en' => 'Balance'],
        ];
        $languages = [
            ['value' => 'ar', 'emoji' => '🇸🇦', 'label_ar' => 'العربية', 'label_en' => 'Arabic'],
            ['value' => 'en', 'emoji' => '🇬🇧', 'label_ar' => 'الإنجليزية', 'label_en' => 'English'],
            ['value' => 'both', 'emoji' => '🌐', 'label_ar' => 'كلاهما', 'label_en' => 'Both'],
            ['value' => 'other', 'emoji' => '🗣️', 'label_ar' => 'أخرى', 'label_en' => 'Other'],
        ];
        $sleepHours = [
            ['value' => 'lt5', 'emoji' => '😴', 'label_ar' => 'أقل من 5', 'label_en' => 'Under 5'],
            ['value' => '5-6', 'emoji' => '🌙', 'label_ar' => '5–6', 'label_en' => '5–6'],
            ['value' => '7-8', 'emoji' => '✨', 'label_ar' => '7–8', 'label_en' => '7–8'],
            ['value' => '9plus', 'emoji' => '🛏️', 'label_ar' => '9 أو أكثر', 'label_en' => '9+'],
        ];
        $developAreas = [
            ['value' => 'emotions', 'emoji' => '💛', 'label_ar' => 'المشاعر', 'label_en' => 'Emotions'],
            ['value' => 'relationships', 'emoji' => '💞', 'label_ar' => 'العلاقات', 'label_en' => 'Relationships'],
            ['value' => 'confidence', 'emoji' => '💪', 'label_ar' => 'الثقة', 'label_en' => 'Confidence'],
            ['value' => 'career', 'emoji' => '🚀', 'label_ar' => 'المهنة', 'label_en' => 'Career'],
            ['value' => 'health', 'emoji' => '🏃', 'label_ar' => 'الصحة', 'label_en' => 'Health'],
            ['value' => 'spirituality', 'emoji' => '🕊️', 'label_ar' => 'الروحانية', 'label_en' => 'Spirituality'],
            ['value' => 'boundaries', 'emoji' => '🚧', 'label_ar' => 'الحدود', 'label_en' => 'Boundaries'],
        ];
        $stability = [
            ['value' => 'stable', 'emoji' => '🏡', 'label_ar' => 'مستقرة', 'label_en' => 'Stable'],
            ['value' => 'many_changes', 'emoji' => '🌊', 'label_ar' => 'تغيّرات كثيرة', 'label_en' => 'Many changes'],
            ['value' => 'mixed', 'emoji' => '🎭', 'label_ar' => 'مزيج', 'label_en' => 'Mixed'],
            ['value' => 'prefer_not', 'emoji' => '🤐', 'label_ar' => 'أفضل التخطي', 'label_en' => 'Prefer not'],
        ];
        $shareStyle = [
            ['value' => 'keep_inside', 'emoji' => '🤫', 'label_ar' => 'أحتفظ بها', 'label_en' => 'Keep inside'],
            ['value' => 'share', 'emoji' => '🗣️', 'label_ar' => 'أشاركها', 'label_en' => 'Share'],
            ['value' => 'depends', 'emoji' => '⚖️', 'label_ar' => 'حسب الموقف', 'label_en' => 'Depends'],
            ['value' => 'prefer_not', 'emoji' => '🤐', 'label_ar' => 'تخطي', 'label_en' => 'Prefer not'],
        ];
        $dreamRecall = [
            ['value' => 'rarely', 'emoji' => '🌫️', 'label_ar' => 'نادراً', 'label_en' => 'Rarely'],
            ['value' => 'sometimes', 'emoji' => '🌙', 'label_ar' => 'أحياناً', 'label_en' => 'Sometimes'],
            ['value' => 'often', 'emoji' => '✨', 'label_ar' => 'غالباً', 'label_en' => 'Often'],
            ['value' => 'almost_always', 'emoji' => '🌟', 'label_ar' => 'دائماً تقريباً', 'label_en' => 'Almost always'],
        ];
        $feedbackStyle = [
            ['value' => 'direct', 'emoji' => '🎯', 'label_ar' => 'مباشر', 'label_en' => 'Direct'],
            ['value' => 'gradual', 'emoji' => '🌊', 'label_ar' => 'تدريجي', 'label_en' => 'Gradual'],
        ];
        $exerciseStyle = [
            ['value' => 'practical', 'emoji' => '🛠️', 'label_ar' => 'تمارين عملية', 'label_en' => 'Practical'],
            ['value' => 'reflective', 'emoji' => '🪞', 'label_ar' => 'تأملية', 'label_en' => 'Reflective'],
            ['value' => 'both', 'emoji' => '🔀', 'label_ar' => 'الاثنان', 'label_en' => 'Both'],
        ];

        return [
            [
                'slug' => 'identity',
                'name_ar' => 'الهوية والخلفية',
                'name_en' => 'Identity and Background',
                'description_ar' => 'خلفية شخصية وثقافية يصرّح بها المستخدم.',
                'description_en' => 'Declared cultural and personal background.',
                'display_order' => 1,
                'stage' => 'initial',
                'is_sensitive' => false,
                'is_active' => true,
                'questions' => [
                    ['key' => 'A01', 'type' => 'short_text', 'stage1' => true, 'ar' => 'ما الاسم الذي تفضل أن نناديك به؟', 'en' => 'What name would you like us to use?'],
                    ['key' => 'A02', 'type' => 'age_range', 'stage1' => true, 'options' => $age, 'ar' => 'ما هي فئتك العمرية؟', 'en' => 'What is your age range?'],
                    ['key' => 'A03', 'type' => 'single_choice', 'stage1' => true, 'options' => $languages, 'ar' => 'بأي لغة تشعر براحة أكبر عند التعبير عن مشاعرك؟', 'en' => 'Which language do you feel most comfortable using when expressing emotions?'],
                    ['key' => 'A04', 'type' => 'short_text', 'ar' => 'أين نشأت؟', 'en' => 'Where did you grow up?'],
                    ['key' => 'A05', 'type' => 'short_text', 'stage1' => true, 'ar' => 'أين تعيش حالياً؟', 'en' => 'Where do you currently live?'],
                    ['key' => 'A06', 'type' => 'yes_no', 'options' => $yesNo, 'ar' => 'هل عشت في بيئات ثقافية مختلفة؟', 'en' => 'Have you lived in different cultural environments?', 'conditional' => ['show_followups_if' => 'yes']],
                    ['key' => 'A07', 'type' => 'long_text', 'options' => $lifeStages, 'ar' => 'كيف تصف البيئة التي نشأت فيها؟', 'en' => 'How would you describe the environment in which you grew up?', 'ar_desc' => 'اختر أقرب وصف؛ الكتابة غير لازمة.', 'en_desc' => 'Pick the closest tag; writing is optional.'],
                    ['key' => 'A08', 'type' => 'single_choice', 'options' => $stability, 'ar' => 'هل كانت بيئة طفولتك مستقرة عموماً أم شهدت تغيّرات كثيرة؟', 'en' => 'Was your childhood environment generally stable or did it involve many changes?'],
                    ['key' => 'A09', 'type' => 'scale', 'options' => $scale, 'ar' => 'ما مدى أهمية خلفيتك الثقافية لهويتك؟', 'en' => 'How important is your cultural background to your identity?'],
                    ['key' => 'A10', 'type' => 'yes_no', 'options' => $yesNo, 'ar' => 'هل هناك تقاليد ثقافية تؤثر بقوة على تفسيرك للحياة؟', 'en' => 'Are there cultural traditions that strongly influence your interpretation of life?'],
                ],
            ],
            [
                'slug' => 'emotional',
                'name_ar' => 'العالم العاطفي',
                'name_en' => 'Emotional World',
                'description_ar' => 'وصف المستخدم لتجاربه العاطفية.',
                'description_en' => 'User description of emotional experiences.',
                'display_order' => 2,
                'stage' => 'initial',
                'is_sensitive' => false,
                'is_active' => true,
                'questions' => [
                    ['key' => 'B01', 'type' => 'single_choice', 'stage1' => true, 'options' => $emotionalStates, 'ar' => 'كيف تصف حالتك العاطفية الحالية؟', 'en' => 'How would you describe your current emotional state?'],
                    ['key' => 'B02', 'type' => 'multiple_choice', 'stage1' => true, 'options' => $emotions, 'ar' => 'أي المشاعر تختبرها بشكل متكرر؟', 'en' => 'Which emotions do you experience most frequently?'],
                    ['key' => 'B03', 'type' => 'multiple_choice', 'options' => $values, 'ar' => 'ما الذي يجعلك تشعر بسعادة حقيقية؟', 'en' => 'What makes you feel genuinely happy?'],
                    ['key' => 'B04', 'type' => 'multiple_choice', 'stage1' => true, 'options' => $concerns, 'ar' => 'ما الذي يقلقك أكثر حالياً؟', 'en' => 'What currently causes you the most concern?'],
                    ['key' => 'B05', 'type' => 'single_choice', 'options' => $shareStyle, 'ar' => 'كيف تستجيب عادة عندما تشعر بالحزن؟', 'en' => 'How do you usually respond when you feel sad?'],
                    ['key' => 'B06', 'type' => 'single_choice', 'options' => $shareStyle, 'ar' => 'كيف تعبّر عن الغضب؟', 'en' => 'How do you express anger?'],
                    ['key' => 'B07', 'type' => 'scale', 'options' => $scale, 'ar' => 'ما مدى راحتك في التعبير عن مشاعرك؟', 'en' => 'How comfortable are you expressing your emotions?'],
                    ['key' => 'B08', 'type' => 'single_choice', 'options' => $shareStyle, 'ar' => 'هل تحتفظ بمشاعرك لنفسك عادة أم تشاركها؟', 'en' => 'Do you usually keep emotions to yourself or share them?'],
                    ['key' => 'B09', 'type' => 'single_choice', 'options' => $emotions, 'ar' => 'أي شعور تجد صعوبة أكبر في فهمه أو إدارته؟', 'en' => 'Which emotion do you find most difficult to understand or manage?'],
                    ['key' => 'B10', 'type' => 'scale', 'stage1' => true, 'options' => $scale, 'ar' => 'ما مدى فهمك لنفسك؟', 'en' => 'How well do you feel you understand yourself?'],
                    ['key' => 'B11', 'type' => 'multiple_choice', 'options' => $values, 'ar' => 'ما الذي يجعلك تشعر بالأمان؟', 'en' => 'What makes you feel safe?'],
                    ['key' => 'B12', 'type' => 'multiple_choice', 'options' => $concerns, 'ar' => 'في أي مواقف تشعر أنك لا تستطيع أن تكون على طبيعتك؟', 'en' => 'In which situations do you feel that you cannot be yourself?'],
                ],
            ],
            [
                'slug' => 'childhood',
                'name_ar' => 'الطفولة والتجارب التكوينية',
                'name_en' => 'Childhood and Formative Experiences',
                'description_ar' => 'كل الأسئلة اختيارية وحساسة.',
                'description_en' => 'All questions optional and sensitive.',
                'display_order' => 3,
                'stage' => 'deeper',
                'is_sensitive' => true,
                'is_active' => true,
                'questions' => $this->textQuestions([
                    ['C01', 'كيف تتذكر طفولتك عموماً؟', 'How do you generally remember your childhood?'],
                    ['C02', 'ما أسعد ذكرى من طفولتك؟', 'What is your happiest childhood memory?'],
                    ['C03', 'هل هناك تجربة أثّرت بشكل كبير في من أنت اليوم؟', 'Was there an experience that significantly influenced who you are today?'],
                    ['C04', 'كيف تصف علاقتك بوالدتك؟', 'How would you describe your relationship with your mother?'],
                    ['C05', 'كيف تصف علاقتك بوالدك؟', 'How would you describe your relationship with your father?'],
                    ['C06', 'هل شعرت أنك مفهوم ومسموع في عائلتك؟', 'Did you feel understood and heard in your family?'],
                    ['C07', 'هل شعرت بالأمان في المنزل عموماً؟', 'Did you generally feel safe at home?'],
                    ['C08', 'هل كنت تستطيع التعبير عن آرائك بحرية؟', 'Could you express your opinions freely?'],
                    ['C09', 'هل شعرت بمسؤولية تفوق عمرك؟', 'Did you feel responsible for things beyond your age?'],
                    ['C10', 'هل شعرت أنك تحتاج لإرضاء الآخرين لقبولك؟', 'Did you feel that you needed to please others to receive acceptance?'],
                    ['C11', 'هل هناك ذكريات ما زالت تؤثر على مشاعرك اليوم؟', 'Are there memories from the past that still influence your feelings today?'],
                    ['C12', 'هل هناك تجارب تشعر أنك لم تعالجها بالكامل؟', 'Are there experiences you feel you have not fully processed?'],
                ], sensitive: true, type: 'long_text'),
            ],
            [
                'slug' => 'relationships',
                'name_ar' => 'العلاقات والتعلق',
                'name_en' => 'Relationships and Attachment',
                'description_ar' => 'كيف تختبر القرب والثقة والحدود.',
                'description_en' => 'How you experience closeness, trust, and boundaries.',
                'display_order' => 4,
                'stage' => 'deeper',
                'is_sensitive' => false,
                'is_active' => true,
                'questions' => array_merge(
                    [
                        ['key' => 'D01', 'type' => 'yes_no', 'stage1' => true, 'options' => $yesNo, 'ar' => 'هل تجد سهولة في بناء علاقات قريبة؟', 'en' => 'Do you find it easy to build close relationships?'],
                        ['key' => 'D02', 'type' => 'scale', 'stage1' => true, 'options' => $scale, 'ar' => 'ما مدى سهولة ثقتك بالآخرين؟', 'en' => 'How easily do you trust other people?'],
                    ],
                    $this->textQuestions([
                        ['D03', 'ما أكثر ما تخافه في العلاقات؟', 'What do you fear most in relationships?'],
                        ['D04', 'كيف تستجيب عادة للخلافات؟', 'How do you usually respond to disagreements?'],
                        ['D05', 'هل تجد صعوبة في قول لا؟', 'Do you find it difficult to say no?'],
                        ['D06', 'هل تشعر أحياناً أن الآخرين لا يفهمونك؟', 'Do you sometimes feel misunderstood by others?'],
                        ['D07', 'هل تميل للانسحاب عندما تتأذى عاطفياً؟', 'Do you tend to withdraw when emotionally hurt?'],
                        ['D08', 'ما مدى أهمية المساحة الشخصية بالنسبة لك؟', 'How important is personal space to you?'],
                        ['D09', 'ماذا تحتاج أكثر من الأشخاص المقربين؟', 'What do you need most from people close to you?'],
                        ['D10', 'هل هناك علاقة تشغل تفكيرك كثيراً حالياً؟', 'Is there a relationship currently occupying much of your thoughts?'],
                    ], type: 'long_text')
                ),
            ],
            [
                'slug' => 'self_perception',
                'name_ar' => 'الهوية وإدراك الذات',
                'name_en' => 'Identity and Self-Perception',
                'description_ar' => 'كيف ترى نفسك.',
                'description_en' => 'How you see yourself.',
                'display_order' => 5,
                'stage' => 'deeper',
                'is_sensitive' => false,
                'is_active' => true,
                'questions' => $this->textQuestions([
                    ['E01', 'صف نفسك في ثلاث كلمات.', 'Describe yourself in three words.'],
                    ['E02', 'ما الصفة الشخصية التي تقدرها أكثر؟', 'What personal quality do you appreciate most?'],
                    ['E03', 'ما الصفة التي تود تطويرها؟', 'What personal quality would you like to develop?'],
                    ['E04', 'هل هناك جانب منك نادراً ما تظهره للآخرين؟', 'Is there a side of yourself you rarely show others?'],
                    ['E05', 'هل تشعر أنك تعيش الحياة التي تريدها؟', 'Do you feel that you are living the life you want?'],
                    ['E06', 'هل تتصرف أحياناً بطرق لا تمثل مشاعرك الحقيقية؟', 'Do you sometimes behave in ways that do not represent your true feelings?'],
                    ['E07', 'هل هناك شيء تخاف أن يكتشفه الآخرون عنك؟ (اختياري)', 'Is there something you are afraid others might discover about you? Optional.'],
                    ['E08', 'هل تقارن نفسك بالآخرين؟', 'Do you compare yourself with other people?'],
                    ['E09', 'هل تشعر أنك تحتاج باستمرار لإثبات شيء؟', 'Do you feel that you constantly need to prove something?'],
                    ['E10', 'ما الذي يجعلك تشعر بقيمتك؟', 'What makes you feel valuable?'],
                    ['E11', 'ما أبرز صراع داخلي لديك حالياً؟', 'What is your most significant internal conflict at the moment?'],
                    ['E12', 'أي جزء من نفسك تود فهمه بشكل أفضل؟', 'What part of yourself would you like to understand better?'],
                ], type: 'long_text', sensitiveKeys: ['E07']),
            ],
            [
                'slug' => 'fear_safety',
                'name_ar' => 'الخوف والأمان والسيطرة',
                'name_en' => 'Fear, Safety, and Control',
                'description_ar' => 'ما يمنحك الأمان وما يهدده.',
                'description_en' => 'What gives safety and what threatens it.',
                'display_order' => 6,
                'stage' => 'deeper',
                'is_sensitive' => false,
                'is_active' => true,
                'questions' => array_merge(
                    [
                        ['key' => 'F01', 'type' => 'multiple_choice', 'stage1' => true, 'options' => $fears, 'ar' => 'ما أكثر ما تخاف فقدانه؟', 'en' => 'What are you most afraid of losing?'],
                    ],
                    $this->textQuestions([
                        ['F02', 'ما الذي يجعلك تشعر بفقدان السيطرة؟', 'What makes you feel that you are losing control?'],
                        ['F03', 'هل تقلق بشأن المستقبل؟', 'Do you worry about the future?'],
                        ['F04', 'هل تخاف الفشل أكثر أم عدم المحاولة؟', 'Are you more afraid of failure or of never trying?'],
                        ['F05', 'هل تخاف الوحدة؟', 'Are you afraid of being alone?'],
                        ['F06', 'هل تشعر أحياناً أنك محاصر في ظروف لا تستطيع تغييرها؟', 'Do you sometimes feel trapped in circumstances you cannot change?'],
                        ['F07', 'هل تجد صعوبة في اتخاذ القرارات؟', 'Do you find it difficult to make decisions?'],
                        ['F08', 'ما الذي يمنحك شعوراً بالاستقرار؟', 'What gives you a sense of stability?'],
                        ['F09', 'هل تفضل التحكم بالتفاصيل أم ترك الأمور تمضي طبيعياً؟', 'Do you prefer controlling details or allowing things to unfold naturally?'],
                        ['F10', 'عند مواجهة عدم اليقين، هل تشعر بالفضول أم بالخوف عادة؟', 'When facing uncertainty, do you usually feel curious or afraid?'],
                    ], type: 'long_text')
                ),
            ],
            [
                'slug' => 'beliefs',
                'name_ar' => 'المعتقدات والدين والروحانية',
                'name_en' => 'Beliefs, Religion, and Spirituality',
                'description_ar' => 'قسم اختياري وحساس — لا يُستنتج من بيانات أخرى.',
                'description_en' => 'Optional sensitive section — never inferred.',
                'display_order' => 7,
                'stage' => 'deeper',
                'is_sensitive' => true,
                'is_active' => true,
                'questions' => [
                    ['key' => 'G01', 'type' => 'yes_no', 'sensitive' => true, 'options' => $yesNo, 'ar' => 'هل تعتبر نفسك شخصاً روحانياً؟', 'en' => 'Do you consider yourself a spiritual person?'],
                    ['key' => 'G02', 'type' => 'short_text', 'sensitive' => true, 'ar' => 'هل تتبع ديناً أو تقليداً روحانياً معيناً؟', 'en' => 'Do you follow a particular religion or spiritual tradition?'],
                    ['key' => 'G03', 'type' => 'scale', 'sensitive' => true, 'options' => $scale, 'ar' => 'ما مدى تأثير معتقداتك على قراراتك اليومية؟', 'en' => 'How much do your beliefs influence your daily decisions?'],
                    ['key' => 'G04', 'type' => 'yes_no', 'sensitive' => true, 'options' => $yesNo, 'ar' => 'هل تشعر أن للحياة معنى أو غاية أعمق؟', 'en' => 'Do you feel that life has a deeper meaning or purpose?'],
                    ['key' => 'G05', 'type' => 'long_text', 'sensitive' => true, 'ar' => 'هل تمارس الصلاة أو التأمل أو ممارسة روحانية أخرى؟', 'en' => 'Do you practice prayer, meditation, or another spiritual practice?'],
                    ['key' => 'G06', 'type' => 'yes_no', 'sensitive' => true, 'options' => $yesNo, 'ar' => 'هل تستكشف حالياً أسئلة حول المعنى أو الإيمان أو الوجود؟', 'en' => 'Are you currently exploring questions about meaning, faith, or existence?'],
                    ['key' => 'G07', 'type' => 'yes_no', 'sensitive' => true, 'stage1' => true, 'options' => $yesNo, 'ar' => 'هل ترغب بإدراج منظورات روحية في تحليل الأحلام؟', 'en' => 'Would you like spiritual perspectives included in dream analysis?'],
                    ['key' => 'G08', 'type' => 'yes_no', 'sensitive' => true, 'options' => $yesNo, 'ar' => 'هل تؤمن أن الأحلام قد تحمل معنى روحياً؟', 'en' => 'Do you believe dreams can carry spiritual meaning?'],
                    ['key' => 'G09', 'type' => 'long_text', 'sensitive' => true, 'ar' => 'أي تقاليد دينية أو روحية تود أن يأخذها النظام بعين الاعتبار؟', 'en' => 'Which religious or spiritual traditions would you like the system to consider?'],
                    ['key' => 'G10', 'type' => 'single_choice', 'sensitive' => true, 'stage1' => true, 'options' => $perspectives, 'ar' => 'ما إطار التفسير الذي تفضله؟', 'en' => 'Which interpretive framework do you prefer?'],
                ],
            ],
            [
                'slug' => 'current_life',
                'name_ar' => 'ظروف الحياة الحالية',
                'name_en' => 'Current Life Circumstances',
                'description_ar' => 'مرحلتك الحالية وما يشغل بالك.',
                'description_en' => 'Your current stage and preoccupations.',
                'display_order' => 8,
                'stage' => 'initial',
                'is_sensitive' => false,
                'is_active' => true,
                'questions' => array_merge(
                    [
                        ['key' => 'H01', 'type' => 'single_choice', 'stage1' => true, 'options' => $lifeStages, 'ar' => 'كيف تصف مرحلة حياتك الحالية؟', 'en' => 'How would you describe the stage of life you are currently experiencing?'],
                        ['key' => 'H03', 'type' => 'multiple_choice', 'stage1' => true, 'options' => $thoughtTopics, 'ar' => 'ما الذي يشغل معظم تفكيرك هذه الأيام؟', 'en' => 'What occupies most of your thoughts these days?'],
                    ],
                    $this->textQuestions([
                        ['H02', 'هل أنت راضٍ عن حياتك الحالية؟', 'Are you satisfied with your current life?'],
                        ['H04', 'هل تمر بانتقال كبير في حياتك؟', 'Are you experiencing a major life transition?'],
                        ['H05', 'هل تشعر بضغط من العمل أو الدراسة؟', 'Are you experiencing pressure from work or education?'],
                        ['H06', 'هل لديك مسؤوليات تشعر أحياناً أنها مرهقة؟', 'Do you have responsibilities that sometimes feel overwhelming?'],
                        ['H07', 'هل تشعر أنك عالق في مجال معيّن من حياتك؟', 'Do you feel stuck in a particular area of life?'],
                        ['H08', 'هل هناك قرار مهم تؤجله؟', 'Is there an important decision you are postponing?'],
                        ['H09', 'هل هناك تحدٍّ متكرر لم تجد له حلاً؟', 'Is there a recurring challenge you have not found a solution for?'],
                        ['H10', 'هل تشعر أن حياتك تسير بالاتجاه الذي تريده؟', 'Do you feel that your life is moving in the direction you want?'],
                        ['H11', 'ما أكثر ما تود تغييره الآن؟', 'What would you most like to change at the moment?'],
                        ['H12', 'ماذا تحتاج أكثر في هذه المرحلة من حياتك؟', 'What do you need most during this stage of your life?'],
                    ], type: 'long_text')
                ),
            ],
            [
                'slug' => 'ambition',
                'name_ar' => 'الطموح والمعنى والغاية',
                'name_en' => 'Ambition, Meaning, and Purpose',
                'description_ar' => 'أهدافك ومعنى النجاح لديك.',
                'description_en' => 'Goals and what success means to you.',
                'display_order' => 9,
                'stage' => 'initial',
                'is_sensitive' => false,
                'is_active' => true,
                'questions' => array_merge(
                    [
                        ['key' => 'I01', 'type' => 'multiple_choice', 'stage1' => true, 'options' => $goals, 'ar' => 'ما أهم أهدافك الآن؟ (اختر حتى 3)', 'en' => 'What are your top goals now? (pick up to 3)'],
                    ],
                    $this->textQuestions([
                        ['I02', 'ماذا يعني النجاح بالنسبة لك؟', 'What does success mean to you?'],
                        ['I03', 'هل تشعر أنك تستخدم إمكاناتك؟', 'Do you feel that you are using your potential?'],
                        ['I04', 'ما الذي تعتقد أنه يمنعك من بلوغ أهدافك؟', 'What do you think prevents you from reaching your goals?'],
                        ['I05', 'ما القدرة أو الصفة التي تود تطويرها؟', 'What ability or personal quality would you like to develop?'],
                        ['I06', 'هل تشعر أن لحياتك غاية واضحة؟', 'Do you feel that your life has a clear purpose?'],
                        ['I07', 'ما العادة أو السلوك الذي تود تغييره؟', 'What habit or behavior would you like to change?'],
                        ['I08', 'ما الصفة التي تود تعزيزها في نفسك؟', 'What quality would you like to strengthen within yourself?'],
                        ['I09', 'كيف تتخيل حياتك بعد خمس سنوات؟', 'How do you imagine your life five years from now?'],
                        ['I10', 'ما الذي يمنحك شعوراً عميقاً بالتحقق؟', 'What gives you a deep sense of fulfillment?'],
                    ], type: 'long_text')
                ),
            ],
            [
                'slug' => 'sleep_dreams',
                'name_ar' => 'النوم وتاريخ الأحلام',
                'name_en' => 'Sleep and Dream History',
                'description_ar' => 'أنماط النوم وعلاقتك بالأحلام.',
                'description_en' => 'Sleep patterns and relationship with dreams.',
                'display_order' => 10,
                'stage' => 'initial',
                'is_sensitive' => false,
                'is_active' => true,
                'questions' => [
                    ['key' => 'J01', 'type' => 'single_choice', 'stage1' => true, 'options' => $sleepHours, 'ar' => 'كم ساعة تنام عادة؟', 'en' => 'How many hours do you usually sleep?'],
                    ['key' => 'J02', 'type' => 'yes_no', 'options' => $yesNo, 'ar' => 'هل تستيقظ كثيراً أثناء الليل؟', 'en' => 'Do you frequently wake during the night?'],
                    ['key' => 'J03', 'type' => 'yes_no', 'options' => $yesNo, 'ar' => 'هل تشعر عادة بالراحة عند الاستيقاظ؟', 'en' => 'Do you usually feel rested when you wake up?'],
                    ['key' => 'J04', 'type' => 'single_choice', 'stage1' => true, 'options' => $dreamRecall, 'ar' => 'ما مدى تذكرك لأحلامك؟', 'en' => 'How frequently do you remember your dreams?'],
                    ['key' => 'J05', 'type' => 'yes_no', 'options' => $yesNo, 'ar' => 'هل تختبر كوابيس متكررة؟', 'en' => 'Do you experience recurring nightmares?'],
                    ['key' => 'J06', 'type' => 'yes_no', 'options' => $yesNo, 'ar' => 'هل تتغير أحلامك خلال فترات التوتر؟', 'en' => 'Do your dreams change during stressful periods?'],
                    ['key' => 'J07', 'type' => 'yes_no', 'options' => $yesNo, 'ar' => 'هل تستخدم هاتفك قبل النوم مباشرة؟', 'en' => 'Do you use your phone shortly before sleeping?'],
                    ['key' => 'J08', 'type' => 'yes_no', 'options' => $yesNo, 'ar' => 'هل جدول نومك منتظم؟', 'en' => 'Is your sleeping schedule consistent?'],
                    ['key' => 'J09', 'type' => 'yes_no', 'options' => $yesNo, 'ar' => 'هل تشعر بالتعب كثيراً خلال النهار؟', 'en' => 'Do you frequently feel tired during the day?'],
                    ['key' => 'J10', 'type' => 'yes_no', 'options' => $yesNo, 'ar' => 'هل شهدت مؤخراً تغيّرات في روتين نومك؟', 'en' => 'Have you recently experienced changes in your sleeping routine?'],
                    ['key' => 'J11', 'type' => 'long_text', 'sensitive' => true, 'ar' => 'هل ترغب طوعاً بالإفصاح عن أدوية أو مواد قد تؤثر على النوم؟', 'en' => 'Are there medications or other substances that you voluntarily wish to disclose because they may affect sleep?', 'ar_desc' => 'اختياري وحساس ويتطلب موافقة منفصلة.', 'en_desc' => 'Optional, sensitive, and requires separate consent.'],
                    ['key' => 'K01', 'type' => 'yes_no', 'options' => $yesNo, 'ar' => 'هل تتذكر الأحلام منذ الطفولة؟', 'en' => 'Have you remembered dreams since childhood?'],
                    ['key' => 'K02', 'type' => 'yes_no', 'stage1' => true, 'options' => $yesNo, 'ar' => 'هل تختبر أحلاماً متكررة؟', 'en' => 'Do you experience recurring dreams?', 'conditional' => ['show_if_yes' => ['K03', 'K13']]],
                    ['key' => 'K03', 'type' => 'long_text', 'ar' => 'هل تظهر أماكن معينة بشكل متكرر في أحلامك؟', 'en' => 'Are there places that frequently appear in your dreams?'],
                    ['key' => 'K04', 'type' => 'yes_no', 'options' => $yesNo, 'ar' => 'هل يظهر أشخاص من ماضيك في أحلامك؟', 'en' => 'Do people from your past appear in your dreams?'],
                    ['key' => 'K05', 'type' => 'yes_no', 'options' => $yesNo, 'ar' => 'هل يظهر أشخاص غير مألوفين في أحلامك؟', 'en' => 'Do unfamiliar people appear in your dreams?'],
                    ['key' => 'K06', 'type' => 'single_choice', 'options' => ['realistic', 'imaginative', 'mixed'], 'ar' => 'هل أحلامك عادة واقعية أم خيالية؟', 'en' => 'Are your dreams usually realistic or imaginative?'],
                    ['key' => 'K07', 'type' => 'yes_no', 'options' => $yesNo, 'ar' => 'هل تختبر مشاعر قوية أثناء الأحلام؟', 'en' => 'Do you experience strong emotions during dreams?'],
                    ['key' => 'K08', 'type' => 'yes_no', 'options' => $yesNo, 'ar' => 'هل تشعر أحياناً أنك تستطيع التحكم بأحلامك؟', 'en' => 'Do you sometimes feel able to control your dreams?'],
                    ['key' => 'K09', 'type' => 'yes_no', 'options' => $yesNo, 'ar' => 'هل اختبرت أحلاماً واعية (lucid)؟', 'en' => 'Have you experienced lucid dreaming?'],
                    ['key' => 'K10', 'type' => 'yes_no', 'options' => $yesNo, 'ar' => 'هل تستيقظ بمشاعر تبقى معك؟', 'en' => 'Do you wake up with emotions that remain with you?'],
                    ['key' => 'K11', 'type' => 'long_text', 'ar' => 'هل هناك أحلام معينة ما زلت تتذكرها بوضوح؟', 'en' => 'Are there particular dreams you still remember clearly?'],
                    ['key' => 'K12', 'type' => 'yes_no', 'options' => $yesNo, 'ar' => 'هل تشعر أن بعض الأحلام مرتبطة بتجارب واقعية؟', 'en' => 'Do you feel that some dreams are connected to real-life experiences?'],
                    ['key' => 'K13', 'type' => 'long_text', 'ar' => 'أي رموز أو مشاهد تتكرر أكثر؟', 'en' => 'Which symbols or scenes seem to repeat most often?'],
                    ['key' => 'K14', 'type' => 'yes_no', 'stage1' => true, 'options' => $yesNo, 'ar' => 'هل ترغب أن يطرح الذكاء الاصطناعي أسئلة إضافية بعد كل حلم؟', 'en' => 'Would you like the AI to ask additional questions after each dream?'],
                ],
            ],
            [
                'slug' => 'values',
                'name_ar' => 'القيم والصراعات الداخلية',
                'name_en' => 'Values and Internal Conflicts',
                'description_ar' => 'ما يهمّك وما يتعارض داخلك.',
                'description_en' => 'What matters and internal conflicts.',
                'display_order' => 11,
                'stage' => 'initial',
                'is_sensitive' => false,
                'is_active' => true,
                'questions' => array_merge(
                    [
                        ['key' => 'L01', 'type' => 'multiple_choice', 'stage1' => true, 'options' => $values, 'ar' => 'ما أهم قيمك؟ (اختر ما يناسبك)', 'en' => 'What are your most important values?'],
                    ],
                    $this->textQuestions([
                        ['L02', 'ما الذي لا توافق على التنازل عنه طواعية؟', 'What would you never willingly compromise?'],
                        ['L03', 'هل تتخذ قراراتك وفق رغباتك أم توقعات الآخرين؟', 'Do you make decisions based on your own desires or other people\'s expectations?'],
                        ['L04', 'هل تفعل أحياناً أشياء تتعارض مع معتقداتك؟', 'Do you sometimes do things that conflict with your beliefs?'],
                        ['L05', 'هل تجد صعوبة في وضع حدود شخصية؟', 'Do you find it difficult to establish personal boundaries?'],
                        ['L06', 'هل تشعر بالذنب عندما تعطي الأولوية لنفسك؟', 'Do you feel guilty when prioritizing yourself?'],
                        ['L07', 'هل هناك تعارض بين أسلوب حياتك الحالي وقيمك؟', 'Is there a conflict between your current lifestyle and your personal values?'],
                        ['L08', 'ما المبدأ الذي تود أن تعيش وفقه بثبات أكبر؟', 'What principle would you like to live by more consistently?'],
                        ['L09', 'هل تسامح نفسك عندما تخطئ؟', 'Do you forgive yourself when you make mistakes?'],
                        ['L10', 'ما الذي تحتاج أن تتقبله في نفسك؟', 'What do you need to accept about yourself?'],
                    ], type: 'long_text')
                ),
            ],
            [
                'slug' => 'personal_development',
                'name_ar' => 'النمو الشخصي',
                'name_en' => 'Personal Development',
                'description_ar' => 'كيف تفضّل الاستكشاف والنمو.',
                'description_en' => 'How you prefer to explore and grow.',
                'display_order' => 12,
                'stage' => 'initial',
                'is_sensitive' => false,
                'is_active' => true,
                'questions' => [
                    ['key' => 'M01', 'type' => 'scale', 'stage1' => true, 'options' => $scale, 'ar' => 'ما مدى أهمية فهم نفسك بالنسبة لك؟', 'en' => 'How important is understanding yourself to you?'],
                    ['key' => 'M02', 'type' => 'multiple_choice', 'stage1' => true, 'options' => $developAreas, 'ar' => 'أي مجال في حياتك تود تطويره أكثر؟', 'en' => 'Which area of your life would you most like to develop?'],
                    ['key' => 'M03', 'type' => 'yes_no', 'options' => $yesNo, 'ar' => 'هل تشعر بالراحة في استكشاف أسئلة صعبة عن نفسك؟', 'en' => 'Are you comfortable exploring difficult questions about yourself?'],
                    ['key' => 'M04', 'type' => 'single_choice', 'stage1' => true, 'options' => $feedbackStyle, 'ar' => 'هل تفضّل ملاحظات مباشرة أم تأملاً تدريجياً؟', 'en' => 'Do you prefer direct feedback or gradual reflection?'],
                    ['key' => 'M05', 'type' => 'single_choice', 'stage1' => true, 'options' => $exerciseStyle, 'ar' => 'هل تفضّل تمارين عملية أم أسئلة تأملية؟', 'en' => 'Do you prefer practical exercises or reflective questions?'],
                    ['key' => 'M06', 'type' => 'yes_no', 'options' => $yesNo, 'ar' => 'هل ترغب بمراجعات تقدم أسبوعية؟', 'en' => 'Would you like weekly progress reviews?'],
                    ['key' => 'M07', 'type' => 'yes_no', 'options' => $yesNo, 'ar' => 'هل ترغب بتذكيرات حول مواضيع لم تستكشفها؟', 'en' => 'Would you like reminders about themes you have not explored?'],
                    ['key' => 'M08', 'type' => 'short_text', 'ar' => 'كم من الوقت يمكنك تخصيصه للتأمل اليومي؟', 'en' => 'How much time can you dedicate to personal reflection each day?'],
                    ['key' => 'M09', 'type' => 'long_text', 'ar' => 'كيف ستتعرف على تقدم شخصي ذي معنى؟', 'en' => 'How would you recognize meaningful personal progress?'],
                    ['key' => 'M10', 'type' => 'long_text', 'ar' => 'ما التغيير الذي تود ملاحظته في نفسك بعد ثلاثة أشهر؟', 'en' => 'What change would you like to notice in yourself after three months?'],
                ],
            ],
            [
                'slug' => 'deep_reflection',
                'name_ar' => 'أسئلة تأمل عميق',
                'name_en' => 'Deep Reflection Questions',
                'description_ar' => 'أسئلة مفتوحة اختيارية بالكامل.',
                'description_en' => 'Fully optional open-ended questions.',
                'display_order' => 13,
                'stage' => 'deeper',
                'is_sensitive' => true,
                'is_active' => true,
                'questions' => $this->textQuestions([
                    ['N01', 'ما الذي تشعر أن الآخرين لا يفهمونه عنك؟', 'What do you feel other people do not understand about you?'],
                    ['N02', 'ما الذي حملته داخلياً دون فرصة للتعبير عنه؟', 'What have you carried internally without finding an opportunity to express it?'],
                    ['N03', 'ما أكثر ما تريد فهمه عن نفسك؟', 'What do you most want to understand about yourself?'],
                    ['N04', 'هل هناك جزء من شخصيتك تشعر أنك لم تكتشفه بعد؟', 'Is there a part of your personality you feel you have not discovered yet?'],
                    ['N05', 'أي تجربة تعتقد أنها غيّرتك؟', 'What experience do you believe changed you?'],
                    ['N06', 'ما الذي تخاف مواجهته؟', 'What are you afraid to confront?'],
                    ['N07', 'ما الذي تشعر أنه ينقص حياتك؟', 'What do you feel is missing from your life?'],
                    ['N08', 'ما الذي تود أن تصالحه داخل نفسك؟', 'What would you like to make peace with within yourself?'],
                    ['N09', 'لو استطعت تغيير جانباً واحداً من تفكيرك، ماذا يكون؟', 'If you could change one aspect of your thinking, what would it be?'],
                    ['N10', 'لو استطعت أن تسأل ذاتك الداخلية سؤالاً واحداً، ماذا تسأل؟', 'If you could ask your inner self one question, what would you ask?'],
                ], sensitive: true, type: 'long_text'),
            ],
        ];
    }

    /**
     * @param  array<int, array{0: string, 1: string, 2: string}>  $items
     * @return array<int, array<string, mixed>>
     */
    private function textQuestions(array $items, bool $sensitive = false, string $type = 'long_text', array $sensitiveKeys = []): array
    {
        return array_map(function (array $item) use ($sensitive, $type, $sensitiveKeys) {
            return [
                'key' => $item[0],
                'type' => $type,
                'sensitive' => $sensitive || in_array($item[0], $sensitiveKeys, true),
                'ar' => $item[1],
                'en' => $item[2],
            ];
        }, $items);
    }
}
