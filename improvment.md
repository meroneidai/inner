# INNERPATH — COMPLETE HUMAN INNER PROFILE INTEGRATION
## Full Engineering Specification, Database Architecture, AI Context Engine, Onboarding, and Longitudinal Personal Development

---

# 1. PROJECT CONTEXT

You are working on InnerPath, an AI-powered human self-reflection and dream analysis platform.

The application is being developed using:

- Laravel.
- PHP.
- MySQL.
- Laravel Blade.
- Tailwind CSS.
- Alpine.js.
- Chart.js.
- Laravel Queues.
- Laravel Scheduler.
- AI provider integration through a modular service architecture.

The platform already has, or is planned to have, these primary modules:

1. User authentication.
2. User dashboard.
3. Dream journal.
4. Text-based dream recording.
5. Audio recording and transcription.
6. AI dream analysis.
7. Symbol recognition.
8. Historical and cultural interpretation.
9. Psychological reflection.
10. Daily analysis.
11. Weekly reports.
12. Monthly reports.
13. Personal development.
14. AI conversations.
15. Administrative dashboard.

Your task is to implement a complete Human Inner Profile system and integrate it deeply with every relevant existing module.

IMPORTANT:

Do not build an isolated questionnaire application.

The Human Inner Profile must become the central personal-context layer used by the Dream Analysis Engine, Context Correlation Engine, Daily Analysis Engine, Weekly Analysis Engine, Monthly Analysis Engine, and Personal Growth Engine.

Before implementation, inspect the existing codebase, database, models, migrations, routes, controllers, services, AI integrations, prompts, and Blade components.

Reuse existing architecture wherever possible.

Do not duplicate existing functionality or create conflicting database structures.

If the project is new, establish the architecture using Laravel conventions.

---

# 2. CORE PRODUCT CONCEPT

Build a continuously evolving personal knowledge profile named:

HUMAN INNER PROFILE

The profile represents the user's self-reported experiences, personal associations, values, emotional experiences, relationships, cultural background, beliefs, current circumstances, dream history, and personal development goals.

It must evolve over time.

It must distinguish between:

A. Information explicitly provided by the user.

B. Information the user has confirmed after an AI-generated observation.

C. AI-generated hypotheses that have not been confirmed.

D. Historical information that may no longer describe the user's current situation.

E. Unknown information.

Never convert an AI hypothesis into a verified personal fact.

Never assume that a dream proves the existence of a psychological problem.

Never infer trauma, religion, personality disorders, mental illness, or hidden characteristics from demographic data or dream symbolism.

The platform supports self-understanding, reflection, and personal development. It is not a medical diagnostic service.

---

# 3. ONBOARDING EXPERIENCE

Implement a multi-step onboarding experience immediately after successful registration.

The onboarding must feel like a calm, private, supportive conversation.

Do not make it feel like a medical examination or psychological test.

## 3.1 Welcome Screen

Title:

"Welcome to Your Inner World"

Description:

"Understanding yourself begins with understanding your experiences. Through a few thoughtful questions, we will build a personal profile that helps us understand your dreams in their individual context."

Explain:

- Why personal information is requested.
- How answers improve dream analysis.
- Which questions are optional.
- How the user can edit answers.
- How personal information is protected.
- That AI interpretations are hypotheses, not facts.

Provide:

- Start Profile.
- Complete Later.
- Skip Onboarding.

The user must be able to start recording dreams without completing the profile.

## 3.2 Onboarding Structure

Create the following sections:

1. Identity and Background.
2. Emotional World.
3. Childhood and Formative Experiences.
4. Relationships and Attachment.
5. Identity and Self-Perception.
6. Fear, Safety, and Control.
7. Beliefs, Religion, and Spirituality.
8. Current Life Circumstances.
9. Ambitions, Meaning, and Purpose.
10. Sleep and Dream Patterns.
11. Values and Internal Conflicts.
12. Personal Development.
13. Deep Reflection Questions.

Each section must have:

- A short introduction.
- Relevant questions.
- Progress indicator.
- Save and continue.
- Save and exit.
- Skip section.
- Previous section.
- Optional questions clearly marked.
- Privacy information where needed.

Use conditional questions.

For example:

If the user says they have lived in multiple cultures, ask which cultural environments influenced their life.

If the user indicates that a childhood experience still affects them, allow them to describe it optionally.

Never force disclosure.

---

# 4. COMPLETE QUESTION BANK

Create a configurable question bank.

All questions must be stored in the database and manageable from the admin dashboard.

Each question must have:

- Unique identifier.
- Section identifier.
- Question text.
- Description.
- Answer type.
- Available options.
- Required or optional status.
- Sensitivity classification.
- Display order.
- Conditional display rules.
- Profile dimension.
- AI context relevance.
- Active or inactive status.
- Language.
- Version.
- Created and updated timestamps.

