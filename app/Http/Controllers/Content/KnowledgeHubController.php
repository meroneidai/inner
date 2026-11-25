<?php

namespace App\Http\Controllers\Content;

use App\Http\Controllers\Controller;
use App\Models\Content\Article;
use App\Models\Content\Pillar;
use Illuminate\View\View;

class KnowledgeHubController extends Controller
{
    public function index(): View
    {
        $locale = app()->getLocale();

        $pillars = Pillar::query()
            ->published()
            ->where('locale', $locale)
            ->orderBy('sort_order')
            ->withCount(['articles' => fn ($q) => $q->published()])
            ->get();

        if ($pillars->isEmpty()) {
            $pillars = Pillar::query()
                ->published()
                ->orderBy('sort_order')
                ->withCount(['articles' => fn ($q) => $q->published()])
                ->get();
        }

        $featured = Article::query()
            ->published()
            ->with('pillar')
            ->latest('published_at')
            ->limit(6)
            ->get();

        return view('content.hub', [
            'pillars' => $pillars,
            'featured' => $featured,
        ]);
    }

    public function pillar(Pillar $pillar): View
    {
        abort_unless($pillar->status === 'published', 404);

        $articles = $pillar->articles()
            ->published()
            ->orderByDesc('published_at')
            ->paginate(12);

        $faqs = $pillar->faqs()->where('is_published', true)->get();

        return view('content.pillar', [
            'pillar' => $pillar,
            'articles' => $articles,
            'faqs' => $faqs,
        ]);
    }

    public function article(Pillar $pillar, Article $article): View
    {
        abort_unless(
            $article->pillar_id === $pillar->id
            && $article->editorial_status === 'published',
            404
        );

        $article->load([
            'author',
            'faqs' => fn ($q) => $q->where('is_published', true),
            'references',
            'relatedArticles.pillar',
            'pillar',
        ]);

        return view('content.article', [
            'pillar' => $pillar,
            'article' => $article,
        ]);
    }

    public function feature(string $feature): View
    {
        $pages = $this->featurePages();
        abort_unless(isset($pages[$feature]), 404);

        return view('content.feature', [
            'feature' => $feature,
            'page' => $pages[$feature],
        ]);
    }

    /**
     * @return array<string, array{title: string, tagline: string, body: string, image: string, cta_route: string, cta_label: string, points: list<string>}>
     */
    private function featurePages(): array
    {
        return [
            'dream-journal' => [
                'title' => __('يومية الأحلام'),
                'tagline' => __('احفظ أحلامك قبل أن تتلاشى — نصاً أو صوتاً.'),
                'body' => __('مساحة خاصة لتسجيل الأحلام، تنظيمها، والعودة إليها لاحقاً لرؤية الأنماط والتجارب المرتبطة بها.'),
                'image' => 'images/brand/journal.jpg',
                'cta_route' => 'register',
                'cta_label' => __('ابدأ يوميتك'),
                'points' => [
                    __('تسجيل نصي أو صوتي'),
                    __('حفظ خاص وآمن'),
                    __('مراجعة الأحلام السابقة'),
                    __('ربط الحلم بسياقك الشخصي'),
                ],
            ],
            'dream-analysis' => [
                'title' => __('استكشاف الأحلام'),
                'tagline' => __('تفسيرات محتملة — لا حقائق مطلقة.'),
                'body' => __('يساعدك التحليل على استكشاف الحلم عبر مشاعرك وذكرياتك وظروفك. النتائج احتمالات للتأمل، وليست تشخيصاً أو تنبؤاً.'),
                'image' => 'images/brand/inner-world.jpg',
                'cta_route' => 'register',
                'cta_label' => __('استكشف حلماً'),
                'points' => [
                    __('سياق شخصي لا قواميس عامة فقط'),
                    __('أسئلة تأملية'),
                    __('خطوات عملية صغيرة'),
                    __('حدود واضحة للتحليل'),
                ],
            ],
            'inner-profile' => [
                'title' => __('الملف الداخلي'),
                'tagline' => __('يُبنى تدريجياً… وبإرادتك.'),
                'body' => __('أضف ما تشاء من خلفيتك وقيمك وتجاربك. أنت تتحكم بما تشارك، والملف يساعد على فهم أعمق دون ضغط.'),
                'image' => 'images/brand/doorway-light.jpg',
                'cta_route' => 'register',
                'cta_label' => __('ابنِ ملفك'),
                'points' => [
                    __('أسئلة اختيارية'),
                    __('خصوصية أولاً'),
                    __('سياق لتحليل أدق'),
                    __('بدون درجات نفسية مصطنعة'),
                ],
            ],
            'patterns-and-insights' => [
                'title' => __('الأنماط والرؤى'),
                'tagline' => __('لاحظ الاتجاه… دون تشخيص.'),
                'body' => __('مع الوقت تظهر موضوعات متكررة ومشاعر مألوفة. الملاحظة بداية فهم — وليست حكماً على شخصيتك.'),
                'image' => 'images/brand/still-water.jpg',
                'cta_route' => 'register',
                'cta_label' => __('ابدأ الملاحظة'),
                'points' => [
                    __('موضوعات متكررة'),
                    __('ارتباطات شخصية'),
                    __('تأمل طويل الأمد'),
                    __('بدون ادّعاء سببية طبية'),
                ],
            ],
            'personal-growth' => [
                'title' => __('النمو الشخصي'),
                'tagline' => __('خطوات صغيرة، واضحة، بلطف.'),
                'body' => __('حوّل التأمل إلى عادات بسيطة: أهداف، تذكيرات، وتمارين قصيرة تناسب إيقاعك.'),
                'image' => 'images/brand/light-path.jpg',
                'cta_route' => 'register',
                'cta_label' => __('ابدأ النمو'),
                'points' => [
                    __('موضوعات نمو'),
                    __('مهام يومية/أسبوعية'),
                    __('تمارين تأمل'),
                    __('متابعة تقدّمك'),
                ],
            ],
            'personal-reports' => [
                'title' => __('التقارير الشخصية'),
                'tagline' => __('ملخص أسبوعي وشهري بهدوء.'),
                'body' => __('تقارير تساعد على رؤية الاتجاه دون ضجيج إحصائي — مساحة للتأمل لا للمقارنة.'),
                'image' => 'images/brand/still-water.jpg',
                'cta_route' => 'register',
                'cta_label' => __('اكتشف التقارير'),
                'points' => [
                    __('تقرير أسبوعي'),
                    __('تأمل شهري'),
                    __('ملخصات قابلة للقراءة'),
                    __('إشعارات اختيارية'),
                ],
            ],
            'mobile-app' => [
                'title' => __('تطبيق InnerPath'),
                'tagline' => __('رحلتك معك أينما كنت.'),
                'body' => __('نجهّز تجربة الجوال لتسجيل الأحلام والتأمل بسهولة. حالياً يمكنك البدء من الويب.'),
                'image' => 'images/brand/hero-path.jpg',
                'cta_route' => 'register',
                'cta_label' => __('انضم للرحلة'),
                'points' => [
                    __('قريباً على المتاجر'),
                    __('تسجيل سريع للأحلام'),
                    __('نفس الخصوصية والهدوء'),
                    __('ابدأ الآن من الويب'),
                ],
            ],
        ];
    }
}
