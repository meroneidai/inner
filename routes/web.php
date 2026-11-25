<?php

use App\Http\Controllers\AnalysisController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DreamController;
use App\Http\Controllers\EmotionController;
use App\Http\Controllers\GrowthController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\InnerWorldController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicPageController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SymbolController;
use App\Http\Controllers\Admin\AiSettingController as AdminAiSettingController;
use App\Http\Controllers\Admin\ContentArticleController as AdminContentArticleController;
use App\Http\Controllers\Admin\ContentPillarController as AdminContentPillarController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\PromptController as AdminPromptController;
use App\Http\Controllers\Admin\QuestionBankController as AdminQuestionBankController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Content\KnowledgeHubController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicPageController::class, 'home'])->name('home');
Route::get('/about', [PublicPageController::class, 'about'])->name('about');
Route::get('/how-it-works', [PublicPageController::class, 'howItWorks'])->name('how-it-works');
Route::get('/privacy', [PublicPageController::class, 'privacy'])->name('privacy');
Route::get('/terms', [PublicPageController::class, 'terms'])->name('terms');
Route::get('/editorial', [PublicPageController::class, 'editorial'])->name('editorial');
Route::get('/ai-transparency', [PublicPageController::class, 'aiTransparency'])->name('ai-transparency');
Route::get('/sources', [PublicPageController::class, 'sources'])->name('sources');
Route::get('/contact', [PublicPageController::class, 'contact'])->name('contact');
Route::post('/contact', [PublicPageController::class, 'sendContact'])->name('contact.send');
Route::get('/locale/{locale}', [LocaleController::class, 'update'])->name('locale.switch');
Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');