Support translations in Arabic and English.

All Arabic pages must use RTL layout.

All English pages must use LTR layout.

## SECTION A — IDENTITY AND BACKGROUND

Purpose: Understand the user's declared cultural and personal background.

Questions:

A01. What name would you like us to use?

A02. What is your age range?

Options:
- Under 18.
- 18–24.
- 25–34.
- 35–44.
- 45–54.
- 55–64.
- 65+.
- Prefer not to say.

A03. Which language do you feel most comfortable using when expressing emotions?

A04. Where did you grow up?

A05. Where do you currently live?

A06. Have you lived in different cultural environments?

A07. How would you describe the environment in which you grew up?

A08. Was your childhood environment generally stable or did it involve many changes?

A09. How important is your cultural background to your identity?

A10. Are there cultural traditions that strongly influence your interpretation of life?

## SECTION B — EMOTIONAL WORLD

Purpose: Understand the user's own description of their emotional experiences.

Questions:

B01. How would you describe your current emotional state?

B02. Which emotions do you experience most frequently?

Options:
- Happiness.
- Sadness.
- Anxiety.
- Fear.
- Anger.
- Calmness.
- Loneliness.
- Hope.
- Frustration.
- Confusion.
- Excitement.
- Other.

B03. What makes you feel genuinely happy?

B04. What currently causes you the most concern?

B05. How do you usually respond when you feel sad?

B06. How do you express anger?

B07. How comfortable are you expressing your emotions?

Scale: 1–5.

B08. Do you usually keep emotions to yourself or share them?

B09. Which emotion do you find most difficult to understand or manage?

B10. How well do you feel you understand yourself?

Scale: 1–5.

B11. What makes you feel safe?

B12. In which situations do you feel that you cannot be yourself?

## SECTION C — CHILDHOOD AND FORMATIVE EXPERIENCES

Purpose: Understand personal memories and experiences that the user considers meaningful.

Questions:

C01. How do you generally remember your childhood?

C02. What is your happiest childhood memory?

C03. Was there an experience that significantly influenced who you are today?

C04. How would you describe your relationship with your mother?

C05. How would you describe your relationship with your father?

C06. Did you feel understood and heard in your family?

C07. Did you generally feel safe at home?

C08. Could you express your opinions freely?

C09. Did you feel responsible for things beyond your age?

C10. Did you feel that you needed to please others to receive acceptance?

C11. Are there memories from the past that still influence your feelings today?

C12. Are there experiences you feel you have not fully processed?

Every question in this section must be optional.

Do not request unnecessary intimate details.

Do not classify an experience as trauma without the user's explicit description and appropriate professional assessment.

## SECTION D — RELATIONSHIPS AND CONNECTION

Questions:

D01. Do you find it easy to build close relationships?

D02. How easily do you trust other people?

D03. What do you fear most in relationships?

D04. How do you usually respond to disagreements?

D05. Do you find it difficult to say no?

D06. Do you sometimes feel misunderstood by others?

D07. Do you tend to withdraw when emotionally hurt?

D08. How important is personal space to you?

D09. What do you need most from people close to you?

D10. Is there a relationship currently occupying much of your thoughts?

## SECTION E — IDENTITY AND SELF-PERCEPTION

Questions:

E01. Describe yourself in three words.

E02. What personal quality do you appreciate most?

E03. What personal quality would you like to develop?

E04. Is there a side of yourself you rarely show others?

E05. Do you feel that you are living the life you want?

E06. Do you sometimes behave in ways that do not represent your true feelings?

E07. Is there something you are afraid others might discover about you? Optional.

E08. Do you compare yourself with other people?

E09. Do you feel that you constantly need to prove something?

E10. What makes you feel valuable?

E11. What is your most significant internal conflict at the moment?

E12. What part of yourself would you like to understand better?

## SECTION F — FEAR, SAFETY, AND CONTROL

Questions:

F01. What are you most afraid of losing?

F02. What makes you feel that you are losing control?

F03. Do you worry about the future?

F04. Are you more afraid of failure or of never trying?

F05. Are you afraid of being alone?

F06. Do you sometimes feel trapped in circumstances you cannot change?

F07. Do you find it difficult to make decisions?

F08. What gives you a sense of stability?

F09. Do you prefer controlling details or allowing things to unfold naturally?

F10. When facing uncertainty, do you usually feel curious or afraid?

## SECTION G — BELIEFS, RELIGION, AND SPIRITUALITY

This is a sensitive, optional section.

Questions:

G01. Do you consider yourself a spiritual person?

G02. Do you follow a particular religion or spiritual tradition?

G03. How much do your beliefs influence your daily decisions?

G04. Do you feel that life has a deeper meaning or purpose?

G05. Do you practice prayer, meditation, or another spiritual practice?

