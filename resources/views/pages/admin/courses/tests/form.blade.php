@extends('layouts.dashboard')
@section('title', $quiz->exists ? 'Edit Test' : 'New Test')
@section('content')
    <div class="max-w-4xl mx-auto space-y-6">
        {{-- Header --}}
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.courses.tests.index', $course) }}" class="p-2 text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $quiz->exists ? 'Edit Test' : 'Create New Test' }}</h1>
                <p class="text-gray-500 mt-0.5 truncate">{{ $course->title }}</p>
            </div>
        </div>

        <form method="POST"
            action="{{ $quiz->exists ? route('admin.courses.tests.update', [$course, $quiz]) : route('admin.courses.tests.store', $course) }}"
            x-data="quizBuilder()" class="space-y-6">
            @csrf
            @if($quiz->exists)
                @method('PUT')
            @endif

            @error('questions')
                <div class="bg-red-50 border border-red-200 rounded-xl p-4 flex items-start gap-3">
                    <svg class="w-5 h-5 text-red-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                    <p class="text-sm text-red-700">{{ $message }}</p>
                </div>
            @enderror

            {{-- Test settings --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-5">
                <h2 class="text-lg font-semibold text-gray-900">Test Settings</h2>

                <div class="grid sm:grid-cols-2 gap-5">
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Title <span class="text-red-500">*</span></label>
                        <input type="text" name="title" value="{{ old('title', $quiz->title) }}" required maxlength="255" placeholder="e.g. Chapter 1 Quiz / Final Exam"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition @error('title') border-red-300 @enderror" />
                        @error('title')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Type <span class="text-red-500">*</span></label>
                        <select name="type" required
                            class="px-4 py-2.5 border border-gray-200 rounded-lg text-sm text-gray-700 bg-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition">
                            @foreach(['quiz' => 'Quiz', 'assignment' => 'Assignment', 'final_exam' => 'Final Exam'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('type', $quiz->type) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('type')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Passing Score (%) <span class="text-red-500">*</span></label>
                        <input type="number" name="passing_score" value="{{ old('passing_score', $quiz->passing_score) }}" required min="1" max="100"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition @error('passing_score') border-red-300 @enderror" />
                        @error('passing_score')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Duration (minutes)</label>
                        <input type="number" name="duration_minutes" value="{{ old('duration_minutes', $quiz->duration_minutes) }}" min="1" max="600" placeholder="Optional"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition @error('duration_minutes') border-red-300 @enderror" />
                    </div>

                    <div class="flex items-end pb-1">
                        <label class="inline-flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $quiz->is_active) ? 'checked' : '' }}
                                class="w-5 h-5 rounded border-gray-300 text-primary-600 focus:ring-primary-500 transition" />
                            <span class="text-sm font-medium text-gray-700">Active (visible to students)</span>
                        </label>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" rows="2" maxlength="2000" placeholder="Instructions shown before the test starts..."
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition">{{ old('description', $quiz->description) }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Questions --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-lg font-semibold text-gray-900">Questions <span class="text-sm font-normal text-gray-400" x-show="questions.length" x-text="'(' + questions.length + ')'"></span></h2>
                    <button type="button" @click="addQuestion()"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-secondary-500 hover:bg-secondary-600 rounded-lg transition shadow-sm">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                        Add Question
                    </button>
                </div>

                <template x-for="(q, qi) in questions" :key="qi">
                    <div class="border border-gray-100 rounded-xl p-5 mb-4 bg-gray-50/40">
                        <div class="flex items-start justify-between gap-3 mb-3">
                            <span class="w-7 h-7 bg-primary-600 text-white rounded-lg flex items-center justify-center text-xs font-bold shrink-0 mt-1" x-text="qi + 1"></span>
                            <button type="button" @click="removeQuestion(qi)"
                                class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition ml-auto shrink-0" title="Remove question">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                            </button>
                        </div>

                        <textarea :name="`questions[${qi}][question]`" x-model="q.question" rows="2" maxlength="1000" required
                            placeholder="Question text..." class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition"></textarea>

                        <div class="flex items-center gap-2 mt-2 mb-4">
                            <label class="text-xs font-medium text-gray-500">Points:</label>
                            <input type="number" :name="`questions[${qi}][points]`" x-model.number="q.points" min="1" max="100"
                                class="w-20 px-3 py-1.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500" />
                        </div>

                        {{-- Options --}}
                        <div class="space-y-2">
                            <template x-for="(opt, oi) in q.options" :key="oi">
                                <div class="flex items-center gap-2">
                                    <button type="button" @click="setCorrect(qi, oi)" :title="opt.is_correct ? 'Correct answer' : 'Mark as correct'"
                                        class="shrink-0 w-8 h-8 rounded-lg border flex items-center justify-center transition"
                                        :class="opt.is_correct ? 'bg-secondary-50 border-secondary-400 text-secondary-600' : 'bg-white border-gray-200 text-gray-300 hover:border-secondary-300 hover:text-secondary-400'">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                    </button>
                                    <input type="hidden" :name="`questions[${qi}][options][${oi}][is_correct]`" value="0">
                                    <input type="checkbox" :name="`questions[${qi}][options][${oi}][is_correct]`" value="1" x-model.boolean="opt.is_correct" class="hidden">
                                    <input type="text" :name="`questions[${qi}][options][${oi}][text]`" x-model="opt.text" maxlength="500"
                                        placeholder="Option text..." class="flex-1 px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition" />
                                    <button type="button" @click="removeOption(qi, oi)"
                                        class="shrink-0 p-1.5 text-gray-300 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Remove option">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>
                            </template>
                            <button type="button" @click="addOption(qi)"
                                class="text-xs font-medium text-primary-600 hover:text-primary-700 inline-flex items-center gap-1 pl-10 pt-1">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                                Add Option
                            </button>
                        </div>
                        <p class="text-xs text-gray-400 mt-3 pl-10">Click the checkmark next to an option to mark it correct.</p>
                    </div>
                </template>

                <div x-show="!questions.length" class="border border-dashed border-gray-200 rounded-xl p-10 text-center">
                    <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z"/></svg>
                    <p class="text-sm font-medium text-gray-500">No questions yet</p>
                    <p class="text-sm text-gray-400 mt-1">Each question needs at least 2 options and one marked correct.</p>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('admin.courses.tests.index', $course) }}" class="px-5 py-2.5 text-sm font-medium text-gray-600 hover:text-gray-900 transition">Cancel</a>
                <button type="submit" class="px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg text-sm transition shadow-sm">
                    {{ $quiz->exists ? 'Save Changes' : 'Create Test' }}
                </button>
            </div>
        </form>
    </div>

    <script>
        function quizBuilder() {
            return {
                questions: @js($quiz->relationLoaded('questions')
                    ? $quiz->questions->sortBy('sort_order')->values()->map(fn ($q) => [
                        'question' => $q->question,
                        'points' => (int) $q->points,
                        'options' => $q->options->values()->map(fn ($o) => [
                            'text' => $o->option_text,
                            'is_correct' => (bool) $o->is_correct,
                        ])->all(),
                    ])->all()
                    : []),

                addQuestion() {
                    this.questions.push({
                        question: '',
                        points: 1,
                        options: [{ text: '', is_correct: false }, { text: '', is_correct: false }],
                    });
                },

                removeQuestion(i) {
                    this.questions.splice(i, 1);
                },

                addOption(qi) {
                    this.questions[qi].options.push({ text: '', is_correct: false });
                },

                removeOption(qi, oi) {
                    this.questions[qi].options.splice(oi, 1);
                },

                setCorrect(qi, oi) {
                    const wasCorrect = this.questions[qi].options[oi].is_correct;
                    this.questions[qi].options.forEach((o) => (o.is_correct = false));
                    this.questions[qi].options[oi].is_correct = !wasCorrect;
                },
            };
        }
    </script>
@endsection
