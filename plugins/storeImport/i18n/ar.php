<?php
return array(
    "storeImport.meta.name"          => "استيراد البيانات",
    "storeImport.meta.title"         => "استيراد سريع للبيانات المخزنة",
    "storeImport.meta.desc"          => "يوفر هذا المكون الإضافي وظيفة استيراد بيانات التخزين، مما يتيح استيراد بيانات التخزين بسرعة إلى قرص الشبكة عن طريق مسح التخزين المضاف بواسطة القرص المحدد أو تخزين الكائنات.",
    "storeImport.main.import"        => "يستورد",
    "storeImport.main.dataImport"    => "استيراد بيانات التخزين",
    "storeImport.main.ioNotSup"      => "أنواع التخزين غير المدعومة:",
    "storeImport.main.ioPathErr"     => "دليل البيانات الأصلي غير صحيح ويجب أن يكون دليل تخزين: {io:x}",
    "storeImport.main.ioStoreErr"    => "إن التخزين الذي توجد فيه البيانات الأصلية غير صالح!",
    "storeImport.main.ioNotSupErr"   => "لا يتم دعم استيراد هذا النوع من التخزين:",
    "storeImport.main.ioFromNetErr"  => "لا يمكن توصيل وحدة التخزين التي تتواجد بها البيانات الأصلية.",
    "storeImport.main.ioToErr"       => "دليل تخزين قرص الشبكة خاطئ. يجب أن يكون دليل نظام قرص الشبكة: المساحة الشخصية أو القسم والدلائل الفرعية التابعة له.",
    "storeImport.main.noPermission"  => "ليس لديك الصلاحية لإجراء هذه العملية!",
    "storeImport.main.loginTokenErr" => "انتهت صلاحية تسجيل الدخول. يُرجى طلب الحصول على أمر التنفيذ مرة أخرى.",
    "storeImport.main.ioFromPath"    => "دليل البيانات الخام",
    "storeImport.main.selectPath"    => "حدد الدليل",
    "storeImport.main.ioFromPathDesc" => "يجب أن يكون دليل البيانات الأصلي المراد استيراده دليلاً تحت التخزين",
    "storeImport.main.ioFromPathErr" => "دليل البيانات الأصلي خاطئ، لا بد أن يكون دليل تخزين!",
    "storeImport.main.ioToPath"      => "دليل تخزين القرص الشبكي",
    "storeImport.main.ioToPathDesc"  => "دليل نظام قرص الشبكة الذي يحتاج إلى التخزين، مثل المساحة الشخصية أو قرص شبكة المؤسسة أو الدلائل الفرعية الخاصة به",
    "storeImport.main.ioToPathErr"   => "دليل تخزين قرص الشبكة خاطئ، يجب أن يكون دليل نظام قرص الشبكة!",
    "storeImport.main.rootGroup"     => "قرص شبكة المؤسسة",
    "storeImport.main.importDesc"    => "<h5 style=\";text-align:right;direction:rtl\">من خلال مسح القرص أو تخزين الكائنات، يتم إنشاء الفهرس تلقائيًا لتمكين الاستيراد السريع للملفات إلى قرص الشبكة.</h5> [ر]<div class='mt-10 mb-20' style=\";text-align:right;direction:rtl\"> [ر]<li style=\";text-align:right;direction:rtl\"> 1. يجب الوصول إلى ملف قرص الشبكة من خلال مسار التخزين، <u>لذا قبل الاستيراد، يجب إضافة دليل البيانات الأصلي (أو الدليل الرئيسي) المراد استيراده كتخزين</u> ؛</li> [ر]<li style=\";text-align:right;direction:rtl\"> ٢. لن تُغيّر هذه العملية البيانات الأصلية، بل ستُنشئ فقط ربطًا لفهرس الملف بقرص الشبكة. <u>بعد الاستيراد، لا يُمكن إجراء أي تغييرات على البيانات الأصلية لتجنب إبطال الفهرس</u> .</li> [ر]<li style=\";text-align:right;direction:rtl\"> ٣. يختلف مسار البيانات المستوردة عن مسار بيانات التخزين الافتراضية على قرص الشبكة. يُنصح باستخدام تخزين البيانات المستوردة كمساحة تخزين إضافية (بدلاً من مساحة التخزين الافتراضية للنظام) للحفاظ على بنية بيانات موحدة.</li> [ر]<li style=\";text-align:right;direction:rtl\"> 4. يوصى بعمل نسخة احتياطية لقاعدة البيانات قبل الاستيراد لتجنب الحوادث.</li> \n</div> \n<div style=\";text-align:right;direction:rtl\"> ملاحظة: <u>إذا تجاوز طول مسار الملف ٢٥٦ حرفًا، فسيتم تقييد عملية الاستيراد</u> . تُحفظ السجلات ذات الصلة في مجلد \"سجل فشل الاستيراد - تجاوز الطول ٢٥٦ حرفًا\" ضمن مجلد تخزين قرص الشبكة. يمكنك عرضه ومعالجته بنفسك بعد اكتمال عملية الاستيراد.</div>",
    "storeImport.task.rptErr"        => "المهمة جارية. يُرجى عدم تكرار العملية!",
    "storeImport.task.subErr"        => "المهمة قيد التنفيذ، يرجى عدم إرسالها مرة أخرى!",
    "storeImport.task.stopByUser"    => "تم إنهاء المهمة يدويًا!",
    "storeImport.task.start"         => "قم بتخزين البيانات المستوردة وابدأ:",
    "storeImport.task.end"           => "قم بتخزين البيانات المستوردة وأكمل:",
    "storeImport.task.startExt"      => "،يبدأ",
    "storeImport.task.starting"      => "جاهز للاستيراد...(جاري التحميل، يرجى الانتظار)",
    "storeImport.task.ing"           => "جاري الاستيراد...",
    "storeImport.task.stopErr"       => "إنهاء غير طبيعي!",
    "storeImport.task.stopErrDesc"   => "بيانات استيراد التخزين، انقطاع غير طبيعي",
    "storeImport.task.afterTime"     => "الوقت المتبقي:",
    "storeImport.task.errLog"        => "سجل فشل الاستيراد - الطول يتجاوز 256 حرفًا",
    "storeImport.task.importEnd"     => "تم الاستيراد بالكامل!",
    "storeImport.task.taskEnd"       => "المهمة اكتملت!",
    "storeImport.task.taskErr"       => "مهمة غير طبيعية!",
    "storeImport.task.reqErr"        => "فشل الطلب أو تم إنهاء المهمة!",
    "storeImport.task.getting"       => "جاري التحميل، الرجاء الانتظار",
    "storeImport.task.desc1"         => "عندما تكون كمية البيانات كبيرة، قد تُلغى المهمة بسبب انتهاء المهلة. يمكنك تنفيذ الأمر التالي في الطرفية لاستيراد البيانات:",
    "storeImport.task.desc2"         => "ملاحظة: يستخدم هذا الأمر sudo -u nginx لضمان تنفيذ عملية PHP باستخدام المستخدم الصحيح لتجنب مشاكل أذونات الملفات. إذا لم يكن مستخدم خادم الويب لديك هو nginx، يُرجى استبدال nginx بالمستخدم المناسب وفقًا لتكوين خادمك، مثل root أو www-data/apache، إلخ."
);