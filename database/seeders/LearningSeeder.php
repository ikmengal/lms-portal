<?php

namespace Database\Seeders;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\CourseModule;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LearningSeeder extends Seeder
{
    public function run(): void
    {
        $instructor = User::where('email', 'instructor@lmsportal.com')->first();
        $instructor2 = User::where('email', 'instructor2@lmsportal.com')->first();
        $student = User::where('email', 'student@lmsportal.com')->first();

        if (!$instructor || !$student) {
            return;
        }

        $instructor->update(['bio' => 'Dr. Sarah Johnson has over 12 years of experience in software engineering and teaching. She has worked at Google and Amazon, holds a PhD in Computer Science from Stanford University, and has published over 30 research papers. Her courses have helped more than 100,000 students worldwide launch their tech careers.']);

        if ($instructor2) {
            $instructor2->update(['bio' => 'Michael Chen is a senior cloud architect and full-stack developer with 9 years of hands-on industry experience at Microsoft and Shopify. He specializes in Python, cloud infrastructure and cross-platform development, and loves turning complex topics into approachable, project-driven courses.']);
        }

        // Database-driven categories & levels
        $categoryIds = [];
        $levelIds = [];
        foreach ([
            'Web Development', 'Data Science', 'Artificial Intelligence', 'Mobile Development',
            'Cloud Computing', 'Cyber Security', 'DevOps', 'Project Management',
            'Software Development', 'Digital Marketing', 'Business', 'Design',
            'Programming', 'Databases',
        ] as $i => $name) {
            $cat = \App\Models\CourseCategory::withTrashed()->updateOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name, 'sort_order' => $i, 'is_active' => true]
            );
            $categoryIds[$name] = $cat->id;
        }
        foreach (['Beginner', 'Intermediate', 'Advanced', 'Beginner to Advanced'] as $i => $name) {
            $lvl = \App\Models\CourseLevel::withTrashed()->updateOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name, 'sort_order' => $i, 'is_active' => true]
            );
            $levelIds[$name] = $lvl->id;
        }

        $reviewers = collect([
            ['Alex Turner', 'alex.turner@example.com'],
            ['Lisa Chen', 'lisa.chen@example.com'],
            ['Marcus Brown', 'marcus.brown@example.com'],
            ['Priya Sharma', 'priya.sharma@example.com'],
        ])->map(function ($r) {
            $user = User::firstOrCreate(
                ['email' => $r[1]],
                ['name' => $r[0], 'password' => 'password', 'email_verified_at' => now()]
            );
            $user->assignRole('student');

            return $user;
        });

        $courses = [
            [
                'title' => 'Complete Web Development Bootcamp',
                'subtitle' => 'Full-stack bootcamp from zero to job-ready.',
                'language' => 'English',
                'language_code' => 'en',
                'category' => 'Web Development',
                'level' => 'Beginner',
                'duration_hours' => 42,
                'price' => 49.99,
                'description' => "Go from zero to a job-ready full-stack web developer. This hands-on bootcamp covers HTML, CSS, JavaScript, PHP & MySQL through real projects you will build yourself.\n\nBy the end of this course you will have built and deployed several complete websites and web applications, plus a portfolio to show employers.",
                'translations' => [
                    'ur' => [
                        'title' => 'کمپلیٹ ویب ڈیولپمنٹ بوٹ کیمپ',
                        'subtitle' => 'زیرو سے نوکری کے لیے تیار مکمل سٹیک بوٹ کیمپ۔',
                        'description' => "زیرو سے ایک مکمل فل سٹیک ویب ڈیولپر بنیں۔ یہ عملی بوٹ کیمپ ایچ ٹی ایم ایل، سی ایس ایس، جاوا اسکرپٹ، پی ایچ پی اور مائی ایس کیو ایل کو حقیقی پراجیکٹس کے ذریعے سکھاتا ہے جو آپ خود بنائیں گے۔\n\nکورس مکمل کرنے تک آپ کئی مکمل ویب سائٹس اور ویب ایپلیکیشنز بنا چکے ہوں گے، ساتھ ہی ایک پورٹ فولیو بھی تیار ہو جائے گا جو آجرین کو دکھایا جا سکے۔",
                    ],
                    'ar' => [
                        'title' => 'معسكر تطوير الويب الشامل',
                        'subtitle' => 'معسكر شامل من الصفر حتى الاستعداد للعمل.',
                        'description' => "انتقل من الصفر إلى مطور ويب متكامل جاهز للعمل. يغطي هذا المعسكر العملي هتمل وسيس وجافا سكريبت وبي إتش بي وماي إس كيو إل عبر مشاريع حقيقية ستبنيها بنفسك.\n\nبحلول نهاية الدورة ستكون قد بنيت ونشرت عدة مواقع وتطبيقات ويب كاملة، بالإضافة إلى محفظة أعمال تعرضها على أصحاب العمل.",
                    ],
                ],
                'content_translations' => [
                    'ur' => [
                        'modules' => [
                            'Web Foundations' => 'ویب کی بنیادیں',
                            'HTML Deep Dive' => 'ایچ ٹی ایم ایل میں گہرا مطالعہ',
                            'CSS & Responsive Design' => 'سی ایس ایس اور ریسپانسیو ڈیزائن',
                            'JavaScript Essentials' => 'جاوا اسکرپٹ کی بنیادی باتیں',
                            'Backend with PHP & MySQL' => 'پی ایچ پی اور مائی ایس کیو ایل کے ساتھ بیک اینڈ',
                            'Final Project: Full Website' => 'فائنل پراجیکٹ: مکمل ویب سائٹ',
                        ],
                        'lessons' => [
                            'Introduction to the Web' => 'ویب کا تعارف',
                            'How the Internet Works' => 'انٹرنیٹ کیسے کام کرتا ہے',
                            'Setting Up Your Code Editor' => 'اپنا کوڈ ایڈیٹر سیٹ اپ کرنا',
                            'Your First Web Page' => 'آپ کا پہلا ویب پیج',
                            'HTML Document Structure' => 'ایچ ٹی ایم ایل ڈاکیومنٹ کا ڈھانچہ',
                            'Forms & Input Elements' => 'فارمز اور ان پٹ عناصر',
                            'Semantic HTML5 Tags' => 'سیمنٹک ایچ ٹی ایم ایل 5 ٹیگز',
                            'Tables & Media' => 'ٹیبلز اور میڈیا',
                            'Accessibility Basics' => 'ایکسیسی بیلیٹی کی بنیادی باتیں',
                            'Selectors & Specificity' => 'سیلیکٹرز اور اسپیسیفسیٹی',
                            'Box Model & Positioning' => 'باکس ماڈل اور پوزیشننگ',
                            'Flexbox Layout' => 'فلیکس باکس لے آؤٹ',
                            'CSS Grid' => 'سی ایس ایس گرڈ',
                            'Media Queries & Mobile First' => 'میڈیا کوئریز اور موبائل فرسٹ',
                            'Variables & Data Types' => 'وی ایبلز اور ڈیٹا ٹائپس',
                            'Functions & Events' => 'فنکشنز اور ایونٹس',
                            'DOM Manipulation' => 'ڈی او ایم مینیپولیشن',
                            'Fetch API & JSON' => 'فچ اے پی آئی اور جے سون',
                            'PHP Syntax & Functions' => 'پی ایچ پی سنٹیکس اور فنکشنز',
                            'Connecting to MySQL' => 'مائی ایس کیو ایل سے منسلک ہونا',
                            'CRUD Operations' => 'سی آر یو ڈی آپریشنز',
                            'User Authentication' => 'یوزر آتھنٹیکیشن',
                            'Project Planning & Wireframes' => 'پراجیکٹ پلاننگ اور وائر فرمز',
                            'Building the Frontend' => 'فرنٹ اینڈ بنانا',
                            'Building the Backend' => 'بیک اینڈ بنانا',
                            'Deployment & Going Live' => 'ڈیپلائمنٹ اور لائیو ہونا',
                        ],
                    ],
                    'ar' => [
                        'modules' => [
                            'Web Foundations' => 'أساسيات الويب',
                            'HTML Deep Dive' => 'الغوص في HTML',
                            'CSS & Responsive Design' => 'CSS والتصميم المتجاوب',
                            'JavaScript Essentials' => 'أساسيات جافا سكريبت',
                            'Backend with PHP & MySQL' => 'الواجهة الخلفية مع PHP وMySQL',
                            'Final Project: Full Website' => 'المشروع النهائي: موقع كامل',
                        ],
                    ],
                ],
                'curriculum' => [
                    ['Web Foundations', [['Introduction to the Web', 12], ['How the Internet Works', 15], ['Setting Up Your Code Editor', 10], ['Your First Web Page', 18]]],
                    ['HTML Deep Dive', [['HTML Document Structure', 14], ['Forms & Input Elements', 22], ['Semantic HTML5 Tags', 16], ['Tables & Media', 13], ['Accessibility Basics', 19]]],
                    ['CSS & Responsive Design', [['Selectors & Specificity', 20], ['Box Model & Positioning', 24], ['Flexbox Layout', 28], ['CSS Grid', 26], ['Media Queries & Mobile First', 21]]],
                    ['JavaScript Essentials', [['Variables & Data Types', 18], ['Functions & Events', 25], ['DOM Manipulation', 30], ['Fetch API & JSON', 27]]],
                    ['Backend with PHP & MySQL', [['PHP Syntax & Functions', 26], ['Connecting to MySQL', 24], ['CRUD Operations', 32], ['User Authentication', 35]]],
                    ['Final Project: Full Website', [['Project Planning & Wireframes', 15], ['Building the Frontend', 40], ['Building the Backend', 45], ['Deployment & Going Live', 20]]],
                ],
                'reviews' => [
                    [0, 5, 'Absolutely fantastic bootcamp! The projects are real-world and the explanations are crystal clear. I landed my first junior dev job two months after finishing.'],
                    [1, 5, 'Best web development course I have taken. The PHP & MySQL section finally made backend development click for me.'],
                    [2, 4, 'Very thorough and well paced. I would love even more JavaScript practice exercises, but overall an excellent course.'],
                ],
            ],
            [
                'title' => 'PHP & Laravel for Beginners',
                'subtitle' => 'Learn PHP from scratch, then master Laravel the right way.',
                'language' => 'English',
                'language_code' => 'en',
                'category' => 'Programming',
                'level' => 'Beginner',
                'duration_hours' => 30,
                'price' => 39.99,
                'description' => "Learn PHP from scratch and then master Laravel, the world's most popular PHP framework. Build secure, modern web applications using MVC architecture, Eloquent ORM, and Blade templating.\n\nNo prior PHP experience needed - we start from absolute zero.",
                'translations' => [
                    'ur' => [
                        'title' => 'شروع سے پی ایچ پی اینڈ لاراول',
                        'subtitle' => 'اےس سے پی ایچ پی سیکھیں اور پھر لاراول کو صحیح طریقے سے سیکھیں۔',
                    ],
                    'ar' => [
                        'title' => 'بي إتش بي ولارافل للمبتدئين',
                        'subtitle' => 'تعلّم بي إتش بي من الصفر ثم أتقن لارافل بالطريقة الصحيحة.',
                    ],
                ],
                'curriculum' => [
                    ['PHP Fundamentals', [['PHP Syntax & Variables', 14], ['Arrays & Loops', 18], ['Functions in PHP', 16], ['Working with Forms', 20]]],
                    ['Object Oriented PHP', [['Classes & Objects', 22], ['Inheritance & Interfaces', 24], ['Namespaces & Autoloading', 18]]],
                    ['Getting Started with Laravel', [['Composer & Installation', 12], ['Routing & Controllers', 25], ['Blade Templating', 22], ['Migrations & Seeding', 26]]],
                    ['Eloquent ORM', [['Models & Relationships', 30], ['Query Builder Essentials', 24], ['Validation & Security', 28]]],
                    ['Building a Complete App', [['Authentication Scaffolding', 25], ['CRUD with Laravel', 35], ['Testing & Deployment', 30]]],
                ],
                'reviews' => [
                    [3, 5, 'Laravel finally makes sense! The Eloquent section alone is worth the price. Highly recommended for anyone starting with PHP.'],
                    [0, 4, 'Great progression from plain PHP to the framework. Some sections could be slightly longer but the content quality is top notch.'],
                ],
            ],
            [
                'title' => 'JavaScript ES6+ Mastery',
                'subtitle' => 'Modern JavaScript: closures, async/await, modules & tooling.',
                'language' => 'English',
                'language_code' => 'en',
                'category' => 'Programming',
                'level' => 'Intermediate',
                'duration_hours' => 24,
                'price' => 29.99,
                'description' => "Master modern JavaScript from ES6 onwards. Arrow functions, destructuring, modules, promises, async/await, the DOM, and more - explained deeply with dozens of coding challenges.\n\nPerfect for developers who know the basics and want to write clean, modern JavaScript.",
                'translations' => [
                    'ur' => [
                        'title' => 'جاوا اسکرپٹ ای ایس 6+ ماسٹری',
                        'subtitle' => 'جدید جاوا اسکرپٹ: کلوژرز، ایسینک/ایویٹ، ماڈیولز اور ٹولنگ۔',
                    ],
                    'ar' => [
                        'title' => 'إتقان جافا سكريبت ES6+',
                        'subtitle' => 'جافا سكريبت الحديثة: closures وasync/await والوحدات والأدوات.',
                    ],
                ],
                'curriculum' => [
                    ['Modern Syntax', [['let, const & Block Scope', 15], ['Arrow Functions', 18], ['Template Literals & Destructuring', 22], ['Spread & Rest Operators', 20]]],
                    ['Advanced Functions', [['Closures Explained', 25], ['Higher Order Functions', 22], ['this & call/apply/bind', 28]]],
                    ['Asynchronous JavaScript', [['Promises from Scratch', 30], ['async/await Patterns', 26], ['Fetch & Error Handling', 24]]],
                    ['Modules & Tooling', [['ES Modules', 18], ['NPM & Bundlers Overview', 22], ['Project: Weather App', 40]]],
                ],
                'reviews' => [
                    [1, 5, 'The closures and async sections are worth their weight in gold. Everything is explained with visual examples that actually stick.'],
                    [2, 4, 'Solid intermediate JS course. The weather app project ties all concepts together nicely.'],
                    [3, 4, 'Great deep dive into modern JavaScript features. I use destructuring and spread daily now at work.'],
                ],
            ],
            [
                'title' => 'MySQL Database Design & Administration',
                'subtitle' => 'Design, query and administer production-grade databases.',
                'language' => 'English',
                'language_code' => 'en',
                'category' => 'Databases',
                'level' => 'Intermediate',
                'duration_hours' => 18,
                'price' => 34.99,
                'description' => "Learn to design, build and administer production-grade MySQL databases. Normalization, indexing, transactions, backups, performance tuning and security - everything a backend developer or DBA needs.\n\nIncludes a complete real-world e-commerce database project.",
                'translations' => [
                    'ur' => [
                        'title' => 'مائی ایس کیو ایل ڈیٹا بیس ڈیزائن اور ایڈمنسٹریشن',
                        'subtitle' => 'پروڈکشن گریڈ ڈیٹا بیسز کو ڈیزائن، کوئری اور ایڈمنسٹر کرنا۔',
                    ],
                    'ar' => [
                        'title' => 'تصميم وإدارة قواعد بيانات MySQL',
                        'subtitle' => 'صمّم واستعلم وأدر قواعد البيانات بمستوى الإنتاج.',
                    ],
                ],
                'curriculum' => [
                    ['Relational Database Basics', [['What is a Relational Database', 12], ['Installing MySQL', 15], ['Creating Databases & Tables', 20], ['Data Types Deep Dive', 22]]],
                    ['SQL Querying', [['SELECT, WHERE & ORDER BY', 24], ['JOINs Explained Visually', 30], ['GROUP BY & Aggregates', 26], ['Subqueries & CTEs', 28]]],
                    ['Database Design', [['Normalization 1NF to 3NF', 30], ['Primary & Foreign Keys', 20], ['ER Diagrams & Modeling', 25]]],
                    ['Administration & Performance', [['Indexing Strategies', 28], ['Transactions & Locking', 26], ['Backup & Recovery', 22], ['Users & Permissions', 18]]],
                ],
                'reviews' => [
                    [2, 5, 'The JOINs visualization is the best I have ever seen. Database design finally feels intuitive instead of scary.'],
                    [0, 4, 'Very practical DBA content. The backup/recovery module saved me at work just weeks after finishing the course.'],
                ],
            ],
            [
                'title' => 'UI/UX Design Fundamentals with Figma',
                'subtitle' => 'Design thinking, Figma workflows and a portfolio case study.',
                'language' => 'English',
                'language_code' => 'en',
                'category' => 'Design',
                'level' => 'Beginner',
                'duration_hours' => 20,
                'price' => 44.99,
                'description' => "Learn user interface and user experience design from scratch using Figma. Design principles, color theory, typography, wireframing, prototyping and building a complete design system.\n\nFinish the course with a polished portfolio case study.",
                'translations' => [
                    'ur' => [
                        'title' => 'فگما کے ساتھ یو آئی/یو ایکس ڈیزائن کے بنیادی اصول',
                        'subtitle' => 'ڈیزائن تھنکنگ، فگما ورک فلو اور پورٹ فولیو کیس اسٹڈی۔',
                    ],
                    'ar' => [
                        'title' => 'أساسيات تصميم UI/UX مع فيجما',
                        'subtitle' => 'التفكير التصميمي وسير عمل فيجما ودراسة حالة للمحفظة.',
                    ],
                ],
                'curriculum' => [
                    ['Design Thinking Basics', [['What is UI vs UX', 12], ['The Design Process', 18], ['User Research Fundamentals', 22]]],
                    ['Figma Essentials', [['Interface Tour', 15], ['Frames, Shapes & Tools', 24], ['Auto Layout Mastery', 30], ['Components & Variants', 28]]],
                    ['Visual Design Principles', [['Color Theory for Interfaces', 25], ['Typography Systems', 22], ['Spacing & Grids', 20]]],
                    ['Wireframing to Prototype', [['Low-Fi Wireframes', 24], ['Interactive Prototyping', 28], ['Usability Testing', 22]]],
                    ['Portfolio Case Study', [['Project: Mobile App Redesign', 45], ['Presenting Your Work', 20]]],
                ],
                'reviews' => [
                    [3, 5, 'I went from zero design skills to a portfolio case study in three weeks. The Auto Layout lessons are outstanding.'],
                    [1, 5, 'As a developer wanting to learn design, this was perfect. Practical, visual and immediately applicable.'],
                    [2, 4, 'Great fundamentals course. Would love an advanced follow-up covering design systems at scale.'],
                ],
            ],
            [
                'title' => 'Python for Data Science & Analytics',
                'subtitle' => 'NumPy, Pandas, visualization and intro ML with real datasets.',
                'language' => 'English',
                'language_code' => 'en',
                'category' => 'Data Science',
                'level' => 'Intermediate',
                'duration_hours' => 38,
                'price' => 59.99,
                'instructor_email' => 'instructor2@lmsportal.com',
                'description' => "Learn data science from the ground up with Python. NumPy, Pandas, data visualization, exploratory analysis and an introduction to machine learning with scikit-learn, all taught through real datasets.\n\nBy the end you'll have completed three portfolio-worthy data analysis projects.",
                'translations' => [
                    'ur' => [
                        'title' => 'ڈیٹا سائنس اور اینالیٹکس کے لیے پائتھون',
                        'subtitle' => 'نیو ایم پائے، پانڈاز، ویژولائزیشن اور حقیقی ڈیٹا سیٹس کے ساتھ ایم ایل کا آغاز۔',
                    ],
                    'ar' => [
                        'title' => 'بايثون لعلوم البيانات والتحليلات',
                        'subtitle' => 'NumPy وPandas والتصور ومقدمة في تعلم الآلة ببيانات حقيقية.',
                    ],
                ],
                'curriculum' => [
                    ['Getting Started with Python', [['Python Setup & Jupyter Notebooks', 14], ['Variables, Types & Operators', 18], ['Lists, Tuples & Dictionaries', 20], ['Control Flow & Functions', 22]]],
                    ['NumPy Essentials', [['NumPy Arrays', 18], ['Vectorized Operations', 22], ['Indexing & Broadcasting', 24]]],
                    ['Pandas & DataFrames', [['Series & DataFrames', 22], ['Reading Real Datasets', 20], ['Filtering, Grouping & Aggregation', 28], ['Handling Missing Data', 24]]],
                    ['Data Visualization', [['Matplotlib Fundamentals', 25], ['Seaborn for Statistical Plots', 26], ['Storytelling with Charts', 20]]],
                    ['Intro to Machine Learning', [['Train/Test Split & Metrics', 28], ['Linear Regression', 30], ['Classification with scikit-learn', 32], ['Capstone: Sales Forecasting', 40]]],
                ],
                'reviews' => [
                    [0, 5, 'The Pandas section is gold. I went from spreadsheet-only to analyzing messy real-world datasets with confidence.'],
                    [2, 5, 'Clearly explained and genuinely practical. The sales forecasting capstone looks great in my portfolio.'],
                    [3, 4, 'Strong fundamentals course. Would love a deeper dive into model tuning as a follow-up.'],
                ],
            ],
            [
                'title' => 'Cloud Computing with AWS',
                'subtitle' => 'EC2, S3, Lambda & serverless — build on AWS with confidence.',
                'language' => 'English',
                'language_code' => 'en',
                'category' => 'Cloud Computing',
                'level' => 'Intermediate',
                'duration_hours' => 28,
                'price' => 49.99,
                'instructor_email' => 'instructor2@lmsportal.com',
                'description' => "Master the core building blocks of AWS including EC2, S3, RDS, IAM, Lambda and CloudFormation. Hands-on labs teach you to design secure, scalable and cost-efficient architectures.\n\nIncludes a complete serverless web app project deployed end to end.",
                'translations' => [
                    'ur' => [
                        'title' => 'اے ڈبلیو ایس کے ساتھ کلاؤڈ کمپیوٹنگ',
                        'subtitle' => 'ای سی ٹو، ایس تھری، لیمبڈا اور سرور لیس — اعتماد کے ساتھ اے ڈبلیو ایس پر بنائیں۔',
                    ],
                    'ar' => [
                        'title' => 'الحوسبة السحابية مع AWS',
                        'subtitle' => 'EC2 وS3 وLambda و serverless — ابنِ على AWS بثقة.',
                    ],
                ],
                'curriculum' => [
                    ['Cloud & AWS Foundations', [['Cloud Computing Concepts', 12], ['AWS Global Infrastructure', 16], ['Managing Access with IAM', 20], ['Billing, Cost & Free Tier Limits', 14]]],
                    ['Compute Services', [['EC2 Deep Dive', 24], ['Security Groups & Key Pairs', 18], ['Auto Scaling & Load Balancing', 26]]],
                    ['Storage & Databases', [['S3 Essentials', 22], ['RDS & DynamoDB Basics', 26], ['Designing a Data Layer', 20]]],
                    ['Serverless & Automation', [['Lambda Functions', 28], ['API Gateway', 24], ['CloudFormation Basics', 26]]],
                    ['Capstone: Serverless Web App', [['Architecture Planning', 16], ['Building the Backend', 34], ['Frontend Deployment', 28], ['Monitoring & Cost Review', 18]]],
                ],
                'reviews' => [
                    [1, 5, 'Finally understood IAM and networking. The capstone pulls everything together into something real.'],
                    [3, 5, 'Perfect balance of concepts and hands-on labs. The serverless project actually works, which was hugely motivating.'],
                    [0, 4, 'Great course. Some services deserve even deeper coverage, but the fundamentals are rock solid.'],
                ],
            ],
            [
                'title' => 'Mobile App Development with Flutter',
                'subtitle' => 'One codebase — beautiful, native iOS & Android apps.',
                'language' => 'English',
                'language_code' => 'en',
                'category' => 'Mobile Development',
                'level' => 'Beginner',
                'duration_hours' => 26,
                'price' => 44.99,
                'instructor_email' => 'instructor2@lmsportal.com',
                'description' => "Build beautiful, cross-platform mobile apps for iOS and Android from a single Flutter codebase. Dart fundamentals, widgets, state management, navigation, animations and API integration.\n\nShip a complete weather & task manager app to the app stores by the end of the course.",
                'translations' => [
                    'ur' => [
                        'title' => 'فلاٹر کے ساتھ موبائل ایپ ڈیولپمنٹ',
                        'subtitle' => 'ایک کوڈ بیس — شاندار، نیٹیو آئی او ایس اور اینڈرائیڈ ایپس۔',
                    ],
                    'ar' => [
                        'title' => 'تطوير تطبيقات الجوال مع فلاتر',
                        'subtitle' => 'كود واحد — تطبيقات جميلة وأصلية لنظامي iOS وAndroid.',
                    ],
                ],
                'curriculum' => [
                    ['Dart & Flutter Basics', [['Installing Flutter & Setting Up', 15], ['Dart: Variables, Functions & Classes', 20], ['Your First Flutter App', 18], ['Widgets & the Widget Tree', 22]]],
                    ['Building User Interfaces', [['Layouts with Rows & Columns', 24], ['Stateful vs Stateless Widgets', 25], ['Styling & Themes', 20], ['Forms & Validation', 26]]],
                    ['Navigation & State', [['Navigating Between Screens', 22], ['State Management with Provider', 28], ['Persisting Local Data', 24]]],
                    ['Working with Data', [['Consuming REST APIs', 28], ['Async & Futures in Dart', 22], ['Image & File Handling', 20]]],
                    ['Capstone: Task Manager App', [['Project Planning & Scaffolding', 15], ['Building the UI', 32], ['State Management & Storage', 30], ['Polishing & Submitting to Stores', 24]]],
                ],
                'reviews' => [
                    [2, 5, 'I published a real app to both stores three weeks after starting. Everything is explained step by step.'],
                    [0, 4, 'Excellent for beginners. The provider state management section was worth the whole price.'],
                ],
            ],
            [
                'title' => 'AWS Certified Solutions Architect',
                'subtitle' => 'Get exam-ready for the AWS Solutions Architect Associate certificate.',
                'language' => 'English',
                'language_code' => 'en',
                'category' => 'Cloud Computing',
                'level' => 'Intermediate',
                'duration_hours' => 22,
                'price' => 54.99,
                'instructor_email' => 'instructor2@lmsportal.com',
                'unlocks_at' => now()->addDays(14),
                'description' => "A sprint to the AWS Solutions Architect Associate exam. Cover the blueprint domains with hands-on architecture labs: VPC design, high availability, cost optimization, IAM security and a full 3-tier capstone.\n\nThe course unlocks on a fixed schedule, then every module is released week by week — so you go from zero architecture practice to exam-ready in about a month.",
                'curriculum' => [
                    ['Exam Blueprint & Strategy', [['Domain Weightage Breakdown', 12], ['Hands-On vs Theory Approach', 15], ['Practice Exam Setup', 10]]],
                    ['Architect Core Services', [['VPC Deep Dive', 25], ['High Availability Patterns', 28], ['Cost Optimization Models', 22]]],
                    ['Security & IAM Mastery', [['IAM Policies Deep Dive', 26], ['Encryption & KMS', 20], ['Shared Responsibility Model', 14]]],
                    ['Capstone: Full Architecture', [['Designing a 3-Tier VPC', 30], ['Auto Scaling & RDS Failover', 32], ['Final Practice Exam', 45]]],
                ],
                'reviews' => [],
            ],
        ];

        $progressMap = [100, 100, 65, 30, 0];

        // Content Drip demo: unlock dates are offsets in days from the seed run.
        // Negative => already past (module is open now); positive => future unlock.
        $dripModules = [
            'uiux-design-fundamentals-with-figma' => [
                'Design Thinking Basics' => -6,
                'Figma Essentials' => 2,
                'Visual Design Principles' => 6,
                'Wireframing to Prototype' => 10,
                'Portfolio Case Study' => 15,
            ],
            'mysql-database-design-administration' => [
                'Relational Database Basics' => -8,
                'SQL Querying' => 3,
                'Database Design' => 9,
                'Administration & Performance' => 14,
            ],
            'javascript-es6-mastery' => [
                'Modern Syntax' => -10,
                'Advanced Functions' => -3,
                'Asynchronous JavaScript' => 2,
                'Modules & Tooling' => 9,
            ],
        ];

        // Lesson-level drip inside an already-open module (UI/UX).
        $dripLessons = [
            'uiux-design-fundamentals-with-figma' => [
                'User Research Fundamentals' => 1,
            ],
        ];

        foreach ($courses as $index => $data) {
            $courseSlug = Str::slug($data['title']);
            $curriculum = $data['curriculum'];
            $courseReviews = $data['reviews'];
            $categoryId = $categoryIds[$data['category']] ?? null;
            $levelId = $levelIds[$data['level']] ?? null;
            $courseInstructor = isset($data['instructor_email']) && $data['instructor_email'] === 'instructor2@lmsportal.com'
                ? $instructor2
                : $instructor;
            unset($data['curriculum'], $data['reviews'], $data['category'], $data['level'], $data['instructor_email']);

            $courseSubtitle = $data['subtitle'] ?? null;
            $courseTranslations = $data['translations'] ?? null;
            $contentTranslations = $data['content_translations'] ?? [];
            unset($data['subtitle'], $data['translations'], $data['content_translations']);

            $course = Course::firstOrCreate(
                ['slug' => Str::slug($data['title'])],
                $data
                    + [
                        'subtitle' => $courseSubtitle,
                        'language' => 'English',
                        'language_code' => 'en',
                        'translations' => $courseTranslations,
                        'course_category_id' => $categoryId,
                        'course_level_id' => $levelId,
                        'instructor_id' => $courseInstructor?->id,
                    ]
            );

            foreach ($curriculum as $mi => [$moduleTitle, $lessons]) {
                $moduleTranslations = [];
                foreach ($contentTranslations as $code => $ct) {
                    if (isset($ct['modules'][$moduleTitle])) {
                        $moduleTranslations[$code] = ['title' => $ct['modules'][$moduleTitle]];
                    }
                }

                $moduleUnlocksAt = $dripModules[$courseSlug][$moduleTitle] ?? null;

                $module = CourseModule::updateOrCreate(
                    ['course_id' => $course->id, 'title' => $moduleTitle],
                    ['sort_order' => $mi, 'unlocks_at' => $moduleUnlocksAt !== null ? now()->addDays((int) $moduleUnlocksAt) : null]
                        + ($moduleTranslations ? ['translations' => $moduleTranslations] : [])
                );

                foreach ($lessons as $li => [$lessonTitle, $minutes]) {
                    $lessonTranslations = [];
                    foreach ($contentTranslations as $code => $ct) {
                        if (isset($ct['lessons'][$lessonTitle])) {
                            $lessonTranslations[$code] = ['title' => $ct['lessons'][$lessonTitle]];
                        }
                    }

                    $lessonUnlocksAt = $dripLessons[$courseSlug][$lessonTitle] ?? null;

                    Lesson::updateOrCreate(
                        ['course_module_id' => $module->id, 'title' => $lessonTitle],
                        ['duration_minutes' => $minutes, 'sort_order' => $li, 'unlocks_at' => $lessonUnlocksAt !== null ? now()->addDays((int) $lessonUnlocksAt) : null]
                            + ($lessonTranslations ? ['translations' => $lessonTranslations] : [])
                    );
                }
            }

            foreach ($courseReviews as [$reviewerIdx, $rating, $comment]) {
                Review::firstOrCreate(
                    ['course_id' => $course->id, 'user_id' => $reviewers[$reviewerIdx]->id],
                    ['rating' => $rating, 'comment' => $comment]
                );
            }

            // The main student actively studies the first five courses only.
            if (array_key_exists($index, $progressMap)) {
                $enrollment = Enrollment::firstOrCreate(
                    [
                        'user_id' => $student->id,
                        'course_id' => $course->id,
                    ],
                    [
                        'progress' => $progressMap[$index],
                        'completed_at' => $progressMap[$index] >= 100 ? now()->subDays(rand(5, 40)) : null,
                    ]
                );

                if ($enrollment->isCompleted()) {
                    Certificate::firstOrCreate(
                        [
                            'user_id' => $student->id,
                            'course_id' => $course->id,
                        ],
                        [
                            'code' => 'LMS-' . strtoupper(Str::random(10)),
                            'issued_at' => $enrollment->completed_at ?? now(),
                        ]
                    );
                }
            }
        }
    }
}