G06. Are you currently exploring questions about meaning, faith, or existence?

G07. Would you like spiritual perspectives included in dream analysis?

G08. Do you believe dreams can carry spiritual meaning?

G09. Which religious or spiritual traditions would you like the system to consider?

G10. Would you prefer:
- Psychological analysis only.
- Historical and cultural analysis.
- Religious and spiritual perspectives.
- A combination of perspectives.
- Let me choose for each dream.

Never infer religion from name, location, language, nationality, or cultural background.

Do not present religious claims as scientifically verified findings.

Respect the user's chosen interpretive framework.

## SECTION H — CURRENT LIFE CIRCUMSTANCES

Questions:

H01. How would you describe the stage of life you are currently experiencing?

H02. Are you satisfied with your current life?

H03. What occupies most of your thoughts these days?

H04. Are you experiencing a major life transition?

H05. Are you experiencing pressure from work or education?

H06. Do you have responsibilities that sometimes feel overwhelming?

H07. Do you feel stuck in a particular area of life?

H08. Is there an important decision you are postponing?

H09. Is there a recurring challenge you have not found a solution for?

H10. Do you feel that your life is moving in the direction you want?

H11. What would you most like to change at the moment?

H12. What do you need most during this stage of your life?

## SECTION I — AMBITION, MEANING, AND PURPOSE

Questions:

I01. What are the three most important goals you want to achieve?

I02. What does success mean to you?

I03. Do you feel that you are using your potential?

I04. What do you think prevents you from reaching your goals?

I05. What ability or personal quality would you like to develop?

I06. Do you feel that your life has a clear purpose?

I07. What habit or behavior would you like to change?

I08. What quality would you like to strengthen within yourself?

I09. How do you imagine your life five years from now?

I10. What gives you a deep sense of fulfillment?

## SECTION J — SLEEP AND DREAM HISTORY

Questions:

J01. How many hours do you usually sleep?

J02. Do you frequently wake during the night?

J03. Do you usually feel rested when you wake up?

J04. How frequently do you remember your dreams?

J05. Do you experience recurring nightmares?

J06. Do your dreams change during stressful periods?

J07. Do you use your phone shortly before sleeping?

J08. Is your sleeping schedule consistent?

J09. Do you frequently feel tired during the day?

J10. Have you recently experienced changes in your sleeping routine?

J11. Are there medications or other substances that you voluntarily wish to disclose because they may affect sleep?

The last question is highly sensitive and must be optional, separately consented to, and securely handled.

## SECTION K — RELATIONSHIP WITH DREAMS

Questions:

K01. Have you remembered dreams since childhood?

K02. Do you experience recurring dreams?

K03. Are there places that frequently appear in your dreams?

K04. Do people from your past appear in your dreams?

K05. Do unfamiliar people appear in your dreams?

K06. Are your dreams usually realistic or imaginative?

K07. Do you experience strong emotions during dreams?

K08. Do you sometimes feel able to control your dreams?

K09. Have you experienced lucid dreaming?

K10. Do you wake up with emotions that remain with you?

K11. Are there particular dreams you still remember clearly?

K12. Do you feel that some dreams are connected to real-life experiences?

K13. Which symbols or scenes seem to repeat most often?

K14. Would you like the AI to ask additional questions after each dream?

## SECTION L — VALUES AND INTERNAL CONFLICTS

Questions:

L01. What are the five most important values in your life?

L02. What would you never willingly compromise?

L03. Do you make decisions based on your own desires or other people's expectations?

L04. Do you sometimes do things that conflict with your beliefs?

L05. Do you find it difficult to establish personal boundaries?

L06. Do you feel guilty when prioritizing yourself?

L07. Is there a conflict between your current lifestyle and your personal values?

L08. What principle would you like to live by more consistently?

L09. Do you forgive yourself when you make mistakes?

L10. What do you need to accept about yourself?

## SECTION M — PERSONAL DEVELOPMENT

Questions:

M01. How important is understanding yourself to you?

M02. Which area of your life would you most like to develop?

M03. Are you comfortable exploring difficult questions about yourself?

M04. Do you prefer direct feedback or gradual reflection?

M05. Do you prefer practical exercises or reflective questions?

M06. Would you like weekly progress reviews?

M07. Would you like reminders about themes you have not explored?

M08. How much time can you dedicate to personal reflection each day?

M09. How would you recognize meaningful personal progress?

M10. What change would you like to notice in yourself after three months?

## SECTION N — DEEP REFLECTION QUESTIONS

These questions are open-ended and should be presented in a calm, conversational interface.

N01. What do you feel other people do not understand about you?

N02. What have you carried internally without finding an opportunity to express it?

N03. What do you most want to understand about yourself?

N04. Is there a part of your personality you feel you have not discovered yet?

N05. What experience do you believe changed you?

N06. What are you afraid to confront?