Route::get('/explore', [KnowledgeHubController::class, 'index'])->name('explore.index');
Route::get('/explore/features/{feature}', [KnowledgeHubController::class, 'feature'])->name('explore.feature');
Route::get('/dream-journal', fn () => redirect()->route('explore.feature', 'dream-journal'));
Route::get('/dream-analysis', fn () => redirect()->route('explore.feature', 'dream-analysis'));
Route::get('/inner-profile', fn () => redirect()->route('explore.feature', 'inner-profile'));
Route::get('/patterns-and-insights', fn () => redirect()->route('explore.feature', 'patterns-and-insights'));
Route::get('/personal-growth', fn () => redirect()->route('explore.feature', 'personal-growth'));
Route::get('/personal-reports', fn () => redirect()->route('explore.feature', 'personal-reports'));
Route::get('/mobile-app', fn () => redirect()->route('explore.feature', 'mobile-app'));
Route::get('/explore/{pillar:slug}', [KnowledgeHubController::class, 'pillar'])->name('explore.pillar');
Route::get('/explore/{pillar:slug}/{article:slug}', [KnowledgeHubController::class, 'article'])->name('explore.article')->scopeBindings();

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::get('/onboarding', [OnboardingController::class, 'welcome'])->name('onboarding.show');
    Route::get('/onboarding/welcome', [OnboardingController::class, 'welcome'])->name('onboarding.welcome');
    Route::post('/onboarding/skip', [OnboardingController::class, 'skip'])->name('onboarding.skip');
    Route::get('/onboarding/stage-1', [OnboardingController::class, 'stage1'])->name('onboarding.stage1');
    Route::post('/onboarding/stage-1', [OnboardingController::class, 'saveStage1'])->name('onboarding.stage1.save');
    Route::get('/onboarding/deeper', [OnboardingController::class, 'deeper'])->name('onboarding.deeper');
    Route::get('/onboarding/sections/{slug}', [OnboardingController::class, 'section'])->name('onboarding.section');
    Route::post('/onboarding/sections/{slug}', [OnboardingController::class, 'saveSection'])->name('onboarding.section.save');
    Route::post('/onboarding', [OnboardingController::class, 'store'])->name('onboarding.store');

    Route::get('/profile/inner-world', [InnerWorldController::class, 'index'])->name('inner-world.index');
    Route::get('/profile/inner-world/history', [InnerWorldController::class, 'history'])->name('inner-world.history');
    Route::get('/profile/inner-world/privacy', [InnerWorldController::class, 'privacy'])->name('inner-world.privacy');
    Route::post('/profile/inner-world/privacy', [InnerWorldController::class, 'updatePrivacy'])->name('inner-world.privacy.update');
    Route::get('/profile/inner-world/export', [InnerWorldController::class, 'export'])->name('inner-world.export');
    Route::post('/profile/inner-world/analyze', [InnerWorldController::class, 'analyze'])->name('inner-world.analyze');
    Route::post('/profile/inner-world/goals', [InnerWorldController::class, 'storeGoal'])->name('inner-world.goals.store');
    Route::post('/profile/inner-world/observations/{observation}/confirm', [InnerWorldController::class, 'confirmObservation'])->name('inner-world.observations.confirm');
    Route::post('/profile/inner-world/observations/{observation}/reject', [InnerWorldController::class, 'rejectObservation'])->name('inner-world.observations.reject');
    Route::delete('/profile/inner-world/questions/{question}', [InnerWorldController::class, 'deleteAnswer'])->name('inner-world.answers.delete');
    Route::get('/profile/inner-world/{slug}', [InnerWorldController::class, 'section'])->name('inner-world.section');
    Route::post('/profile/inner-world/{slug}', [InnerWorldController::class, 'updateSection'])->name('inner-world.section.update');

    Route::resource('dreams', DreamController::class);
    Route::post('/dreams/{dream}/transcription', [DreamController::class, 'confirmTranscription'])->name('dreams.transcription.confirm');

    Route::get('/analyses/{analysis}', [AnalysisController::class, 'show'])->name('analyses.show');
    Route::post('/analyses/{analysis}/feedback', [AnalysisController::class, 'feedback'])->name('analyses.feedback');

    Route::get('/symbols', [SymbolController::class, 'index'])->name('symbols.index');
    Route::get('/emotions', [EmotionController::class, 'index'])->name('emotions.index');

    Route::get('/reports/weekly', [ReportController::class, 'weekly'])->name('reports.weekly');
    Route::post('/reports/weekly/generate', [ReportController::class, 'generateWeekly'])->name('reports.weekly.generate');
    Route::get('/reports/weekly/{report}', [ReportController::class, 'showWeekly'])->name('reports.weekly.show');
    Route::get('/reports/monthly', [ReportController::class, 'monthly'])->name('reports.monthly');
    Route::post('/reports/monthly/generate', [ReportController::class, 'generateMonthly'])->name('reports.monthly.generate');
    Route::get('/reports/monthly/{report}', [ReportController::class, 'showMonthly'])->name('reports.monthly.show');

    Route::get('/growth', [GrowthController::class, 'index'])->name('growth.index');
    Route::post('/growth/actions/{action}/complete', [GrowthController::class, 'completeAction'])->name('growth.actions.complete');
    Route::post('/growth/actions/{action}/skip', [GrowthController::class, 'skipAction'])->name('growth.actions.skip');
    Route::post('/growth/themes/{theme}/progress', [GrowthController::class, 'addProgress'])->name('growth.progress');
    Route::get('/reflections', [GrowthController::class, 'reflections'])->name('reflections.index');
    Route::post('/reflections', [GrowthController::class, 'storeReflection'])->name('reflections.store');

    Route::resource('conversations', ConversationController::class)->only(['index', 'create', 'store', 'show']);
    Route::post('/conversations/{conversation}/reply', [ConversationController::class, 'reply'])->name('conversations.reply');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markRead'])->name('notifications.read');

    Route::get('/settings', [SettingsController::class, 'edit'])->name('settings.edit');
    Route::patch('/settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::get('/settings/privacy', [SettingsController::class, 'privacy'])->name('settings.privacy');
    Route::get('/settings/export', [SettingsController::class, 'export'])->name('settings.export');
    Route::delete('/settings/account', [SettingsController::class, 'destroyAccount'])->name('settings.account.destroy');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', AdminDashboardController::class)->name('dashboard');
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::post('/users/{user}/toggle', [AdminUserController::class, 'toggle'])->name('users.toggle');
    Route::get('/prompts', [AdminPromptController::class, 'index'])->name('prompts.index');
    Route::post('/prompts', [AdminPromptController::class, 'store'])->name('prompts.store');
    Route::get('/prompts/{prompt}/edit', [AdminPromptController::class, 'edit'])->name('prompts.edit');
    Route::put('/prompts/{prompt}', [AdminPromptController::class, 'update'])->name('prompts.update');
    Route::post('/prompts/{prompt}/activate', [AdminPromptController::class, 'activate'])->name('prompts.activate');
    Route::get('/ai', [AdminAiSettingController::class, 'index'])->name('ai.index');
    Route::post('/ai', [AdminAiSettingController::class, 'update'])->name('ai.update');
    Route::get('/questions', [AdminQuestionBankController::class, 'index'])->name('questions.index');
    Route::get('/questions/{question}/edit', [AdminQuestionBankController::class, 'edit'])->name('questions.edit');
    Route::put('/questions/{question}', [AdminQuestionBankController::class, 'update'])->name('questions.update');
    Route::post('/questions/{question}/toggle', [AdminQuestionBankController::class, 'toggle'])->name('questions.toggle');

    Route::get('/content/pillars', [AdminContentPillarController::class, 'index'])->name('content.pillars.index');
    Route::get('/content/pillars/{pillar}/edit', [AdminContentPillarController::class, 'edit'])->name('content.pillars.edit');
    Route::put('/content/pillars/{pillar}', [AdminContentPillarController::class, 'update'])->name('content.pillars.update');
    Route::get('/content/articles', [AdminContentArticleController::class, 'index'])->name('content.articles.index');
    Route::get('/content/articles/create', [AdminContentArticleController::class, 'create'])->name('content.articles.create');
    Route::post('/content/articles', [AdminContentArticleController::class, 'store'])->name('content.articles.store');
    Route::get('/content/articles/{article}/edit', [AdminContentArticleController::class, 'edit'])->name('content.articles.edit');
    Route::put('/content/articles/{article}', [AdminContentArticleController::class, 'update'])->name('content.articles.update');
});

require __DIR__.'/auth.php';
