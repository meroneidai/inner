<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicPageController extends Controller
{
    public function home(): View
    {
        return view('public.home');
    }

    public function about(): View
    {
        return view('public.about');
    }

    public function howItWorks(): View
    {
        return view('public.how-it-works');
    }

    public function privacy(): View
    {
        return view('public.privacy');
    }

    public function terms(): View
    {
        return view('public.terms');
    }

    public function contact(): View
    {
        return view('public.contact');
    }

    public function editorial(): View
    {
        return view('public.policy', [
            'title' => __('السياسة التحريرية'),
            'excerpt' => __('كيف نكتب عن الذات والأحلام والإيمان والمعنى — بوضوح ومسؤولية.'),
            'image' => 'images/brand/journal.jpg',
            'body' => <<<'TXT'
InnerPath مساحة تعليمية وتأملية، وليست منصة تشخيص طبي أو نفسي، ولا خدمة تنبؤ، ولا بديلاً عن الدعم المهني.

مبادئنا
- الوضوح قبل الإبهار.
- التعاطف دون استغلال المعاناة.
- التمييز بين الدليل العلمي والتفسير الفلسفي والخبرة الشخصية.
- عدم اختراع اقتباسات أو دراسات أو خبرات مزيفة.
- احترام اختلاف المعتقدات والخلفيات.

ما نتجنبه
- الوعود المبالغ فيها («غيّر حياتك في سبعة أيام»).
- فرض معنى واحد ثابت لرموز الأحلام.
- تحويل أسئلة الضيق الشديد إلى مسار تسويق.
- تقديم المحتوى كعلاج أو استشارة طبية.

المراجعة البشرية
المحتوى الحساس — خاصة ما يتعلق بالصحة النفسية أو الدين — يتطلب مراجعة بشرية قبل النشر. المسودات المدعومة بالذكاء الاصطناعي لا تُنشر تلقائياً.
TXT
        ]);
    }

    public function aiTransparency(): View
    {
        return view('public.policy', [
            'title' => __('شفافية الذكاء الاصطناعي'),
            'excerpt' => __('كيف نستخدم الذكاء الاصطناعي، وما حدوده، وما يبقى تحت سيطرتك.'),
            'image' => 'images/brand/inner-world.jpg',
            'body' => <<<'TXT'
نستخدم الذكاء الاصطناعي للمساعدة في:
- استكشاف الأحلام باحتمالات تفسيرية مرتبطة بسياقك.
- اقتراح أسئلة تأملية وخطوات عملية صغيرة.
- دعم التحرير في المسودات والأفكار — مع مراجعة بشرية قبل النشر العام.

ما لا يفعله الذكاء الاصطناعي في InnerPath
- لا يقدّم تشخيصاً طبياً أو نفسياً.
- لا يتنبأ بالمستقبل بشكل موثوق.
- لا يفرض حقيقة واحدة لمعنى حلمك.
- لا ينشر مقالات حساسة تلقائياً دون مراجعة.

سيطرتك
يمكنك اختيار ما تشاركه في ملفك الداخلي، وتصدير بياناتك أو حذف حسابك وفق سياسة الخصوصية. موافقة التدريب المجهّل اختيارية وغير مطلوبة لاستخدام المنصة الأساسية.
TXT
        ]);
    }

    public function sources(): View
    {
        return view('public.policy', [
            'title' => __('المصادر والمراجع'),
            'excerpt' => __('معاييرنا عند الإشارة إلى أبحاث أو نصوص أو تقاليد.'),
            'image' => 'images/brand/still-water.jpg',
            'body' => <<<'TXT'
عند مناقشة علم النفس أو العافية أو الفلسفة أو الدين، نسعى إلى:
- الاعتماد على مصادر موثوقة قدر الإمكان (أبحاث محكمة، مؤسسات معترف بها، نصوص أصلية، أعمال فلسفية معروفة).
- ذكر المراجع أو روابطها حين تكون الادعاءات واقعية مهمة.
- التصريح بعدم اليقين حين تختلف المدارس أو تكون الأدلة محدودة.
- التفريق بين النص الديني، والتفسير العلمي، والرأي الشخصي.

لن نخترع
- دراسات غير موجودة.
- إحصاءات ملفّقة.
- اقتباسات دينية أو علمية مزيفة.
- شهادات خبراء وهميين.

إذا وجدت خطأ في مرجع أو صياغة، راسلنا عبر صفحة التواصل وسنراجعه.
TXT
        ]);
    }

    public function sendContact(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        // Logged locally in development (MAIL_MAILER=log)
        logger()->info('InnerPath contact form', $data);

        return back()->with('status', __('تم استلام رسالتك. شكراً لتواصلك.'));
    }
}