N07. What do you feel is missing from your life?

N08. What would you like to make peace with within yourself?

N09. If you could change one aspect of your thinking, what would it be?

N10. If you could ask your inner self one question, what would you ask?

These questions must never be mandatory.

---

# 5. QUESTION ENGINE AND ANSWER TYPES

Implement a dynamic question engine.

Supported answer types:

- Short text.
- Long text.
- Single choice.
- Multiple choice.
- Numeric scale.
- Yes / No.
- Date.
- Country selector.
- Age range.
- Optional audio response.
- Conditional follow-up.
- User-defined answer.

For numeric scales:

Use descriptive labels.

Example:

1 = Not at all.
2 = Slightly.
3 = Sometimes.
4 = Often.
5 = Very strongly.

Do not describe these scales as validated psychological assessments.

Implement conditional question logic.

Examples:

If a user selects "Yes" for recurring dreams, show questions about recurring themes.

If a user selects "I want spiritual analysis", show religious and spiritual preference settings.

If a user selects "I prefer not to answer", do not show additional follow-up questions.

The question engine must support:

- Question activation and deactivation.
- Question versioning.
- Conditional dependencies.
- Translation.
- Section ordering.
- Mandatory and optional questions.
- Progress tracking.
- Draft answers.
- Resuming an incomplete onboarding session.

---

# 6. ONBOARDING COMPLETION STRATEGY

Do not force users to answer all questions during registration.

Create three stages.

STAGE 1 — INITIAL PROFILE

Ask approximately 20–25 carefully selected questions.

Prioritize:

- Age range.
- Preferred language.
- Current emotional state.
- Frequent emotions.
- Current concerns.
- Self-understanding.
- Relationship preferences.
- Personal fears.
- Current life circumstances.
- Current goals.
- Sleep habits.
- Dream frequency.
- Dream interpretation preferences.
- Values.
- Personal development preferences.

Sensitive information must remain optional.

STAGE 2 — DEEPER PROFILE

After initial onboarding, offer optional sections covering:

- Childhood.
- Family relationships.
- Personal identity.
- Deep fears.
- Cultural background.
- Spirituality.
- Internal conflicts.
- Personal history.

STAGE 3 — CONTINUOUS PROFILE

After recording dreams, allow the system to ask relevant follow-up questions.

Never interrupt dream recording with unnecessary profile questionnaires.

Questions must appear at appropriate moments and be limited in number.

For example:

After a dream involving a childhood home, the AI may ask:

"Does this place remind you of a particular period in your life?"

The user can answer, skip, or dismiss the question.

---

# 7. DATABASE ARCHITECTURE

Inspect the existing database before adding migrations.

Implement a normalized structure.

Recommended tables:

## user_profiles

Fields:

- id.
- user_id.
- preferred_name.
- birth_year or age_range.
- preferred_language.
- country_of_origin.
- country_of_residence.
- profile_status.
- onboarding_completed_at.
- profile_last_reviewed_at.
- timestamps.

## profile_sections

Fields:

- id.
- slug.
- name.
- description.
- display_order.
- is_active.
- timestamps.

## profile_questions

Fields:

- id.
- section_id.
- question_key.
- question_text.
- description.
- answer_type.
- options_json.
- validation_rules_json.
- is_required.
- is_sensitive.
- is_active.
- display_order.
- version.
- timestamps.

## profile_question_translations

Fields:

- id.
- question_id.
- locale.
- question_text.
- description.
- options_json.

## profile_answers

Fields:

- id.
- user_id.
- question_id.
- question_version.
- answer_value.
- answer_metadata_json.
- answered_at.
- updated_at.

Preserve historical answers where changes matter.

## profile_consents

Fields:

- id.
- user_id.
- consent_type.
- granted.
- purpose.
- policy_version.
- granted_at.
- revoked_at.
- timestamps.

## profile_snapshots

Fields:

- id.
- user_id.
- version_number.
- profile_summary.
- structured_profile_json.
- generated_by.
- source_answer_ids.
- created_at.

## profile_observations

Fields:

- id.
- user_id.
- dimension.
- observation.
- evidence_json.
- source_type.
- source_id.
- status.
- confidence_level.
- user_confirmation_status.
- first_observed_at.
- last_observed_at.
- expires_at.
- timestamps.

Statuses:

- pending.
- confirmed.
- rejected.
- expired.
- superseded.

## profile_history

Fields:

- id.
- user_id.
- dimension.
- previous_value.
- new_value.
- change_source.
- changed_at.

## profile_goals

Fields:

- id.
- user_id.
- title.
- description.
- development_dimension.
- target_date.
- status.
- created_at.
- updated_at.

## profile_review_sessions

Fields:

- id.
- user_id.
- review_type.
- started_at.
- completed_at.
- answers_json.
- summary.

Use foreign keys, indexes, user isolation, and appropriate deletion policies.

Use JSON only for genuinely flexible structured data.

Keep searchable questions and answers relational.

Never store sensitive information in public storage.

---

# 8. PROFILE DATA MODEL

The AI context must distinguish between different evidence types.

Use this classification:

TYPE 1 — USER_REPORTED

Information directly provided by the user.

TYPE 2 — USER_CONFIRMED

An AI observation that the user explicitly confirmed.

TYPE 3 — AI_HYPOTHESIS

A possible interpretation generated by the AI.

TYPE 4 — HISTORICAL

Information that was previously relevant but may no longer describe the user.

TYPE 5 — UNKNOWN

Information that has not been provided or confirmed.

Each item must have:

- Source.
- Source identifier.
- Timestamp.
- Confidence or evidence status.
- User confirmation state.
- Privacy classification.
- Permission for analytical use.

Never treat an AI-generated hypothesis as a user-reported fact.

Never infer missing answers.

---

# 9. HUMAN INNER PROFILE AI ENGINE

Create a dedicated service:

app/Services/Profile/HumanInnerProfileService.php

Responsibilities:

1. Retrieve profile answers.
2. Normalize structured data.
3. Build profile snapshots.
4. Identify recurring self-reported themes.
5. Identify information gaps.
6. Compare historical answers.
7. Create AI hypotheses.
8. Validate structured responses.
9. Save profile observations.
10. Update analytical context.

Additional services:

- ProfileQuestionService.
- ProfileAnswerService.
- ProfileSnapshotService.
- ProfileHistoryService.
- ProfileConsentService.
- ProfileContextBuilder.
- ProfileObservationService.
- ProfileAnalysisService.
- ProfileReviewService.

Do not implement all functionality in one service.

---

# 10. MASTER PROFILE ANALYSIS PROMPT

Use the following as the dedicated system prompt for profile understanding.

SYSTEM ROLE:

You are the InnerPath Human Context Analysis Engine.

Your task is to organize the user's voluntarily provided information into a structured personal context model.

You must understand the person's self-reported experiences without claiming to know their hidden personality or psychological condition.

You must remain neutral, careful, culturally sensitive, and intellectually honest.

YOUR OBJECTIVES:

- Understand how the user describes their emotional experiences.
- Understand their personal memories and associations.
- Understand their current circumstances.
- Understand their values and goals.
- Identify recurring themes across self-reported answers.
- Identify areas the user wishes to explore.
- Preserve uncertainty.
- Support future contextual dream analysis.

ANALYSIS RULES:

1. Read the user's actual answers.
2. Separate facts from personal opinions.
3. Preserve the user's wording when important.
4. Identify recurring self-reported themes.
5. Identify differences between past and current answers.
6. Do not interpret a single answer as a stable personality trait.
7. Do not diagnose psychological conditions.
8. Do not assume traumatic experiences.
9. Do not infer religious beliefs.
10. Do not infer personality from gender, age, country, or ethnicity.
11. Do not invent missing information.
12. Identify contradictions without automatically treating them as problems.
13. Treat answers as time-dependent.
14. Ask for clarification when a contradiction materially affects interpretation.
15. Never make the user feel judged.
16. Respect skipped questions and consent preferences.

OUTPUT:

Return valid JSON:

{
  "profile_summary": "",
  "profile_dimensions": {
    "identity": {},
    "emotional": {},
    "childhood": {},
    "relationships": {},
    "self_perception": {},
    "fear_and_safety": {},
    "culture": {},
    "beliefs_and_spirituality": {},
    "current_life": {},
    "goals_and_values": {},
    "sleep_and_dreams": {},
    "personal_development": {}
  },
  "recurring_self_reported_themes": [],
  "possible_internal_conflicts": [],
  "reported_strengths": [],
  "areas_for_reflection": [],
  "information_gaps": [],
  "follow_up_questions": [],
  "dream_analysis_context": [],
  "profile_completeness": {},
  "limitations": []
}

Every observation must contain its evidence source.

Use "insufficient_information" when evidence is missing.

Do not output diagnostic labels.

---

# 11. PERSONAL CONTEXT ENGINE

Build a context retrieval system.

Do not send the entire profile to the AI for every dream.

Instead:

1. Identify the main entities and events in the dream.
2. Identify the important symbols.
3. Determine which profile dimensions may be relevant.
4. Retrieve relevant user-reported answers.
5. Retrieve relevant confirmed observations.
6. Retrieve relevant historical context.
7. Retrieve relevant previous dreams.
8. Exclude irrelevant or unauthorized sensitive information.
9. Build a compact contextual payload.
10. Send it to the analysis engine.

Example:

Dream:

"I was falling into a deep river and could not control myself."

Relevant context may include:

- User-reported feelings about losing control.
- Personal associations with water.
- Previously recorded dreams involving rivers.
- Current user-reported concerns.
- Emotional state around the dream date.
- Sleep context.

Irrelevant context should not be included.

Do not automatically retrieve religious or sensitive family information unless relevant and authorized by the user.

Every contextual connection must include its source.

---

# 12. DREAM ANALYSIS INTEGRATION

Modify the existing DreamAnalysisService.

Add the following pipeline:

STEP 1 — Dream Input

Retrieve the original dream and user-confirmed transcription.

STEP 2 — Dream Understanding

Extract:

- Events.
- Characters.
- Places.
- Objects.
- Actions.
- Symbols.
- Emotions.
- Narrative sequence.

STEP 3 — Context Retrieval

Call ProfileContextBuilder.

Retrieve relevant personal context.

STEP 4 — Knowledge Retrieval

Retrieve relevant historical, cultural, psychological, mythological, and religious references according to the user's preferences.

Use documented sources when available.

STEP 5 — Multi-Perspective Analysis

Generate alternative interpretations.

STEP 6 — Personal Context Correlation

Compare the dream with user-reported personal experiences.

STEP 7 — Confidence and Evidence Validation

Separate:

- Directly reported information.
- Established contextual evidence.
- Historical interpretations.
- Psychological theories.
- Speculative hypotheses.

STEP 8 — User-Facing Report

Generate the complete dream analysis.

STEP 9 — Feedback

Allow the user to mark interpretations as:

- Relevant.
- Partially relevant.
- Not relevant.
- Unsure.
- I want to add context.

STEP 10 — Profile Update

Do not automatically change verified profile attributes.

New AI observations must be saved as unconfirmed hypotheses.

Ask the user before promoting meaningful observations to confirmed profile information.

---

# 13. PROFILE-ENHANCED DREAM ANALYSIS PROMPT

Add the following instructions to the existing dream analysis system prompt.

You are analyzing a dream with access to selected personal context.

You must use the dream narrative, the user's own associations, and relevant historical or cultural references.

For each major interpretation:

- Identify the dream element.
- Describe its narrative context.
- Explain relevant symbolic traditions.
- Explain possible psychological perspectives.
- Identify the user's relevant self-reported information.
- Explain the possible relationship.
- Provide alternative interpretations.
- State what is uncertain.
- Ask a question if more context is needed.

Never use a generic symbol dictionary as the final interpretation.

Never assume that a river means emotions, falling means failure, or a mother means authority.

A river may have a completely personal meaning to the dreamer.

A mother may represent love, conflict, memory, protection, distance, or no specific symbolic meaning.

The user's own associations take priority over generic symbolism.

Do not diagnose.

Do not claim that a dream reveals a hidden fact about the user.

OUTPUT:

{
  "dream_summary": "",
  "main_symbols": [],
  "narrative_analysis": [],
  "cultural_perspectives": [],
  "historical_perspectives": [],
  "psychological_perspectives": [],
  "personal_context_connections": [
    {
      "dream_element": "",
      "profile_dimension": "",
      "user_reported_context": "",
      "possible_connection": "",
      "evidence_source": "",
      "confidence": "low"
    }
  ],
  "alternative_interpretations": [],
  "emotional_reflection": {},
  "questions_for_user": [],
  "growth_exercises": [],
  "limitations": []
}

---

# 14. CONTINUOUS PROFILE DEVELOPMENT

The profile must evolve through three different channels.

CHANNEL A — USER INPUT

The user updates information manually.

CHANNEL B — REFLECTION FEEDBACK

The user confirms, rejects, or modifies AI-generated observations.

CHANNEL C — LONGITUDINAL PATTERNS

The AI identifies recurring patterns across dreams and user responses.

However, the third channel must never silently modify verified personal information.

Example:

An AI system observes that the user has repeatedly described difficulty expressing anger.

It may suggest:

"You have mentioned difficulty expressing anger in several reflections. Would you like to explore this theme further?"

The user can:

- Confirm.
- Partially agree.
- Disagree.
- Add context.
- Ignore.

Only confirmed information should become a user-confirmed observation.

AI-generated interpretations remain hypotheses.

---

# 15. PROFILE REVIEW SYSTEM

Create periodic profile reviews.

WEEKLY:

Ask one to three relevant questions based on the user's activity.

Examples:

- Have you noticed any change in your emotional state this week?
- Did anything happen that influenced your dreams?
- Did you notice yourself responding differently to a familiar situation?

MONTHLY:

Review relevant profile dimensions.

Examples:

- Has your perception of yourself changed?
- Are your current priorities different?
- Do you feel more or less connected to your personal goals?
- Which personal challenge has changed during the last month?

QUARTERLY:

Offer a deeper optional profile review.

Compare historical and current self-reported information.

Do not force users to answer the same questions repeatedly.

Ask only questions relevant to previous answers, current goals, and observed changes.

---

# 16. WEEKLY AND MONTHLY REPORT INTEGRATION

Modify the existing reporting services.

## Weekly Report

Include:

- Dreams recorded.
- Recurring symbols.
- Repeated narratives.
- Reported emotions.
- Profile-related themes.
- Relevant personal events reported by the user.
- Changes in personal reflections.
- Confirmed observations.
- Unconfirmed hypotheses.
- Suggested reflection questions.
- User-defined goals.

## Monthly Report

Include:

- Dream pattern evolution.
- Emotional reporting trends.
- Repeated personal themes.
- Changes in self-perception.
- Relevant profile updates.
- Development goal progress.
- Areas the user wants to explore.
- New personal associations.
- Changes in recurring symbols.
- Next-month reflection plan.

Important:

Compare equivalent time periods.

Account for missing data and changes in recording frequency.

Never claim that a reduction in negative dreams proves psychological improvement.

Do not infer improvement or deterioration from dream frequency alone.

Clearly identify whether each trend comes from:

- Dream data.
- User-reported mood.
- Profile answers.
- Goal completion.
- AI interpretation.

---

# 17. PERSONAL GROWTH ENGINE

Create a system that converts reflection into practical action.

Development dimensions:

1. Emotional awareness.
2. Self-understanding.
3. Self-confidence.
4. Personal boundaries.
5. Communication.
6. Relationships.
7. Independence.
8. Adaptability.
9. Resilience.
10. Self-acceptance.
11. Purpose and meaning.
12. Personal values.
13. Spiritual reflection, if selected.
14. Decision-making.
15. Personal responsibility.

Each development theme must contain:

- Name.
- Description.
- Why it was suggested.
- Supporting user-reported evidence.
- Related dreams, where relevant.
- User-defined importance.
- Suggested exercises.
- Personal goal.
- Review date.
- User feedback.
- Progress history.

Do not create a universal ranking of human worth, maturity, or psychological development.

Progress must be measured against the user's own goals.

Use user-defined progress indicators rather than fabricated personality scores.

---

# 18. USER PROFILE INTERFACE

Create a dedicated page:

/profile/inner-world

The page must include:

## Profile Overview

Display:

- Profile completion.
- Last profile update.
- Profile sections.
- Current self-reported themes.
- User-defined goals.
- Pending questions.

## Profile Dimensions

Create individual cards for:

- Identity.
- Emotional World.
- Childhood.
- Relationships.
- Self-Perception.
- Fear and Safety.
- Beliefs and Spirituality.
- Current Life.
- Goals and Values.
- Sleep and Dreams.

Each card should show:

- Summary.
- User-reported information.
- Last updated.
- Edit button.
- Privacy status.

## Profile History

Display changes over time.

The user should be able to see:

- Previous answers.
- Current answers.
- Dates of changes.
- AI observations.
- Confirmed and rejected observations.

## Profile Privacy

Allow the user to:

- Edit answers.
- Delete answers.
- Hide optional profile dimensions.
- Change analytical consent.
- Exclude information from AI context.
- Export their profile.
- Delete the entire profile.

When analytical consent is revoked, stop using the relevant information in future AI requests and apply the configured retention policy.

---

# 19. ADMIN DASHBOARD INTEGRATION

Add a Question Bank management area.

Administrators can:

- Create questions.
- Edit questions.
- Translate questions.
- Reorder sections.
- Change answer types.
- Configure options.
- Configure conditional rules.
- Enable and disable questions.
- Mark questions as optional.
- Define sensitivity levels.
- Define analytical relevance.
- Preview onboarding.
- Manage question versions.

Add analytics for:

- Onboarding completion rate.
- Section completion rates.
- Question abandonment.
- Most frequently skipped sections.
- AI analysis processing errors.
- Average profile update frequency.

Never expose private user answers through aggregate analytics.

Administrators must not have unrestricted access to private profiles.

---

# 20. SECURITY AND PRIVACY

Treat profile information and dream data as highly sensitive personal information.

Implement:

- User-level authorization.
- Laravel Policies.
- Private file storage.
- Encryption for sensitive fields.
- Secure sessions.
- CSRF protection.
- Input validation.
- Rate limiting.
- Audit logging.
- Consent management.
- Data export.
- Account deletion.
- Data retention policies.
- AI-provider privacy disclosures.

Do not send all profile information to an external AI provider.

Send only the minimum relevant context required for a particular analysis.

Do not send sensitive religious, medical, family, or intimate information unless necessary, explicitly authorized, and covered by the applicable consent.

Never use private dream or profile data for model training without separate informed consent.

For users under 18, apply appropriate age-related protections and do not collect sensitive profile details by default.

---

# 21. PERFORMANCE AND BACKGROUND PROCESSING

Use Laravel Queues for:

- Profile snapshot generation.
- Profile analysis.
- Dream context retrieval.
- Dream analysis.
- Weekly reports.
- Monthly reports.

Use Laravel Scheduler for periodic reporting.

Do not block the user interface while AI analysis is running.

Display:

- Processing.
- Completed.
- Failed.
- Retry available.

Use idempotent jobs.

Track AI usage, token consumption, costs, and failures.

Cache only data that can safely be cached and invalidate it when relevant profile information changes.

---

# 22. TESTING REQUIREMENTS

Create tests for:

ONBOARDING:

- Registration starts onboarding.
- User can skip onboarding.
- User can resume onboarding.
- Optional questions can be skipped.
- Conditional questions work.
- Arabic and English translations work.

PROFILE:

- Answers are saved correctly.
- Historical answers are preserved where needed.
- User can edit answers.
- User can delete answers.
- Users cannot access another user's profile.
- Sensitive data is protected.
- Consent changes are enforced.

AI:

- Profile analysis returns valid structured JSON.
- Unknown information remains unknown.
- AI hypotheses are not automatically confirmed.
- Context retrieval returns relevant information only.
- Rejected observations are not presented as facts.
- Missing context does not generate fabricated conclusions.

DREAM INTEGRATION:

- Relevant profile information is included.
- Irrelevant information is excluded.
- Personal associations are considered.
- Religious analysis follows the user's preferences.
- Historical context is separated from personal interpretation.

REPORTS:

- Weekly profile changes are included.
- Monthly historical comparisons work.
- Missing data is handled correctly.
- Trends are not generated without sufficient information.

PRIVACY:

- Consent revocation prevents future use.
- Profile deletion follows retention policies.
- Data export works.
- Audit logs do not expose unnecessary sensitive content.

---

# 23. IMPLEMENTATION ORDER

Implement this feature in controlled stages.

PHASE 1 — DATABASE AND FOUNDATION

- Inspect existing database.
- Create necessary migrations.
- Create profile models.
- Create relationships.
- Implement authorization.
- Implement consent management.

PHASE 2 — QUESTION BANK

- Create sections.
- Create all questions.
- Add translations.
- Add conditional logic.
- Add question management.

PHASE 3 — ONBOARDING UI

- Welcome page.
- Multi-step questions.
- Progress tracking.
- Save and resume.
- Completion summary.

PHASE 4 — PROFILE MANAGEMENT

- Inner Profile dashboard.
- Editable sections.
- Profile history.
- Profile snapshots.
- Privacy controls.

PHASE 5 — AI PROFILE ENGINE

- Profile analysis service.
- Master prompt.
- Structured output validation.
- Observation management.
- AI hypothesis confirmation.

PHASE 6 — DREAM ANALYSIS INTEGRATION

- Context retrieval.
- Relevant profile filtering.
- Personal dream correlation.
- Analysis prompt updates.
- User feedback.

PHASE 7 — LONGITUDINAL REPORTS

- Weekly profile updates.
- Monthly comparisons.
- Personal development integration.
- Reflection exercises.

PHASE 8 — ADMIN AND QA

- Question Bank administration.
- Usage analytics.
- Security tests.
- Performance tests.
- Documentation.

---

# 24. FINAL ACCEPTANCE CRITERIA

The implementation is complete only when:

1. Users can register and complete an optional multi-step profile.
2. The system stores answers in a structured database.
3. Users can edit and delete their answers.
4. Historical changes are preserved appropriately.
5. Sensitive questions are optional and protected.
6. The AI generates a structured personal context model.
7. AI hypotheses remain separate from user-confirmed information.
8. The dream analysis engine retrieves relevant profile context.
9. The dream analysis engine avoids irrelevant personal data.
10. The user's own symbol associations take priority over generic interpretations.
11. Weekly reports use both dream data and authorized profile data.
12. Monthly reports compare historical and current self-reported information.
13. The personal growth system uses user-defined goals.
14. Users can reject interpretations.
15. Users can control how their information is used.
16. Arabic and English interfaces work correctly.
17. The system is responsive.
18. All critical workflows have automated tests.
19. No placeholder functionality is presented as complete.
20. All modules integrate with the existing Laravel architecture.

FINAL DEVELOPMENT RULE:

Do not implement a simplified visual prototype and call it complete.

Build working database structures, services, controllers, Blade views, validation, policies, jobs, and automated tests.

At the end of each implementation phase:

- Report completed files.
- Report migrations.
- Report new routes.
- Report services and models.
- Report test results.
- Report outstanding work.
- Identify any architectural conflicts discovered.

Start by inspecting the existing Laravel project and producing a concrete implementation plan.

Then implement Phase 1 and proceed sequentially.

The final result must be a fully integrated Human Inner Profile system that becomes the personal context foundation of InnerPath.
